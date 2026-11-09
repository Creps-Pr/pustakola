@props(['label' => null, 'name', 'value' => null, 'help' => null, 'required' => false, 'rows' => 4])
@php $galat = $errors->first($name); @endphp
<div {{ $attributes->only('class') }}>
    @if ($label)
        <label for="{{ $name }}" class="label">{{ $label }}@if ($required)<span class="ml-0.5 text-rose-500">*</span>@endif</label>
    @endif
    <textarea
        id="{{ $name }}"
        name="{{ $name }}"
        rows="{{ $rows }}"
        @required($required)
        {{ $attributes->except('class')->merge(['class' => 'input'.($galat ? ' input-error' : '')]) }}
    >{{ old($name, $value) }}</textarea>
    @if ($galat)
        <p class="mt-1.5 text-sm text-rose-600">{{ $galat }}</p>
    @elseif ($help)
        <p class="mt-1.5 text-xs text-slate-500">{{ $help }}</p>
    @endif
</div>
