<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Approval Chain Details') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('approval_chains.edit', $approvalChain) }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition">
                    Edit
                </a>
                <a href="{{ route('approval_chains.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition">
                    Back to List
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Chain Details -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Approval Chain Information</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Department</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $approvalChain->department->name }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">User</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $approvalChain->user->name }}</p>
                            <p class="text-xs text-gray-500">{{ $approvalChain->user->email }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Level</label>
                            <p class="mt-1 text-sm text-gray-900">Level {{ $approvalChain->getLevel() }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Path</label>
                            <p class="mt-1 text-sm text-gray-500 font-mono">{{ $approvalChain->path }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Created</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $approvalChain->created_at->format('M d, Y g:i A') }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Last Updated</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $approvalChain->updated_at->format('M d, Y g:i A') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Related Approvers -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Hierarchy Context</h3>

                    <!-- Next Level Approvers -->
                    @if($approvalChain->nextApprovers()->count() > 0)
                        <div class="mb-6">
                            <h4 class="text-md font-medium text-gray-700 mb-2">Next Level Approvers</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($approvalChain->nextApprovers() as $next)
                                    <div class="border border-gray-200 rounded-lg p-3">
                                        <p class="font-medium">{{ $next->user->name }}</p>
                                        <p class="text-sm text-gray-500">Level {{ $next->getLevel() }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Previous Level Approvers -->
                    @if($approvalChain->previousApprovers()->count() > 0)
                        <div class="mb-6">
                            <h4 class="text-md font-medium text-gray-700 mb-2">Previous Level Approvers</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($approvalChain->previousApprovers() as $previous)
                                    <div class="border border-gray-200 rounded-lg p-3">
                                        <p class="font-medium">{{ $previous->user->name }}</p>
                                        <p class="text-sm text-gray-500">Level {{ $previous->getLevel() }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Same Level Approvers -->
                    @if($approvalChain->sameLevelApprovers()->count() > 0)
                        <div>
                            <h4 class="text-md font-medium text-gray-700 mb-2">Same Level Approvers</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($approvalChain->sameLevelApprovers() as $same)
                                    <div class="border border-gray-200 rounded-lg p-3">
                                        <p class="font-medium">{{ $same->user->name }}</p>
                                        <p class="text-sm text-gray-500">Level {{ $same->getLevel() }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
