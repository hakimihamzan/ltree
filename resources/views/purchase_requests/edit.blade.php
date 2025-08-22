<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit Purchase Request') }}
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

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Edit Purchase Request</h3>
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-sm text-yellow-800">
                            <p>You can only edit purchase requests that haven't started the approval process yet.</p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('purchase_requests.update', $purchaseRequest) }}">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 gap-6">
                            <!-- Department -->
                            <div>
                                <label for="department_id" class="block text-sm font-medium text-gray-700">Department</label>
                                <select name="department_id" id="department_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Select Department</option>
                                    @foreach($departments as $department)
                                        <option value="{{ $department->id }}"
                                            {{ old('department_id', $purchaseRequest->department_id) == $department->id ? 'selected' : '' }}>
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
                                <input type="text" name="item" id="item" value="{{ old('item', $purchaseRequest->item) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="e.g., Office Supplies, Software License, Equipment">
                                @error('item')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Amount -->
                            <div>
                                <label for="amount" class="block text-sm font-medium text-gray-700">Amount ($)</label>
                                <input type="number" name="amount" id="amount" value="{{ old('amount', $purchaseRequest->amount) }}"
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
                                    Update Purchase Request
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
