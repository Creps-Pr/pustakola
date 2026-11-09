<x-layouts.admin :title="$anggota->name">
    @php $rp = fn ($n) => 'Rp '.number_format($n, 0, ',', '.'); @endphp

    <x-page-header :title="$anggota->name" :subtitle="$anggota->kode_anggota">
        <x-slot:actions>
            <a href="{{ route('admin.anggota.index') }}" class="btn btn-secondary btn-sm"><x-icon name="chevron-left" class="size-4" /> Kembali</a>
            <a href="{{ route('admin.anggota.edit', $anggota) }}" class="btn btn-secondary btn-sm"><x-icon name="pencil" class="size-4" /> Ubah</a>
            @if ($anggota->isAktif())
                <a href="{{ route('admin.peminjaman.create', ['anggota' => $anggota->id]) }}" class="btn btn-primary btn-sm"><x-icon name="book-plus" class="size-4" /> Catat peminjaman</a>
            @endif
        </x-slot:actions>
    </x-page-header>

    <div class="grid gap-6 lg:grid-cols-3">
        {{-- Profil --}}
        <aside class="card h-fit p-6">
            <div class="flex flex-col items-center text-center">
                <x-avatar :user="$anggota" size="lg" class="!size-20 !text-2xl" />
                <h2 class="mt-4 text-lg font-semibold text-slate-900">{{ $anggota->name }}</h2>
                <x-badge class="mt-2" :color="$anggota->isAktif() ? 'emerald' : 'slate'" dot>{{ $anggota->isAktif() ? 'Anggota aktif' : 'Nonaktif' }}</x-badge>
            </div>

            <dl class="mt-6 space-y-4 border-t border-slate-100 pt-6 text-sm">
                <div class="flex gap-3"><dt class="mt-0.5 text-slate-400"><x-icon name="mail" class="size-4.5" /></dt><dd class="min-w-0 break-words text-slate-700">{{ $anggota->email }}</dd></div>
                <div class="flex gap-3"><dt class="mt-0.5 text-slate-400"><x-icon name="phone" class="size-4.5" /></dt><dd class="text-slate-700">{{ $anggota->telepon ?: '–' }}</dd></div>
                <div class="flex gap-3"><dt class="mt-0.5 text-slate-400"><x-icon name="map-pin" class="size-4.5" /></dt><dd class="text-slate-700">{{ $anggota->alamat ?: '–' }}</dd></div>
                <div class="flex gap-3"><dt class="mt-0.5 text-slate-400"><x-icon name="calendar" class="size-4.5" /></dt><dd class="text-slate-700">Bergabung {{ optional($anggota->tanggal_bergabung)->translatedFormat('d F Y') ?? '–' }}</dd></div>
            </dl>
        </aside>

        <div class="space-y-6 lg:col-span-2">
            <div class="grid grid-cols-3 gap-4">
                <x-stat-card label="Total pinjam" :value="$ringkasan['total']" icon="folder-clock" color="indigo" />
                <x-stat-card label="Aktif" :value="$ringkasan['aktif']" icon="book-open" color="sky" />
                <x-stat-card label="Denda" :value="$rp($ringkasan['denda'])" icon="banknote" :color="$ringkasan['denda'] ? 'amber' : 'emerald'" />
            </div>

            <section class="card overflow-hidden">
                <h2 class="px-6 pt-6 pb-4 text-base font-semibold text-slate-900">Sedang dipinjam</h2>
                @if ($aktif->isEmpty())
                    <x-empty-state icon="book-open" title="Tidak ada pinjaman aktif" class="pt-2" />
                @else
                    <div class="overflow-x-auto">
                        <table class="tbl">
                            <thead><tr><th>Buku</th><th>Jatuh tempo</th><th>Status</th><th class="text-right">Aksi</th></tr></thead>
                            <tbody>
                                @foreach ($aktif as $p)
                                    <tr>
                                        <td class="max-w-56"><a href="{{ route('admin.peminjaman.show', $p) }}" class="block truncate font-medium text-slate-900 hover:text-brand-700">{{ $p->buku->judul }}</a><p class="font-mono text-[11px] text-slate-400">{{ $p->kode }}</p></td>
                                        <td class="whitespace-nowrap">{{ $p->jatuh_tempo->translatedFormat('d M Y') }}</td>
                                        <td><x-badge :color="$p->status_tampil['warna']" dot>{{ $p->status_tampil['label'] }}</x-badge></td>
                                        <td class="text-right">
                                            <form method="POST" action="{{ route('admin.peminjaman.kembali', $p) }}" data-konfirmasi="Catat pengembalian &quot;{{ $p->buku->judul }}&quot;?@if ($p->sudahTerlambat()) Denda keterlambatan {{ $rp($p->dendaSaatIni()) }} akan dihitung.@endif" data-judul="Konfirmasi pengembalian" data-tombol="Ya, kembalikan" data-bahaya="false">
                                                @csrf
                                                <button type="submit" class="btn btn-soft btn-sm"><x-icon name="undo-2" class="size-4" /> Kembalikan</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </section>

            <section class="card overflow-hidden">
                <h2 class="px-6 pt-6 pb-4 text-base font-semibold text-slate-900">Riwayat pengembalian</h2>
                @if ($riwayat->isEmpty())
                    <x-empty-state icon="folder-clock" title="Belum ada riwayat" class="pt-2" />
                @else
                    <div class="overflow-x-auto">
                        <table class="tbl">
                            <thead><tr><th>Buku</th><th>Dipinjam</th><th>Dikembalikan</th><th>Status</th><th class="text-right">Denda</th></tr></thead>
                            <tbody>
                                @foreach ($riwayat as $p)
                                    <tr>
                                        <td class="max-w-56"><a href="{{ route('admin.peminjaman.show', $p) }}" class="block truncate font-medium text-slate-900 hover:text-brand-700">{{ $p->buku->judul }}</a></td>
                                        <td class="whitespace-nowrap">{{ $p->tanggal_pinjam->translatedFormat('d M Y') }}</td>
                                        <td class="whitespace-nowrap">{{ $p->tanggal_kembali?->translatedFormat('d M Y') }}</td>
                                        <td><x-badge :color="$p->status_tampil['warna']" dot>{{ $p->status_tampil['label'] }}</x-badge></td>
                                        <td class="text-right tabular-nums">{{ $p->denda ? $rp($p->denda) : '–' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{ $riwayat->links() }}
                @endif
            </section>
        </div>
    </div>
</x-layouts.admin>
