@php
    $rincian = [
        'Kode buku' => $buku->kode,
        'ISBN' => $buku->isbn,
        'Penerbit' => $buku->penerbit,
        'Tahun terbit' => $buku->tahun_terbit,
        'Jumlah halaman' => $buku->halaman ? $buku->halaman.' halaman' : null,
        'Bahasa' => $buku->bahasa,
        'Lokasi rak' => $buku->lokasi_rak,
    ];
@endphp
<x-layouts.app :title="$buku->judul" :deskripsi="\Illuminate\Support\Str::limit($buku->sinopsis ?: $buku->judul.' oleh '.$buku->penulis, 150)">
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8 lg:py-12">

        <nav class="flex items-center gap-1.5 text-sm text-slate-500" aria-label="Breadcrumb">
            <a href="{{ route('katalog.index') }}" class="transition hover:text-brand-600">Katalog</a>
            <x-icon name="chevron-right" class="size-4 text-slate-300" />
            @if ($buku->kategori)
                <a href="{{ route('katalog.index', ['kategori' => $buku->kategori->slug]) }}" class="transition hover:text-brand-600">{{ $buku->kategori->nama }}</a>
                <x-icon name="chevron-right" class="size-4 text-slate-300" />
            @endif
            <span class="truncate text-slate-900">{{ $buku->judul }}</span>
        </nav>

        <div class="mt-8 grid gap-10 lg:grid-cols-[18rem_minmax(0,1fr)] xl:grid-cols-[20rem_minmax(0,1fr)]">
            <div>
                <div class="mx-auto max-w-[16rem] lg:max-w-none lg:sticky lg:top-24">
                    <x-buku-cover :buku="$buku" size="lg" class="shadow-xl" />
                </div>
            </div>

            <div class="min-w-0">
                @if ($buku->kategori)
                    <x-badge :color="$buku->kategori->warna">{{ $buku->kategori->nama }}</x-badge>
                @endif
                <h1 class="mt-3 text-3xl font-semibold tracking-tight text-slate-900 sm:text-4xl">{{ $buku->judul }}</h1>
                <p class="mt-2 text-lg text-slate-600">oleh <span class="font-medium text-slate-900">{{ $buku->penulis }}</span></p>

                {{-- Ketersediaan --}}
                <div class="card mt-8 flex flex-col gap-5 p-5 sm:flex-row sm:items-center sm:justify-between sm:p-6">
                    <div class="flex items-center gap-4">
                        <span class="grid size-12 shrink-0 place-items-center rounded-xl {{ $buku->tersedia > 0 ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }}">
                            <x-icon :name="$buku->tersedia > 0 ? 'badge-check' : 'clock'" class="size-6" />
                        </span>
                        <div>
                            <p class="text-base font-semibold text-slate-900">
                                {{ $buku->tersedia > 0 ? 'Tersedia untuk dipinjam' : 'Sedang dipinjam semua' }}
                            </p>
                            <p class="text-sm text-slate-500">
                                {{ $buku->tersedia }} dari {{ $buku->jumlah_stok }} eksemplar tersedia
                                @if ($buku->lokasi_rak) &middot; Rak {{ $buku->lokasi_rak }} @endif
                            </p>
                        </div>
                    </div>

                    @auth
                        @if (auth()->user()->isStaf())
                            <div class="flex flex-wrap gap-2">
                                <a href="{{ route('admin.buku.edit', $buku) }}" class="btn btn-secondary btn-sm"><x-icon name="pencil" class="size-4" /> Ubah</a>
                                @if ($buku->tersedia > 0)
                                    <a href="{{ route('admin.peminjaman.create', ['buku' => $buku->id]) }}" class="btn btn-primary btn-sm"><x-icon name="book-plus" class="size-4" /> Catat peminjaman</a>
                                @endif
                            </div>
                        @endif
                    @endauth
                </div>

                @guest
                    <p class="mt-4 flex items-start gap-2 text-sm text-slate-500">
                        <x-icon name="info" class="mt-0.5 size-4 shrink-0" />
                        <span>Untuk meminjam, <a href="{{ route('register') }}" class="link">daftar sebagai anggota</a> lalu tunjukkan nomor anggota Anda di meja layanan.</span>
                    </p>
                @endguest
                @auth
                    @if (auth()->user()->isAnggota())
                        <p class="mt-4 flex items-start gap-2 text-sm text-slate-500">
                            <x-icon name="info" class="mt-0.5 size-4 shrink-0" />
                            <span>Datang ke meja layanan dengan nomor anggota <span class="font-mono font-medium text-slate-700">{{ auth()->user()->kode_anggota }}</span> untuk meminjam buku ini.</span>
                        </p>
                    @endif
                @endauth

                @if ($buku->sinopsis)
                    <div class="mt-10">
                        <h2 class="text-lg font-semibold">Sinopsis</h2>
                        <p class="mt-3 max-w-3xl leading-relaxed text-slate-600">{{ $buku->sinopsis }}</p>
                    </div>
                @endif

                <div class="mt-10">
                    <h2 class="text-lg font-semibold">Informasi buku</h2>
                    <dl class="card mt-4 divide-y divide-slate-100">
                        @foreach (array_filter($rincian, fn ($v) => filled($v)) as $label => $nilai)
                            <div class="grid grid-cols-3 gap-4 px-5 py-3.5 text-sm">
                                <dt class="text-slate-500">{{ $label }}</dt>
                                <dd class="col-span-2 font-medium text-slate-900">{{ $nilai }}</dd>
                            </div>
                        @endforeach
                    </dl>
                </div>
            </div>
        </div>

        @if ($terkait->isNotEmpty())
            <section class="mt-16 border-t border-slate-200 pt-12">
                <h2 class="text-2xl font-semibold tracking-tight">Buku terkait</h2>
                <div class="mt-8 grid grid-cols-2 gap-x-5 gap-y-9 sm:grid-cols-4">
                    @foreach ($terkait as $b)
                        <x-buku-kartu :buku="$b" />
                    @endforeach
                </div>
            </section>
        @endif
    </div>
</x-layouts.app>
