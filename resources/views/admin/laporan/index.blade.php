<x-layouts.admin title="Laporan">
    @php $rp = fn ($n) => 'Rp '.number_format($n, 0, ',', '.'); @endphp

    <x-page-header title="Laporan peminjaman" :subtitle="$dari->translatedFormat('d F Y').' – '.$sampai->translatedFormat('d F Y')">
        <x-slot:actions>
            <button type="button" onclick="window.print()" class="btn btn-secondary btn-sm"><x-icon name="printer" class="size-4" /> Cetak</button>
            <a href="{{ route('admin.laporan.ekspor', ['dari' => $dari->toDateString(), 'sampai' => $sampai->toDateString(), 'status' => $status]) }}" class="btn btn-primary btn-sm"><x-icon name="download" class="size-4" /> Ekspor CSV</a>
        </x-slot:actions>
    </x-page-header>

    <form method="GET" class="card mb-6 grid gap-4 p-4 sm:grid-cols-2 lg:grid-cols-5 lg:items-end print:hidden">
        <x-input label="Dari tanggal" name="dari" type="date" :value="$dari->toDateString()" />
        <x-input label="Sampai tanggal" name="sampai" type="date" :value="$sampai->toDateString()" />
        <x-select label="Status" name="status" :value="$status" placeholder="Semua status"
            :options="['dipinjam' => 'Sedang dipinjam', 'terlambat' => 'Terlambat', 'dikembalikan' => 'Dikembalikan', 'denda' => 'Denda belum lunas']" />
        <div class="flex gap-2 lg:col-span-2">
            <button type="submit" class="btn btn-primary"><x-icon name="funnel" class="size-4" /> Tampilkan</button>
            <a href="{{ route('admin.laporan.index') }}" class="btn btn-secondary">Bulan ini</a>
        </div>
    </form>

    <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
        <x-stat-card label="Total transaksi" :value="number_format($ringkasan['total'], 0, ',', '.')" icon="receipt" color="indigo" />
        <x-stat-card label="Masih dipinjam" :value="number_format($ringkasan['dipinjam'], 0, ',', '.')" icon="book-open" color="sky" />
        <x-stat-card label="Dikembalikan" :value="number_format($ringkasan['dikembalikan'], 0, ',', '.')" icon="book-open-check" color="emerald" />
        <x-stat-card label="Total denda" :value="$rp($ringkasan['denda'])" icon="banknote" color="amber" />
    </div>

    <section class="card mt-6 overflow-hidden">
        @if ($data->isEmpty())
            <x-empty-state icon="file-text" title="Tidak ada data" text="Tidak ada transaksi pada periode dan filter yang dipilih." />
        @else
            <div class="overflow-x-auto">
                <table class="tbl">
                    <thead>
                        <tr><th>Kode</th><th>Anggota</th><th>Buku</th><th>Pinjam</th><th>Jatuh tempo</th><th>Kembali</th><th>Status</th><th class="text-right">Denda</th></tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $p)
                            <tr>
                                <td class="font-mono text-xs"><a href="{{ route('admin.peminjaman.show', $p) }}" class="text-brand-600 hover:underline">{{ $p->kode }}</a></td>
                                <td>{{ $p->anggota?->name }}</td>
                                <td class="max-w-52"><p class="truncate">{{ $p->buku?->judul }}</p></td>
                                <td class="whitespace-nowrap">{{ $p->tanggal_pinjam->translatedFormat('d M Y') }}</td>
                                <td class="whitespace-nowrap">{{ $p->jatuh_tempo->translatedFormat('d M Y') }}</td>
                                <td class="whitespace-nowrap">{{ $p->tanggal_kembali?->translatedFormat('d M Y') ?? '–' }}</td>
                                <td><x-badge :color="$p->status_tampil['warna']" dot>{{ $p->status_tampil['label'] }}</x-badge></td>
                                <td class="text-right tabular-nums">{{ $p->dendaSaatIni() ? $rp($p->dendaSaatIni()) : '–' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $data->links() }}
        @endif
    </section>
</x-layouts.admin>
