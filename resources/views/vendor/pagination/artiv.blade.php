@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-center gap-2">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <button disabled
                    class="px-4 py-2 rounded-full bg-white border border-slate-200 text-sm font-medium text-slate-400 cursor-not-allowed">
                Sebelumnya
            </button>
        @else
            <a href="{{ $paginator->previousPageUrl() }}"
                class="px-4 py-2 rounded-full bg-white border border-slate-200 text-sm font-medium text-slate-600 hover:bg-slate-50 transition">
                Sebelumnya
            </a>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <span class="text-slate-400 px-2">{{ $element }}</span>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="w-9 h-9 rounded-full bg-[#D5FC55] text-neutral-900 font-bold text-sm flex items-center justify-center">
                            {{ $page }}
                        </span>
                    @else
                        <a href="{{ $url }}"
                            class="w-9 h-9 rounded-full bg-white border border-slate-200 text-slate-600 font-medium text-sm flex items-center justify-center hover:bg-slate-50 transition">
                            {{ $page }}
                        </a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}"
                class="px-4 py-2 rounded-full bg-white border border-slate-200 text-sm font-medium text-slate-600 hover:bg-slate-50 transition">
                Selanjutnya
            </a>
        @else
            <button disabled
                    class="px-4 py-2 rounded-full bg-white border border-slate-200 text-sm font-medium text-slate-400 cursor-not-allowed">
                Selanjutnya
            </button>
        @endif
    </nav>
@endif