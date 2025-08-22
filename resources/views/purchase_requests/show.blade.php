<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Purchase Request Details') }}
            </h2>
            <div class="flex space-x-2">
                @if($purchaseRequest->requester_id === Auth::id())
                    <a href="{{ route('purchase_requests.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition">
                        Back to My Requests
                    </a>
                @else
                    <a href="{{ route('purchase_requests.pending') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition">
                        Back to Pending Approvals
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Request Details -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Request Information</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Item</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $purchaseRequest->item }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Amount</label>
                            <p class="mt-1 text-sm text-gray-900 font-semibold">${{ number_format($purchaseRequest->amount, 2) }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Department</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $purchaseRequest->department->name }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Requester</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $purchaseRequest->requester->name }}</p>
                            <p class="text-xs text-gray-500">{{ $purchaseRequest->requester->email }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Status</label>
                            <span class="mt-1 px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                @if($purchaseRequest->status === 'approved') bg-green-100 text-green-800
                                @elseif($purchaseRequest->status === 'rejected') bg-red-100 text-red-800
                                @else bg-yellow-100 text-yellow-800 @endif">
                                {{ ucfirst($purchaseRequest->status) }}
                            </span>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Current Level</label>
                            <p class="mt-1 text-sm text-gray-900">Level {{ $purchaseRequest->current_approval_level }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Created</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $purchaseRequest->created_at->format('M d, Y g:i A') }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Last Updated</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $purchaseRequest->updated_at->format('M d, Y g:i A') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Approval Progress -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Approval Progress</h3>

                    @if($purchaseRequest->approvers->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Level</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Approver</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Approved At</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($purchaseRequest->approvers->sortBy('approvalChain.path') as $approver)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">Level {{ $approver->approvalChain->getLevel() }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">{{ $approver->approvalChain->user->name }}</div>
                                                <div class="text-sm text-gray-500">{{ $approver->approvalChain->user->email }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($approver->has_approved === true)
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                        Approved
                                                    </span>
                                                @elseif($approver->has_approved === false)
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                        Pending
                                                    </span>
                                                @else
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                        Not Required
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $approver->approved_at ? $approver->approved_at->format('M d, Y g:i A') : '-' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-500">No approval progress available.</p>
                    @endif
                </div>
            </div>

            <!-- Action Buttons for Approvers -->
            @php
                $userApprover = $purchaseRequest->approvers()
                    ->whereHas('approvalChain', function($query) {
                        $query->where('user_id', Auth::id());
                    })
                    ->where('has_approved', false)
                    ->first();
            @endphp

            @if($userApprover && $purchaseRequest->status === 'pending')
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Your Action Required</h3>
                        <p class="text-gray-600 mb-4">This purchase request is waiting for your approval.</p>

                        <div class="flex space-x-4">
                            <form method="POST" action="{{ route('purchase_requests.approve', $purchaseRequest) }}" class="inline">
                                @csrf
                                <button type="submit"
                                    class="bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded-lg transition"
                                    onclick="return confirm('Are you sure you want to approve this request?')">
                                    Approve Request
                                </button>
                            </form>

                            <form method="POST" action="{{ route('purchase_requests.reject', $purchaseRequest) }}" class="inline">
                                @csrf
                                <button type="submit"
                                    class="bg-red-500 hover:bg-red-600 text-white px-6 py-2 rounded-lg transition"
                                    onclick="return confirm('Are you sure you want to reject this request?')">
                                    Reject Request
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
