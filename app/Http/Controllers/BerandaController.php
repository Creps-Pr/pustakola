<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Kategori;
use App\Models\Peminjaman;
use App\Models\User;
use Illuminate\View\View;

class BerandaController extends Controller
{
    public function index(): View
    {
        $statistik = [
            'judul' => Buku::count(),
            'eksemplar' => (int) Buku::sum('jumlah_stok'),
            'anggota' => User::anggota()->count(),
            'kategori' => Kategori::count(),
            'transaksi' => Peminjaman::count(),
        ];

        $kategori = Kategori::withCount('buku')->orderBy('nama')->get();

        $terbaru = Buku::with('kategori')->latest('id')->take(8)->get();

        $populer = Buku::with('kategori')
            ->withCount('peminjaman')
            ->orderByDesc('peminjaman_count')
            ->orderBy('judul')
            ->take(6)
            ->get();

        return view('beranda', compact('statistik', 'kategori', 'terbaru', 'populer'));
    }
}
