<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Buku;
use App\Models\Peminjaman;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PeminjamanController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));
        $status = (string) $request->query('status', '');

        $peminjaman = Peminjaman::with(['anggota', 'buku'])
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('kode', 'like', "%{$q}%")
                        ->orWhereHas('anggota', fn ($a) => $a->where('name', 'like', "%{$q}%")->orWhere('kode_anggota', 'like', "%{$q}%"))
                        ->orWhereHas('buku', fn ($b) => $b->where('judul', 'like', "%{$q}%")->orWhere('kode', 'like', "%{$q}%"));
                });
            })
            ->when($status === 'dipinjam', fn ($query) => $query->aktif())
            ->when($status === 'terlambat', fn ($query) => $query->terlambat())
            ->when($status === 'dikembalikan', fn ($query) => $query->where('status', Peminjaman::DIKEMBALIKAN))
            ->when($status === 'denda', fn ($query) => $query->dendaBelumLunas())
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        $hitung = [
            '' => Peminjaman::count(),
            'dipinjam' => Peminjaman::aktif()->count(),
            'terlambat' => Peminjaman::terlambat()->count(),
            'dikembalikan' => Peminjaman::where('status', Peminjaman::DIKEMBALIKAN)->count(),
            'denda' => Peminjaman::dendaBelumLunas()->count(),
        ];

        return view('admin.peminjaman.index', compact('peminjaman', 'q', 'status', 'hitung'));
    }

    public function create(Request $request): View
    {
        $anggota = User::anggota()->aktif()->orderBy('name')->get(['id', 'name', 'kode_anggota']);
        $buku = Buku::tersedia()->orderBy('judul')->get(['id', 'judul', 'penulis', 'kode', 'tersedia']);

        return view('admin.peminjaman.create', [
            'anggota' => $anggota,
            'buku' => $buku,
            'anggotaDipilih' => $request->query('anggota'),
            'bukuDipilih' => $request->query('buku'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'buku_id' => ['required', 'exists:buku,id'],
            'tanggal_pinjam' => ['required', 'date', 'before_or_equal:today'],
            'lama' => ['required', 'integer', 'min:1', 'max:60'],
            'catatan' => ['nullable', 'string', 'max:500'],
        ]);

        $anggota = User::anggota()->find($data['user_id']);

        if (! $anggota || ! $anggota->isAktif()) {
            return back()->withInput()->withErrors(['user_id' => 'Anggota tidak ditemukan atau tidak aktif.']);
        }

        $maks = (int) config('perpustakaan.maks_pinjam');

        if ($anggota->jumlahPinjamAktif() >= $maks) {
            return back()->withInput()->withErrors([
                'user_id' => "{$anggota->name} sudah meminjam {$maks} buku, yaitu batas maksimal peminjaman.",
            ]);
        }

        if ($anggota->punyaKeterlambatan()) {
            return back()->withInput()->withErrors([
                'user_id' => "{$anggota->name} masih memiliki buku yang terlambat dikembalikan.",
            ]);
        }

        if ($anggota->peminjaman()->aktif()->where('buku_id', $data['buku_id'])->exists()) {
            return back()->withInput()->withErrors([
                'buku_id' => 'Anggota ini sedang meminjam judul yang sama.',
            ]);
        }

        $peminjaman = null;

        try {
            DB::transaction(function () use ($data, $request, &$peminjaman) {
                $buku = Buku::lockForUpdate()->findOrFail($data['buku_id']);

                if ($buku->tersedia < 1) {
                    throw new \RuntimeException('Stok buku "'.$buku->judul.'" sedang habis.');
                }

                $pinjam = Carbon::parse($data['tanggal_pinjam'])->startOfDay();

                $peminjaman = Peminjaman::create([
                    'kode' => Peminjaman::buatKode(),
                    'user_id' => $data['user_id'],
                    'buku_id' => $buku->id,
                    'petugas_id' => $request->user()->id,
                    'tanggal_pinjam' => $pinjam,
                    'jatuh_tempo' => $pinjam->copy()->addDays((int) $data['lama']),
                    'status' => Peminjaman::DIPINJAM,
                    'catatan' => $data['catatan'] ?? null,
                ]);

                $buku->decrement('tersedia');
            });
        } catch (\RuntimeException $e) {
            return back()->withInput()->withErrors(['buku_id' => $e->getMessage()]);
        }

        return redirect()->route('admin.peminjaman.show', $peminjaman)
            ->with('sukses', 'Peminjaman berhasil dicatat. Jatuh tempo: '.$peminjaman->jatuh_tempo->translatedFormat('d F Y').'.');
    }

    public function show(Peminjaman $peminjaman): View
    {
        $peminjaman->load(['anggota', 'buku.kategori', 'petugas']);

        return view('admin.peminjaman.show', compact('peminjaman'));
    }

    public function kembali(Peminjaman $peminjaman): RedirectResponse
    {
        if (! $peminjaman->sedangDipinjam()) {
            return back()->with('error', 'Buku ini sudah dikembalikan sebelumnya.');
        }

        DB::transaction(function () use ($peminjaman) {
            $peminjaman->tanggal_kembali = today();
            $hari = $peminjaman->hariTerlambat();
            $denda = Peminjaman::hitungDenda($hari);

            $peminjaman->forceFill([
                'status' => Peminjaman::DIKEMBALIKAN,
                'denda' => $denda,
                'denda_lunas' => $denda === 0,
            ])->save();

            $buku = Buku::lockForUpdate()->find($peminjaman->buku_id);
            if ($buku) {
                $buku->update(['tersedia' => min($buku->jumlah_stok, $buku->tersedia + 1)]);
            }
        });

        $pesan = 'Pengembalian berhasil dicatat.';

        if ($peminjaman->denda > 0) {
            $pesan .= ' Terlambat '.$peminjaman->hariTerlambat().' hari, denda Rp '.number_format($peminjaman->denda, 0, ',', '.').'.';
        }

        return back()->with('sukses', $pesan);
    }

    public function perpanjang(Peminjaman $peminjaman): RedirectResponse
    {
        if (! $peminjaman->bisaDiperpanjang()) {
            return back()->with('error', 'Peminjaman ini tidak dapat diperpanjang (sudah terlambat atau mencapai batas perpanjangan).');
        }

        $peminjaman->update([
            'jatuh_tempo' => $peminjaman->jatuh_tempo->copy()->addDays((int) config('perpustakaan.lama_perpanjang')),
            'perpanjangan' => $peminjaman->perpanjangan + 1,
        ]);

        return back()->with('sukses', 'Masa pinjam diperpanjang hingga '.$peminjaman->jatuh_tempo->translatedFormat('d F Y').'.');
    }

    public function lunasi(Peminjaman $peminjaman): RedirectResponse
    {
        if ($peminjaman->sedangDipinjam() || $peminjaman->denda < 1 || $peminjaman->denda_lunas) {
            return back()->with('error', 'Tidak ada denda yang perlu dilunasi pada transaksi ini.');
        }

        $peminjaman->update(['denda_lunas' => true]);

        return back()->with('sukses', 'Denda sebesar Rp '.number_format($peminjaman->denda, 0, ',', '.').' ditandai lunas.');
    }
}
