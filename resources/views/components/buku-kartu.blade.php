@props(['buku'])
<a href="{{ route('katalog.show', $buku) }}" class="group flex flex-col focus-visible:outline-none">
    <div class="relative">
        <x-buku-cover :buku="$buku" class="transition duration-300 group-hover:-translate-y-1 group-hover:shadow-lift group-focus-visible:ring-2 group-focus-visible:ring-brand-600" />
        <span class="absolute top-2 right-2">
            @if ($buku->tersedia > 0)
                <x-badge color="emerald" dot class="bg-white/95 backdrop-blur">Tersedia</x-badge>
            @else
                <x-badge color="rose" dot class="bg-white/95 backdrop-blur">Dipinjam</x-badge>
            @endif
        </span>
    </div>
    <div class="mt-3 min-w-0">
        @if ($buku->kategori)
            <p class="truncate text-xs font-medium text-brand-600">{{ $buku->kategori->nama }}</p>
        @endif
        <h3 class="mt-0.5 line-clamp-2 text-sm leading-snug font-semibold text-slate-900 transition group-hover:text-brand-700">{{ $buku->judul }}</h3>
        <p class="mt-0.5 truncate text-xs text-slate-500">{{ $buku->penulis }}</p>
    </div>
</a>
