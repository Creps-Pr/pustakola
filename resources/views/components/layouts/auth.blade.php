@props(['title' => null, 'judul', 'subjudul' => null])
<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ? $title.' · ' : '' }}{{ config('perpustakaan.nama') }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-white font-sans text-slate-900 antialiased">
<div class="grid min-h-full lg:grid-cols-[minmax(0,1.05fr)_minmax(0,1fr)]">

    {{-- Panel merek --}}
    <aside class="relative hidden overflow-hidden bg-brand-950 lg:block">
        <div class="absolute inset-0 bg-linear-to-br from-brand-900 via-brand-950 to-slate-950"></div>
        <div class="absolute -top-32 -right-32 size-[28rem] rounded-full bg-brand-600/30 blur-3xl"></div>
        <div class="absolute -bottom-40 -left-24 size-[26rem] rounded-full bg-violet-600/20 blur-3xl"></div>
        <div class="absolute inset-0 opacity-[0.07]" style="background-image: radial-gradient(#fff 1px, transparent 1px); background-size: 22px 22px;"></div>

        <div class="relative flex h-full flex-col justify-between p-12 xl:p-16">
            <a href="{{ route('beranda') }}" class="flex items-center gap-3">
                <span class="grid size-10 place-items-center rounded-xl bg-white/10 text-white ring-1 ring-white/20 backdrop-blur">
                    <x-icon name="book-open" class="size-5" />
                </span>
                <span class="text-xl font-semibold tracking-tight text-white">{{ config('perpustakaan.nama') }}</span>
            </a>

            <div class="max-w-md">
                <p class="text-xs font-semibold tracking-[0.18em] text-brand-300 uppercase">{{ config('perpustakaan.tagline') }}</p>
                <h2 class="mt-4 text-4xl leading-[1.15] font-semibold tracking-tight text-white xl:text-[2.75rem]">
                    Satu tempat untuk menemukan bacaan berikutnya.
                </h2>
                <p class="mt-5 text-base leading-relaxed text-slate-300">
                    Cari koleksi, pantau masa pinjam, dan perpanjang buku langsung dari akun Anda, kapan pun dan di mana pun.
                </p>

                <ul class="mt-10 space-y-4 text-sm text-slate-200">
                    <li class="flex items-center gap-3"><span class="grid size-8 place-items-center rounded-lg bg-white/10 ring-1 ring-white/15"><x-icon name="search" class="size-4" /></span> Katalog dengan pencarian dan filter lengkap</li>
                    <li class="flex items-center gap-3"><span class="grid size-8 place-items-center rounded-lg bg-white/10 ring-1 ring-white/15"><x-icon name="repeat" class="size-4" /></span> Perpanjangan masa pinjam secara mandiri</li>
                    <li class="flex items-center gap-3"><span class="grid size-8 place-items-center rounded-lg bg-white/10 ring-1 ring-white/15"><x-icon name="clock" class="size-4" /></span> Pengingat jatuh tempo dan riwayat peminjaman</li>
                </ul>
            </div>

            <p class="text-xs text-slate-400">&copy; {{ now()->year }} {{ config('perpustakaan.nama') }}</p>
        </div>
    </aside>

    {{-- Formulir --}}
    <main class="flex flex-col justify-center px-6 py-10 sm:px-12 lg:px-16 xl:px-24">
        <div class="mx-auto w-full max-w-md">
            <a href="{{ route('beranda') }}" class="mb-10 flex items-center gap-2.5 lg:hidden">
                <span class="grid size-9 place-items-center rounded-xl bg-brand-600 text-white"><x-icon name="book-open" class="size-5" /></span>
                <span class="text-lg font-semibold tracking-tight">{{ config('perpustakaan.nama') }}</span>
            </a>

            <h1 class="text-3xl font-semibold tracking-tight text-slate-900">{{ $judul }}</h1>
            @if ($subjudul)
                <p class="mt-2 text-sm text-slate-500">{{ $subjudul }}</p>
            @endif

            <div class="mt-8">
                {{ $slot }}
            </div>
        </div>
    </main>
</div>

<x-flash />
<x-konfirmasi-modal />
</body>
</html>
