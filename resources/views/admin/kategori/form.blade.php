@php $edit = $kategori->exists; @endphp
<x-layouts.admin :title="$edit ? 'Ubah Kategori' : 'Tambah Kategori'">
    <x-page-header :title="$edit ? 'Ubah kategori' : 'Tambah kategori'" subtitle="Warna dipakai pada lencana di katalog dan panel.">
        <x-slot:actions>
            <a href="{{ route('admin.kategori.index') }}" class="btn btn-secondary btn-sm"><x-icon name="chevron-left" class="size-4" /> Kembali</a>
        </x-slot:actions>
    </x-page-header>

    <form method="POST" action="{{ $edit ? route('admin.kategori.update', $kategori) : route('admin.kategori.store') }}" class="card mx-auto max-w-2xl space-y-5 p-6">
        @csrf
        @if ($edit) @method('PUT') @endif

        <x-input label="Nama kategori" name="nama" :value="$kategori->nama" required />
        <x-textarea label="Deskripsi" name="deskripsi" :value="$kategori->deskripsi" rows="3" />

        <fieldset x-data="{ warna: @js(old('warna', $kategori->warna ?: 'indigo')) }">
            <legend class="label">Warna</legend>
            <div class="flex flex-wrap gap-3">
                @foreach (\App\Support\Warna::DAFTAR as $kunci => $nama)
                    <label class="cursor-pointer" title="{{ $nama }}">
                        <input type="radio" name="warna" value="{{ $kunci }}" x-model="warna" class="peer sr-only">
                        <span class="grid size-10 place-items-center rounded-full ring-2 ring-transparent ring-offset-2 transition peer-checked:ring-slate-900 peer-focus-visible:ring-brand-600 {{ \App\Support\Warna::titik($kunci) }}">
                            <span x-show="warna === '{{ $kunci }}'" x-cloak><x-icon name="check" class="size-5 text-white" /></span>
                        </span>
                        <span class="sr-only">{{ $nama }}</span>
                    </label>
                @endforeach
            </div>
            @error('warna') <p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p> @enderror
        </fieldset>

        <div class="flex justify-end gap-3 border-t border-slate-100 pt-5">
            <a href="{{ route('admin.kategori.index') }}" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">{{ $edit ? 'Simpan perubahan' : 'Simpan kategori' }}</button>
        </div>
    </form>
</x-layouts.admin>
