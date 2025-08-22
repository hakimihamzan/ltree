<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Create Purchase Request') }}
            </h2>
            <a href="{{ route('purchase_requests.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition">
                Back to Requests
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
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Purchase Request Information</h3>
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-sm text-blue-800">
                            <p>Your purchase request will go through the approval chain for the selected department.
                            Each level of approvers will need to approve before it moves to the next level.</p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('purchase_requests.store') }}">
                        @csrf

                        <div class="grid grid-cols-1 gap-6">
                            <!-- Department -->
                            <div>
                                <label for="department_id" class="block text-sm font-medium text-gray-700">Department</label>
                                <select name="department_id" id="department_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Select Department</option>
                                    @foreach($departments as $department)
                                        <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                            {{ $department->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('department_id')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Item -->
                            <div>
                                <label for="item" class="block text-sm font-medium text-gray-700">Item Description</label>
                                <input type="text" name="item" id="item" value="{{ old('item') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="e.g., Office Supplies, Software License, Equipment">
                                @error('item')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Amount -->
                            <div>
                                <label for="amount" class="block text-sm font-medium text-gray-700">Amount ($)</label>
                                <input type="number" name="amount" id="amount" value="{{ old('amount') }}"
                                    step="0.01" min="0"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="0.00">
                                @error('amount')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Submit Button -->
                            <div>
                                <button type="submit"
                                    class="w-full bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg transition font-semibold text-lg shadow-md hover:shadow-lg"
                                    style="width: 100%; background-color: #3b82f6; color: white; padding: 0.75rem 1.5rem; border-radius: 0.5rem; font-weight: 600; font-size: 1.125rem; border: none; cursor: pointer; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                                    Submit Purchase Request
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
