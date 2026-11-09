<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KatalogController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));
        $slug = (string) $request->query('kategori', '');
        $ketersediaan = (string) $request->query('ketersediaan', '');
        $urut = (string) $request->query('urut', 'terbaru');

        $kategoriAktif = $slug !== '' ? Kategori::where('slug', $slug)->first() : null;

        $buku = Buku::with('kategori')
            ->cari($q)
            ->when($kategoriAktif, fn ($query) => $query->where('kategori_id', $kategoriAktif->id))
            ->when($ketersediaan === 'tersedia', fn ($query) => $query->tersedia())
            ->when($urut === 'judul', fn ($query) => $query->orderBy('judul'))
            ->when($urut === 'penulis', fn ($query) => $query->orderBy('penulis'))
            ->when($urut === 'tahun', fn ($query) => $query->orderByDesc('tahun_terbit'))
            ->when(! in_array($urut, ['judul', 'penulis', 'tahun'], true), fn ($query) => $query->latest('id'))
            ->paginate(12)
            ->withQueryString();

        $kategori = Kategori::withCount('buku')->orderBy('nama')->get();

        return view('katalog.index', compact('buku', 'kategori', 'kategoriAktif', 'q', 'ketersediaan', 'urut'));
    }

    public function show(Buku $buku): View
    {
        $buku->load('kategori');

        $terkait = Buku::with('kategori')
            ->where('id', '!=', $buku->id)
            ->when($buku->kategori_id, fn ($query) => $query->where('kategori_id', $buku->kategori_id))
            ->inRandomOrder()
            ->take(4)
            ->get();

        return view('katalog.show', compact('buku', 'terkait'));
    }
}
