@php
    $chain = $node['chain'];
    $children = $node['children'];

    $currentPrefix = $parentPrefix . ($depth === 0 ? '' : ($isLast ? '└── ' : '├── '));
    $nextPrefix = $parentPrefix . ($depth === 0 ? '' : ($isLast ? '    ' : '│   '));
@endphp

<div class="leading-relaxed text-gray-700">
    <span class="text-blue-600">{!! $currentPrefix !!}</span>

    @if($depth === 0)
        <span class="text-lg">🔹</span>
    @endif

    <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs font-semibold mr-2">
        L{{ $chain->getLevel() }}
    </span>
    <span class="font-semibold text-gray-900">{{ $chain->user->name }}</span>
    <span class="text-xs text-gray-500 ml-2">({{ $chain->user->email }})</span>

    <!-- Action buttons -->
    <span class="ml-3 space-x-2">
        <a href="{{ route('approval_chains.edit', $chain) }}" class="text-blue-600 hover:text-blue-800 text-xs">Edit</a>
        <span class="text-gray-300">|</span>
        <form method="POST" action="{{ route('approval_chains.destroy', $chain) }}" class="inline">
            @csrf
            @method('DELETE')
            <button type="submit" class="text-red-600 hover:text-red-800 text-xs" onclick="return confirm('Are you sure?')">Delete</button>
        </form>
    </span>
</div>

@if($children && $children->count() > 0)
    @foreach($children as $index => $child)
        @php
            $isChildLast = $index === $children->count() - 1;
        @endphp
        @include('approval_chains._hierarchy_node', [
            'node' => $child,
            'depth' => $depth + 1,
            'isLast' => $isChildLast,
            'parentPrefix' => $nextPrefix
        ])
    @endforeach
@endif
