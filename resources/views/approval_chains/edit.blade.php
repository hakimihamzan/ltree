<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit Approval Chain') }}
            </h2>
            <a href="{{ route('approval_chains.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition">
                Back to Approval Chains
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <h4 class="font-semibold">Please fix the following errors:</h4>
                    <ul class="list-disc list-inside mt-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Edit Approval Chain</h3>
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-sm text-yellow-800">
                            <p><strong>Warning:</strong> Changing the approval chain may affect existing purchase requests that are currently in progress.</p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('approval_chains.update', $approvalChain) }}">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 gap-6">
                            <!-- Department -->
                            <div>
                                <label for="department_id" class="block text-sm font-medium text-gray-700">Department</label>
                                <select name="department_id" id="department_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    onchange="updateParentOptions()">
                                    <option value="">Select Department</option>
                                    @foreach($departments as $department)
                                        <option value="{{ $department->id }}"
                                            {{ old('department_id', $approvalChain->department_id) == $department->id ? 'selected' : '' }}>
                                            {{ $department->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('department_id')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- User -->
                            <div>
                                <label for="user_id" class="block text-sm font-medium text-gray-700">User (Approver)</label>
                                <select name="user_id" id="user_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Select User</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}"
                                            {{ old('user_id', $approvalChain->user_id) == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }} ({{ $user->email }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('user_id')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Parent Chain -->
                            <div>
                                <label for="parent_chain_id" class="block text-sm font-medium text-gray-700">Parent Approver (Optional)</label>
                                <select name="parent_chain_id" id="parent_chain_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">None (Root Level)</option>
                                    @foreach($parentChains as $chain)
                                        @php
                                            $currentParentId = null;
                                            if($approvalChain->path) {
                                                $pathParts = explode('.', $approvalChain->path);
                                                if(count($pathParts) > 2) {
                                                    $currentParentId = $pathParts[count($pathParts) - 2];
                                                }
                                            }
                                        @endphp
                                        <option value="{{ $chain->id }}"
                                            {{ old('parent_chain_id', $currentParentId) == $chain->id ? 'selected' : '' }}>
                                            Level {{ $chain->getLevel() }}: {{ $chain->user->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <p class="mt-1 text-sm text-gray-500">Select a parent to create a sub-level approval. Leave empty for root level.</p>
                                <div id="parent-loading" class="mt-1 text-sm text-blue-600 hidden">Loading available parents...</div>
                                @error('parent_chain_id')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Level Preview -->
                            <div id="level-preview" class="hidden">
                                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                                    <h4 class="font-medium text-green-800 mb-2">Updated Approver Level</h4>
                                    <p class="text-sm text-green-700">
                                        This approver will be at <span id="preview-level" class="font-bold">Level 1</span> in the approval hierarchy.
                                    </p>
                                </div>
                            </div>

                            <!-- Current Path Display -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Current Path</label>
                                <p class="mt-1 text-sm text-gray-500 font-mono bg-gray-50 p-2 rounded">{{ $approvalChain->path }}</p>
                            </div>

                            <!-- Submit Button -->
                            <div>
                                <button type="submit"
                                    class="w-full bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg transition font-semibold text-lg shadow-md hover:shadow-lg"
                                    style="width: 100%; background-color: #3b82f6; color: white; padding: 0.75rem 1.5rem; border-radius: 0.5rem; font-weight: 600; font-size: 1.125rem; border: none; cursor: pointer; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                                    Update Approval Chain
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function updateParentOptions() {
            const departmentId = document.getElementById('department_id').value;
            const userId = document.getElementById('user_id').value;
            const parentSelect = document.getElementById('parent_chain_id');
            const loadingDiv = document.getElementById('parent-loading');
            const levelPreview = document.getElementById('level-preview');

            // Hide level preview when department changes
            levelPreview.classList.add('hidden');

            if (!departmentId) {
                parentSelect.innerHTML = '<option value="">None (Root Level)</option>';
                return;
            }

            // Show loading indicator
            loadingDiv.classList.remove('hidden');

            // Build URL with user_id and exclude_chain_id parameters
            let url = `/approval_chains_parents/${departmentId}?exclude_chain_id={{ $approvalChain->id }}`;
            if (userId) {
                url += `&user_id=${userId}`;
            }

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    // Clear existing options
                    parentSelect.innerHTML = '<option value="">None (Root Level)</option>';

                    // Add new options
                    data.forEach(chain => {
                        const option = document.createElement('option');
                        option.value = chain.id;
                        option.textContent = chain.text;
                        option.dataset.level = chain.level;
                        parentSelect.appendChild(option);
                    });

                    // Hide loading indicator
                    loadingDiv.classList.add('hidden');
                })
                .catch(error => {
                    console.error('Error fetching parent chains:', error);
                    loadingDiv.classList.add('hidden');
                });
        }

        function updateLevelPreview() {
            const parentSelect = document.getElementById('parent_chain_id');
            const levelPreview = document.getElementById('level-preview');
            const previewLevel = document.getElementById('preview-level');

            if (parentSelect.value === '') {
                // Root level
                previewLevel.textContent = 'Level 1';
                levelPreview.classList.remove('hidden');
            } else {
                // Child level
                const selectedOption = parentSelect.options[parentSelect.selectedIndex];
                const parentLevel = parseInt(selectedOption.dataset.level || 1);
                const newLevel = parentLevel + 1;
                previewLevel.textContent = `Level ${newLevel}`;
                levelPreview.classList.remove('hidden');
            }
        }

        // Add event listeners
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('department_id').addEventListener('change', updateParentOptions);
            document.getElementById('user_id').addEventListener('change', updateParentOptions);
            document.getElementById('parent_chain_id').addEventListener('change', updateLevelPreview);
        });
    </script>
</x-app-layout>
