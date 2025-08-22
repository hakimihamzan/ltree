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
                $query->whereRaw('nlevel(path) = ?', [$this->current_approval_level + 1]); // +1 because path includes department
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

        // Mark all other approvers at current level as "not needed" since one already approved
        $this->currentLevelApprovers()
            ->where('has_approved', false)
            ->update(['has_approved' => null]); // null = not needed anymore

        // Get next level approvers
        $nextLevel = $this->current_approval_level + 1;
        $nextLevelChains = ApprovalChain::where('department_id', $this->department_id)
            ->whereRaw('nlevel(path) = ?', [$nextLevel + 1]) // +1 because path includes department
            ->get();

        if ($nextLevelChains->isEmpty()) {
            // No more levels, mark as fully approved
            $this->update(['status' => 'approved']);
            return true;
        }

        // Create approver records for next level
        foreach ($nextLevelChains as $chain) {
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
        // Get first level approvers (level 2 in path since level 1 is department)
        $firstLevelChains = ApprovalChain::where('department_id', $this->department_id)
            ->whereRaw('nlevel(path) = ?', [2])
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
