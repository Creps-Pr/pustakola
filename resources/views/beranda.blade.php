<x-layouts.app title="Beranda" :deskripsi="config('perpustakaan.tagline').'. Cari buku, pantau peminjaman, dan jelajahi koleksi.'">

    {{-- ============================== HERO ============================== --}}
    <section class="relative isolate overflow-hidden bg-brand-950">
        <div class="absolute inset-0 -z-10 bg-linear-to-br from-brand-900 via-brand-950 to-slate-950"></div>
        <div class="absolute -top-40 -right-40 -z-10 size-[34rem] rounded-full bg-brand-600/30 blur-3xl"></div>
        <div class="absolute -bottom-48 -left-32 -z-10 size-[30rem] rounded-full bg-violet-600/20 blur-3xl"></div>
        <div class="absolute inset-0 -z-10 opacity-[0.06]" style="background-image: radial-gradient(#fff 1px, transparent 1px); background-size: 24px 24px;"></div>

        <div class="mx-auto grid max-w-7xl items-center gap-14 px-4 py-16 sm:px-6 sm:py-20 lg:grid-cols-[1.1fr_0.9fr] lg:px-8 lg:py-28">
            <div class="animate-fade-up">
                <span class="inline-flex items-center gap-2 rounded-full bg-white/10 px-3.5 py-1.5 text-xs font-medium text-brand-100 ring-1 ring-white/15 backdrop-blur">
                    <x-icon name="sparkles" class="size-3.5 text-brand-300" />
                    {{ number_format($statistik['judul'], 0, ',', '.') }} judul &middot; {{ number_format($statistik['eksemplar'], 0, ',', '.') }} eksemplar siap dipinjam
                </span>

                <h1 class="mt-6 text-4xl font-semibold tracking-tight text-white sm:text-5xl lg:text-[3.5rem] lg:leading-[1.08]">
                    Temukan bacaan berikutnya yang akan Anda cintai.
                </h1>
                <p class="mt-5 max-w-xl text-base leading-relaxed text-slate-300 sm:text-lg">
                    Jelajahi koleksi {{ config('perpustakaan.nama') }}, cek ketersediaan secara langsung, dan kelola masa pinjam dari satu akun.
                </p>

                <form action="{{ route('katalog.index') }}" method="GET" class="mt-9 flex max-w-xl flex-col gap-2 rounded-2xl bg-white p-2 shadow-lift sm:flex-row sm:items-center" role="search">
                    <label for="cari-beranda" class="sr-only">Cari buku</label>
                    <div class="relative flex-1">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400"><x-icon name="search" class="size-5" /></span>
                        <input id="cari-beranda" type="search" name="q" placeholder="Cari judul, penulis, atau ISBN" class="w-full rounded-xl border-0 bg-transparent py-3 pr-3 pl-11 text-[15px] text-slate-900 placeholder:text-slate-400 focus:ring-0 focus:outline-none">
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg">Cari buku</button>
                </form>

                @if ($kategori->isNotEmpty())
                    <div class="mt-6 flex flex-wrap items-center gap-2 text-sm">
                        <span class="text-slate-400">Populer:</span>
                        @foreach ($kategori->sortByDesc('buku_count')->take(4) as $k)
                            <a href="{{ route('katalog.index', ['kategori' => $k->slug]) }}" class="rounded-full bg-white/10 px-3 py-1 text-slate-200 ring-1 ring-white/10 transition hover:bg-white/20">{{ $k->nama }}</a>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Tumpukan sampul --}}
            <div class="relative mx-auto hidden h-[27rem] w-full max-w-md lg:block" aria-hidden="true">
                @php
                    $posisi = [
                        'left-0 top-14 w-44 -rotate-6',
                        'left-1/2 top-0 z-10 w-52 -translate-x-1/2',
                        'right-0 top-20 w-44 rotate-6',
                    ];
                @endphp
                @foreach ($populer->take(3) as $i => $b)
                    <div class="absolute {{ $posisi[$i] }} drop-shadow-2xl">
                        <x-buku-cover :buku="$b" size="lg" />
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============================== STATISTIK ============================== --}}
    <section class="relative z-10 mx-auto -mt-10 max-w-7xl px-4 sm:px-6 lg:px-8">
        <dl class="card grid grid-cols-2 divide-slate-100 lg:grid-cols-4 lg:divide-x">
            @foreach ([
                ['Judul buku', $statistik['judul'], 'library'],
                ['Anggota terdaftar', $statistik['anggota'], 'users'],
                ['Kategori', $statistik['kategori'], 'tags'],
                ['Transaksi peminjaman', $statistik['transaksi'], 'arrow-left-right'],
            ] as [$label, $nilai, $ikon])
                <div class="flex items-center gap-4 p-5 sm:p-6">
                    <span class="grid size-11 shrink-0 place-items-center rounded-xl bg-brand-50 text-brand-600"><x-icon :name="$ikon" class="size-5" /></span>
                    <div>
                        <dd class="text-2xl font-semibold tracking-tight text-slate-900 tabular-nums">{{ number_format($nilai, 0, ',', '.') }}</dd>
                        <dt class="text-sm text-slate-500">{{ $label }}</dt>
                    </div>
                </div>
            @endforeach
        </dl>
    </section>

    {{-- ============================== KATEGORI ============================== --}}
    @if ($kategori->isNotEmpty())
        <section class="mx-auto max-w-7xl px-4 pt-20 sm:px-6 lg:px-8">
            <div class="flex items-end justify-between gap-4">
                <div>
                    <p class="eyebrow">Jelajahi</p>
                    <h2 class="mt-2 text-3xl font-semibold tracking-tight">Telusuri berdasarkan kategori</h2>
                </div>
                <a href="{{ route('katalog.index') }}" class="link hidden items-center gap-1.5 text-sm sm:inline-flex">Semua koleksi <x-icon name="arrow-right" class="size-4" /></a>
            </div>

            <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($kategori as $k)
                    <a href="{{ route('katalog.index', ['kategori' => $k->slug]) }}" class="card group flex items-start gap-4 p-5 transition hover:-translate-y-0.5 hover:shadow-lift">
                        <span class="grid size-11 shrink-0 place-items-center rounded-xl {{ \App\Support\Warna::ikon($k->warna) }}">
                            <x-icon name="book-copy" class="size-5" />
                        </span>
                        <div class="min-w-0">
                            <h3 class="truncate font-semibold text-slate-900 transition group-hover:text-brand-700">{{ $k->nama }}</h3>
                            <p class="mt-0.5 text-sm text-slate-500">{{ $k->buku_count }} judul</p>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    {{-- ============================== POPULER ============================== --}}
    @if ($populer->isNotEmpty())
        <section class="mx-auto max-w-7xl px-4 pt-20 sm:px-6 lg:px-8">
            <div class="flex items-end justify-between gap-4">
                <div>
                    <p class="eyebrow">Paling sering dipinjam</p>
                    <h2 class="mt-2 text-3xl font-semibold tracking-tight">Favorit para anggota</h2>
                </div>
            </div>
            <div class="mt-8 grid grid-cols-2 gap-x-5 gap-y-9 sm:grid-cols-3 lg:grid-cols-6">
                @foreach ($populer as $b)
                    <x-buku-kartu :buku="$b" />
                @endforeach
            </div>
        </section>
    @endif

    {{-- ============================== TERBARU ============================== --}}
    @if ($terbaru->isNotEmpty())
        <section class="mx-auto max-w-7xl px-4 pt-20 sm:px-6 lg:px-8">
            <div class="flex items-end justify-between gap-4">
                <div>
                    <p class="eyebrow">Koleksi baru</p>
                    <h2 class="mt-2 text-3xl font-semibold tracking-tight">Baru ditambahkan</h2>
                </div>
                <a href="{{ route('katalog.index') }}" class="link inline-flex items-center gap-1.5 text-sm">Lihat semua <x-icon name="arrow-right" class="size-4" /></a>
            </div>
            <div class="mt-8 grid grid-cols-2 gap-x-5 gap-y-9 sm:grid-cols-4 lg:grid-cols-8">
                @foreach ($terbaru as $b)
                    <x-buku-kartu :buku="$b" />
                @endforeach
            </div>
        </section>
    @endif

    {{-- ============================== LAYANAN ============================== --}}
    <section id="layanan" class="mx-auto max-w-7xl scroll-mt-20 px-4 py-20 sm:px-6 lg:px-8">
        <div class="max-w-2xl">
            <p class="eyebrow">Layanan</p>
            <h2 class="mt-2 text-3xl font-semibold tracking-tight">Aturan peminjaman yang jelas dan adil</h2>
            <p class="mt-3 text-slate-600">Ringkasan ketentuan yang berlaku bagi seluruh anggota {{ config('perpustakaan.nama') }}.</p>
        </div>

        <div class="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ([
                ['calendar-days', 'Masa pinjam', config('perpustakaan.lama_pinjam').' hari', 'Setiap buku dapat dipinjam selama '.config('perpustakaan.lama_pinjam').' hari sejak tanggal peminjaman.'],
                ['book-copy', 'Batas peminjaman', config('perpustakaan.maks_pinjam').' buku', 'Anda boleh meminjam hingga '.config('perpustakaan.maks_pinjam').' buku pada waktu yang bersamaan.'],
                ['repeat', 'Perpanjangan', config('perpustakaan.maks_perpanjang').'× perpanjang', 'Perpanjang mandiri dari akun, tambahan '.config('perpustakaan.lama_perpanjang').' hari per perpanjangan, selama belum terlambat.'],
                ['banknote', 'Denda keterlambatan', 'Rp '.number_format(config('perpustakaan.denda_per_hari'), 0, ',', '.').' / hari', 'Denda dihitung per hari keterlambatan untuk setiap buku yang dikembalikan lewat jatuh tempo.'],
            ] as [$ikon, $judul, $nilai, $uraian])
                <div class="card p-6">
                    <span class="grid size-11 place-items-center rounded-xl bg-brand-50 text-brand-600"><x-icon :name="$ikon" class="size-5" /></span>
                    <p class="mt-5 text-sm font-medium text-slate-500">{{ $judul }}</p>
                    <p class="mt-1 text-xl font-semibold tracking-tight text-slate-900">{{ $nilai }}</p>
                    <p class="mt-3 text-sm leading-relaxed text-slate-600">{{ $uraian }}</p>
                </div>
            @endforeach
        </div>

        @guest
            <div class="relative mt-12 overflow-hidden rounded-3xl bg-brand-600 px-6 py-12 text-center sm:px-12">
                <div class="absolute -top-24 -right-16 size-72 rounded-full bg-white/10 blur-2xl"></div>
                <div class="absolute -bottom-28 -left-10 size-72 rounded-full bg-violet-400/20 blur-2xl"></div>
                <div class="relative">
                    <h2 class="text-2xl font-semibold tracking-tight text-white sm:text-3xl">Siap mulai membaca?</h2>
                    <p class="mx-auto mt-3 max-w-xl text-brand-100">Daftar sebagai anggota untuk memantau pinjaman, memperpanjang masa pinjam, dan melihat riwayat bacaan Anda.</p>
                    <div class="mt-8 flex flex-col items-center justify-center gap-3 sm:flex-row">
                        <a href="{{ route('register') }}" class="btn btn-lg bg-white text-brand-700 shadow-sm hover:bg-brand-50">Daftar sekarang</a>
                        <a href="{{ route('login') }}" class="btn btn-lg text-white ring-1 ring-white/40 ring-inset hover:bg-white/10">Saya sudah punya akun</a>
                    </div>
                </div>
            </div>
        @endguest
    </section>
</x-layouts.app>
