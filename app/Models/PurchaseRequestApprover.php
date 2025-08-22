<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseRequestApprover extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    // Remove boolean cast for has_approved to allow null values

    public function purchaseRequest()
    {
        return $this->belongsTo(PurchaseRequest::class);
    }

    public function approvalChain()
    {
        return $this->belongsTo(ApprovalChain::class);
    }

    public function user()
    {
        return $this->hasOneThrough(User::class, ApprovalChain::class, 'id', 'id', 'approval_chain_id', 'user_id');
    }
}
