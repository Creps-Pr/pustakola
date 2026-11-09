<?php

use App\Http\Middleware\EnsureRole;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => EnsureRole::class,
        ]);

        // Tamu yang membuka halaman terproteksi diarahkan ke halaman masuk.
        $middleware->redirectGuestsTo(fn (Request $request) => route('login'));

        // Pengguna yang sudah masuk diarahkan ke beranda sesuai perannya.
        $middleware->redirectUsersTo(fn (Request $request) => $request->user()?->homeUrl() ?? url('/'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
