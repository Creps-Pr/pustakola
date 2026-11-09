<x-layouts.admin title="Kategori">
    <x-page-header title="Kategori buku" subtitle="Kelompokkan koleksi agar mudah ditelusuri anggota.">
        <x-slot:actions>
            <a href="{{ route('admin.kategori.create') }}" class="btn btn-primary"><x-icon name="plus" class="size-4" /> Tambah kategori</a>
        </x-slot:actions>
    </x-page-header>

    @if ($kategori->isEmpty())
        <div class="card">
            <x-empty-state icon="tags" title="Belum ada kategori" text="Buat kategori pertama untuk mulai mengelompokkan buku.">
                <a href="{{ route('admin.kategori.create') }}" class="btn btn-primary btn-sm">Tambah kategori</a>
            </x-empty-state>
        </div>
    @else
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            @foreach ($kategori as $k)
                <article class="card flex flex-col p-5">
                    <div class="flex items-start justify-between gap-3">
                        <span class="grid size-11 place-items-center rounded-xl {{ \App\Support\Warna::ikon($k->warna) }}"><x-icon name="tag" class="size-5" /></span>
                        <div class="flex items-center gap-1">
                            <a href="{{ route('admin.kategori.edit', $k) }}" class="btn-icon" aria-label="Ubah {{ $k->nama }}" title="Ubah"><x-icon name="pencil" class="size-4" /></a>
                            <form method="POST" action="{{ route('admin.kategori.destroy', $k) }}" data-konfirmasi="Kategori &quot;{{ $k->nama }}&quot; akan dihapus." data-judul="Hapus kategori?" data-tombol="Ya, hapus">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-icon hover:bg-rose-50 hover:text-rose-600" aria-label="Hapus {{ $k->nama }}" title="Hapus"><x-icon name="trash" class="size-4" /></button>
                            </form>
                        </div>
                    </div>
                    <h2 class="mt-4 text-base font-semibold text-slate-900">{{ $k->nama }}</h2>
                    <p class="mt-1 line-clamp-2 min-h-10 text-sm text-slate-500">{{ $k->deskripsi ?: 'Belum ada deskripsi.' }}</p>
                    <div class="mt-4 flex items-center justify-between border-t border-slate-100 pt-4">
                        <x-badge :color="$k->warna" dot>{{ $k->buku_count }} judul</x-badge>
                        <a href="{{ route('admin.buku.index', ['kategori' => $k->id]) }}" class="link text-sm">Lihat buku</a>
                    </div>
                </article>
            @endforeach
        </div>
    @endif
</x-layouts.admin>
