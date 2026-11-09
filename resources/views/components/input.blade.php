@props(['label' => null, 'name', 'type' => 'text', 'value' => null, 'help' => null, 'required' => false, 'icon' => null])
@php
    $galat = $errors->first($name);
    $kelas = 'input'.($icon ? ' pl-10' : '').($galat ? ' input-error' : '');
@endphp
<div {{ $attributes->only('class') }}>
    @if ($label)
        <label for="{{ $name }}" class="label">{{ $label }}@if ($required)<span class="ml-0.5 text-rose-500">*</span>@endif</label>
    @endif
    <div class="relative">
        @if ($icon)
            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                <x-icon :name="$icon" class="size-4.5" />
            </span>
        @endif
        <input
            id="{{ $name }}"
            name="{{ $name }}"
            type="{{ $type }}"
            @if (! in_array($type, ['password', 'file'], true)) value="{{ old($name, $value) }}" @endif
            @required($required)
            @if ($galat) aria-invalid="true" @endif
            {{ $attributes->except('class')->merge(['class' => $kelas]) }}
        >
    </div>
    @if ($galat)
        <p class="mt-1.5 text-sm text-rose-600">{{ $galat }}</p>
    @elseif ($help)
        <p class="mt-1.5 text-xs text-slate-500">{{ $help }}</p>
    @endif
</div>
