@props(['href', 'icon', 'active' => false, 'badge' => null])
<a
    href="{{ $href }}"
    @if ($active) aria-current="page" @endif
    {{ $attributes->class([
        'group relative flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition',
        'bg-white/10 text-white' => $active,
        'text-slate-300 hover:bg-white/5 hover:text-white' => ! $active,
    ]) }}
>
    @if ($active)
        <span class="absolute inset-y-1.5 -left-3 w-1 rounded-r-full bg-brand-400"></span>
    @endif
    <x-icon :name="$icon" :class="'size-[18px] '.($active ? 'text-brand-300' : 'text-slate-400 group-hover:text-slate-200')" />
    <span class="flex-1 truncate">{{ $slot }}</span>
    @if ($badge)
        <span class="rounded-full bg-rose-500 px-2 py-0.5 text-[11px] font-semibold text-white tabular-nums">{{ $badge }}</span>
    @endif
</a>
