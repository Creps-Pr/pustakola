<x-layouts.admin title="Petugas">
    <x-page-header title="Petugas & administrator" subtitle="Kelola akun yang dapat mengakses panel ini.">
        <x-slot:actions>
            <a href="{{ route('admin.petugas.create') }}" class="btn btn-primary"><x-icon name="user-plus" class="size-4" /> Tambah akun staf</a>
        </x-slot:actions>
    </x-page-header>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="tbl">
                <thead><tr><th>Nama</th><th>Peran</th><th>Telepon</th><th>Status</th><th class="text-right">Aksi</th></tr></thead>
                <tbody>
                    @foreach ($petugas as $p)
                        <tr>
                            <td>
                                <div class="flex items-center gap-3">
                                    <x-avatar :user="$p" />
                                    <div class="min-w-0">
                                        <p class="truncate font-medium text-slate-900">{{ $p->name }}@if ($p->id === auth()->id()) <span class="text-xs font-normal text-slate-400">(Anda)</span>@endif</p>
                                        <p class="truncate text-xs text-slate-500">{{ $p->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td><x-badge :color="$p->isAdmin() ? 'violet' : 'sky'">{{ $p->label_peran }}</x-badge></td>
                            <td class="whitespace-nowrap">{{ $p->telepon ?: '–' }}</td>
                            <td><x-badge :color="$p->isAktif() ? 'emerald' : 'slate'" dot>{{ $p->isAktif() ? 'Aktif' : 'Nonaktif' }}</x-badge></td>
                            <td>
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('admin.petugas.edit', $p) }}" class="btn-icon" aria-label="Ubah {{ $p->name }}" title="Ubah"><x-icon name="pencil" class="size-4" /></a>
                                    @if ($p->id !== auth()->id())
                                        <form method="POST" action="{{ route('admin.petugas.destroy', $p) }}" data-konfirmasi="Akun {{ $p->name }} akan dihapus permanen." data-judul="Hapus akun staf?" data-tombol="Ya, hapus">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-icon hover:bg-rose-50 hover:text-rose-600" aria-label="Hapus {{ $p->name }}" title="Hapus"><x-icon name="trash" class="size-4" /></button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.admin>
