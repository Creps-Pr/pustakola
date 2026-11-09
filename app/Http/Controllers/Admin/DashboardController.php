<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Buku;
use App\Models\Peminjaman;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $statistik = [
            'judul' => Buku::count(),
            'eksemplar' => (int) Buku::sum('jumlah_stok'),
            'anggota' => User::anggota()->aktif()->count(),
            'dipinjam' => Peminjaman::aktif()->count(),
            'terlambat' => Peminjaman::terlambat()->count(),
            'denda' => (int) Peminjaman::dendaBelumLunas()->sum('denda'),
            'hari_ini' => Peminjaman::whereDate('tanggal_pinjam', today())->count(),
            'kembali_hari_ini' => Peminjaman::whereDate('tanggal_kembali', today())->count(),
        ];

        // Tren 6 bulan terakhir: jumlah peminjaman dan pengembalian per bulan.
        $awal = now()->startOfMonth()->subMonths(5);
        $bulan = collect(range(0, 5))->map(fn ($i) => $awal->copy()->addMonths($i));

        $dipinjam = Peminjaman::where('tanggal_pinjam', '>=', $awal->toDateString())
            ->get(['tanggal_pinjam'])
            ->groupBy(fn ($p) => $p->tanggal_pinjam->format('Y-m'))
            ->map->count();

        $dikembalikan = Peminjaman::whereNotNull('tanggal_kembali')
            ->where('tanggal_kembali', '>=', $awal->toDateString())
            ->get(['tanggal_kembali'])
            ->groupBy(fn ($p) => $p->tanggal_kembali->format('Y-m'))
            ->map->count();

        $tren = [
            'label' => $bulan->map(fn ($b) => $b->translatedFormat('M Y'))->all(),
            'dipinjam' => $bulan->map(fn ($b) => (int) ($dipinjam[$b->format('Y-m')] ?? 0))->all(),
            'dikembalikan' => $bulan->map(fn ($b) => (int) ($dikembalikan[$b->format('Y-m')] ?? 0))->all(),
        ];

        // Peminjaman per kategori.
        $perKategori = Peminjaman::query()
            ->join('buku', 'buku.id', '=', 'peminjaman.buku_id')
            ->leftJoin('kategori', 'kategori.id', '=', 'buku.kategori_id')
            ->select(DB::raw("COALESCE(kategori.nama, 'Tanpa kategori') as nama"), DB::raw('COUNT(*) as total'))
            ->groupBy('kategori.id', 'kategori.nama')
            ->orderByDesc('total')
            ->get();

        $kategori = [
            'label' => $perKategori->pluck('nama')->all(),
            'total' => $perKategori->pluck('total')->map(fn ($n) => (int) $n)->all(),
        ];

        $terlambat = Peminjaman::with(['anggota', 'buku'])
            ->terlambat()
            ->orderBy('jatuh_tempo')
            ->take(6)
            ->get();

        $terbaru = Peminjaman::with(['anggota', 'buku'])
            ->latest('id')
            ->take(8)
            ->get();

        $populer = Buku::withCount('peminjaman')
            ->orderByDesc('peminjaman_count')
            ->orderBy('judul')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('statistik', 'tren', 'kategori', 'terlambat', 'terbaru', 'populer'));
    }
}
