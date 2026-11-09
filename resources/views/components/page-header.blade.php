@props(['title', 'subtitle' => null])
<div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between print:mb-4">
    <div class="min-w-0">
        <h1 class="text-2xl font-semibold tracking-tight text-slate-900">{{ $title }}</h1>
        @if ($subtitle)
            <p class="mt-1 text-sm text-slate-500">{{ $subtitle }}</p>
        @endif
    </div>
    @isset($actions)
        <div class="flex flex-wrap items-center gap-2 print:hidden">{{ $actions }}</div>
    @endisset
</div>
