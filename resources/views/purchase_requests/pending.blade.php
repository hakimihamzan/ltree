<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Pending Approvals') }}
            </h2>
            <a href="{{ route('dashboard') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition">
                Back to Dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
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

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if($pendingApprovals->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Requester</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Current Level</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($pendingApprovals as $approval)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">{{ $approval->purchaseRequest->item }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">${{ number_format($approval->purchaseRequest->amount, 2) }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">{{ $approval->purchaseRequest->requester->name }}</div>
                                                <div class="text-sm text-gray-500">{{ $approval->purchaseRequest->requester->email }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">{{ $approval->purchaseRequest->department->name }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">Level {{ $approval->purchaseRequest->current_approval_level }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $approval->purchaseRequest->created_at->format('M d, Y') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                                <a href="{{ route('purchase_requests.show', $approval->purchaseRequest) }}"
                                                   class="text-blue-600 hover:text-blue-900">View Details</a>
                                                <div class="flex space-x-2 mt-2">
                                                    <form method="POST" action="{{ route('purchase_requests.approve', $approval->purchaseRequest) }}" class="inline">
                                                        @csrf
                                                        <button type="submit"
                                                            class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-sm transition"
                                                            onclick="return confirm('Are you sure you want to approve this request?')">
                                                            Approve
                                                        </button>
                                                    </form>
                                                    <form method="POST" action="{{ route('purchase_requests.reject', $approval->purchaseRequest) }}" class="inline">
                                                        @csrf
                                                        <button type="submit"
                                                            class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm transition"
                                                            onclick="return confirm('Are you sure you want to reject this request?')">
                                                            Reject
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            {{ $pendingApprovals->links() }}
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="text-6xl text-gray-300 mb-4">✓</div>
                            <p class="text-gray-500 text-lg">No pending approvals!</p>
                            <p class="text-gray-400 text-sm">All caught up with your approval tasks.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
