<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Approval Chains') }}
            </h2>
            <div class="flex space-x-2">
                @if($departmentId)
                    <a href="{{ route('approval_chains.create', ['department_id' => $departmentId]) }}"
                       class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition">
                        Add New Approver
                    </a>
                @endif
                <a href="{{ route('approval_chains.create') }}" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition">
                    Add Approval Chain
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Department Filter -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('approval_chains.index') }}" class="flex items-center space-x-4">
                        <div class="flex-1">
                            <label for="department_id" class="block text-sm font-medium text-gray-700">Filter by Department</label>
                            <select name="department_id" id="department_id"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                onchange="this.form.submit()">
                                <option value="">All Departments</option>
                                @foreach($departments as $department)
                                    <option value="{{ $department->id }}" {{ $departmentId == $department->id ? 'selected' : '' }}>
                                        {{ $department->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>
            </div>

            @if($approvalChains->count() > 0)
                @php
                    // Group approval chains by department
                    $chainsByDepartment = $approvalChains->groupBy('department_id');
                    
                    // Define the hierarchy building function once, outside the loop
                    function buildApprovalChainHierarchy($parentChain, $allChains) {
                        $children = collect($allChains)->filter(function($chain) use ($parentChain) {
                            return strpos($chain->path, $parentChain->path . '.') === 0 &&
                                   substr_count($chain->path, '.') === substr_count($parentChain->path, '.') + 1;
                        });

                        return $children->map(function($child) use ($allChains) {
                            return [
                                'chain' => $child,
                                'children' => buildApprovalChainHierarchy($child, $allChains)
                            ];
                        });
                    }
                @endphp

                @foreach($chainsByDepartment as $deptId => $chains)
                    @php
                        $department = $chains->first()->department;

                        // Build hierarchical structure
                        $chainArray = $chains->all();
                        $rootChains = collect($chainArray)->filter(function($chain) {
                            return $chain->getLevel() == 1;
                        });

                        $hierarchyTree = $rootChains->map(function($root) use ($chainArray) {
                            return [
                                'chain' => $root,
                                'children' => buildApprovalChainHierarchy($root, $chainArray)
                            ];
                        });
                    @endphp

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
                        <div class="p-6">
                            <div class="flex justify-between items-center mb-6">
                                <h3 class="text-xl font-semibold text-gray-900">{{ $department->name }} Department</h3>
                                <span class="text-sm text-gray-500">{{ $chains->count() }} approvers in hierarchy</span>
                            </div>

                            <!-- Hierarchical Tree Visualization -->
                            <div class="bg-gray-50 rounded-lg p-6">
                                <h4 class="text-lg font-semibold text-gray-900 mb-4 text-center">📊 Approval Hierarchy</h4>
                                <div class="bg-white rounded-lg p-4 border-2 border-blue-200 font-mono text-sm">
                                    @foreach($hierarchyTree as $rootIndex => $rootNode)
                                        @include('approval_chains._hierarchy_node', [
                                            'node' => $rootNode,
                                            'depth' => 0,
                                            'isLast' => true,
                                            'parentPrefix' => ''
                                        ])
                                        @if(!$loop->last)
                                            <div class="mb-3"></div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>

                            <!-- Approval Flow Description -->
                            <div class="mt-6 bg-blue-50 rounded-lg p-4">
                                <h4 class="font-medium text-blue-900 mb-2">🔄 How Approval Works</h4>
                                <p class="text-sm text-blue-800">
                                    Purchase requests flow down the hierarchy. Each approver can either <strong>approve</strong> (moves to their direct subordinates) or <strong>reject</strong> (stops the process).
                                    If an approver has no subordinates, their approval <strong>completes</strong> the request.
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach

                <!-- Pagination -->
                @if($approvalChains->hasPages())
                    <div class="mt-6">
                        {{ $approvalChains->links() }}
                    </div>
                @endif
            @else
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-12 text-center">
                        <div class="text-6xl text-gray-300 mb-4">🏗️</div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No Approval Chains Found</h3>
                        <p class="text-gray-500 mb-6">Create your first approval chain to start managing purchase request approvals.</p>

                        @if($departmentId)
                            <a href="{{ route('approval_chains.create', ['department_id' => $departmentId]) }}"
                               class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg transition font-medium">
                                Create First Approval Chain
                            </a>
                        @else
                            <a href="{{ route('approval_chains.create') }}"
                               class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg transition font-medium">
                                Create Approval Chain
                            </a>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>

    <style>
        /* Custom styles for better visual hierarchy */
        .approval-level-1 { background: linear-gradient(135deg, #fef3c7 0%, #fcd34d 100%); }
        .approval-level-2 { background: linear-gradient(135deg, #dbeafe 0%, #60a5fa 100%); }
        .approval-level-3 { background: linear-gradient(135deg, #e0e7ff 0%, #8b5cf6 100%); }
        .approval-level-4 { background: linear-gradient(135deg, #fce7f3 0%, #ec4899 100%); }
    </style>
</x-app-layout>
