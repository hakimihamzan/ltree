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
    
    public function currentApprover()
    {
        return $this->belongsTo(ApprovalChain::class, 'current_approver_id');
    }
}
