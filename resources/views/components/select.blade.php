@props(['label' => null, 'name', 'options' => [], 'value' => null, 'placeholder' => null, 'help' => null, 'required' => false])
@php
    $galat = $errors->first($name);
    $terpilih = (string) old($name, $value);
@endphp
<div {{ $attributes->only('class') }}>
    @if ($label)
        <label for="{{ $name }}" class="label">{{ $label }}@if ($required)<span class="ml-0.5 text-rose-500">*</span>@endif</label>
    @endif
    <select
        id="{{ $name }}"
        name="{{ $name }}"
        @required($required)
        {{ $attributes->except('class')->merge(['class' => 'input'.($galat ? ' input-error' : '')]) }}
    >
        @if ($placeholder !== null)
            <option value="">{{ $placeholder }}</option>
        @endif
        @foreach ($options as $kunci => $teks)
            <option value="{{ $kunci }}" @selected($terpilih === (string) $kunci)>{{ $teks }}</option>
        @endforeach
    </select>
    @if ($galat)
        <p class="mt-1.5 text-sm text-rose-600">{{ $galat }}</p>
    @elseif ($help)
        <p class="mt-1.5 text-xs text-slate-500">{{ $help }}</p>
    @endif
</div>
