<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Department;
use App\Models\ApprovalChain;
use App\Models\PurchaseRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class PurchaseRequestApprovalTest extends TestCase
{
    use RefreshDatabase;

    protected $users;
    protected $department;

    protected function setUp(): void
    {
        parent::setUp();

        // Create users
        $this->users = [
            'mike' => User::factory()->create(['name' => 'Mike', 'email' => 'mike@example.com']),
            'lisa' => User::factory()->create(['name' => 'Lisa', 'email' => 'lisa@example.com']),
            'jane' => User::factory()->create(['name' => 'Jane', 'email' => 'jane@example.com']),
            'sarah' => User::factory()->create(['name' => 'Sarah', 'email' => 'sarah@example.com']),
            'requester' => User::factory()->create(['name' => 'Requester', 'email' => 'requester@example.com']),
        ];

        // Create department
        $this->department = Department::create([
            'name' => 'IT Department',
            'code' => 'IT'
        ]);

        // Create approval chains
        // Mike (Level 1 - Root)
        $mike = ApprovalChain::create([
            'department_id' => $this->department->id,
            'user_id' => $this->users['mike']->id,
        ]);
        $mike->update(['path' => $mike->id]);

        // Lisa (Level 2 - Terminal approver, no descendants)
        $lisa = ApprovalChain::create([
            'department_id' => $this->department->id,
            'user_id' => $this->users['lisa']->id,
        ]);
        $lisa->update(['path' => $mike->path . '.' . $lisa->id]);

        // Jane (Level 2 - Has descendants)
        $jane = ApprovalChain::create([
            'department_id' => $this->department->id,
            'user_id' => $this->users['jane']->id,
        ]);
        $jane->update(['path' => $mike->path . '.' . $jane->id]);

        // Sarah (Level 3 - Under Jane)
        $sarah = ApprovalChain::create([
            'department_id' => $this->department->id,
            'user_id' => $this->users['sarah']->id,
        ]);
        $sarah->update(['path' => $jane->path . '.' . $sarah->id]);
    }

    public function test_terminal_approver_completes_request()
    {
        // Create a purchase request
        $purchaseRequest = PurchaseRequest::create([
            'department_id' => $this->department->id,
            'requester_id' => $this->users['requester']->id,
            'item' => 'Test Item',
            'amount' => 100.00,
            'status' => 'pending',
            'current_approval_level' => 1,
        ]);

        // Initialize approval process
        $purchaseRequest->initializeApprovalProcess();

        // Verify initial state
        $this->assertEquals('pending', $purchaseRequest->status);
        $this->assertEquals(1, $purchaseRequest->current_approval_level);
        $this->assertCount(1, $purchaseRequest->approvers); // Only Mike at level 1

        // Mike approves (moves to level 2)
        $mikeApprover = $purchaseRequest->approvers()->first();
        $mikeApprover->update([
            'has_approved' => true,
            'approved_at' => now(),
        ]);
        $purchaseRequest->moveToNextLevel();

        // After Mike's approval, should move to level 2 with Lisa and Jane
        $purchaseRequest->refresh();
        $this->assertEquals('pending', $purchaseRequest->status);
        $this->assertEquals(2, $purchaseRequest->current_approval_level);
        $this->assertCount(3, $purchaseRequest->approvers); // Mike + Lisa + Jane

        // Lisa approves (terminal approver - should complete the request)
        $lisaApprover = $purchaseRequest->approvers()
            ->whereHas('approvalChain', function($query) {
                $query->where('user_id', $this->users['lisa']->id);
            })->first();

        $lisaApprover->update([
            'has_approved' => true,
            'approved_at' => now(),
        ]);

        $purchaseRequest->moveToNextLevel();

        // After Lisa's approval, request should be completed
        $purchaseRequest->refresh();
        $this->assertEquals('approved', $purchaseRequest->status);
        $this->assertEquals(2, $purchaseRequest->current_approval_level); // Stays at level 2

        // Jane should be marked as "not needed" (has_approved = null)
        $janeApprover = $purchaseRequest->approvers()
            ->whereHas('approvalChain', function($query) {
                $query->where('user_id', $this->users['jane']->id);
            })->first();
        $this->assertNull($janeApprover->has_approved);
    }

    public function test_non_terminal_approver_continues_to_descendants()
    {
        // Create a purchase request
        $purchaseRequest = PurchaseRequest::create([
            'department_id' => $this->department->id,
            'requester_id' => $this->users['requester']->id,
            'item' => 'Test Item',
            'amount' => 100.00,
            'status' => 'pending',
            'current_approval_level' => 1,
        ]);

        // Initialize approval process
        $purchaseRequest->initializeApprovalProcess();

        // Mike approves (moves to level 2)
        $mikeApprover = $purchaseRequest->approvers()->first();
        $mikeApprover->update([
            'has_approved' => true,
            'approved_at' => now(),
        ]);
        $purchaseRequest->moveToNextLevel();

        // Jane approves (has descendants - should continue to Sarah)
        $janeApprover = $purchaseRequest->approvers()
            ->whereHas('approvalChain', function($query) {
                $query->where('user_id', $this->users['jane']->id);
            })->first();

        $janeApprover->update([
            'has_approved' => true,
            'approved_at' => now(),
        ]);

        $purchaseRequest->moveToNextLevel();

        // After Jane's approval, should move to level 3 with Sarah
        $purchaseRequest->refresh();
        $this->assertEquals('pending', $purchaseRequest->status);
        $this->assertEquals(3, $purchaseRequest->current_approval_level);

        // Should have Sarah as approver at level 3
        $sarahApprover = $purchaseRequest->approvers()
            ->whereHas('approvalChain', function($query) {
                $query->where('user_id', $this->users['sarah']->id);
            })->first();
        $this->assertNotNull($sarahApprover);
        $this->assertFalse($sarahApprover->has_approved);

        // Now Sarah approves (terminal - should complete)
        $sarahApprover->update([
            'has_approved' => true,
            'approved_at' => now(),
        ]);

        $purchaseRequest->moveToNextLevel();

        // After Sarah's approval, request should be completed
        $purchaseRequest->refresh();
        $this->assertEquals('approved', $purchaseRequest->status);
    }
}
