@props(['title' => null, 'deskripsi' => null])
@php
    $pengguna = auth()->user();

    $menu = [
        ['Beranda', route('beranda'), request()->routeIs('beranda')],
        ['Katalog', route('katalog.index'), request()->routeIs('katalog.*')],
    ];

    if ($pengguna) {
        $menu[] = $pengguna->isStaf()
            ? ['Panel Admin', route('admin.dashboard'), false]
            : ['Akun Saya', route('akun.index'), request()->routeIs('akun.*')];
    }
@endphp
<!DOCTYPE html>
<html lang="id" class="h-full scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ? $title.' · ' : '' }}{{ config('perpustakaan.nama') }}</title>
    <meta name="description" content="{{ $deskripsi ?? config('perpustakaan.tagline') }}">
    <meta name="theme-color" content="#4f46e5">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-full flex-col bg-slate-50 font-sans text-slate-900 antialiased">
    <a href="#konten" class="sr-only rounded-lg bg-white px-4 py-2 text-sm font-medium shadow-lift focus:not-sr-only focus:fixed focus:top-3 focus:left-3 focus:z-[120]">Lewati ke konten</a>

    <header x-data="{ menu: false }" class="sticky top-0 z-40 border-b border-slate-200/80 bg-white/85 backdrop-blur-md print:hidden">
        <div class="mx-auto flex h-16 max-w-7xl items-center justify-between gap-6 px-4 sm:px-6 lg:px-8">
            <a href="{{ route('beranda') }}" class="flex items-center gap-2.5" aria-label="{{ config('perpustakaan.nama') }} - beranda">
                <span class="grid size-9 place-items-center rounded-xl bg-brand-600 text-white shadow-sm shadow-brand-600/30">
                    <x-icon name="book-open" class="size-5" />
                </span>
                <span class="text-lg font-semibold tracking-tight text-slate-900">{{ config('perpustakaan.nama') }}</span>
            </a>

            <nav class="hidden items-center gap-1 md:flex" aria-label="Navigasi utama">
                @foreach ($menu as [$nama, $url, $aktif])
                    <a href="{{ $url }}" @class([
                        'rounded-lg px-3.5 py-2 text-sm font-medium transition',
                        'bg-brand-50 text-brand-700' => $aktif,
                        'text-slate-600 hover:bg-slate-100 hover:text-slate-900' => ! $aktif,
                    ])>{{ $nama }}</a>
                @endforeach
            </nav>

            <div class="flex items-center gap-2">
                @auth
                    <div x-data="{ buka: false }" class="relative" x-on:click.outside="buka = false" x-on:keydown.escape.window="buka = false">
                        <button type="button" x-on:click="buka = !buka" class="flex items-center gap-2 rounded-full p-1 pr-2.5 transition hover:bg-slate-100" aria-haspopup="menu" x-bind:aria-expanded="buka">
                            <x-avatar :user="$pengguna" />
                            <span class="hidden text-sm font-medium text-slate-700 sm:block">{{ \Illuminate\Support\Str::words($pengguna->name, 1, '') }}</span>
                            <x-icon name="chevron-down" class="size-4 text-slate-400" />
                        </button>

                        <div
                            x-show="buka"
                            x-cloak
                            x-transition:enter="transition duration-150 ease-out"
                            x-transition:enter-start="scale-95 opacity-0"
                            x-transition:enter-end="scale-100 opacity-100"
                            x-transition:leave="transition duration-100 ease-in"
                            x-transition:leave-start="scale-100 opacity-100"
                            x-transition:leave-end="scale-95 opacity-0"
                            class="absolute right-0 mt-2 w-64 origin-top-right rounded-xl bg-white p-1.5 shadow-lift ring-1 ring-slate-200"
                            role="menu"
                        >
                            <div class="px-3 py-2.5">
                                <p class="truncate text-sm font-semibold text-slate-900">{{ $pengguna->name }}</p>
                                <p class="truncate text-xs text-slate-500">{{ $pengguna->email }}</p>
                                <x-badge color="indigo" class="mt-2">{{ $pengguna->label_peran }}</x-badge>
                            </div>
                            <div class="my-1 h-px bg-slate-100"></div>
                            <a href="{{ $pengguna->homeUrl() }}" role="menuitem" class="flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm text-slate-700 transition hover:bg-slate-50">
                                <x-icon :name="$pengguna->isStaf() ? 'layout-dashboard' : 'book-bookmark'" class="size-4 text-slate-400" />
                                {{ $pengguna->isStaf() ? 'Panel admin' : 'Peminjaman saya' }}
                            </a>
                            <a href="{{ route('profil.edit') }}" role="menuitem" class="flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm text-slate-700 transition hover:bg-slate-50">
                                <x-icon name="user" class="size-4 text-slate-400" /> Profil saya
                            </a>
                            <div class="my-1 h-px bg-slate-100"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" role="menuitem" class="flex w-full items-center gap-2.5 rounded-lg px-3 py-2 text-left text-sm text-rose-600 transition hover:bg-rose-50">
                                    <x-icon name="log-out" class="size-4" /> Keluar
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="btn btn-ghost btn-sm hidden sm:inline-flex">Masuk</a>
                    <a href="{{ route('register') }}" class="btn btn-primary btn-sm">Daftar</a>
                @endauth

                <button type="button" class="btn-icon md:hidden" x-on:click="menu = !menu" aria-label="Buka menu" x-bind:aria-expanded="menu">
                    <x-icon name="menu" class="size-5" />
                </button>
            </div>
        </div>

        <div x-show="menu" x-cloak x-transition class="border-t border-slate-200 bg-white md:hidden">
            <nav class="mx-auto flex max-w-7xl flex-col gap-1 px-4 py-3" aria-label="Navigasi seluler">
                @foreach ($menu as [$nama, $url, $aktif])
                    <a href="{{ $url }}" @class([
                        'rounded-lg px-3 py-2.5 text-sm font-medium',
                        'bg-brand-50 text-brand-700' => $aktif,
                        'text-slate-700 hover:bg-slate-50' => ! $aktif,
                    ])>{{ $nama }}</a>
                @endforeach
                @guest
                    <a href="{{ route('login') }}" class="rounded-lg px-3 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">Masuk</a>
                @endguest
            </nav>
        </div>
    </header>

    <main id="konten" class="flex-1">
        {{ $slot }}
    </main>

    <footer class="border-t border-slate-200 bg-white print:hidden">
        <div class="mx-auto grid max-w-7xl gap-10 px-4 py-12 sm:px-6 md:grid-cols-3 lg:px-8">
            <div>
                <div class="flex items-center gap-2.5">
                    <span class="grid size-9 place-items-center rounded-xl bg-brand-600 text-white">
                        <x-icon name="book-open" class="size-5" />
                    </span>
                    <span class="text-lg font-semibold tracking-tight">{{ config('perpustakaan.nama') }}</span>
                </div>
                <p class="mt-4 max-w-xs text-sm leading-relaxed text-slate-500">
                    {{ config('perpustakaan.tagline') }}. Temukan, pinjam, dan kelola bacaan Anda dengan mudah dalam satu tempat.
                </p>
            </div>

            <div>
                <h2 class="text-sm font-semibold text-slate-900">Kunjungi kami</h2>
                <ul class="mt-4 space-y-3 text-sm text-slate-600">
                    <li class="flex gap-2.5"><x-icon name="map-pin" class="mt-0.5 size-4 text-slate-400" /> {{ config('perpustakaan.alamat') }}</li>
                    <li class="flex gap-2.5"><x-icon name="clock" class="mt-0.5 size-4 text-slate-400" /> {{ config('perpustakaan.jam_buka') }}</li>
                    <li class="flex gap-2.5"><x-icon name="phone" class="mt-0.5 size-4 text-slate-400" /> {{ config('perpustakaan.telepon') }}</li>
                    <li class="flex gap-2.5"><x-icon name="mail" class="mt-0.5 size-4 text-slate-400" /> {{ config('perpustakaan.email') }}</li>
                </ul>
            </div>

            <div>
                <h2 class="text-sm font-semibold text-slate-900">Tautan</h2>
                <ul class="mt-4 space-y-3 text-sm text-slate-600">
                    <li><a href="{{ route('katalog.index') }}" class="transition hover:text-brand-600">Katalog buku</a></li>
                    <li><a href="{{ route('beranda') }}#layanan" class="transition hover:text-brand-600">Layanan &amp; aturan peminjaman</a></li>
                    @guest
                        <li><a href="{{ route('register') }}" class="transition hover:text-brand-600">Daftar jadi anggota</a></li>
                        <li><a href="{{ route('login') }}" class="transition hover:text-brand-600">Masuk</a></li>
                    @endguest
                </ul>
            </div>
        </div>
        <div class="border-t border-slate-100">
            <p class="mx-auto max-w-7xl px-4 py-5 text-xs text-slate-500 sm:px-6 lg:px-8">
                &copy; {{ now()->year }} {{ config('perpustakaan.nama') }}. Hak cipta dilindungi.
            </p>
        </div>
    </footer>

    <x-flash />
    <x-konfirmasi-modal />
</body>
</html>
