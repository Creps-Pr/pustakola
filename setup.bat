@echo off
chcp 65001 >nul
title Pemasangan Pustakaloka
echo ============================================================
echo   Pemasangan Pustakaloka (Laravel + Tailwind CSS) - Laragon
echo ============================================================
echo.
echo Pastikan Laragon sudah berjalan (Apache/Nginx + MySQL aktif)
echo dan skrip ini dijalankan dari Terminal Laragon.
echo.
pause

if not exist .env copy .env.example .env >nul

echo [1/6] Membuat database "perpustakaan" ...
mysql -uroot -e "CREATE DATABASE IF NOT EXISTS perpustakaan CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
if errorlevel 1 (
    echo     ! Gagal membuat database otomatis. Buat database bernama "perpustakaan"
    echo       lewat HeidiSQL/phpMyAdmin, lalu jalankan ulang skrip ini.
    pause
    exit /b 1
)

echo [2/6] Memasang paket PHP (Composer) ...
call composer install --no-interaction
if errorlevel 1 goto gagal

echo [3/6] Membuat kunci aplikasi ...
findstr /b /c:"APP_KEY=base64" .env >nul || call php artisan key:generate --force
if errorlevel 1 goto gagal

echo [4/6] Memasang paket frontend (npm) ...
call npm install
if errorlevel 1 goto gagal

echo [5/6] Membangun aset Tailwind CSS ...
call npm run build
if errorlevel 1 goto gagal

echo [6/6] Migrasi dan data contoh ...
call php artisan migrate:fresh --seed --force
if errorlevel 1 goto gagal
call php artisan storage:link

echo.
echo ============================================================
echo   SELESAI. Buka:  http://perpustakaan.test
echo.
echo   Admin    : admin@pustakaloka.test    / password
echo   Petugas  : petugas@pustakaloka.test  / password
echo   Anggota  : lihat daftar di README.md / password
echo ============================================================
pause
exit /b 0

:gagal
echo.
echo ! Terjadi kesalahan pada langkah di atas. Baca pesan galatnya, perbaiki, lalu jalankan ulang setup.bat.
pause
exit /b 1
