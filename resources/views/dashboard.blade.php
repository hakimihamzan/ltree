<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Quick Actions -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg" style="background-color: white; border-radius: 0.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <div class="p-6" style="padding: 1.5rem;">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4" style="font-size: 1.125rem; font-weight: 600; color: #111827; margin-bottom: 1rem;">Quick Actions</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem;">
                        <a href="{{ route('purchase_requests.create') }}"
                           class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-4 rounded-lg text-center transition-colors duration-200 shadow-md hover:shadow-lg"
                           style="background-color: #3b82f6; color: white; padding: 1rem 1.5rem; border-radius: 0.5rem; text-align: center; text-decoration: none; display: block; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                            <div class="font-semibold text-lg" style="font-weight: 600; font-size: 1.125rem;">New Purchase Request</div>
                            <div class="text-sm opacity-90 mt-1" style="font-size: 0.875rem; opacity: 0.9; margin-top: 0.25rem;">Create a new request</div>
                        </a>
                        <a href="{{ route('purchase_requests.pending') }}"
                           class="bg-green-500 hover:bg-green-600 text-white px-6 py-4 rounded-lg text-center transition-colors duration-200 shadow-md hover:shadow-lg"
                           style="background-color: #10b981; color: white; padding: 1rem 1.5rem; border-radius: 0.5rem; text-align: center; text-decoration: none; display: block; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                            <div class="font-semibold text-lg" style="font-weight: 600; font-size: 1.125rem;">Pending Approvals</div>
                            <div class="text-sm opacity-90 mt-1" style="font-size: 0.875rem; opacity: 0.9; margin-top: 0.25rem;">({{ $pendingApprovals->total() }}) waiting for you</div>
                        </a>
                        <a href="{{ route('departments.index') }}"
                           class="bg-purple-500 hover:bg-purple-600 text-white px-6 py-4 rounded-lg text-center transition-colors duration-200 shadow-md hover:shadow-lg"
                           style="background-color: #8b5cf6; color: white; padding: 1rem 1.5rem; border-radius: 0.5rem; text-align: center; text-decoration: none; display: block; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                            <div class="font-semibold text-lg" style="font-weight: 600; font-size: 1.125rem;">Departments</div>
                            <div class="text-sm opacity-90 mt-1" style="font-size: 0.875rem; opacity: 0.9; margin-top: 0.25rem;">Manage departments</div>
                        </a>
                        <a href="{{ route('users.index') }}"
                           class="bg-indigo-500 hover:bg-indigo-600 text-white px-6 py-4 rounded-lg text-center transition-colors duration-200 shadow-md hover:shadow-lg"
                           style="background-color: #6366f1; color: white; padding: 1rem 1.5rem; border-radius: 0.5rem; text-align: center; text-decoration: none; display: block; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                            <div class="font-semibold text-lg" style="font-weight: 600; font-size: 1.125rem;">Users</div>
                            <div class="text-sm opacity-90 mt-1" style="font-size: 0.875rem; opacity: 0.9; margin-top: 0.25rem;">Manage users</div>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Submitted Purchase Requests -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Your Submitted Requests</h3>
                        <a href="{{ route('purchase_requests.index') }}" class="text-blue-600 hover:text-blue-800">View All</a>
                    </div>

                    @if($submittedRequests->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Level</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($submittedRequests->take(5) as $request)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <a href="{{ route('purchase_requests.show', $request) }}" class="text-blue-600 hover:text-blue-900">
                                                    {{ $request->item }}
                                                </a>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">${{ number_format($request->amount, 2) }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $request->department->name }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                    @if($request->status === 'approved') bg-green-100 text-green-800
                                                    @elseif($request->status === 'rejected') bg-red-100 text-red-800
                                                    @else bg-yellow-100 text-yellow-800 @endif">
                                                    {{ ucfirst($request->status) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">Level {{ $request->current_approval_level }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $request->created_at->format('M d, Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-500">No purchase requests submitted yet.</p>
                    @endif
                </div>
            </div>

            <!-- Pending Approvals -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Pending Your Approval</h3>
                        <a href="{{ route('purchase_requests.pending') }}" class="text-blue-600 hover:text-blue-800">View All</a>
                    </div>

                    @if($pendingApprovals->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Requester</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($pendingApprovals->take(5) as $approval)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <a href="{{ route('purchase_requests.show', $approval->purchaseRequest) }}" class="text-blue-600 hover:text-blue-900">
                                                    {{ $approval->purchaseRequest->item }}
                                                </a>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">${{ number_format($approval->purchaseRequest->amount, 2) }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $approval->purchaseRequest->requester->name }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $approval->purchaseRequest->department->name }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $approval->purchaseRequest->created_at->format('M d, Y') }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                                <form method="POST" action="{{ route('purchase_requests.approve', $approval->purchaseRequest) }}" class="inline">
                                                    @csrf
                                                    <button type="submit" class="text-green-600 hover:text-green-900">Approve</button>
                                                </form>
                                                <form method="POST" action="{{ route('purchase_requests.reject', $approval->purchaseRequest) }}" class="inline">
                                                    @csrf
                                                    <button type="submit" class="text-red-600 hover:text-red-900">Reject</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-500">No pending approvals.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
