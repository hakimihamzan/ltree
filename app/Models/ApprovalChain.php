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

    // Get next approver in chain
    public function nextApprovers()
    {
        return ApprovalChain::where('department_id', $this->department_id)
            ->whereRaw("path ~ '{$this->path}.*{1}'")
            ->orderByRaw('nlevel(path)')
            ->first();
    }

    // Get previous approver
    public function previousApprover()
    {
        $parentPath = implode('.', array_slice(explode('.', $this->path), 0, -1));
        return ApprovalChain::where('department_id', $this->department_id)
            ->where('path', $parentPath)->first();
    }
}
