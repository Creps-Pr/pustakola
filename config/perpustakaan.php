<?php

/*
|--------------------------------------------------------------------------
| Aturan Perpustakaan
|--------------------------------------------------------------------------
| Semua aturan sirkulasi dapat diubah lewat file .env tanpa mengubah kode.
*/

return [

    // Nama perpustakaan yang tampil di situs, kartu anggota, dan laporan.
    'nama' => env('PERPUSTAKAAN_NAMA', 'Pustakaloka'),
    'tagline' => env('PERPUSTAKAAN_TAGLINE', 'Perpustakaan Digital Modern'),
    'alamat' => env('PERPUSTAKAAN_ALAMAT', 'Jl. Merdeka No. 17, Jakarta Pusat 10110'),
    'telepon' => env('PERPUSTAKAAN_TELEPON', '(021) 5550 1234'),
    'email' => env('PERPUSTAKAAN_EMAIL', 'layanan@pustakaloka.test'),
    'jam_buka' => env('PERPUSTAKAAN_JAM_BUKA', 'Senin – Sabtu, 08.00 – 20.00 WIB'),

    // Lama peminjaman standar (hari).
    'lama_pinjam' => (int) env('PERPUSTAKAAN_LAMA_PINJAM', 7),

    // Denda keterlambatan per hari (Rupiah).
    'denda_per_hari' => (int) env('PERPUSTAKAAN_DENDA_PER_HARI', 1000),

    // Jumlah buku maksimal yang boleh dipinjam bersamaan oleh satu anggota.
    'maks_pinjam' => (int) env('PERPUSTAKAAN_MAKS_PINJAM', 3),

    // Berapa kali satu peminjaman boleh diperpanjang, dan berapa hari tiap perpanjangan.
    'maks_perpanjang' => (int) env('PERPUSTAKAAN_MAKS_PERPANJANG', 2),
    'lama_perpanjang' => (int) env('PERPUSTAKAAN_LAMA_PERPANJANG', 7),

];
