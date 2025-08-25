<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

/**
 * @mixin IdeHelperPurchaseRequestApprover
 */
class PurchaseRequestApprover extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    // Remove boolean cast for has_approved to allow null values

    public function purchaseRequest(): BelongsTo
    {
        return $this->belongsTo(PurchaseRequest::class);
    }

    public function approvalChain(): BelongsTo
    {
        return $this->belongsTo(ApprovalChain::class);
    }

    public function user(): HasOneThrough
    {
        return $this->hasOneThrough(User::class, ApprovalChain::class, 'id', 'id', 'approval_chain_id', 'user_id');
    }
}
