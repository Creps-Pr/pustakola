<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AkunController extends Controller
{
    public function index(Request $request): View
    {
        $pengguna = $request->user();

        $aktif = $pengguna->peminjaman()
            ->with('buku.kategori')
            ->aktif()
            ->orderBy('jatuh_tempo')
            ->get();

        $riwayat = $pengguna->peminjaman()
            ->with('buku')
            ->where('status', Peminjaman::DIKEMBALIKAN)
            ->latest('tanggal_kembali')
            ->latest('id')
            ->paginate(8);

        $ringkasan = [
            'aktif' => $aktif->count(),
            'terlambat' => $aktif->filter->sudahTerlambat()->count(),
            'total' => $pengguna->peminjaman()->count(),
            'denda' => $pengguna->dendaBelumLunas()
                + $aktif->sum(fn (Peminjaman $p) => $p->dendaSaatIni()),
        ];

        return view('akun.index', compact('pengguna', 'aktif', 'riwayat', 'ringkasan'));
    }

    public function perpanjang(Request $request, Peminjaman $peminjaman): RedirectResponse
    {
        abort_unless($peminjaman->user_id === $request->user()->id, 403);

        if (! $peminjaman->bisaDiperpanjang()) {
            return back()->with('error', 'Peminjaman ini tidak dapat diperpanjang. Mungkin sudah terlambat atau mencapai batas perpanjangan.');
        }

        $peminjaman->update([
            'jatuh_tempo' => $peminjaman->jatuh_tempo->copy()->addDays((int) config('perpustakaan.lama_perpanjang')),
            'perpanjangan' => $peminjaman->perpanjangan + 1,
        ]);

        return back()->with('sukses', 'Masa pinjam diperpanjang hingga '.$peminjaman->jatuh_tempo->translatedFormat('d F Y').'.');
    }
}
