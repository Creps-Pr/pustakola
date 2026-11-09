<?php

use App\Http\Controllers\Admin\AnggotaController;
use App\Http\Controllers\Admin\BukuController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\KategoriController;
use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\Admin\PeminjamanController;
use App\Http\Controllers\Admin\PetugasController;
use App\Http\Controllers\AkunController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BerandaController;
use App\Http\Controllers\KatalogController;
use App\Http\Controllers\ProfilController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Halaman publik
|--------------------------------------------------------------------------
*/
Route::get('/', [BerandaController::class, 'index'])->name('beranda');
Route::get('/katalog', [KatalogController::class, 'index'])->name('katalog.index');
Route::get('/katalog/{buku}', [KatalogController::class, 'show'])->name('katalog.show');

/*
|--------------------------------------------------------------------------
| Autentikasi
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/masuk', [AuthController::class, 'formMasuk'])->name('login');
    Route::post('/masuk', [AuthController::class, 'masuk'])->name('login.proses');
    Route::get('/daftar', [AuthController::class, 'formDaftar'])->name('register');
    Route::post('/daftar', [AuthController::class, 'daftar'])->name('register.proses');
});

Route::post('/keluar', [AuthController::class, 'keluar'])->middleware('auth')->name('logout');

/*
|--------------------------------------------------------------------------
| Area terautentikasi
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    Route::get('/profil', [ProfilController::class, 'edit'])->name('profil.edit');
    Route::put('/profil', [ProfilController::class, 'update'])->name('profil.update');

    // Area anggota
    Route::middleware('role:anggota')->prefix('akun')->name('akun.')->group(function () {
        Route::get('/', [AkunController::class, 'index'])->name('index');
        Route::post('/peminjaman/{peminjaman}/perpanjang', [AkunController::class, 'perpanjang'])->name('perpanjang');
    });

    // Area admin & petugas
    Route::middleware('role:admin,petugas')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        Route::resource('buku', BukuController::class)->except('show')->parameters(['buku' => 'buku']);
Route::resource('kategori', KategoriController::class)->except('show')->parameters(['kategori' => 'kategori']);
Route::resource('anggota', AnggotaController::class)->parameters(['anggota' => 'anggota']);

        Route::get('/peminjaman', [PeminjamanController::class, 'index'])->name('peminjaman.index');
        Route::get('/peminjaman/baru', [PeminjamanController::class, 'create'])->name('peminjaman.create');
        Route::post('/peminjaman', [PeminjamanController::class, 'store'])->name('peminjaman.store');
        Route::get('/peminjaman/{peminjaman}', [PeminjamanController::class, 'show'])->name('peminjaman.show');
        Route::post('/peminjaman/{peminjaman}/kembali', [PeminjamanController::class, 'kembali'])->name('peminjaman.kembali');
        Route::post('/peminjaman/{peminjaman}/perpanjang', [PeminjamanController::class, 'perpanjang'])->name('peminjaman.perpanjang');
        Route::post('/peminjaman/{peminjaman}/lunasi', [PeminjamanController::class, 'lunasi'])->name('peminjaman.lunasi');

        Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
        Route::get('/laporan/ekspor', [LaporanController::class, 'ekspor'])->name('laporan.ekspor');

        // Manajemen petugas hanya untuk administrator
        Route::middleware('role:admin')->resource('petugas', PetugasController::class)->except('show')->parameters(['petugas' => 'petugas']);
    });
});
