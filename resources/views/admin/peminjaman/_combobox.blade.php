{{-- Kotak pencarian dengan daftar pilihan (combobox) berbasis Alpine. --}}
@php $galat = $errors->first($name); @endphp
<div
    x-data="{
        items: @js($items),
        dipilih: @js($dipilih ? (int) $dipilih : null),
        q: '',
        buka: false,
        aktif: 0,
        get pilihan() { return this.items.find(i => i.id === this.dipilih) ?? null },
        get hasil() {
            const k = this.q.trim().toLowerCase();
            return (k ? this.items.filter(i => (i.judul + ' ' + i.sub).toLowerCase().includes(k)) : this.items).slice(0, 50);
        },
        pilih(i) { this.dipilih = i.id; this.q = ''; this.buka = false },
        hapus() { this.dipilih = null; this.q = ''; this.$nextTick(() => this.$refs.cari.focus()) },
    }"
    x-on:click.outside="buka = false"
    x-on:keydown.escape="buka = false"
    class="relative"
>
    <label for="{{ $name }}_cari" class="label">{{ $label }}<span class="ml-0.5 text-rose-500">*</span></label>
    <input type="hidden" name="{{ $name }}" x-bind:value="dipilih ?? ''">

    <template x-if="pilihan">
        <div class="flex items-center gap-3 rounded-lg bg-white px-3.5 py-2.5 shadow-sm ring-1 ring-inset {{ $galat ? 'ring-rose-400' : 'ring-slate-300' }}">
            <span class="text-slate-400"><x-icon :name="$icon" class="size-4.5" /></span>
            <div class="min-w-0 flex-1">
                <p class="truncate text-sm font-medium text-slate-900" x-text="pilihan.judul"></p>
                <p class="truncate text-xs text-slate-500" x-text="pilihan.sub"></p>
            </div>
            <button type="button" x-on:click="hapus()" class="btn-icon -mr-1.5" aria-label="Ganti pilihan"><x-icon name="x" class="size-4" /></button>
        </div>
    </template>

    <div x-show="! pilihan" class="relative">
        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400"><x-icon name="search" class="size-4.5" /></span>
        <input
            type="text" id="{{ $name }}_cari" x-ref="cari" x-model="q" autocomplete="off"
            x-on:focus="buka = true" x-on:input="buka = true; aktif = 0"
            x-on:keydown.arrow-down.prevent="aktif = Math.min(aktif + 1, hasil.length - 1)"
            x-on:keydown.arrow-up.prevent="aktif = Math.max(aktif - 1, 0)"
            x-on:keydown.enter.prevent="hasil[aktif] && pilih(hasil[aktif])"
            placeholder="{{ $placeholder }}"
            class="input pl-10 {{ $galat ? 'input-error' : '' }}"
            role="combobox" aria-expanded="false" x-bind:aria-expanded="buka"
        >
        <ul x-show="buka" x-cloak x-transition.opacity.duration.100ms class="absolute z-20 mt-2 max-h-64 w-full overflow-y-auto rounded-xl bg-white p-1.5 shadow-lift ring-1 ring-slate-200" role="listbox">
            <template x-for="(i, n) in hasil" :key="i.id">
                <li role="option">
                    <button type="button" x-on:click="pilih(i)" x-on:mouseenter="aktif = n"
                        x-bind:class="aktif === n ? 'bg-brand-50' : ''" class="block w-full rounded-lg px-3 py-2 text-left">
                        <span class="block truncate text-sm font-medium text-slate-900" x-text="i.judul"></span>
                        <span class="block truncate text-xs text-slate-500" x-text="i.sub"></span>
                    </button>
                </li>
            </template>
            <li x-show="hasil.length === 0" class="px-3 py-6 text-center text-sm text-slate-500">Tidak ada hasil yang cocok.</li>
        </ul>
    </div>

    @if ($galat)
        <p class="mt-1.5 text-sm text-rose-600">{{ $galat }}</p>
    @endif
</div>
