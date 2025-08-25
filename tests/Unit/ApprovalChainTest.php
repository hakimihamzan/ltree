<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Department;
use App\Models\ApprovalChain;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;

class ApprovalChainTest extends TestCase
{
    use RefreshDatabase;

    protected $department;
    protected $users;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test department
        $this->department = Department::create([
            'name' => 'Test Finance',
            'description' => 'Test Finance Department'
        ]);

        // Create test users
        $this->users = [
            'john' => User::create([
                'name' => 'John Doe',
                'email' => 'john@test.com',
                'password' => bcrypt('password'),
                'department_id' => $this->department->id
            ]),
            'mike' => User::create([
                'name' => 'Mike Smith',
                'email' => 'mike@test.com',
                'password' => bcrypt('password'),
                'department_id' => $this->department->id
            ]),
            'lisa' => User::create([
                'name' => 'Lisa Wang',
                'email' => 'lisa@test.com',
                'password' => bcrypt('password'),
                'department_id' => $this->department->id
            ]),
            'jane' => User::create([
                'name' => 'Jane Davis',
                'email' => 'jane@test.com',
                'password' => bcrypt('password'),
                'department_id' => $this->department->id
            ]),
            'sarah' => User::create([
                'name' => 'Sarah Martinez',
                'email' => 'sarah@test.com',
                'password' => bcrypt('password'),
                'department_id' => $this->department->id
            ]),
            'kyle' => User::create([
                'name' => 'Kyle Reese',
                'email' => 'kyle@test.com',
                'password' => bcrypt('password'),
                'department_id' => $this->department->id
            ])
        ];
    }

    #[Test]
    public function test_level_calculation_is_correct()
    {
        // Create approval chains with different paths
        $john = ApprovalChain::create([
            'department_id' => $this->department->id,
            'user_id' => $this->users['john']->id,
            'path' => '1'
        ]);

        $mike = ApprovalChain::create([
            'department_id' => $this->department->id,
            'user_id' => $this->users['mike']->id,
            'path' => '1.2'
        ]);

        $lisa = ApprovalChain::create([
            'department_id' => $this->department->id,
            'user_id' => $this->users['lisa']->id,
            'path' => '1.2.12'
        ]);

        $sarah = ApprovalChain::create([
            'department_id' => $this->department->id,
            'user_id' => $this->users['sarah']->id,
            'path' => '1.2.3.11'
        ]);

        // Test level calculations
        $this->assertEquals(1, $john->getLevel());
        $this->assertEquals(2, $mike->getLevel());
        $this->assertEquals(3, $lisa->getLevel());
        $this->assertEquals(4, $sarah->getLevel());
    }

    #[Test]
    public function test_terminal_approver_has_no_subordinates()
    {
        // Create hierarchy: John -> Mike -> Lisa (terminal)
        $john = ApprovalChain::create([
            'department_id' => $this->department->id,
            'user_id' => $this->users['john']->id,
            'path' => '1'
        ]);

        $mike = ApprovalChain::create([
            'department_id' => $this->department->id,
            'user_id' => $this->users['mike']->id,
            'path' => '1.2'
        ]);

        $lisa = ApprovalChain::create([
            'department_id' => $this->department->id,
            'user_id' => $this->users['lisa']->id,
            'path' => '1.2.12'
        ]);

        // Test subordinate relationships
        $this->assertCount(1, $john->nextApprovers()); // John has Mike
        $this->assertCount(1, $mike->nextApprovers()); // Mike has Lisa
        $this->assertCount(0, $lisa->nextApprovers()); // Lisa has no subordinates (terminal)

        // Verify specific relationships
        $this->assertTrue($john->nextApprovers()->contains('user_id', $this->users['mike']->id));
        $this->assertTrue($mike->nextApprovers()->contains('user_id', $this->users['lisa']->id));
    }

    #[Test]
    public function test_isolated_root_approver_scenario()
    {
        // Create Kyle as isolated root approver (like in Finance department)
        $kyle = ApprovalChain::create([
            'department_id' => $this->department->id,
            'user_id' => $this->users['kyle']->id,
            'path' => '9'
        ]);

        // Kyle should have no subordinates
        $this->assertCount(0, $kyle->nextApprovers());
        $this->assertEquals(1, $kyle->getLevel());
    }

    #[Test]
    public function test_mike_to_lisa_approval_completion_scenario()
    {
        // Recreate the exact Finance structure for Mike -> Lisa scenario
        $john = ApprovalChain::create([
            'department_id' => $this->department->id,
            'user_id' => $this->users['john']->id,
            'path' => '1'
        ]);

        $mike = ApprovalChain::create([
            'department_id' => $this->department->id,
            'user_id' => $this->users['mike']->id,
            'path' => '1.2'
        ]);

        $lisa = ApprovalChain::create([
            'department_id' => $this->department->id,
            'user_id' => $this->users['lisa']->id,
            'path' => '1.2.12'
        ]);

        $jane = ApprovalChain::create([
            'department_id' => $this->department->id,
            'user_id' => $this->users['jane']->id,
            'path' => '1.2.3'
        ]);

        $sarah = ApprovalChain::create([
            'department_id' => $this->department->id,
            'user_id' => $this->users['sarah']->id,
            'path' => '1.2.3.11'
        ]);

        // Test the approval flow: Mike -> Lisa
        $this->assertCount(2, $mike->nextApprovers()); // Mike has 2 subordinates: Lisa and Jane
        $this->assertTrue($mike->nextApprovers()->contains('user_id', $this->users['lisa']->id));
        $this->assertTrue($mike->nextApprovers()->contains('user_id', $this->users['jane']->id));

        // Lisa is terminal approver - purchase request should end here
        $this->assertCount(0, $lisa->nextApprovers());

        // Jane has subordinates (Sarah), so her approval would continue
        $this->assertCount(1, $jane->nextApprovers());
        $this->assertTrue($jane->nextApprovers()->contains('user_id', $this->users['sarah']->id));

        // Sarah is terminal in her branch
        $this->assertCount(0, $sarah->nextApprovers());
    }

    #[Test]
    public function test_multiple_approval_paths_under_same_parent()
    {
        // Create structure where Mike has multiple subordinates with different depths
        $john = ApprovalChain::create([
            'department_id' => $this->department->id,
            'user_id' => $this->users['john']->id,
            'path' => '1'
        ]);

        $mike = ApprovalChain::create([
            'department_id' => $this->department->id,
            'user_id' => $this->users['mike']->id,
            'path' => '1.2'
        ]);

        // Lisa - terminal at level 3
        $lisa = ApprovalChain::create([
            'department_id' => $this->department->id,
            'user_id' => $this->users['lisa']->id,
            'path' => '1.2.12'
        ]);

        // Jane -> Sarah - continues to level 4
        $jane = ApprovalChain::create([
            'department_id' => $this->department->id,
            'user_id' => $this->users['jane']->id,
            'path' => '1.2.3'
        ]);

        $sarah = ApprovalChain::create([
            'department_id' => $this->department->id,
            'user_id' => $this->users['sarah']->id,
            'path' => '1.2.3.11'
        ]);

        // Verify Mike can approve to either Lisa (terminal) or Jane (continues)
        $mikeSubordinates = $mike->nextApprovers();
        $this->assertCount(2, $mikeSubordinates);
        $this->assertTrue($mikeSubordinates->contains('user_id', $this->users['lisa']->id));
        $this->assertTrue($mikeSubordinates->contains('user_id', $this->users['jane']->id));

        // Verify different completion scenarios
        // Scenario 1: Mike -> Lisa = COMPLETE
        $this->assertCount(0, $lisa->nextApprovers());

        // Scenario 2: Mike -> Jane -> Sarah = COMPLETE
        $this->assertCount(1, $jane->nextApprovers());
        $this->assertCount(0, $sarah->nextApprovers());
    }

    #[Test]
    public function test_ltree_path_queries_work_correctly()
    {
        // Create a complex hierarchy to test ltree operations
        $chains = [
            ApprovalChain::create([
                'department_id' => $this->department->id,
                'user_id' => $this->users['john']->id,
                'path' => '1'
            ]),
            ApprovalChain::create([
                'department_id' => $this->department->id,
                'user_id' => $this->users['mike']->id,
                'path' => '1.2'
            ]),
            ApprovalChain::create([
                'department_id' => $this->department->id,
                'user_id' => $this->users['lisa']->id,
                'path' => '1.2.12'
            ]),
            ApprovalChain::create([
                'department_id' => $this->department->id,
                'user_id' => $this->users['jane']->id,
                'path' => '1.2.3'
            ]),
            ApprovalChain::create([
                'department_id' => $this->department->id,
                'user_id' => $this->users['sarah']->id,
                'path' => '1.2.3.11'
            ]),
            ApprovalChain::create([
                'department_id' => $this->department->id,
                'user_id' => $this->users['kyle']->id,
                'path' => '9'
            ])
        ];

        // Test that we can find direct children using ltree queries
        $john = $chains[0];
        $mike = $chains[1];
        $kyle = $chains[5];

        // John should have Mike as direct child
        $johnChildren = ApprovalChain::where('department_id', $this->department->id)
            ->whereRaw('path <@ ? AND nlevel(path) = nlevel(?) + 1', [$john->path, $john->path])
            ->get();
        $this->assertCount(1, $johnChildren);
        $this->assertEquals($this->users['mike']->id, $johnChildren->first()->user_id);

        // Mike should have Lisa and Jane as direct children
        $mikeChildren = ApprovalChain::where('department_id', $this->department->id)
            ->whereRaw('path <@ ? AND nlevel(path) = nlevel(?) + 1', [$mike->path, $mike->path])
            ->get();
        $this->assertCount(2, $mikeChildren);
        $this->assertTrue($mikeChildren->contains('user_id', $this->users['lisa']->id));
        $this->assertTrue($mikeChildren->contains('user_id', $this->users['jane']->id));

        // Kyle should have no children
        $kyleChildren = ApprovalChain::where('department_id', $this->department->id)
            ->whereRaw('path <@ ? AND nlevel(path) = nlevel(?) + 1', [$kyle->path, $kyle->path])
            ->get();
        $this->assertCount(0, $kyleChildren);
    }

    #[Test]
    public function test_approval_chain_creation_maintains_hierarchy()
    {
        // Test that creating approval chains maintains proper hierarchy
        $john = ApprovalChain::create([
            'department_id' => $this->department->id,
            'user_id' => $this->users['john']->id,
            'path' => '1'
        ]);

        // Add Mike under John
        $mike = ApprovalChain::create([
            'department_id' => $this->department->id,
            'user_id' => $this->users['mike']->id,
            'path' => $john->path . '.2'
        ]);

        // Add Lisa under Mike
        $lisa = ApprovalChain::create([
            'department_id' => $this->department->id,
            'user_id' => $this->users['lisa']->id,
            'path' => $mike->path . '.12'
        ]);

        // Verify hierarchy is maintained
        $this->assertEquals('1', $john->path);
        $this->assertEquals('1.2', $mike->path);
        $this->assertEquals('1.2.12', $lisa->path);

        // Verify parent-child relationships
        $this->assertTrue($john->nextApprovers()->contains('id', $mike->id));
        $this->assertTrue($mike->nextApprovers()->contains('id', $lisa->id));
        $this->assertCount(0, $lisa->nextApprovers());
    }

    #[Test]
    public function test_department_isolation()
    {
        // Create another department
        $itDepartment = Department::create([
            'name' => 'IT Department',
            'description' => 'Information Technology'
        ]);

        $itUser = User::create([
            'name' => 'IT Manager',
            'email' => 'it@test.com',
            'password' => bcrypt('password'),
            'department_id' => $itDepartment->id
        ]);

        // Create approval chains in both departments
        $financeChain = ApprovalChain::create([
            'department_id' => $this->department->id,
            'user_id' => $this->users['john']->id,
            'path' => '1'
        ]);

        $itChain = ApprovalChain::create([
            'department_id' => $itDepartment->id,
            'user_id' => $itUser->id,
            'path' => '1'
        ]);

        // Verify that nextApprovers only looks within the same department
        $this->assertCount(0, $financeChain->nextApprovers());
        $this->assertCount(0, $itChain->nextApprovers());

        // Add subordinate in finance
        $financeSubordinate = ApprovalChain::create([
            'department_id' => $this->department->id,
            'user_id' => $this->users['mike']->id,
            'path' => '1.2'
        ]);

        // Finance chain should now have subordinate, but IT chain should not
        $this->assertCount(1, $financeChain->nextApprovers());
        $this->assertCount(0, $itChain->nextApprovers());
    }
}
