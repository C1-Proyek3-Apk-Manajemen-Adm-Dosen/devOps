@php
    $chunk = $chunk ?? 5; // maximum page buttons to show in the window (not counting first/last)
    $current = $paginator->currentPage();
    $total = $paginator->lastPage();

    // If total pages small, show all
    $showAllThreshold = $chunk + 2; // small extra for first+last

    // Helper to build url preserving query
    $purl = function ($p) {
        return request()->fullUrlWithQuery(['page' => $p]);
    };
@endphp

@if ($paginator->hasPages())
    <nav class="flex items-center justify-between gap-3" role="navigation" aria-label="Pagination">
        <div class="text-sm text-gray-500">
            Menampilkan {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} dari {{ $paginator->total() }} hasil
        </div>

        <div class="flex items-center gap-2">
            {{-- Prev arrow --}}
            @if ($current > 1)
                <a href="{{ ($purl)($current - 1) }}" class="w-9 h-9 inline-flex items-center justify-center rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-50" aria-label="Sebelumnya">&larr;</a>
            @else
                <span class="w-9 h-9 inline-flex items-center justify-center rounded-lg border border-gray-200 text-gray-300 cursor-default" aria-disabled="true">&larr;</span>
            @endif

            {{-- If small total, render all pages --}}
            @if ($total <= $showAllThreshold)
                @for ($i = 1; $i <= $total; $i++)
                    @if ($i == $current)
                        <span class="w-9 h-9 inline-flex items-center justify-center rounded-lg bg-[#050C9C] text-white font-semibold">{{ $i }}</span>
                    @else
                        <a href="{{ ($purl)($i) }}" class="w-9 h-9 inline-flex items-center justify-center rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50">{{ $i }}</a>
                    @endif
                @endfor

            @else
                {{-- Always show first page --}}
                @if (1 == $current)
                    <span class="w-9 h-9 inline-flex items-center justify-center rounded-lg bg-[#050C9C] text-white font-semibold">1</span>
                @else
                    <a href="{{ ($purl)(1) }}" class="w-9 h-9 inline-flex items-center justify-center rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50">1</a>
                @endif

                {{-- Calculate window around current --}}
                @php
                    $half = (int) floor($chunk / 2);
                    $start = max(2, $current - $half);
                    $end = min($total - 1, $start + $chunk - 1);
                    if ($end - $start + 1 < $chunk) {
                        $start = max(2, $end - $chunk + 1);
                    }
                @endphp

                {{-- Left ellipsis if needed --}}
                @if ($start > 2)
                    <span class="px-2 text-gray-400">…</span>
                @endif

                {{-- Page buttons in window --}}
                @for ($i = $start; $i <= $end; $i++)
                    @if ($i == $current)
                        <span class="w-9 h-9 inline-flex items-center justify-center rounded-lg bg-[#050C9C] text-white font-semibold">{{ $i }}</span>
                    @else
                        <a href="{{ ($purl)($i) }}" class="w-9 h-9 inline-flex items-center justify-center rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50">{{ $i }}</a>
                    @endif
                @endfor

                {{-- Right ellipsis if needed --}}
                @if ($end < $total - 1)
                    <span class="px-2 text-gray-400">…</span>
                @endif

                {{-- Last page --}}
                @if ($current == $total)
                    <span class="w-9 h-9 inline-flex items-center justify-center rounded-lg bg-[#050C9C] text-white font-semibold">{{ $total }}</span>
                @else
                    <a href="{{ ($purl)($total) }}" class="w-9 h-9 inline-flex items-center justify-center rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50">{{ $total }}</a>
                @endif

            @endif

            {{-- Next arrow --}}
            @if ($current < $total)
                <a href="{{ ($purl)($current + 1) }}" class="w-9 h-9 inline-flex items-center justify-center rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-50" aria-label="Berikutnya">&rarr;</a>
            @else
                <span class="w-9 h-9 inline-flex items-center justify-center rounded-lg border border-gray-200 text-gray-300 cursor-default" aria-disabled="true">&rarr;</span>
            @endif
        </div>
    </nav>
@endif
