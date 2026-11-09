<x-layouts.admin title="Dasbor">
    @php
        $rp = fn ($n) => 'Rp '.number_format($n, 0, ',', '.');
        $sapaan = match (true) {
            now()->hour < 11 => 'Selamat pagi',
            now()->hour < 15 => 'Selamat siang',
            now()->hour < 18 => 'Selamat sore',
            default => 'Selamat malam',
        };
    @endphp

    <x-page-header :title="$sapaan.', '.\Illuminate\Support\Str::words(auth()->user()->name, 1, '')" subtitle="Ringkasan aktivitas perpustakaan hari ini.">
        <x-slot:actions>
            <a href="{{ route('admin.laporan.index') }}" class="btn btn-secondary btn-sm"><x-icon name="chart-column" class="size-4" /> Lihat laporan</a>
            <a href="{{ route('admin.buku.create') }}" class="btn btn-secondary btn-sm"><x-icon name="book-plus" class="size-4" /> Tambah buku</a>
        </x-slot:actions>
    </x-page-header>

    {{-- Statistik utama --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-stat-card label="Judul buku" :value="number_format($statistik['judul'], 0, ',', '.')" icon="library" color="indigo" :hint="number_format($statistik['eksemplar'], 0, ',', '.').' eksemplar di koleksi'" />
        <x-stat-card label="Anggota aktif" :value="number_format($statistik['anggota'], 0, ',', '.')" icon="users" color="teal" hint="Terdaftar dan berstatus aktif" />
        <x-stat-card label="Sedang dipinjam" :value="number_format($statistik['dipinjam'], 0, ',', '.')" icon="book-open" color="sky" :hint="$statistik['hari_ini'].' dipinjam & '.$statistik['kembali_hari_ini'].' dikembalikan hari ini'" />
        <x-stat-card label="Terlambat" :value="number_format($statistik['terlambat'], 0, ',', '.')" icon="clock" :color="$statistik['terlambat'] ? 'rose' : 'emerald'" :hint="'Denda belum lunas: '.$rp($statistik['denda'])" />
    </div>

    {{-- Grafik --}}
    <div class="mt-6 grid gap-6 lg:grid-cols-3">
        <section class="card p-6 lg:col-span-2">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-base font-semibold text-slate-900">Tren sirkulasi</h2>
                    <p class="text-sm text-slate-500">Peminjaman dan pengembalian 6 bulan terakhir</p>
                </div>
                <span class="grid size-9 place-items-center rounded-lg bg-brand-50 text-brand-600"><x-icon name="trending-up" class="size-5" /></span>
            </div>
            <div class="relative mt-5 h-72">
                <canvas data-chart="tren" data-config="{{ json_encode($tren) }}" role="img" aria-label="Grafik tren peminjaman dan pengembalian"></canvas>
            </div>
        </section>

        <section class="card p-6">
            <h2 class="text-base font-semibold text-slate-900">Peminjaman per kategori</h2>
            <p class="text-sm text-slate-500">Seluruh waktu</p>
            @if (count($kategori['label']))
                <div class="relative mt-5 h-72">
                    <canvas data-chart="kategori" data-config="{{ json_encode($kategori) }}" role="img" aria-label="Grafik peminjaman per kategori"></canvas>
                </div>
            @else
                <x-empty-state icon="chart-column" title="Belum ada data" text="Grafik muncul setelah ada peminjaman." />
            @endif
        </section>
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-3">
        {{-- Perlu tindak lanjut --}}
        <section class="card overflow-hidden lg:col-span-2">
            <div class="flex items-center justify-between px-6 pt-6 pb-4">
                <div>
                    <h2 class="text-base font-semibold text-slate-900">Perlu tindak lanjut</h2>
                    <p class="text-sm text-slate-500">Buku yang melewati jatuh tempo</p>
                </div>
                <a href="{{ route('admin.peminjaman.index', ['status' => 'terlambat']) }}" class="link text-sm">Lihat semua</a>
            </div>

            @if ($terlambat->isEmpty())
                <x-empty-state icon="circle-check" title="Semua tepat waktu" text="Tidak ada peminjaman yang terlambat saat ini." class="pt-4" />
            @else
                <div class="overflow-x-auto">
                    <table class="tbl">
                        <thead>
                            <tr><th>Anggota</th><th>Buku</th><th>Terlambat</th><th class="text-right">Denda berjalan</th></tr>
                        </thead>
                        <tbody>
                            @foreach ($terlambat as $p)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.anggota.show', $p->anggota) }}" class="font-medium text-slate-900 hover:text-brand-700">{{ $p->anggota->name }}</a>
                                        <p class="text-xs text-slate-500">{{ $p->anggota->telepon ?: $p->anggota->email }}</p>
                                    </td>
                                    <td class="max-w-56"><p class="truncate">{{ $p->buku->judul }}</p></td>
                                    <td><x-badge color="rose" dot>{{ $p->hariTerlambat() }} hari</x-badge></td>
                                    <td class="text-right font-medium tabular-nums text-rose-600">{{ $rp($p->dendaSaatIni()) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>

        {{-- Buku terpopuler --}}
        <section class="card p-6">
            <h2 class="text-base font-semibold text-slate-900">Buku terpopuler</h2>
            <p class="text-sm text-slate-500">Paling sering dipinjam</p>

            <ol class="mt-5 space-y-4">
                @forelse ($populer as $i => $b)
                    <li class="flex items-center gap-3">
                        <span class="w-4 text-sm font-semibold text-slate-400 tabular-nums">{{ $i + 1 }}</span>
                        <div class="w-9 shrink-0"><x-buku-cover :buku="$b" size="sm" /></div>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-medium text-slate-900">{{ $b->judul }}</p>
                            <p class="truncate text-xs text-slate-500">{{ $b->penulis }}</p>
                        </div>
                        <span class="text-sm font-semibold text-slate-700 tabular-nums">{{ $b->peminjaman_count }}×</span>
                    </li>
                @empty
                    <li class="text-sm text-slate-500">Belum ada data.</li>
                @endforelse
            </ol>
        </section>
    </div>

    {{-- Aktivitas terbaru --}}
    <section class="card mt-6 overflow-hidden">
        <div class="flex items-center justify-between px-6 pt-6 pb-4">
            <div>
                <h2 class="text-base font-semibold text-slate-900">Transaksi terbaru</h2>
                <p class="text-sm text-slate-500">8 aktivitas peminjaman terakhir</p>
            </div>
            <a href="{{ route('admin.peminjaman.index') }}" class="link text-sm">Semua transaksi</a>
        </div>
        <div class="overflow-x-auto">
            <table class="tbl">
                <thead>
                    <tr><th>Kode</th><th>Anggota</th><th>Buku</th><th>Tanggal pinjam</th><th>Status</th></tr>
                </thead>
                <tbody>
                    @foreach ($terbaru as $p)
                        <tr>
                            <td><a href="{{ route('admin.peminjaman.show', $p) }}" class="font-mono text-xs font-medium text-brand-600 hover:underline">{{ $p->kode }}</a></td>
                            <td>{{ $p->anggota->name }}</td>
                            <td class="max-w-64"><p class="truncate">{{ $p->buku->judul }}</p></td>
                            <td class="whitespace-nowrap">{{ $p->tanggal_pinjam->translatedFormat('d M Y') }}</td>
                            <td><x-badge :color="$p->status_tampil['warna']" dot>{{ $p->status_tampil['label'] }}</x-badge></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
</x-layouts.admin>
