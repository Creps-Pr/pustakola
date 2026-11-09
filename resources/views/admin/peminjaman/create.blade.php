<x-layouts.admin title="Peminjaman Baru">
    @php
        $dataAnggota = $anggota->map(fn ($a) => [
            'id' => $a->id,
            'judul' => $a->name,
            'sub' => $a->kode_anggota,
        ])->values();

        $dataBuku = $buku->map(fn ($b) => [
            'id' => $b->id,
            'judul' => $b->judul,
            'sub' => $b->penulis.' · '.$b->kode.' · sisa '.$b->tersedia,
        ])->values();
    @endphp

    <x-page-header title="Peminjaman baru" subtitle="Catat peminjaman buku oleh anggota.">
        <x-slot:actions>
            <a href="{{ route('admin.peminjaman.index') }}" class="btn btn-secondary btn-sm"><x-icon name="chevron-left" class="size-4" /> Kembali</a>
        </x-slot:actions>
    </x-page-header>

    <form method="POST" action="{{ route('admin.peminjaman.store') }}" class="grid gap-6 lg:grid-cols-3"
        x-data="{ lama: {{ (int) old('lama', config('perpustakaan.lama_pinjam')) }}, tanggal: @js(old('tanggal_pinjam', today()->toDateString())),
            get tempo() {
                const d = new Date(this.tanggal + 'T00:00:00');
                if (isNaN(d)) return '–';
                d.setDate(d.getDate() + Number(this.lama || 0));
                return d.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
            } }">
        @csrf

        <div class="space-y-6 lg:col-span-2">
            <section class="card p-6">
                <h2 class="text-base font-semibold text-slate-900">Anggota & buku</h2>
                <div class="mt-5 space-y-5">
                    @include('admin.peminjaman._combobox', ['name' => 'user_id', 'label' => 'Anggota', 'items' => $dataAnggota, 'dipilih' => old('user_id', $anggotaDipilih), 'placeholder' => 'Cari nama atau nomor anggota…', 'icon' => 'user'])
                    @include('admin.peminjaman._combobox', ['name' => 'buku_id', 'label' => 'Buku', 'items' => $dataBuku, 'dipilih' => old('buku_id', $bukuDipilih), 'placeholder' => 'Cari judul, penulis, atau kode buku…', 'icon' => 'book-open'])
                </div>
            </section>

            <section class="card p-6">
                <h2 class="text-base font-semibold text-slate-900">Periode pinjam</h2>
                <div class="mt-5 grid gap-5 sm:grid-cols-2">
                    <x-input label="Tanggal pinjam" name="tanggal_pinjam" type="date" :value="today()->toDateString()" :max="today()->toDateString()" x-model="tanggal" required />
                    <x-input label="Lama pinjam (hari)" name="lama" type="number" :value="config('perpustakaan.lama_pinjam')" min="1" max="60" x-model="lama" required />
                    <x-textarea class="sm:col-span-2" label="Catatan" name="catatan" rows="2" help="Opsional, mis. kondisi buku saat dipinjam." />
                </div>
            </section>
        </div>

        <aside class="space-y-6">
            <section class="card p-6">
                <h2 class="text-base font-semibold text-slate-900">Ringkasan</h2>
                <div class="mt-4 rounded-xl bg-brand-50 p-4">
                    <p class="text-xs font-medium tracking-wider text-brand-700 uppercase">Jatuh tempo</p>
                    <p class="mt-1 text-base font-semibold text-brand-900" x-text="tempo"></p>
                </div>
                <ul class="mt-5 space-y-3 text-sm text-slate-600">
                    <li class="flex gap-3"><x-icon name="info" class="mt-0.5 size-4.5 text-slate-400" /> Maksimal {{ config('perpustakaan.maks_pinjam') }} buku per anggota.</li>
                    <li class="flex gap-3"><x-icon name="repeat" class="mt-0.5 size-4.5 text-slate-400" /> Dapat diperpanjang {{ config('perpustakaan.maks_perpanjang') }}× (+{{ config('perpustakaan.lama_perpanjang') }} hari).</li>
                    <li class="flex gap-3"><x-icon name="banknote" class="mt-0.5 size-4.5 text-slate-400" /> Denda Rp {{ number_format(config('perpustakaan.denda_per_hari'), 0, ',', '.') }} per hari keterlambatan.</li>
                </ul>
            </section>

            <div class="card flex flex-col gap-3 p-6">
                <button type="submit" class="btn btn-primary w-full"><x-icon name="check" class="size-4" /> Simpan peminjaman</button>
                <a href="{{ route('admin.peminjaman.index') }}" class="btn btn-secondary w-full">Batal</a>
            </div>
        </aside>
    </form>
</x-layouts.admin>
