<?php

namespace App\Http\Controllers;

use App\Models\PurchaseRequest;
use App\Models\PurchaseRequestApprover;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PurchaseRequestController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $purchaseRequests = $user->submittedPurchaseRequests()
            ->with(['department', 'approvers.approvalChain.user'])
            ->latest()
            ->paginate(10);

        return view('purchase_requests.index', compact('purchaseRequests'));
    }

    public function create()
    {
        $departments = Department::all();
        return view('purchase_requests.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'item' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
        ]);

        DB::transaction(function() use ($request) {
            $purchaseRequest = PurchaseRequest::create([
                'department_id' => $request->department_id,
                'requester_id' => Auth::id(),
                'item' => $request->item,
                'amount' => $request->amount,
                'status' => 'pending',
                'current_approval_level' => 1,
            ]);

            // Initialize approval process
            $purchaseRequest->initializeApprovalProcess();
        });

        return redirect()->route('purchase_requests.index')
            ->with('success', 'Purchase request created successfully.');
    }

    public function show(PurchaseRequest $purchaseRequest)
    {
        // Check if user can view this purchase request
        $user = Auth::user();
        $canView = $purchaseRequest->requester_id === $user->id ||
                   $purchaseRequest->approvers()->whereHas('approvalChain', function($query) use ($user) {
                       $query->where('user_id', $user->id);
                   })->exists();

        if (!$canView) {
            abort(403, 'Unauthorized access to this purchase request.');
        }

        $purchaseRequest->load([
            'department',
            'requester',
            'approvers.approvalChain.user'
        ]);

        return view('purchase_requests.show', compact('purchaseRequest'));
    }

    public function edit(PurchaseRequest $purchaseRequest)
    {
        // Only requester can edit if it's still pending at level 1
        if ($purchaseRequest->requester_id !== Auth::id() || $purchaseRequest->current_approval_level > 1) {
            abort(403, 'Cannot edit this purchase request.');
        }

        $departments = Department::all();
        return view('purchase_requests.edit', compact('purchaseRequest', 'departments'));
    }

    public function update(Request $request, PurchaseRequest $purchaseRequest)
    {
        // Only requester can edit if it's still pending at level 1
        if ($purchaseRequest->requester_id !== Auth::id() || $purchaseRequest->current_approval_level > 1) {
            abort(403, 'Cannot edit this purchase request.');
        }

        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'item' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
        ]);

        $purchaseRequest->update([
            'department_id' => $request->department_id,
            'item' => $request->item,
            'amount' => $request->amount,
        ]);

        return redirect()->route('purchase_requests.index')
            ->with('success', 'Purchase request updated successfully.');
    }

    public function destroy(PurchaseRequest $purchaseRequest)
    {
        // Only requester can delete if it's still pending at level 1
        if ($purchaseRequest->requester_id !== Auth::id() || $purchaseRequest->current_approval_level > 1) {
            abort(403, 'Cannot delete this purchase request.');
        }

        $purchaseRequest->delete();

        return redirect()->route('purchase_requests.index')
            ->with('success', 'Purchase request deleted successfully.');
    }

    public function approve(PurchaseRequest $purchaseRequest)
    {
        $user = Auth::user();

        // Find the approver record for this user and purchase request
        $approver = $purchaseRequest->approvers()
            ->whereHas('approvalChain', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->where('has_approved', false)
            ->first();

        if (!$approver) {
            return redirect()->back()->with('error', 'You are not authorized to approve this request.');
        }

        DB::transaction(function() use ($approver, $purchaseRequest) {
            // Mark as approved
            $approver->update([
                'has_approved' => true,
                'approved_at' => now(),
            ]);

            // Try to move to next level
            $purchaseRequest->moveToNextLevel();
        });

        return redirect()->back()->with('success', 'Purchase request approved successfully.');
    }

    public function reject(PurchaseRequest $purchaseRequest)
    {
        $user = Auth::user();

        // Find the approver record for this user and purchase request
        $approver = $purchaseRequest->approvers()
            ->whereHas('approvalChain', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->where('has_approved', false)
            ->first();

        if (!$approver) {
            return redirect()->back()->with('error', 'You are not authorized to reject this request.');
        }

        $purchaseRequest->update(['status' => 'rejected']);

        return redirect()->back()->with('success', 'Purchase request rejected.');
    }

    public function pending()
    {
        $user = Auth::user();

        $pendingApprovals = PurchaseRequestApprover::whereHas('approvalChain', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->where('has_approved', false)
            ->with(['purchaseRequest.department', 'purchaseRequest.requester'])
            ->latest()
            ->paginate(10);

        return view('purchase_requests.pending', compact('pendingApprovals'));
    }
}
