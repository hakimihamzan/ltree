<?php

namespace App\Http\Controllers;

use App\Models\ApprovalChain;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApprovalChainController extends Controller
{
    public function index(Request $request)
    {
        $departmentId = $request->get('department_id');
        $departments = Department::all();

        $approvalChains = ApprovalChain::with(['department', 'user'])
            ->when($departmentId, function($query) use ($departmentId) {
                return $query->where('department_id', $departmentId);
            })
            ->orderByRaw('department_id, nlevel(path), path')
            ->paginate(15);

        return view('approval_chains.index', compact('approvalChains', 'departments', 'departmentId'));
    }

    public function create(Request $request)
    {
        $departments = Department::all();
        $departmentId = $request->get('department_id');
        $users = User::all();

        $parentChains = [];
        if ($departmentId) {
            $parentChains = ApprovalChain::where('department_id', $departmentId)
                ->orderByRaw('nlevel(path), path')
                ->get();
        }

        return view('approval_chains.create', compact('departments', 'users', 'departmentId', 'parentChains'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'user_id' => 'required|exists:users,id',
            'parent_chain_id' => 'nullable|exists:approval_chains,id',
        ]);

        DB::transaction(function() use ($request) {
            $approvalChain = ApprovalChain::create([
                'department_id' => $request->department_id,
                'user_id' => $request->user_id,
            ]);

            // Generate path using user IDs
            if ($request->parent_chain_id) {
                $parent = ApprovalChain::find($request->parent_chain_id);
                $path = $parent->path . '.' . $approvalChain->user_id;
            } else {
                // Root level (first approver level)
                $path = $approvalChain->user_id;
            }

            $approvalChain->update(['path' => $path]);
        });

        return redirect()->route('approval_chains.index', ['department_id' => $request->department_id])
            ->with('success', 'Approval chain created successfully.');
    }

    public function show(ApprovalChain $approvalChain)
    {
        $approvalChain->load(['department', 'user']);
        return view('approval_chains.show', compact('approvalChain'));
    }

    public function edit(ApprovalChain $approvalChain)
    {
        $departments = Department::all();
        $users = User::all();

        $parentChains = ApprovalChain::where('department_id', $approvalChain->department_id)
            ->where('id', '!=', $approvalChain->id)
            ->orderByRaw('nlevel(path), path')
            ->get();

        return view('approval_chains.edit', compact('approvalChain', 'departments', 'users', 'parentChains'));
    }

    public function update(Request $request, ApprovalChain $approvalChain)
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'user_id' => 'required|exists:users,id',
            'parent_chain_id' => 'nullable|exists:approval_chains,id',
        ]);

        DB::transaction(function() use ($request, $approvalChain) {
            $approvalChain->update([
                'department_id' => $request->department_id,
                'user_id' => $request->user_id,
            ]);

            // Regenerate path using user IDs
            if ($request->parent_chain_id) {
                $parent = ApprovalChain::find($request->parent_chain_id);
                $path = $parent->path . '.' . $approvalChain->user_id;
            } else {
                $path = $approvalChain->user_id;
            }

            $approvalChain->update(['path' => $path]);
        });

        return redirect()->route('approval_chains.index', ['department_id' => $request->department_id])
            ->with('success', 'Approval chain updated successfully.');
    }

    public function destroy(ApprovalChain $approvalChain)
    {
        $departmentId = $approvalChain->department_id;
        $approvalChain->delete();

        return redirect()->route('approval_chains.index', ['department_id' => $departmentId])
            ->with('success', 'Approval chain deleted successfully.');
    }

    public function getParentChains($departmentId)
    {
        $userId = request()->get('user_id');
        $excludeChainId = request()->get('exclude_chain_id'); // For editing existing chains

        $query = ApprovalChain::where('department_id', $departmentId)
            ->with('user')
            ->orderByRaw('nlevel(path), path');

        // Don't include chains for the same user (prevent circular hierarchies)
        if ($userId) {
            $query->where('user_id', '!=', $userId);
        }

        // When editing, exclude the current chain itself
        if ($excludeChainId) {
            $query->where('id', '!=', $excludeChainId);
        }

        $parentChains = $query->get()
            ->map(function($chain) {
                return [
                    'id' => $chain->id,
                    'text' => 'Level ' . $chain->getLevel() . ': ' . $chain->user->name . ' (' . $chain->user->email . ')',
                    'level' => $chain->getLevel(),
                    'path' => $chain->path
                ];
            });

        return response()->json($parentChains);
    }
}
