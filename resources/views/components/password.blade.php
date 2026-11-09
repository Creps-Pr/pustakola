@props(['label' => 'Kata sandi', 'name' => 'password', 'required' => false, 'help' => null, 'autocomplete' => 'current-password'])
@php $galat = $errors->first($name); @endphp
<div x-data="{ lihat: false }" {{ $attributes->only('class') }}>
    <label for="{{ $name }}" class="label">{{ $label }}@if ($required)<span class="ml-0.5 text-rose-500">*</span>@endif</label>
    <div class="relative">
        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
            <x-icon name="lock" class="size-4.5" />
        </span>
        <input
            id="{{ $name }}"
            name="{{ $name }}"
            type="password"
            x-bind:type="lihat ? 'text' : 'password'"
            autocomplete="{{ $autocomplete }}"
            @required($required)
            @if ($galat) aria-invalid="true" @endif
            {{ $attributes->except('class')->merge(['class' => 'input pl-10 pr-11'.($galat ? ' input-error' : '')]) }}
        >
        <button
            type="button"
            x-on:click="lihat = !lihat"
            class="absolute inset-y-0 right-0 flex items-center px-3.5 text-slate-400 transition hover:text-slate-600"
            x-bind:aria-label="lihat ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'"
        >
            <span x-show="! lihat"><x-icon name="eye" class="size-4.5" /></span>
            <span x-show="lihat" x-cloak><x-icon name="eye-off" class="size-4.5" /></span>
        </button>
    </div>
    @if ($galat)
        <p class="mt-1.5 text-sm text-rose-600">{{ $galat }}</p>
    @elseif ($help)
        <p class="mt-1.5 text-xs text-slate-500">{{ $help }}</p>
    @endif
</div>
