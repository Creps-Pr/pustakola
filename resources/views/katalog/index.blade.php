@php
    $filterAktif = array_filter([
        'q' => $q,
        'kategori' => $kategoriAktif?->slug,
        'ketersediaan' => $ketersediaan,
        'urut' => $urut !== 'terbaru' ? $urut : null,
    ]);
    $adaFilter = $q !== '' || $kategoriAktif || $ketersediaan === 'tersedia';
@endphp
<x-layouts.app title="Katalog buku" deskripsi="Telusuri seluruh koleksi buku perpustakaan berdasarkan judul, penulis, atau kategori.">

    <section class="border-b border-slate-200 bg-white">
        <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
            <p class="eyebrow">Katalog</p>
            <h1 class="mt-2 text-3xl font-semibold tracking-tight sm:text-4xl">Telusuri koleksi</h1>

            <form action="{{ route('katalog.index') }}" method="GET" class="mt-6 flex max-w-2xl flex-col gap-3 sm:flex-row" role="search">
                @if ($kategoriAktif) <input type="hidden" name="kategori" value="{{ $kategoriAktif->slug }}"> @endif
                @if ($ketersediaan) <input type="hidden" name="ketersediaan" value="{{ $ketersediaan }}"> @endif
                @if ($urut !== 'terbaru') <input type="hidden" name="urut" value="{{ $urut }}"> @endif
                <div class="relative flex-1">
                    <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400"><x-icon name="search" class="size-5" /></span>
                    <label for="q" class="sr-only">Cari buku</label>
                    <input id="q" type="search" name="q" value="{{ $q }}" placeholder="Cari judul, penulis, penerbit, atau ISBN" class="input py-3 pl-11">
                </div>
                <button type="submit" class="btn btn-primary">Cari</button>
            </form>
        </div>
    </section>

    <div class="mx-auto grid max-w-7xl gap-8 px-4 py-10 sm:px-6 lg:grid-cols-[16rem_minmax(0,1fr)] lg:px-8">

        {{-- Filter kategori --}}
        <aside class="lg:sticky lg:top-24 lg:self-start" aria-label="Filter kategori">
            <h2 class="text-xs font-semibold tracking-[0.14em] text-slate-500 uppercase">Kategori</h2>
            <nav class="mt-3 flex gap-2 overflow-x-auto pb-2 lg:flex-col lg:gap-1 lg:overflow-visible lg:pb-0">
                <a href="{{ route('katalog.index', array_diff_key($filterAktif, ['kategori' => 1])) }}" @class([
                    'flex shrink-0 items-center justify-between gap-3 rounded-lg px-3 py-2 text-sm font-medium transition',
                    'bg-brand-50 text-brand-700' => ! $kategoriAktif,
                    'text-slate-600 hover:bg-white hover:text-slate-900' => $kategoriAktif,
                ])>
                    <span>Semua kategori</span>
                </a>
                @foreach ($kategori as $k)
                    <a href="{{ route('katalog.index', array_merge($filterAktif, ['kategori' => $k->slug])) }}" @class([
                        'flex shrink-0 items-center justify-between gap-3 rounded-lg px-3 py-2 text-sm font-medium transition',
                        'bg-brand-50 text-brand-700' => $kategoriAktif?->id === $k->id,
                        'text-slate-600 hover:bg-white hover:text-slate-900' => $kategoriAktif?->id !== $k->id,
                    ])>
                        <span class="flex items-center gap-2.5">
                            <span class="size-2 rounded-full {{ \App\Support\Warna::titik($k->warna) }}"></span>
                            {{ $k->nama }}
                        </span>
                        <span class="text-xs tabular-nums text-slate-400">{{ $k->buku_count }}</span>
                    </a>
                @endforeach
            </nav>
        </aside>

        {{-- Hasil --}}
        <section aria-live="polite">
            <form action="{{ route('katalog.index') }}" method="GET" class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                @if ($q !== '') <input type="hidden" name="q" value="{{ $q }}"> @endif
                @if ($kategoriAktif) <input type="hidden" name="kategori" value="{{ $kategoriAktif->slug }}"> @endif

                <p class="text-sm text-slate-600">
                    <span class="font-semibold text-slate-900">{{ number_format($buku->total(), 0, ',', '.') }}</span> buku ditemukan
                    @if ($kategoriAktif) di <span class="font-medium text-slate-900">{{ $kategoriAktif->nama }}</span> @endif
                    @if ($q !== '') untuk &ldquo;<span class="font-medium text-slate-900">{{ $q }}</span>&rdquo; @endif
                </p>

                <div class="flex flex-wrap items-center gap-3">
                    <label class="flex cursor-pointer items-center gap-2 text-sm text-slate-600">
                        <input type="checkbox" name="ketersediaan" value="tersedia" class="size-4 rounded border-slate-300 text-brand-600 focus:ring-brand-600" @checked($ketersediaan === 'tersedia') onchange="this.form.requestSubmit()">
                        Hanya yang tersedia
                    </label>
                    <label class="sr-only" for="urut">Urutkan</label>
                    <select id="urut" name="urut" class="input w-auto py-2" onchange="this.form.requestSubmit()">
                        <option value="terbaru" @selected($urut === 'terbaru')>Terbaru</option>
                        <option value="judul" @selected($urut === 'judul')>Judul A–Z</option>
                        <option value="penulis" @selected($urut === 'penulis')>Penulis A–Z</option>
                        <option value="tahun" @selected($urut === 'tahun')>Tahun terbit</option>
                    </select>
                </div>
            </form>

            @if ($buku->isEmpty())
                <div class="card mt-6">
                    <x-empty-state icon="search" title="Buku tidak ditemukan" text="Coba ubah kata kunci, pilih kategori lain, atau hapus filter yang aktif.">
                        @if ($adaFilter)
                            <a href="{{ route('katalog.index') }}" class="btn btn-secondary btn-sm">Reset pencarian</a>
                        @endif
                    </x-empty-state>
                </div>
            @else
                <div class="mt-6 grid grid-cols-2 gap-x-5 gap-y-9 sm:grid-cols-3 xl:grid-cols-4">
                    @foreach ($buku as $b)
                        <x-buku-kartu :buku="$b" />
                    @endforeach
                </div>

                <div class="card mt-10 overflow-hidden">
                    {{ $buku->links() }}
                </div>
            @endif
        </section>
    </div>
</x-layouts.app>
