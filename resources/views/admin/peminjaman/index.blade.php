<x-layouts.admin title="Peminjaman">
    @php
        $rp = fn ($n) => 'Rp '.number_format($n, 0, ',', '.');
        $tab = [
            '' => 'Semua',
            'dipinjam' => 'Dipinjam',
            'terlambat' => 'Terlambat',
            'dikembalikan' => 'Dikembalikan',
            'denda' => 'Denda belum lunas',
        ];
    @endphp

    <x-page-header title="Peminjaman" subtitle="Pantau seluruh transaksi sirkulasi buku.">
        <x-slot:actions>
            <a href="{{ route('admin.peminjaman.create') }}" class="btn btn-primary"><x-icon name="book-plus" class="size-4" /> Peminjaman baru</a>
        </x-slot:actions>
    </x-page-header>

    <div class="card overflow-hidden">
        <div class="flex gap-1 overflow-x-auto border-b border-slate-100 px-3 pt-2" role="tablist" aria-label="Filter status">
            @foreach ($tab as $kunci => $label)
                <a href="{{ route('admin.peminjaman.index', array_filter(['status' => $kunci, 'q' => $q])) }}"
                   role="tab" aria-selected="{{ $status === (string) $kunci ? 'true' : 'false' }}"
                   @class([
                       'flex items-center gap-2 border-b-2 px-3 py-2.5 text-sm font-medium whitespace-nowrap transition',
                       'border-brand-600 text-brand-700' => $status === (string) $kunci,
                       'border-transparent text-slate-500 hover:text-slate-800' => $status !== (string) $kunci,
                   ])>
                    {{ $label }}
                    <span @class(['rounded-full px-2 py-0.5 text-xs tabular-nums', 'bg-brand-50 text-brand-700' => $status === (string) $kunci, 'bg-slate-100 text-slate-600' => $status !== (string) $kunci])>{{ $hitung[$kunci] }}</span>
                </a>
            @endforeach
        </div>

        <form method="GET" class="flex gap-3 border-b border-slate-100 p-4">
            @if ($status) <input type="hidden" name="status" value="{{ $status }}"> @endif
            <div class="relative flex-1">
                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400"><x-icon name="search" class="size-4.5" /></span>
                <input type="search" name="q" value="{{ $q }}" placeholder="Cari kode transaksi, anggota, atau judul buku…" class="input pl-10" aria-label="Cari peminjaman">
            </div>
            <button type="submit" class="btn btn-primary">Cari</button>
        </form>

        @if ($peminjaman->isEmpty())
            <x-empty-state icon="arrow-left-right" title="Tidak ada transaksi" text="Belum ada transaksi yang cocok dengan filter ini." />
        @else
            <div class="overflow-x-auto">
                <table class="tbl">
                    <thead><tr><th>Transaksi</th><th>Anggota</th><th>Buku</th><th>Jatuh tempo</th><th>Status</th><th class="text-right">Aksi</th></tr></thead>
                    <tbody>
                        @foreach ($peminjaman as $p)
                            <tr>
                                <td class="whitespace-nowrap">
                                    <a href="{{ route('admin.peminjaman.show', $p) }}" class="font-mono text-xs font-medium text-brand-600 hover:underline">{{ $p->kode }}</a>
                                    <p class="text-xs text-slate-500">{{ $p->tanggal_pinjam->translatedFormat('d M Y') }}</p>
                                </td>
                                <td>
                                    <a href="{{ route('admin.anggota.show', $p->anggota) }}" class="font-medium text-slate-900 hover:text-brand-700">{{ $p->anggota->name }}</a>
                                    <p class="font-mono text-[11px] text-slate-400">{{ $p->anggota->kode_anggota }}</p>
                                </td>
                                <td class="max-w-56"><p class="truncate">{{ $p->buku->judul }}</p></td>
                                <td class="whitespace-nowrap">
                                    <span @class(['text-rose-600 font-medium' => $p->sudahTerlambat()])>{{ $p->jatuh_tempo->translatedFormat('d M Y') }}</span>
                                    @if ($p->sudahTerlambat())<p class="text-xs text-rose-500">{{ $p->hariTerlambat() }} hari · {{ $rp($p->dendaSaatIni()) }}</p>@endif
                                </td>
                                <td><x-badge :color="$p->status_tampil['warna']" dot>{{ $p->status_tampil['label'] }}</x-badge></td>
                                <td>
                                    <div class="flex items-center justify-end gap-1">
                                        @if ($p->sedangDipinjam())
                                            <form method="POST" action="{{ route('admin.peminjaman.kembali', $p) }}" data-konfirmasi="Catat pengembalian &quot;{{ $p->buku->judul }}&quot; oleh {{ $p->anggota->name }}?@if ($p->sudahTerlambat()) Denda {{ $rp($p->dendaSaatIni()) }} akan dihitung.@endif" data-judul="Konfirmasi pengembalian" data-tombol="Ya, kembalikan" data-bahaya="false">
                                                @csrf
                                                <button type="submit" class="btn btn-soft btn-sm"><x-icon name="undo-2" class="size-4" /> Kembalikan</button>
                                            </form>
                                        @elseif ($p->denda > 0 && ! $p->denda_lunas)
                                            <form method="POST" action="{{ route('admin.peminjaman.lunasi', $p) }}" data-konfirmasi="Tandai denda {{ $rp($p->denda) }} sebagai lunas?" data-judul="Pelunasan denda" data-tombol="Ya, lunas" data-bahaya="false">
                                                @csrf
                                                <button type="submit" class="btn btn-soft btn-sm"><x-icon name="hand-coins" class="size-4" /> Lunasi</button>
                                            </form>
                                        @endif
                                        <a href="{{ route('admin.peminjaman.show', $p) }}" class="btn-icon" aria-label="Detail {{ $p->kode }}" title="Detail"><x-icon name="chevron-right" class="size-4" /></a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $peminjaman->links() }}
        @endif
    </div>
</x-layouts.admin>
