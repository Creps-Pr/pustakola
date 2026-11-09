<x-layouts.admin :title="$peminjaman->kode">
    @php
        $rp = fn ($n) => 'Rp '.number_format($n, 0, ',', '.');
        $b = $peminjaman->buku;
        $a = $peminjaman->anggota;
    @endphp

    <x-page-header :title="'Transaksi '.$peminjaman->kode" :subtitle="'Dicatat '.$peminjaman->created_at->translatedFormat('d F Y, H:i').($peminjaman->petugas ? ' oleh '.$peminjaman->petugas->name : '')">
        <x-slot:actions>
            <a href="{{ route('admin.peminjaman.index') }}" class="btn btn-secondary btn-sm"><x-icon name="chevron-left" class="size-4" /> Kembali</a>
            <button type="button" onclick="window.print()" class="btn btn-secondary btn-sm"><x-icon name="printer" class="size-4" /> Cetak</button>
        </x-slot:actions>
    </x-page-header>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <section class="card p-6">
                <div class="flex flex-col gap-5 sm:flex-row">
                    <div class="w-28 shrink-0"><x-buku-cover :buku="$b" /></div>
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div class="min-w-0">
                                @if ($b->kategori)<p class="text-xs font-medium text-brand-600">{{ $b->kategori->nama }}</p>@endif
                                <h2 class="text-lg font-semibold text-slate-900">{{ $b->judul }}</h2>
                                <p class="text-sm text-slate-500">{{ $b->penulis }}</p>
                            </div>
                            <x-badge :color="$peminjaman->status_tampil['warna']" dot>{{ $peminjaman->status_tampil['label'] }}</x-badge>
                        </div>

                        <dl class="mt-5 grid grid-cols-2 gap-x-4 gap-y-4 text-sm sm:grid-cols-3">
                            <div><dt class="text-xs text-slate-500">Kode buku</dt><dd class="font-mono font-medium text-slate-800">{{ $b->kode }}</dd></div>
                            <div><dt class="text-xs text-slate-500">Lokasi rak</dt><dd class="font-medium text-slate-800">{{ $b->lokasi_rak ?: '–' }}</dd></div>
                            <div><dt class="text-xs text-slate-500">Diperpanjang</dt><dd class="font-medium text-slate-800">{{ $peminjaman->perpanjangan }} / {{ config('perpustakaan.maks_perpanjang') }}×</dd></div>
                            <div><dt class="text-xs text-slate-500">Tanggal pinjam</dt><dd class="font-medium text-slate-800">{{ $peminjaman->tanggal_pinjam->translatedFormat('d F Y') }}</dd></div>
                            <div><dt class="text-xs text-slate-500">Jatuh tempo</dt><dd @class(['font-medium', 'text-rose-600' => $peminjaman->sudahTerlambat(), 'text-slate-800' => ! $peminjaman->sudahTerlambat()])>{{ $peminjaman->jatuh_tempo->translatedFormat('d F Y') }}</dd></div>
                            <div><dt class="text-xs text-slate-500">Tanggal kembali</dt><dd class="font-medium text-slate-800">{{ $peminjaman->tanggal_kembali?->translatedFormat('d F Y') ?? '–' }}</dd></div>
                        </dl>

                        @if ($peminjaman->catatan)
                            <p class="mt-5 rounded-lg bg-slate-50 p-3 text-sm text-slate-600"><span class="font-medium text-slate-700">Catatan:</span> {{ $peminjaman->catatan }}</p>
                        @endif
                    </div>
                </div>
            </section>

            {{-- Aksi --}}
            @if ($peminjaman->sedangDipinjam())
                <section class="card flex flex-col gap-3 p-6 sm:flex-row print:hidden">
                    <form method="POST" action="{{ route('admin.peminjaman.kembali', $peminjaman) }}" class="flex-1" data-konfirmasi="Catat pengembalian &quot;{{ $b->judul }}&quot;?@if ($peminjaman->sudahTerlambat()) Denda {{ $rp($peminjaman->dendaSaatIni()) }} akan dihitung.@endif" data-judul="Konfirmasi pengembalian" data-tombol="Ya, kembalikan" data-bahaya="false">
                        @csrf
                        <button type="submit" class="btn btn-primary w-full"><x-icon name="undo-2" class="size-4" /> Catat pengembalian</button>
                    </form>
                    @if ($peminjaman->bisaDiperpanjang())
                        <form method="POST" action="{{ route('admin.peminjaman.perpanjang', $peminjaman) }}" class="flex-1" data-konfirmasi="Perpanjang masa pinjam selama {{ config('perpustakaan.lama_perpanjang') }} hari?" data-judul="Perpanjang peminjaman" data-tombol="Ya, perpanjang" data-bahaya="false">
                            @csrf
                            <button type="submit" class="btn btn-secondary w-full"><x-icon name="repeat" class="size-4" /> Perpanjang</button>
                        </form>
                    @endif
                </section>
            @elseif ($peminjaman->denda > 0 && ! $peminjaman->denda_lunas)
                <section class="card flex flex-col gap-3 p-6 print:hidden">
                    <form method="POST" action="{{ route('admin.peminjaman.lunasi', $peminjaman) }}" data-konfirmasi="Tandai denda {{ $rp($peminjaman->denda) }} sebagai lunas?" data-judul="Pelunasan denda" data-tombol="Ya, lunas" data-bahaya="false">
                        @csrf
                        <button type="submit" class="btn btn-primary w-full"><x-icon name="hand-coins" class="size-4" /> Tandai denda lunas</button>
                    </form>
                </section>
            @endif
        </div>

        <aside class="space-y-6">
            <section class="card p-6">
                <h2 class="text-sm font-semibold text-slate-900">Peminjam</h2>
                <div class="mt-4 flex items-center gap-3">
                    <x-avatar :user="$a" />
                    <div class="min-w-0">
                        <a href="{{ route('admin.anggota.show', $a) }}" class="block truncate font-medium text-slate-900 hover:text-brand-700">{{ $a->name }}</a>
                        <p class="font-mono text-xs text-slate-500">{{ $a->kode_anggota }}</p>
                    </div>
                </div>
                <dl class="mt-4 space-y-2 border-t border-slate-100 pt-4 text-sm text-slate-600">
                    <div class="flex gap-2"><x-icon name="mail" class="mt-0.5 size-4 text-slate-400" /> <span class="break-all">{{ $a->email }}</span></div>
                    <div class="flex gap-2"><x-icon name="phone" class="mt-0.5 size-4 text-slate-400" /> {{ $a->telepon ?: '–' }}</div>
                </dl>
            </section>

            <section class="card p-6">
                <h2 class="text-sm font-semibold text-slate-900">Denda</h2>
                @php $denda = $peminjaman->dendaSaatIni(); @endphp
                <p @class(['mt-3 text-3xl font-semibold tracking-tight tabular-nums', 'text-rose-600' => $denda > 0 && ! $peminjaman->denda_lunas, 'text-slate-900' => $denda === 0, 'text-emerald-600' => $denda > 0 && $peminjaman->denda_lunas])>{{ $rp($denda) }}</p>
                <p class="mt-1 text-sm text-slate-500">
                    @if ($peminjaman->sedangDipinjam())
                        @if ($peminjaman->sudahTerlambat()) Terlambat {{ $peminjaman->hariTerlambat() }} hari (denda berjalan). @else Belum ada keterlambatan. @endif
                    @elseif ($denda === 0)
                        Dikembalikan tepat waktu.
                    @elseif ($peminjaman->denda_lunas)
                        Terlambat {{ $peminjaman->hariTerlambat() }} hari &middot; sudah lunas.
                    @else
                        Terlambat {{ $peminjaman->hariTerlambat() }} hari &middot; belum lunas.
                    @endif
                </p>
            </section>
        </aside>
    </div>
</x-layouts.admin>
