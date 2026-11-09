<x-layouts.app title="Akun saya">
    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">

        {{-- Kepala profil --}}
        <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-4">
                <x-avatar :user="$pengguna" size="lg" />
                <div>
                    <h1 class="text-2xl font-semibold tracking-tight">Halo, {{ \Illuminate\Support\Str::words($pengguna->name, 1, '') }}</h1>
                    <p class="mt-1 flex flex-wrap items-center gap-x-3 gap-y-1 text-sm text-slate-500">
                        <span class="inline-flex items-center gap-1.5"><x-icon name="id-card" class="size-4" /> <span class="font-mono">{{ $pengguna->kode_anggota }}</span></span>
                        <span class="inline-flex items-center gap-1.5"><x-icon name="calendar" class="size-4" /> Anggota sejak {{ optional($pengguna->tanggal_bergabung)->translatedFormat('F Y') ?? '-' }}</span>
                    </p>
                </div>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('profil.edit') }}" class="btn btn-secondary btn-sm"><x-icon name="pencil" class="size-4" /> Ubah profil</a>
                <a href="{{ route('katalog.index') }}" class="btn btn-primary btn-sm"><x-icon name="search" class="size-4" /> Cari buku</a>
            </div>
        </div>

        {{-- Ringkasan --}}
        <div class="mt-8 grid grid-cols-2 gap-4 lg:grid-cols-4">
            <x-stat-card label="Sedang dipinjam" :value="$ringkasan['aktif'].' / '.config('perpustakaan.maks_pinjam')" icon="book-open" color="sky" />
            <x-stat-card label="Terlambat" :value="$ringkasan['terlambat']" icon="clock" :color="$ringkasan['terlambat'] ? 'rose' : 'emerald'" />
            <x-stat-card label="Total peminjaman" :value="$ringkasan['total']" icon="folder-clock" color="indigo" />
            <x-stat-card label="Denda" :value="'Rp '.number_format($ringkasan['denda'], 0, ',', '.')" icon="banknote" :color="$ringkasan['denda'] ? 'amber' : 'emerald'" :hint="$ringkasan['denda'] ? 'Silakan lunasi di meja layanan.' : 'Tidak ada tunggakan.'" />
        </div>

        {{-- Pinjaman aktif --}}
        <section class="mt-12">
            <h2 class="text-xl font-semibold tracking-tight">Pinjaman aktif</h2>

            @if ($aktif->isEmpty())
                <div class="card mt-4">
                    <x-empty-state icon="book-open" title="Belum ada buku yang dipinjam" text="Temukan buku yang menarik di katalog, lalu pinjam di meja layanan.">
                        <a href="{{ route('katalog.index') }}" class="btn btn-primary btn-sm">Jelajahi katalog</a>
                    </x-empty-state>
                </div>
            @else
                <div class="mt-4 grid gap-4 lg:grid-cols-2">
                    @foreach ($aktif as $p)
                        @php
                            $sisa = (int) today()->diffInDays($p->jatuh_tempo, false);
                            $terlambat = $p->sudahTerlambat();
                        @endphp
                        <article class="card flex gap-5 p-5">
                            <a href="{{ route('katalog.show', $p->buku) }}" class="w-20 shrink-0 sm:w-24">
                                <x-buku-cover :buku="$p->buku" size="sm" />
                            </a>
                            <div class="flex min-w-0 flex-1 flex-col">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <h3 class="line-clamp-2 font-semibold text-slate-900"><a href="{{ route('katalog.show', $p->buku) }}" class="transition hover:text-brand-700">{{ $p->buku->judul }}</a></h3>
                                        <p class="mt-0.5 truncate text-sm text-slate-500">{{ $p->buku->penulis }}</p>
                                    </div>
                                    <x-badge :color="$p->status_tampil['warna']" dot>{{ $p->status_tampil['label'] }}</x-badge>
                                </div>

                                <dl class="mt-4 grid grid-cols-2 gap-3 text-sm">
                                    <div>
                                        <dt class="text-xs text-slate-500">Dipinjam</dt>
                                        <dd class="font-medium text-slate-800">{{ $p->tanggal_pinjam->translatedFormat('d M Y') }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-xs text-slate-500">Jatuh tempo</dt>
                                        <dd @class(['font-medium', 'text-rose-600' => $terlambat, 'text-slate-800' => ! $terlambat])>{{ $p->jatuh_tempo->translatedFormat('d M Y') }}</dd>
                                    </div>
                                </dl>

                                <p @class(['mt-3 text-sm font-medium', 'text-rose-600' => $terlambat, 'text-slate-600' => ! $terlambat])>
                                    @if ($terlambat)
                                        Terlambat {{ $p->hariTerlambat() }} hari &middot; denda berjalan Rp {{ number_format($p->dendaSaatIni(), 0, ',', '.') }}
                                    @elseif ($sisa === 0)
                                        Jatuh tempo hari ini
                                    @else
                                        Sisa {{ $sisa }} hari
                                    @endif
                                </p>

                                <div class="mt-auto flex items-center justify-between gap-3 pt-4">
                                    <span class="text-xs text-slate-500">Diperpanjang {{ $p->perpanjangan }}/{{ config('perpustakaan.maks_perpanjang') }}×</span>
                                    @if ($p->bisaDiperpanjang())
                                        <form method="POST" action="{{ route('akun.perpanjang', $p) }}" data-konfirmasi="Perpanjang masa pinjam &quot;{{ $p->buku->judul }}&quot; selama {{ config('perpustakaan.lama_perpanjang') }} hari?" data-judul="Perpanjang peminjaman" data-tombol="Ya, perpanjang" data-bahaya="false">
                                            @csrf
                                            <button type="submit" class="btn btn-soft btn-sm"><x-icon name="repeat" class="size-4" /> Perpanjang</button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </section>

        {{-- Riwayat --}}
        <section class="mt-12">
            <h2 class="text-xl font-semibold tracking-tight">Riwayat peminjaman</h2>
            <div class="card mt-4 overflow-hidden">
                @if ($riwayat->isEmpty())
                    <x-empty-state icon="folder-clock" title="Belum ada riwayat" text="Buku yang sudah Anda kembalikan akan muncul di sini." />
                @else
                    <div class="overflow-x-auto">
                        <table class="tbl">
                            <thead>
                                <tr>
                                    <th>Buku</th>
                                    <th>Dipinjam</th>
                                    <th>Dikembalikan</th>
                                    <th>Status</th>
                                    <th class="text-right">Denda</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($riwayat as $p)
                                    <tr>
                                        <td>
                                            <a href="{{ route('katalog.show', $p->buku) }}" class="font-medium text-slate-900 transition hover:text-brand-700">{{ $p->buku->judul }}</a>
                                            <p class="text-xs text-slate-500">{{ $p->kode }}</p>
                                        </td>
                                        <td class="whitespace-nowrap">{{ $p->tanggal_pinjam->translatedFormat('d M Y') }}</td>
                                        <td class="whitespace-nowrap">{{ $p->tanggal_kembali?->translatedFormat('d M Y') }}</td>
                                        <td><x-badge :color="$p->status_tampil['warna']" dot>{{ $p->status_tampil['label'] }}</x-badge></td>
                                        <td class="text-right tabular-nums">{{ $p->denda ? 'Rp '.number_format($p->denda, 0, ',', '.') : '–' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{ $riwayat->links() }}
                @endif
            </div>
        </section>
    </div>
</x-layouts.app>
