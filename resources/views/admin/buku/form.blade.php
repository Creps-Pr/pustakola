@php
    $edit = $buku->exists;
    $contohSampul = new \App\Models\Buku(['judul' => $buku->judul ?: 'Judul Buku', 'penulis' => $buku->penulis ?: 'Nama Penulis']);
@endphp
<x-layouts.admin :title="$edit ? 'Ubah Buku' : 'Tambah Buku'">
    <x-page-header :title="$edit ? 'Ubah data buku' : 'Tambah buku baru'" :subtitle="$edit ? $buku->judul : 'Lengkapi informasi buku untuk menambahkannya ke koleksi.'">
        <x-slot:actions>
            <a href="{{ route('admin.buku.index') }}" class="btn btn-secondary btn-sm"><x-icon name="chevron-left" class="size-4" /> Kembali</a>
        </x-slot:actions>
    </x-page-header>

    <form method="POST" enctype="multipart/form-data" action="{{ $edit ? route('admin.buku.update', $buku) : route('admin.buku.store') }}" class="grid gap-6 lg:grid-cols-3">
        @csrf
        @if ($edit) @method('PUT') @endif

        <div class="space-y-6 lg:col-span-2">
            <section class="card p-6">
                <h2 class="text-base font-semibold text-slate-900">Informasi utama</h2>
                <div class="mt-5 grid gap-5 sm:grid-cols-2">
                    <x-input class="sm:col-span-2" label="Judul buku" name="judul" :value="$buku->judul" required />
                    <x-input label="Penulis" name="penulis" :value="$buku->penulis" required />
                    <x-input label="Penerbit" name="penerbit" :value="$buku->penerbit" />
                    <x-input label="Tahun terbit" name="tahun_terbit" type="number" :value="$buku->tahun_terbit" min="1000" :max="now()->year + 1" />
                    <x-input label="Jumlah halaman" name="halaman" type="number" :value="$buku->halaman" min="1" />
                    <x-select label="Kategori" name="kategori_id" :value="$buku->kategori_id" placeholder="Tanpa kategori" :options="$kategori->pluck('nama', 'id')->all()" />
                    <x-input label="Bahasa" name="bahasa" :value="$buku->bahasa" required />
                    <x-textarea class="sm:col-span-2" label="Sinopsis" name="sinopsis" :value="$buku->sinopsis" rows="5" help="Ringkasan singkat yang tampil di halaman katalog." />
                </div>
            </section>

            <section class="card p-6">
                <h2 class="text-base font-semibold text-slate-900">Inventaris</h2>
                <div class="mt-5 grid gap-5 sm:grid-cols-2">
                    <x-input label="Kode buku" name="kode" :value="$buku->kode" help="Kosongkan untuk dibuat otomatis." />
                    <x-input label="ISBN" name="isbn" :value="$buku->isbn" />
                    <x-input label="Jumlah eksemplar" name="jumlah_stok" type="number" :value="$buku->jumlah_stok" min="1" required
                        :help="$edit ? $buku->dipinjam.' eksemplar sedang dipinjam.' : null" />
                    <x-input label="Lokasi rak" name="lokasi_rak" :value="$buku->lokasi_rak" placeholder="Contoh: A-03" />
                </div>
            </section>
        </div>

        <aside class="space-y-6">
            <section class="card p-6" x-data="{ pratinjau: @js($buku->sampul_url), hapus: false }">
                <h2 class="text-base font-semibold text-slate-900">Sampul</h2>
                <div class="mx-auto mt-5 w-40">
                    <template x-if="pratinjau && ! hapus">
                        <img x-bind:src="pratinjau" alt="Pratinjau sampul" class="aspect-[2/3] w-full rounded-md object-cover shadow-md ring-1 ring-black/10">
                    </template>
                    <template x-if="! pratinjau || hapus">
                        <div class="w-full"><x-buku-cover :buku="$contohSampul" /></div>
                    </template>
                </div>

                <label class="btn btn-secondary btn-sm mt-5 w-full cursor-pointer">
                    <x-icon name="upload" class="size-4" /> Pilih gambar
                    <input type="file" name="sampul" accept="image/png,image/jpeg,image/webp" class="sr-only"
                        x-on:change="if ($event.target.files[0]) { pratinjau = URL.createObjectURL($event.target.files[0]); hapus = false }">
                </label>
                <p class="mt-2 text-center text-xs text-slate-500">JPG, PNG, atau WebP. Maksimal 2 MB.</p>
                @error('sampul') <p class="mt-2 text-center text-sm text-rose-600">{{ $message }}</p> @enderror

                @if ($edit && $buku->sampul)
                    <label class="mt-4 flex items-center justify-center gap-2 text-sm text-slate-600">
                        <input type="checkbox" name="hapus_sampul" value="1" x-model="hapus" class="size-4 rounded border-slate-300 text-brand-600 focus:ring-brand-600">
                        Hapus sampul saat ini
                    </label>
                @endif
            </section>

            <div class="card flex flex-col gap-3 p-6">
                <button type="submit" class="btn btn-primary w-full">{{ $edit ? 'Simpan perubahan' : 'Simpan buku' }}</button>
                <a href="{{ route('admin.buku.index') }}" class="btn btn-secondary w-full">Batal</a>
            </div>
        </aside>
    </form>
</x-layouts.admin>
