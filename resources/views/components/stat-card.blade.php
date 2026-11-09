@props(['label', 'value', 'icon', 'color' => 'indigo', 'hint' => null])
<div {{ $attributes->class('card p-5') }}>
    <div class="flex items-start justify-between gap-3">
        <div class="min-w-0">
            <p class="text-sm font-medium text-slate-500">{{ $label }}</p>
            <p class="mt-2 text-3xl font-semibold tracking-tight text-slate-900 tabular-nums">{{ $value }}</p>
        </div>
        <span class="grid size-11 shrink-0 place-items-center rounded-xl {{ \App\Support\Warna::ikon($color) }}">
            <x-icon :name="$icon" class="size-5" />
        </span>
    </div>
    @if ($hint)
        <p class="mt-3 text-xs text-slate-500">{{ $hint }}</p>
    @endif
</div>
