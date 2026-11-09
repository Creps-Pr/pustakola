<x-layouts.admin title="Anggota">
    <x-page-header title="Anggota" subtitle="Daftar anggota perpustakaan beserta status peminjamannya.">
        <x-slot:actions>
            <a href="{{ route('admin.anggota.create') }}" class="btn btn-primary"><x-icon name="user-plus" class="size-4" /> Daftarkan anggota</a>
        </x-slot:actions>
    </x-page-header>

    <div class="card overflow-hidden">
        <form method="GET" class="flex flex-col gap-3 border-b border-slate-100 p-4 sm:flex-row sm:items-center">
            <div class="relative flex-1">
                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400"><x-icon name="search" class="size-4.5" /></span>
                <input type="search" name="q" value="{{ $q }}" placeholder="Cari nama, email, telepon, atau nomor anggota…" class="input pl-10" aria-label="Cari anggota">
            </div>
            <select name="status" class="input sm:w-44" aria-label="Filter status">
                <option value="">Semua status</option>
                <option value="aktif" @selected($status === 'aktif')>Aktif</option>
                <option value="nonaktif" @selected($status === 'nonaktif')>Nonaktif</option>
            </select>
            <div class="flex gap-2">
                <button type="submit" class="btn btn-primary"><x-icon name="funnel" class="size-4" /> Terapkan</button>
                @if ($q || $status)
                    <a href="{{ route('admin.anggota.index') }}" class="btn btn-secondary">Reset</a>
                @endif
            </div>
        </form>

        @if ($anggota->isEmpty())
            <x-empty-state icon="users" title="Anggota tidak ditemukan" text="Coba kata kunci lain atau daftarkan anggota baru." />
        @else
            <div class="overflow-x-auto">
                <table class="tbl">
                    <thead>
                        <tr><th>Anggota</th><th>No. anggota</th><th>Kontak</th><th>Pinjaman aktif</th><th>Status</th><th class="text-right">Aksi</th></tr>
                    </thead>
                    <tbody>
                        @foreach ($anggota as $a)
                            <tr>
                                <td>
                                    <div class="flex items-center gap-3">
                                        <x-avatar :user="$a" />
                                        <div class="min-w-0">
                                            <a href="{{ route('admin.anggota.show', $a) }}" class="block truncate font-medium text-slate-900 hover:text-brand-700">{{ $a->name }}</a>
                                            <p class="truncate text-xs text-slate-500">{{ $a->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="whitespace-nowrap font-mono text-xs">{{ $a->kode_anggota }}</td>
                                <td class="whitespace-nowrap">{{ $a->telepon ?: '–' }}</td>
                                <td class="tabular-nums">{{ $a->pinjam_aktif }} / {{ config('perpustakaan.maks_pinjam') }}</td>
                                <td><x-badge :color="$a->isAktif() ? 'emerald' : 'slate'" dot>{{ $a->isAktif() ? 'Aktif' : 'Nonaktif' }}</x-badge></td>
                                <td>
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('admin.anggota.show', $a) }}" class="btn-icon" aria-label="Detail {{ $a->name }}" title="Detail"><x-icon name="eye" class="size-4" /></a>
                                        <a href="{{ route('admin.anggota.edit', $a) }}" class="btn-icon" aria-label="Ubah {{ $a->name }}" title="Ubah"><x-icon name="pencil" class="size-4" /></a>
                                        <form method="POST" action="{{ route('admin.anggota.destroy', $a) }}" data-konfirmasi="Akun {{ $a->name }} akan dihapus permanen." data-judul="Hapus anggota?" data-tombol="Ya, hapus">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-icon hover:bg-rose-50 hover:text-rose-600" aria-label="Hapus {{ $a->name }}" title="Hapus"><x-icon name="trash" class="size-4" /></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $anggota->links() }}
        @endif
    </div>
</x-layouts.admin>
