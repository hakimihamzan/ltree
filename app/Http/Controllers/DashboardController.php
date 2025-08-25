<?php

namespace App\Http\Controllers;

use App\Models\PurchaseRequest;
use App\Models\PurchaseRequestApprover;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Get submitted purchase requests
        $submittedRequests = $user->submittedPurchaseRequests()
            ->with(['department', 'approvers.approvalChain.user'])
            ->latest()
            ->paginate(10, ['*'], 'submitted');

        // Get pending approvals for this user
        $pendingApprovals = PurchaseRequestApprover::whereHas('approvalChain', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->where('has_approved', false)
            ->with(['purchaseRequest.department', 'purchaseRequest.requester'])
            ->latest()
            ->paginate(10, ['*'], 'pending');

        return view('dashboard', compact('submittedRequests', 'pendingApprovals'));
    }
}
