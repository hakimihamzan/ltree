<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseRequest extends Model
{
    protected $guarded = ['id'];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function approvers()
    {
        return $this->hasMany(PurchaseRequestApprover::class);
    }

    public function currentLevelApprovers()
    {
        return $this->approvers()
            ->whereHas('approvalChain', function ($query) {
                $query->whereRaw('nlevel(path) = ?', [$this->current_approval_level]);
            });
    }

    public function pendingApprovers()
    {
        return $this->currentLevelApprovers()->where('has_approved', false);
    }

    public function availableApprovers()
    {
        // Approvers who can still approve (haven't been bypassed)
        return $this->currentLevelApprovers()->whereIn('has_approved', [false, null]);
    }

    public function canMoveToNextLevel()
    {
        // For OR logic: can move if ANY approver at current level has approved
        return $this->currentLevelApprovers()->where('has_approved', true)->count() > 0;
    }

    public function moveToNextLevel()
    {
        if (!$this->canMoveToNextLevel()) {
            return false;
        }

        // Get the approver who just approved
        $approvedApprover = $this->currentLevelApprovers()
            ->where('has_approved', true)
            ->with('approvalChain')
            ->first();

        if (!$approvedApprover) {
            return false;
        }

        // Mark all other approvers at current level as "not needed" since one already approved
        $this->currentLevelApprovers()
            ->where('has_approved', false)
            ->update(['has_approved' => null]); // null = not needed anymore

        // Check if the approver who approved has any descendants
        $nextApproversForThisApprover = $approvedApprover->approvalChain->nextApprovers();

        if ($nextApproversForThisApprover->isEmpty()) {
            // This approver has no descendants, so approve the request
            $this->update(['status' => 'approved']);
            return true;
        }

        // Create approver records for the descendants of the approver who approved
        foreach ($nextApproversForThisApprover as $chain) {
            $this->approvers()->create([
                'approval_chain_id' => $chain->id,
                'has_approved' => false
            ]);
        }

        $this->increment('current_approval_level');
        return true;
    }

    public function initializeApprovalProcess()
    {
        // Get first level approvers (level 1 in path)
        $firstLevelChains = ApprovalChain::where('department_id', $this->department_id)
            ->whereRaw('nlevel(path) = ?', [1])
            ->get();

        foreach ($firstLevelChains as $chain) {
            $this->approvers()->create([
                'approval_chain_id' => $chain->id,
                'has_approved' => false
            ]);
        }

        $this->update(['current_approval_level' => 1]);
    }
}
