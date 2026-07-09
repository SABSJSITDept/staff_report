@if ($paginator->hasPages())
    <div class="flex items-center justify-between px-3 py-2.5">
        {{-- Results Info --}}
        <div class="flex items-center">
            <p class="text-[11px] font-bold text-slate-500 uppercase tracking-widest">
                {{ $paginator->firstItem() }} - {{ $paginator->lastItem() }} <span class="font-medium lowercase tracking-normal text-slate-400 mx-0.5">of</span> {{ $paginator->total() }}
            </p>
        </div>

        {{-- Pagination Elements --}}
        <nav class="flex items-center gap-1 bg-white p-0.5 rounded-lg border border-slate-200/60 shadow-sm" role="navigation" aria-label="Pagination Navigation">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span class="w-7 h-7 flex items-center justify-center rounded-md text-slate-300 cursor-default" aria-hidden="true">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path></svg>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="w-7 h-7 flex items-center justify-center rounded-md text-slate-600 hover:bg-slate-100 hover:text-indigo-600 transition" aria-label="{{ __('pagination.previous') }}">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path></svg>
                </a>
            @endif

            <div class="hidden sm:flex items-center">
                @foreach ($elements as $element)
                    @if (is_string($element))
                        <span class="w-7 h-7 flex items-center justify-center text-xs font-medium text-slate-400">{{ $element }}</span>
                    @endif

                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span class="w-7 h-7 flex items-center justify-center text-[11px] font-bold text-white bg-indigo-600 rounded-md shadow-sm shadow-indigo-200" aria-current="page">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" class="w-7 h-7 flex items-center justify-center text-[11px] font-medium text-slate-600 hover:bg-slate-100 hover:text-indigo-600 rounded-md transition">{{ $page }}</a>
                            @endif
                        @endforeach
                    @endif
                @endforeach
            </div>

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="w-7 h-7 flex items-center justify-center rounded-md text-slate-600 hover:bg-slate-100 hover:text-indigo-600 transition" aria-label="{{ __('pagination.next') }}">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path></svg>
                </a>
            @else
                <span class="w-7 h-7 flex items-center justify-center rounded-md text-slate-300 cursor-default" aria-hidden="true">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path></svg>
                </span>
            @endif
        </nav>
    </div>
@endif
