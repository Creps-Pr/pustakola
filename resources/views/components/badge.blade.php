@props(['color' => 'slate', 'dot' => false])
<span {{ $attributes->class(['inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-medium whitespace-nowrap ring-1 ring-inset', \App\Support\Warna::lencana($color)]) }}>
    @if ($dot)
        <span class="size-1.5 rounded-full {{ \App\Support\Warna::titik($color) }}"></span>
    @endif
    {{ $slot }}
</span>
