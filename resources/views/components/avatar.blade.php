@props(['user', 'size' => 'md'])
@php
    $ukuran = match ($size) {
        'sm' => 'size-8 text-xs',
        'lg' => 'size-16 text-xl',
        default => 'size-9 text-xs',
    };
@endphp
<span {{ $attributes->class(['grid shrink-0 place-items-center rounded-full bg-brand-100 font-semibold text-brand-700 ring-2 ring-white', $ukuran]) }} aria-hidden="true">{{ $user->inisial }}</span>
