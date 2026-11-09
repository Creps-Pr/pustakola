@props(['title' => null])
@php
    $pengguna = auth()->user();
    $jumlahTerlambat = \App\Models\Peminjaman::terlambat()->count();
@endphp
<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex">
    <title>{{ $title ? $title.' · ' : '' }}Panel {{ config('perpustakaan.nama') }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-slate-100/70 font-sans text-slate-900 antialiased">
<div x-data="{ sidebar: false }" x-on:keydown.escape.window="sidebar = false" class="min-h-full lg:flex">

    {{-- Latar gelap untuk sidebar seluler --}}
    <div x-show="sidebar" x-cloak x-transition.opacity x-on:click="sidebar = false" class="fixed inset-0 z-40 bg-slate-900/60 backdrop-blur-sm lg:hidden print:hidden"></div>

    {{-- Sidebar --}}
    <aside
        x-bind:class="sidebar ? 'translate-x-0' : '-translate-x-full'"
        class="fixed inset-y-0 left-0 z-50 flex w-72 flex-col bg-slate-900 transition-transform duration-200 ease-out lg:sticky lg:top-0 lg:z-auto lg:h-screen lg:translate-x-0 print:hidden"
        aria-label="Menu panel"
    >
        <div class="flex h-16 shrink-0 items-center justify-between px-5">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5">
                <span class="grid size-9 place-items-center rounded-xl bg-brand-500 text-white shadow-lg shadow-brand-500/30">
                    <x-icon name="book-open" class="size-5" />
                </span>
                <span class="text-base font-semibold tracking-tight text-white">{{ config('perpustakaan.nama') }}</span>
            </a>
            <button type="button" class="btn-icon text-slate-400 hover:bg-white/10 hover:text-white lg:hidden" x-on:click="sidebar = false" aria-label="Tutup menu">
                <x-icon name="x" class="size-5" />
            </button>
        </div>

        <nav class="flex-1 space-y-6 overflow-y-auto px-5 py-4" aria-label="Navigasi admin">
            <div class="space-y-1">
                <x-nav-link :href="route('admin.dashboard')" icon="layout-dashboard" :active="request()->routeIs('admin.dashboard')">Dasbor</x-nav-link>
            </div>

            <div>
                <p class="mb-2 px-3 text-[11px] font-semibold tracking-[0.14em] text-slate-500 uppercase">Koleksi</p>
                <div class="space-y-1">
                    <x-nav-link :href="route('admin.buku.index')" icon="library" :active="request()->routeIs('admin.buku.*')">Data Buku</x-nav-link>
                    <x-nav-link :href="route('admin.kategori.index')" icon="tags" :active="request()->routeIs('admin.kategori.*')">Kategori</x-nav-link>
                </div>
            </div>

            <div>
                <p class="mb-2 px-3 text-[11px] font-semibold tracking-[0.14em] text-slate-500 uppercase">Sirkulasi</p>
                <div class="space-y-1">
                    <x-nav-link :href="route('admin.peminjaman.index')" icon="arrow-left-right" :active="request()->routeIs('admin.peminjaman.index') || request()->routeIs('admin.peminjaman.show')" :badge="$jumlahTerlambat ?: null">Peminjaman</x-nav-link>
                    <x-nav-link :href="route('admin.peminjaman.create')" icon="book-plus" :active="request()->routeIs('admin.peminjaman.create')">Peminjaman Baru</x-nav-link>
                    <x-nav-link :href="route('admin.anggota.index')" icon="users" :active="request()->routeIs('admin.anggota.*')">Anggota</x-nav-link>
                </div>
            </div>

            <div>
                <p class="mb-2 px-3 text-[11px] font-semibold tracking-[0.14em] text-slate-500 uppercase">Analitik</p>
                <div class="space-y-1">
                    <x-nav-link :href="route('admin.laporan.index')" icon="chart-column" :active="request()->routeIs('admin.laporan.*')">Laporan</x-nav-link>
                </div>
            </div>

            @if ($pengguna->isAdmin())
                <div>
                    <p class="mb-2 px-3 text-[11px] font-semibold tracking-[0.14em] text-slate-500 uppercase">Administrasi</p>
                    <div class="space-y-1">
                        <x-nav-link :href="route('admin.petugas.index')" icon="shield-check" :active="request()->routeIs('admin.petugas.*')">Petugas</x-nav-link>
                    </div>
                </div>
            @endif
        </nav>

        <div class="shrink-0 border-t border-white/10 p-4">
            <a href="{{ route('beranda') }}" target="_blank" rel="noopener" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm text-slate-300 transition hover:bg-white/5 hover:text-white">
                <x-icon name="external-link" class="size-4 text-slate-400" /> Lihat situs publik
            </a>
        </div>
    </aside>

    <div class="flex min-w-0 flex-1 flex-col">
        {{-- Bilah atas --}}
        <header class="sticky top-0 z-30 flex h-16 shrink-0 items-center justify-between gap-4 border-b border-slate-200/80 bg-white/85 px-4 backdrop-blur-md sm:px-6 lg:px-8 print:hidden">
            <div class="flex items-center gap-3">
                <button type="button" class="btn-icon lg:hidden" x-on:click="sidebar = true" aria-label="Buka menu">
                    <x-icon name="menu" class="size-5" />
                </button>
                <p class="hidden text-sm text-slate-500 sm:block">{{ now()->translatedFormat('l, d F Y') }}</p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('admin.peminjaman.create') }}" class="btn btn-primary btn-sm">
                    <x-icon name="plus" class="size-4" /> <span class="hidden sm:inline">Peminjaman baru</span><span class="sm:hidden">Pinjam</span>
                </a>

                <div x-data="{ buka: false }" class="relative" x-on:click.outside="buka = false" x-on:keydown.escape.window="buka = false">
                    <button type="button" x-on:click="buka = !buka" class="flex items-center gap-2 rounded-full p-1 pr-2.5 transition hover:bg-slate-100" aria-haspopup="menu" x-bind:aria-expanded="buka">
                        <x-avatar :user="$pengguna" />
                        <span class="hidden text-left leading-tight md:block">
                            <span class="block text-sm font-medium text-slate-800">{{ \Illuminate\Support\Str::words($pengguna->name, 2, '') }}</span>
                            <span class="block text-xs text-slate-500">{{ $pengguna->label_peran }}</span>
                        </span>
                        <x-icon name="chevron-down" class="size-4 text-slate-400" />
                    </button>
                    <div
                        x-show="buka"
                        x-cloak
                        x-transition:enter="transition duration-150 ease-out"
                        x-transition:enter-start="scale-95 opacity-0"
                        x-transition:enter-end="scale-100 opacity-100"
                        class="absolute right-0 mt-2 w-60 origin-top-right rounded-xl bg-white p-1.5 shadow-lift ring-1 ring-slate-200"
                        role="menu"
                    >
                        <div class="px-3 py-2.5">
                            <p class="truncate text-sm font-semibold text-slate-900">{{ $pengguna->name }}</p>
                            <p class="truncate text-xs text-slate-500">{{ $pengguna->email }}</p>
                        </div>
                        <div class="my-1 h-px bg-slate-100"></div>
                        <a href="{{ route('profil.edit') }}" role="menuitem" class="flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm text-slate-700 transition hover:bg-slate-50">
                            <x-icon name="user" class="size-4 text-slate-400" /> Profil saya
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" role="menuitem" class="flex w-full items-center gap-2.5 rounded-lg px-3 py-2 text-left text-sm text-rose-600 transition hover:bg-rose-50">
                                <x-icon name="log-out" class="size-4" /> Keluar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <main class="flex-1 px-4 py-6 sm:px-6 lg:px-8 lg:py-8 print:p-0">
            {{ $slot }}
        </main>
    </div>
</div>

<x-flash />
<x-konfirmasi-modal />
</body>
</html>
