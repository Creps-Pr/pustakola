# Pustakaloka — Sistem Informasi Perpustakaan

Aplikasi web perpustakaan modern berbasis **Laravel 12**, **Tailwind CSS 4**, dan **Alpine.js**, dirancang untuk dijalankan di **Laragon**.

## Fitur

**Situs publik & anggota**
- Beranda dengan statistik, kategori, buku terbaru dan terpopuler
- Katalog dengan pencarian, filter kategori/ketersediaan, dan pengurutan
- Halaman detail buku (sinopsis, stok, lokasi rak, buku terkait)
- Pendaftaran & masuk anggota
- Dasbor anggota: pinjaman aktif, sisa hari, denda berjalan, perpanjangan mandiri, riwayat
- Ubah profil dan kata sandi

**Panel admin / petugas**
- Dasbor dengan grafik tren sirkulasi, grafik per kategori, daftar keterlambatan, buku terpopuler
- Data buku (CRUD, unggah sampul + pratinjau, kontrol stok otomatis)
- Kategori dengan pilihan warna
- Anggota (CRUD, detail dan riwayat per anggota)
- Sirkulasi: peminjaman baru (kotak pencarian anggota/buku), pengembalian, perpanjangan, denda otomatis, pelunasan denda
- Laporan berfilter periode/status, cetak, dan ekspor CSV (kompatibel Excel)
- Manajemen akun staf (khusus administrator)
- Hak akses berbasis peran: Administrator, Petugas, Anggota

**Aturan sirkulasi** (ubah di `.env`): lama pinjam 7 hari, denda Rp 1.000/hari, maksimal 3 buku, perpanjangan 2× (+7 hari). Anggota dengan buku terlambat tidak dapat meminjam lagi.

## Cara pasang di Laragon

Kebutuhan: Laragon Full (PHP 8.2+, MySQL, Composer, Node.js).

1. Ekstrak folder ini ke `C:\laragon\www\perpustakaan`
   (nama folder `perpustakaan` otomatis menjadi `http://perpustakaan.test`).
2. Jalankan **Start All** di Laragon.
3. Buka **Terminal Laragon**, masuk ke folder proyek, lalu jalankan:
   ```
   cd C:\laragon\www\perpustakaan
   setup.bat
   ```
   Skrip ini membuat database, memasang Composer & npm, membangun CSS, migrasi, dan mengisi data contoh.
4. Buka **http://perpustakaan.test**.

### Cara manual (bila tidak memakai setup.bat)
```
copy .env.example .env
mysql -uroot -e "CREATE DATABASE perpustakaan CHARACTER SET utf8mb4"
composer install
php artisan key:generate
npm install
npm run build
php artisan migrate --seed
php artisan storage:link
```
Untuk pengembangan dengan hot-reload, jalankan `npm run dev` (biarkan terbuka) alih-alih `npm run build`.

Bila Laragon tidak memakai MySQL tanpa kata sandi, sesuaikan `DB_USERNAME` / `DB_PASSWORD` di `.env`.

## Akun contoh (kata sandi semua: `password`)

| Peran | Email |
|---|---|
| Administrator | admin@pustakaloka.test |
| Petugas | petugas@pustakaloka.test |
| Anggota | anggota@pustakaloka.test |
| Anggota lain | alya.rahmawati@contoh.test, bagas.setiawan@contoh.test, … |

Ganti semua kata sandi ini sebelum aplikasi dipakai sungguhan.

## Struktur singkat

```
app/Http/Controllers/Admin/   Controller panel admin
app/Models/                   User, Buku, Kategori, Peminjaman
config/perpustakaan.php       Aturan perpustakaan (dibaca dari .env)
resources/views/admin/        Halaman panel
resources/views/components/   Komponen Blade (tombol, kartu, input, ikon, ...)
resources/icons/              Ikon SVG (gaya Lucide)
resources/css/app.css         Tema Tailwind (warna merek, tombol, tabel)
database/seeders/             Data contoh
```

Identitas visual (warna merek) dapat diubah di bagian `@theme` pada `resources/css/app.css`; nama dan kontak perpustakaan di `.env`.

## Tampilan

Tipografi memakai **Inter** (dibundel lewat npm, tanpa CDN), ikon SVG konsisten satu keluarga, warna merek indigo, dan seluruh halaman responsif untuk ponsel, tablet, dan desktop.
