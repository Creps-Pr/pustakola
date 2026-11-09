<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Tampilan paginasi kustom (Tailwind).
        Paginator::defaultView('pagination.custom');

        // Parameter {anggota} hanya cocok dengan pengguna ber-peran anggota.
        Route::bind('anggota', fn ($nilai) => User::anggota()->findOrFail($nilai));

        // Parameter {petugas} hanya cocok dengan admin/petugas.
        Route::bind('petugas', fn ($nilai) => User::staf()->findOrFail($nilai));
    }
}
