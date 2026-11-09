<x-layouts.admin title="Data Buku">
    <x-page-header title="Data buku" subtitle="Kelola koleksi, stok, dan lokasi rak.">
        <x-slot:actions>
            <a href="{{ route('admin.buku.create') }}" class="btn btn-primary"><x-icon name="plus" class="size-4" /> Tambah buku</a>
        </x-slot:actions>
    </x-page-header>

    <div class="card overflow-hidden">
        <form method="GET" class="flex flex-col gap-3 border-b border-slate-100 p-4 lg:flex-row lg:items-center">
            <div class="relative flex-1">
                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400"><x-icon name="search" class="size-4.5" /></span>
                <input type="search" name="q" value="{{ $q }}" placeholder="Cari judul, penulis, penerbit, ISBN, atau kode…" class="input pl-10" aria-label="Cari buku">
            </div>
            <select name="kategori" class="input lg:w-52" aria-label="Filter kategori">
                <option value="">Semua kategori</option>
                @foreach ($kategori as $k)
                    <option value="{{ $k->id }}" @selected((string) $kategoriId === (string) $k->id)>{{ $k->nama }}</option>
                @endforeach
            </select>
            <select name="stok" class="input lg:w-44" aria-label="Filter ketersediaan">
                <option value="">Semua stok</option>
                <option value="tersedia" @selected($stok === 'tersedia')>Tersedia</option>
                <option value="habis" @selected($stok === 'habis')>Sedang habis</option>
            </select>
            <div class="flex gap-2">
                <button type="submit" class="btn btn-primary"><x-icon name="funnel" class="size-4" /> Terapkan</button>
                @if ($q || $kategoriId || $stok)
                    <a href="{{ route('admin.buku.index') }}" class="btn btn-secondary">Reset</a>
                @endif
            </div>
        </form>

        @if ($buku->isEmpty())
            <x-empty-state icon="library" title="Buku tidak ditemukan" text="Coba ubah kata kunci atau filter, atau tambahkan buku baru.">
                <a href="{{ route('admin.buku.create') }}" class="btn btn-primary btn-sm">Tambah buku</a>
            </x-empty-state>
        @else
            <div class="overflow-x-auto">
                <table class="tbl">
                    <thead>
                        <tr>
                            <th>Buku</th>
                            <th>Kategori</th>
                            <th>Rak</th>
                            <th>Stok</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($buku as $b)
                            <tr>
                                <td>
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 shrink-0"><x-buku-cover :buku="$b" size="sm" /></div>
                                        <div class="min-w-0">
                                            <a href="{{ route('katalog.show', $b) }}" target="_blank" class="line-clamp-1 font-medium text-slate-900 hover:text-brand-700">{{ $b->judul }}</a>
                                            <p class="truncate text-xs text-slate-500">{{ $b->penulis }}@if ($b->tahun_terbit) &middot; {{ $b->tahun_terbit }}@endif</p>
                                            <p class="font-mono text-[11px] text-slate-400">{{ $b->kode }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if ($b->kategori)
                                        <x-badge :color="$b->kategori->warna">{{ $b->kategori->nama }}</x-badge>
                                    @else
                                        <span class="text-slate-400">–</span>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap font-mono text-xs">{{ $b->lokasi_rak ?: '–' }}</td>
                                <td class="whitespace-nowrap">
                                    <x-badge :color="$b->tersedia > 0 ? 'emerald' : 'rose'" dot>{{ $b->tersedia }} / {{ $b->jumlah_stok }}</x-badge>
                                </td>
                                <td>
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('admin.buku.edit', $b) }}" class="btn-icon" aria-label="Ubah {{ $b->judul }}" title="Ubah"><x-icon name="pencil" class="size-4" /></a>
                                        <form method="POST" action="{{ route('admin.buku.destroy', $b) }}" data-konfirmasi="Buku &quot;{{ $b->judul }}&quot; akan dihapus permanen. Tindakan ini tidak dapat dibatalkan." data-judul="Hapus buku?" data-tombol="Ya, hapus">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-icon hover:bg-rose-50 hover:text-rose-600" aria-label="Hapus {{ $b->judul }}" title="Hapus"><x-icon name="trash" class="size-4" /></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $buku->links() }}
        @endif
    </div>
</x-layouts.admin>
