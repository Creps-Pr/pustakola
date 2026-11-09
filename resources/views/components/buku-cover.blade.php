@props(['buku', 'size' => 'md'])
@php
    $gradien = \App\Support\Warna::sampul($buku->judul);
    $judulKelas = match ($size) {
        'sm' => 'text-[8px] leading-tight',
        'lg' => 'text-xl leading-snug',
        default => 'text-[13px] leading-snug',
    };
    $penulisKelas = match ($size) {
        'sm' => 'text-[6px]',
        'lg' => 'text-xs',
        default => 'text-[10px]',
    };
    $ikonKelas = match ($size) {
        'sm' => 'size-3',
        'lg' => 'size-6',
        default => 'size-4',
    };
@endphp
<div {{ $attributes->class('relative aspect-[2/3] w-full overflow-hidden rounded-md bg-slate-200 shadow-md ring-1 ring-black/10') }}>
    @if ($buku->sampul_url)
        <img src="{{ $buku->sampul_url }}" alt="Sampul buku {{ $buku->judul }}" loading="lazy" class="size-full object-cover">
    @else
        <div class="absolute inset-0 bg-linear-to-br {{ $gradien }}"></div>
        <div class="absolute -top-6 -right-6 size-24 rounded-full bg-white/10"></div>
        <div class="absolute -bottom-8 -left-4 size-28 rounded-full bg-black/10"></div>
        <div class="absolute inset-y-0 left-0 w-[7%] bg-black/15"></div>
        <div class="absolute inset-y-0 left-[7%] w-px bg-white/25"></div>

        <div class="relative flex h-full flex-col justify-between p-[11%] pl-[16%] text-white">
            <x-icon name="book-open" :class="$ikonKelas.' opacity-80'" />
            <div>
                <p class="line-clamp-5 font-semibold tracking-tight {{ $judulKelas }}">{{ $buku->judul }}</p>
                <div class="my-[6%] h-px w-6 bg-white/50"></div>
                <p class="line-clamp-2 font-medium tracking-wide text-white/75 uppercase {{ $penulisKelas }}">{{ $buku->penulis }}</p>
            </div>
        </div>
    @endif
</div>
