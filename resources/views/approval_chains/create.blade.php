<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-blue-800 leading-tight" style="color: #1e40af;">
                Add New Approver
            </h2>
            <a href="{{ route('approval_chains.index') }}" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition font-semibold">
                Back to Approval Chains
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Progress Steps -->
            <div class="mb-8">
                <div class="flex items-center justify-center space-x-4">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-green-500 text-white rounded-full flex items-center justify-center text-sm font-medium">1</div>
                        <span class="ml-2 text-sm font-bold text-green-700" style="color: #15803d;">Choose Department</span>
                    </div>
                    <div class="w-8 h-0.5 bg-orange-300"></div>
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-orange-400 text-white rounded-full flex items-center justify-center text-sm font-medium" id="step2">2</div>
                        <span class="ml-2 text-sm font-bold text-orange-600" id="step2-text" style="color: #ea580c;">Select User</span>
                    </div>
                    <div class="w-8 h-0.5 bg-orange-300"></div>
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-purple-500 text-white rounded-full flex items-center justify-center text-sm font-medium" id="step3">3</div>
                        <span class="ml-2 text-sm font-bold text-purple-600" id="step3-text" style="color: #9333ea;">Set Position</span>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-blue-50 to-purple-50 overflow-hidden shadow-lg sm:rounded-lg border-2 border-blue-200">
                <div class="p-8">
                    <form method="POST" action="{{ route('approval_chains.store') }}" id="approval-form">
                        @csrf

                        <!-- Step 1: Department Selection -->
                        <div id="step-1" class="step-content">
                            <div class="text-center mb-8">
                                <h3 class="text-3xl font-bold text-blue-800 mb-4" style="color: #1e40af;">🏢 Choose a Department</h3>
                                <p class="text-lg text-purple-700" style="color: #7c3aed;">Select the department where you want to add an approver</p>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 max-w-2xl mx-auto">
                                @foreach($departments as $department)
                                    <div class="department-card border-3 border-blue-300 rounded-lg p-6 cursor-pointer hover:border-green-500 hover:bg-green-100 transition-all duration-200 bg-gradient-to-r from-cyan-100 to-blue-100 shadow-lg"
                                         onclick="selectDepartment({{ $department->id }}, '{{ $department->name }}', this)">
                                        <div class="text-center">
                                            <div class="w-16 h-16 bg-gradient-to-r from-pink-500 to-red-500 rounded-full flex items-center justify-center text-white text-2xl font-bold mx-auto mb-4 shadow-lg">
                                                {{ strtoupper(substr($department->name, 0, 2)) }}
                                            </div>
                                            <h4 class="text-xl font-bold text-blue-800 mb-2" style="color: #1e40af;">{{ $department->name }}</h4>
                                            <p class="text-sm font-semibold text-purple-600" style="color: #9333ea;">
                                                👥 {{ $department->users->count() }} users •
                                                ✅ {{ $department->approvalChains->count() }} approvers
                                            </p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <input type="hidden" name="department_id" id="department_id" value="{{ old('department_id', $departmentId) }}">
                            @error('department_id')
                                <p class="mt-4 text-sm text-red-600 text-center">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Step 2: User Selection -->
                        <div id="step-2" class="step-content hidden">
                            <div class="text-center mb-8">
                                <h3 class="text-3xl font-bold text-orange-700 mb-4" style="color: #c2410c;">👤 Select User to Add as Approver</h3>
                                <p class="text-lg text-green-700" style="color: #15803d;">Choose who will be the approver in <span id="selected-dept-name" class="font-bold text-red-600" style="color: #dc2626;"></span></p>
                            </div>

                            <div id="users-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 max-w-4xl mx-auto">
                                <!-- Users will be loaded here dynamically -->
                            </div>

                            <input type="hidden" name="user_id" id="user_id" value="{{ old('user_id') }}">
                            @error('user_id')
                                <p class="mt-4 text-sm text-red-600 text-center">{{ $message }}</p>
                            @enderror

                            <div class="flex justify-center mt-8">
                                <button type="button" onclick="goToStep(1)" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition mr-4">
                                    Back
                                </button>
                            </div>
                        </div>

                        <!-- Step 3: Position Selection -->
                        <div id="step-3" class="step-content hidden">
                            <div class="text-center mb-8">
                                <h3 class="text-2xl font-bold text-gray-900 mb-4">Set Approval Position</h3>
                                <p class="text-gray-600">Choose where <span id="selected-user-name" class="font-medium text-blue-600"></span> should be in the approval hierarchy</p>
                            </div>

                            <!-- Current Hierarchy Display -->
                            <div id="current-hierarchy" class="mb-8">
                                <!-- Hierarchy will be loaded here -->
                            </div>

                            <!-- Position Options -->
                            <div class="max-w-2xl mx-auto">
                                <div class="space-y-4">
                                    <div class="position-option border-2 border-gray-200 rounded-lg p-4 cursor-pointer hover:border-green-500 hover:bg-green-50 transition-all duration-200"
                                         onclick="selectPosition('', 1, this)">>
                                        <div class="flex items-center">
                                            <div class="w-12 h-12 bg-green-500 text-white rounded-full flex items-center justify-center text-lg font-bold mr-4">1</div>
                                            <div>
                                                <h4 class="text-lg font-semibold text-gray-900">Root Level Approver</h4>
                                                <p class="text-sm text-gray-500">First person to approve requests (Level 1)</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="sub-level-options">
                                        <!-- Sub-level options will be populated here -->
                                    </div>
                                </div>
                            </div>

                            <input type="hidden" name="parent_chain_id" id="parent_chain_id" value="{{ old('parent_chain_id') }}">
                            @error('parent_chain_id')
                                <p class="mt-4 text-sm text-red-600 text-center">{{ $message }}</p>
                            @enderror

                            <div class="flex justify-center mt-8 space-x-4">
                                <button type="button" onclick="goToStep(2)" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition">
                                    Back
                                </button>
                                <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-8 py-2 rounded-lg transition font-semibold">
                                    Add Approver
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <style>
        .step-content {
            min-height: 400px;
        }
        .department-card.selected {
            border-color: #3b82f6 !important;
            background-color: #eff6ff !important;
        }
        .user-card.selected {
            border-color: #3b82f6 !important;
            background-color: #eff6ff !important;
        }
        .position-option.selected {
            border-color: #10b981 !important;
            background-color: #ecfdf5 !important;
        }
    </style>

    <script>
        let currentStep = 1;
        let selectedDepartment = null;
        let selectedUser = null;
        let approvalChains = [];

        // Initialize with pre-selected department if available
        document.addEventListener('DOMContentLoaded', function() {
            const departmentId = document.getElementById('department_id').value;
            if (departmentId) {
                // Find the department and auto-select it
                const departments = @json($departments);
                const dept = departments.find(d => d.id == departmentId);
                if (dept) {
                    selectDepartment(dept.id, dept.name, null);
                }
            }
        });

        function selectDepartment(deptId, deptName, element) {
            selectedDepartment = { id: deptId, name: deptName };
            document.getElementById('department_id').value = deptId;
            document.getElementById('selected-dept-name').textContent = deptName;

            // Highlight selected department
            document.querySelectorAll('.department-card').forEach(card => {
                card.classList.remove('selected');
            });

            if (element) {
                element.classList.add('selected');
            }

            // Load users for this department
            loadUsers(deptId);

            setTimeout(function() { goToStep(2); }, 300);
        }

        function loadUsers(departmentId) {
            const usersGrid = document.getElementById('users-grid');
            usersGrid.innerHTML = '<div class="col-span-full text-center text-gray-500">Loading users...</div>';

            const users = @json($users);

            usersGrid.innerHTML = '';
            users.forEach(function(user) {
                const userCard = document.createElement('div');
                userCard.className = 'user-card border-2 border-gray-200 rounded-lg p-4 cursor-pointer hover:border-blue-500 hover:bg-blue-50 transition-all duration-200';
                userCard.onclick = function(e) {
                    selectUser(user.id, user.name, user.email, e.target);
                };

                const initials = user.name.substring(0, 2).toUpperCase();
                userCard.innerHTML =
                    '<div class="text-center">' +
                        '<div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-pink-600 rounded-full flex items-center justify-center text-white text-lg font-bold mx-auto mb-3">' +
                            initials +
                        '</div>' +
                        '<h4 class="text-sm font-semibold text-gray-900 mb-1">' + user.name + '</h4>' +
                        '<p class="text-xs text-gray-500">' + user.email + '</p>' +
                    '</div>';

                usersGrid.appendChild(userCard);
            });
        }

        function selectUser(userId, userName, userEmail, element) {
            selectedUser = { id: userId, name: userName, email: userEmail };
            document.getElementById('user_id').value = userId;
            document.getElementById('selected-user-name').textContent = userName;

            // Highlight selected user
            document.querySelectorAll('.user-card').forEach(function(card) {
                card.classList.remove('selected');
            });

            if (element && element.closest) {
                element.closest('.user-card').classList.add('selected');
            }

            // Load approval chains for position selection
            loadApprovalChains(selectedDepartment.id);

            setTimeout(function() { goToStep(3); }, 300);
        }

        function loadApprovalChains(departmentId) {
            const hierarchyDiv = document.getElementById('current-hierarchy');
            const subLevelOptions = document.getElementById('sub-level-options');

            hierarchyDiv.innerHTML = '<div class="text-center text-gray-500">Loading current hierarchy...</div>';
            subLevelOptions.innerHTML = '';

            fetch('/approval_chains_parents/' + departmentId)
                .then(function(response) { return response.json(); })
                .then(function(data) {
                    approvalChains = data;
                    displayCurrentHierarchy(data);
                    displaySubLevelOptions(data);
                })
                .catch(function(error) {
                    console.error('Error loading approval chains:', error);
                    hierarchyDiv.innerHTML = '<div class="text-center text-red-500">Error loading hierarchy</div>';
                });
        }

        function displayCurrentHierarchy(chains) {
            const hierarchyDiv = document.getElementById('current-hierarchy');

            console.log('displayCurrentHierarchy called with:', chains);

            if (chains.length === 0) {
                hierarchyDiv.innerHTML =
                    '<div class="text-center py-8">' +
                        '<div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">' +
                            '<svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">' +
                                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>' +
                            '</svg>' +
                        '</div>' +
                        '<h4 class="text-lg font-semibold text-gray-900 mb-2">No Approvers Yet</h4>' +
                        '<p class="text-gray-500">This department doesn\'t have any approvers. You\'ll be the first!</p>' +
                    '</div>';
                return;
            }

            // Simple test: show basic list first
            let testHTML = '<div class="bg-yellow-100 p-4 mb-4"><h4>DEBUG: Raw Data</h4>';
            chains.forEach(function(chain) {
                testHTML += '<div>ID: ' + chain.id + ' | Level: ' + chain.level + ' | Path: ' + chain.path + ' | Name: ' + chain.text + '</div>';
            });
            testHTML += '</div>';

            // Build hierarchical tree structure
            function buildHierarchyTree(chains) {
                console.log('Building hierarchy tree from chains:', chains);

                // Find root nodes (level 1)
                const roots = chains.filter(function(chain) {
                    return chain.level === 1;
                });

                console.log('Found roots:', roots);

                function buildChildren(parentChain, allChains) {
                    const children = allChains.filter(function(chain) {
                        // Check if this chain is a direct child of parentChain
                        return chain.path.indexOf(parentChain.path + '.') === 0 &&
                               chain.path.split('.').length === parentChain.path.split('.').length + 1;
                    });

                    return children.map(function(child) {
                        return {
                            ...child,
                            children: buildChildren(child, allChains)
                        };
                    });
                }

                return roots.map(function(root) {
                    return {
                        ...root,
                        children: buildChildren(root, chains)
                    };
                });
            }

            function renderNode(node, depth, isLast, parentPrefix) {
                const name = node.text.split(': ')[1].split(' (')[0];
                const currentPrefix = parentPrefix + (depth === 0 ? '' : (isLast ? '└── ' : '├── '));
                const nextPrefix = parentPrefix + (depth === 0 ? '' : (isLast ? '    ' : '│   '));

                let html = '<div class="font-mono text-sm leading-relaxed text-gray-700">' +
                           '<span class="text-blue-600">' + currentPrefix + '</span>';

                if (depth === 0) {
                    html += '<span class="text-lg">🔹</span> ';
                }

                html += '<span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs font-semibold mr-2">' +
                        'L' + node.level + '</span>' +
                        '<span class="font-semibold text-gray-900">' + name + '</span>' +
                        '</div>';

                if (node.children && node.children.length > 0) {
                    node.children.forEach(function(child, index) {
                        const isChildLast = index === node.children.length - 1;
                        html += renderNode(child, depth + 1, isChildLast, nextPrefix);
                    });
                }

                return html;
            }

            const hierarchyTree = buildHierarchyTree(chains);
            console.log('Built hierarchy tree:', hierarchyTree);

            let hierarchyHTML = testHTML +
                '<div class="bg-gray-50 rounded-lg p-6 mb-6">' +
                    '<h4 class="text-lg font-semibold text-gray-900 mb-4 text-center">📊 Current Approval Hierarchy</h4>' +
                    '<div class="bg-white rounded-lg p-4 border-2 border-blue-200">';

            hierarchyTree.forEach(function(rootNode) {
                hierarchyHTML += renderNode(rootNode, 0, true, '');
                hierarchyHTML += '<div class="mb-3"></div>'; // Space between root trees
            });

            hierarchyHTML +=
                    '</div>' +
                '</div>';

            hierarchyDiv.innerHTML = hierarchyHTML;
        }        function displaySubLevelOptions(chains) {
            const subLevelOptions = document.getElementById('sub-level-options');

            if (chains.length === 0) {
                return;
            }

            let optionsHTML = '';

            // Group by level for display
            const byLevel = chains.reduce(function(acc, chain) {
                const level = chain.level;
                if (!acc[level]) acc[level] = [];
                acc[level].push(chain);
                return acc;
            }, {});

            Object.keys(byLevel).sort(function(a, b) { return parseInt(a) - parseInt(b); }).forEach(function(level) {
                const levelChains = byLevel[level];

                levelChains.forEach(function(chain) {
                    const name = chain.text.split(': ')[1].split(' (')[0];
                    const nextLevel = parseInt(level) + 1;

                    optionsHTML +=
                        '<div class="position-option border-2 border-gray-200 rounded-lg p-4 cursor-pointer hover:border-green-500 hover:bg-green-50 transition-all duration-200"' +
                             ' onclick="selectPosition(' + chain.id + ', ' + nextLevel + ', this)">' +
                            '<div class="flex items-center">' +
                                '<div class="w-12 h-12 bg-green-500 text-white rounded-full flex items-center justify-center text-lg font-bold mr-4">' +
                                    nextLevel +
                                '</div>' +
                                '<div>' +
                                    '<h4 class="text-lg font-semibold text-gray-900">Under ' + name + '</h4>' +
                                    '<p class="text-sm text-gray-500">Will be at Level ' + nextLevel + ' (reports to ' + name + ')</p>' +
                                '</div>' +
                            '</div>' +
                        '</div>';
                });
            });

            subLevelOptions.innerHTML = optionsHTML;
        }

        function selectPosition(parentId, level, element) {
            document.getElementById('parent_chain_id').value = parentId || '';

            // Highlight selected position
            document.querySelectorAll('.position-option').forEach(function(option) {
                option.classList.remove('selected');
            });

            if (element && element.closest) {
                element.closest('.position-option').classList.add('selected');
            }
        }

        function goToStep(step) {
            // Hide all steps
            document.querySelectorAll('.step-content').forEach(function(content) {
                content.classList.add('hidden');
            });

            // Show target step
            document.getElementById('step-' + step).classList.remove('hidden');

            // Update progress indicators
            for (let i = 1; i <= 3; i++) {
                const stepEl = document.getElementById('step' + i);
                const stepText = document.getElementById('step' + i + '-text');

                if (stepEl && stepText) {
                    if (i <= step) {
                        stepEl.classList.remove('bg-orange-400', 'bg-purple-500');
                        stepEl.classList.add('bg-green-500');
                        stepText.classList.remove('text-orange-600', 'text-purple-600');
                        stepText.classList.add('text-green-700');
                    } else {
                        stepEl.classList.remove('bg-green-500');
                        if (i === 2) {
                            stepEl.classList.add('bg-orange-400');
                            stepText.classList.add('text-orange-600');
                        } else if (i === 3) {
                            stepEl.classList.add('bg-purple-500');
                            stepText.classList.add('text-purple-600');
                        }
                    }
                }
            }

            currentStep = step;
        }
    </script>
</x-app-layout>
