<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApprovalChain extends Model
{
    protected $guarded = ['id'];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Get next approver level (all approvers at the next level)
    public function nextApprovers()
    {
        $nextLevel = $this->getLevel() + 1;
        return ApprovalChain::where('department_id', $this->department_id)
            ->whereRaw('nlevel(path) = ?', [$nextLevel])
            ->get();
    }

    // Get previous approver level
    public function previousApprovers()
    {
        $prevLevel = $this->getLevel() - 1;
        if ($prevLevel < 2) return collect(); // Level 1 is department

        return ApprovalChain::where('department_id', $this->department_id)
            ->whereRaw('nlevel(path) = ?', [$prevLevel])
            ->get();
    }

    // Get the level of this approver in the hierarchy
    public function getLevel()
    {
        return substr_count($this->path, '.') + 1;
    }

    // Get all approvers at the same level
    public function sameLevelApprovers()
    {
        return ApprovalChain::where('department_id', $this->department_id)
            ->whereRaw('nlevel(path) = nlevel(?)', [$this->path])
            ->where('id', '!=', $this->id)
            ->get();
    }
}
