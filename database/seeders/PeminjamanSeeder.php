<?php

namespace Database\Seeders;

use App\Models\Buku;
use App\Models\Peminjaman;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class PeminjamanSeeder extends Seeder
{
    public function run(): void
    {
        mt_srand(2026); // hasil acak yang sama setiap kali seeding

        $anggota = User::anggota()->pluck('id')->all();
        $buku = Buku::pluck('id')->all();
        $petugas = User::staf()->pluck('id')->all();
        $lama = (int) config('perpustakaan.lama_pinjam');
        $denda = (int) config('perpustakaan.denda_per_hari');
        $hariIni = Carbon::today();

        $aktifPerAnggota = [];
        $aktifPerBuku = [];

        for ($i = 1; $i <= 90; $i++) {
            // Sebagian besar riwayat tersebar 6 bulan terakhir; sisanya baru-baru ini.
            $hariLalu = $i <= 72 ? mt_rand(12, 175) : mt_rand(0, 11);
            $pinjam = $hariIni->copy()->subDays($hariLalu);
            $tempo = $pinjam->copy()->addDays($lama);

            $userId = $anggota[array_rand($anggota)];
            $bukuId = $buku[array_rand($buku)];

            // Peminjaman yang sudah lewat 20 hari pasti sudah dikembalikan.
            $kembali = null;
            if ($hariLalu > 20 || mt_rand(1, 100) <= 35) {
                $kembali = $tempo->copy()->addDays(mt_rand(-5, 6));
                if ($kembali->lt($pinjam)) {
                    $kembali = $pinjam->copy()->addDay();
                }
                if ($kembali->gt($hariIni)) {
                    $kembali = $hariIni->copy();
                }
            }

            // Jaga batas pinjam anggota dan stok buku untuk peminjaman yang masih aktif.
            if ($kembali === null) {
                if (($aktifPerAnggota[$userId] ?? 0) >= 2 || ($aktifPerBuku[$bukuId] ?? 0) >= 2) {
                    $kembali = $hariIni->copy();
                    if ($kembali->lt($pinjam)) {
                        $kembali = $pinjam->copy();
                    }
                } else {
                    $aktifPerAnggota[$userId] = ($aktifPerAnggota[$userId] ?? 0) + 1;
                    $aktifPerBuku[$bukuId] = ($aktifPerBuku[$bukuId] ?? 0) + 1;
                }
            }

            $hariTerlambat = $kembali && $kembali->gt($tempo) ? (int) $tempo->diffInDays($kembali) : 0;
            $totalDenda = $hariTerlambat * $denda;

            Peminjaman::create([
                'kode' => 'TRX-'.$pinjam->format('ymd').'-'.str_pad((string) $i, 4, '0', STR_PAD_LEFT),
                'user_id' => $userId,
                'buku_id' => $bukuId,
                'petugas_id' => $petugas[array_rand($petugas)],
                'tanggal_pinjam' => $pinjam,
                'jatuh_tempo' => $tempo,
                'tanggal_kembali' => $kembali,
                'status' => $kembali ? Peminjaman::DIKEMBALIKAN : Peminjaman::DIPINJAM,
                'denda' => $kembali ? $totalDenda : 0,
                'denda_lunas' => $kembali ? ($totalDenda === 0 || mt_rand(1, 100) <= 75) : false,
                'perpanjangan' => 0,
                'created_at' => $pinjam,
                'updated_at' => $kembali ?? $pinjam,
            ]);
        }

        // Sinkronkan stok tersedia dengan peminjaman yang masih aktif.
        foreach (Buku::all() as $b) {
            $dipinjam = Peminjaman::aktif()->where('buku_id', $b->id)->count();
            $b->update(['tersedia' => max(0, $b->jumlah_stok - $dipinjam)]);
        }
    }
}
