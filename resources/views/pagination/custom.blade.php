@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Navigasi halaman" class="flex flex-col items-center justify-between gap-3 border-t border-slate-100 px-5 py-4 sm:flex-row print:hidden">
        <p class="text-sm text-slate-500">
            Menampilkan
            <span class="font-medium text-slate-700">{{ $paginator->firstItem() }}</span>–<span class="font-medium text-slate-700">{{ $paginator->lastItem() }}</span>
            dari
            <span class="font-medium text-slate-700">{{ $paginator->total() }}</span> data
        </p>

        <div class="flex items-center gap-1">
            @if ($paginator->onFirstPage())
                <span class="grid size-9 place-items-center rounded-lg text-slate-300" aria-disabled="true"><x-icon name="chevron-left" class="size-4" /></span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="grid size-9 place-items-center rounded-lg text-slate-600 transition hover:bg-slate-100" aria-label="Halaman sebelumnya"><x-icon name="chevron-left" class="size-4" /></a>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="grid size-9 place-items-center text-sm text-slate-400">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $halaman => $url)
                        @if ($halaman == $paginator->currentPage())
                            <span aria-current="page" class="grid size-9 place-items-center rounded-lg bg-brand-600 text-sm font-semibold text-white shadow-sm">{{ $halaman }}</span>
                        @else
                            <a href="{{ $url }}" class="grid size-9 place-items-center rounded-lg text-sm font-medium text-slate-600 transition hover:bg-slate-100" aria-label="Ke halaman {{ $halaman }}">{{ $halaman }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="grid size-9 place-items-center rounded-lg text-slate-600 transition hover:bg-slate-100" aria-label="Halaman berikutnya"><x-icon name="chevron-right" class="size-4" /></a>
            @else
                <span class="grid size-9 place-items-center rounded-lg text-slate-300" aria-disabled="true"><x-icon name="chevron-right" class="size-4" /></span>
            @endif
        </div>
    </nav>
@endif
