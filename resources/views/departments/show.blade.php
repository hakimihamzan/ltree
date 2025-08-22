<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $department->name }} Department
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('approval_chains.index', ['department_id' => $department->id]) }}" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition">
                    View Approval Chains
                </a>
                <a href="{{ route('departments.edit', $department) }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition">
                    Edit Department
                </a>
                <a href="{{ route('departments.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition">
                    Back to Departments
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Department Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center">
                        <div class="text-3xl font-bold text-blue-600">{{ $department->users->count() }}</div>
                        <div class="text-sm font-medium text-gray-500">Users</div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center">
                        <div class="text-3xl font-bold text-green-600">{{ $department->approvalChains->count() }}</div>
                        <div class="text-sm font-medium text-gray-500">Approval Chains</div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center">
                        <div class="text-3xl font-bold text-purple-600">{{ $department->purchaseRequests->count() }}</div>
                        <div class="text-sm font-medium text-gray-500">Purchase Requests</div>
                    </div>
                </div>
            </div>

            <!-- Department Information -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Department Information</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Department Name</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $department->name }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Created</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $department->created_at->format('M d, Y g:i A') }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Last Updated</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $department->updated_at->format('M d, Y g:i A') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Users in Department -->
            @if($department->users->count() > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Department Users</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($department->users as $user)
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <div class="flex items-center mb-2">
                                        <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-full flex items-center justify-center text-white font-bold">
                                            {{ strtoupper(substr($user->name, 0, 2)) }}
                                        </div>
                                        <div class="ml-3">
                                            <h4 class="font-medium text-gray-900">{{ $user->name }}</h4>
                                            <p class="text-sm text-gray-500">{{ $user->email }}</p>
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <a href="{{ route('users.show', $user) }}" class="text-blue-600 hover:text-blue-800 text-sm">View Profile</a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Approval Chains -->
            @if($department->approvalChains->count() > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">Approval Chains</h3>
                            <a href="{{ route('approval_chains.index', ['department_id' => $department->id]) }}" class="text-blue-600 hover:text-blue-800">View Full Hierarchy</a>
                        </div>

                        @php
                            $chainsByLevel = $department->approvalChains->groupBy(function($chain) {
                                return $chain->getLevel();
                            });
                        @endphp

                        <div class="space-y-4">
                            @foreach($chainsByLevel->sortKeys() as $level => $levelChains)
                                <div class="border-l-4 border-blue-200 pl-4">
                                    <h4 class="font-medium text-gray-900 mb-2">Level {{ $level }}</h4>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($levelChains as $chain)
                                            <span class="bg-blue-100 text-blue-800 text-sm px-3 py-1 rounded-full">
                                                {{ $chain->user->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Recent Purchase Requests -->
            @if($department->purchaseRequests->count() > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Purchase Requests</h3>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Requester</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($department->purchaseRequests->take(5) as $request)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">{{ $request->item }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">${{ number_format($request->amount, 2) }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">{{ $request->requester->name }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                    @if($request->status === 'approved') bg-green-100 text-green-800
                                                    @elseif($request->status === 'rejected') bg-red-100 text-red-800
                                                    @else bg-yellow-100 text-yellow-800 @endif">
                                                    {{ ucfirst($request->status) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $request->created_at->format('M d, Y') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <a href="{{ route('purchase_requests.show', $request) }}" class="text-blue-600 hover:text-blue-900">View</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if($department->purchaseRequests->count() > 5)
                            <div class="mt-4 text-center">
                                <p class="text-gray-500">Showing 5 of {{ $department->purchaseRequests->count() }} total requests</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
