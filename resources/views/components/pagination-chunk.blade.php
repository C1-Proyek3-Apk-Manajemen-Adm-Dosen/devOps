@php
    $chunk = $chunk ?? 5;
    $current = $paginator->currentPage();
    $total = $paginator->lastPage();

    // Helper to build url preserving query
    $purl = function ($p) {
        return request()->fullUrlWithQuery(['page' => $p]);
    };
@endphp

@if ($paginator->hasPages())
    <nav class="flex items-center justify-end" role="navigation" aria-label="Pagination">
        <div class="flex items-center gap-1.5">
            {{-- Prev --}}
            @if ($current > 1)
                <a href="{{ ($purl)($current - 1) }}"
                   class="px-2.5 py-1.5 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-[#050C9C] hover:text-white hover:border-[#050C9C] transition-all duration-200 shadow-sm"
                   aria-label="Sebelumnya">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
            @else
                <span class="px-2.5 py-1.5 text-xs font-medium text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed"
                      aria-disabled="true">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </span>
            @endif

            @php
                // Determine sliding window excluding first/last
                $half = (int) floor(($chunk - 2) / 2); // reserve slots for first/last
                $windowStart = max(2, $current - $half);
                $windowEnd = min($total - 1, $current + $half);
                // widen window if it's short
                while ($windowEnd - $windowStart + 1 < ($chunk - 2) && $windowStart > 2) {
                    $windowStart--;
                }
                while ($windowEnd - $windowStart + 1 < ($chunk - 2) && $windowEnd < $total - 1) {
                    $windowEnd++;
                }
            @endphp

            {{-- First page --}}
            @if (1 == $current)
                <span class="px-3 py-1.5 text-xs font-bold text-white bg-[#050C9C] rounded-lg shadow-md">1</span>
            @else
                <a href="{{ ($purl)(1) }}" class="px-3 py-1.5 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-[#050C9C] hover:text-white hover:border-[#050C9C] transition-all duration-200 shadow-sm">1</a>
            @endif

            {{-- Left ellipsis --}}
            @if ($windowStart > 2)
                <span class="px-2 text-gray-400">...</span>
            @endif

            {{-- Middle window --}}
            @for ($i = $windowStart; $i <= $windowEnd; $i++)
                @if ($i == $current)
                    <span class="px-3 py-1.5 text-xs font-bold text-white bg-[#050C9C] rounded-lg shadow-md">{{ $i }}</span>
                @else
                    <a href="{{ ($purl)($i) }}" class="px-3 py-1.5 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-[#050C9C] hover:text-white hover:border-[#050C9C] transition-all duration-200 shadow-sm">{{ $i }}</a>
                @endif
            @endfor

            {{-- Right ellipsis --}}
            @if ($windowEnd < $total - 1)
                <span class="px-2 text-gray-400">...</span>
            @endif

            {{-- Last page (only if more than one page) --}}
            @if ($total > 1)
                @if ($current == $total)
                    <span class="px-3 py-1.5 text-xs font-bold text-white bg-[#050C9C] rounded-lg shadow-md">{{ $total }}</span>
                @else
                    <a href="{{ ($purl)($total) }}" class="px-3 py-1.5 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-[#050C9C] hover:text-white hover:border-[#050C9C] transition-all duration-200 shadow-sm">{{ $total }}</a>
                @endif
            @endif

            {{-- Next --}}
            @if ($current < $total)
                <a href="{{ ($purl)($current + 1) }}"
                   class="px-2.5 py-1.5 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-[#050C9C] hover:text-white hover:border-[#050C9C] transition-all duration-200 shadow-sm"
                   aria-label="Berikutnya">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            @else
                <span class="px-2.5 py-1.5 text-xs font-medium text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed"
                      aria-disabled="true">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </span>
            @endif
        </div>
    </nav>
@endif
