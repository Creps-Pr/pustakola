<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function formMasuk(): View
    {
        return view('auth.login');
    }

    public function masuk(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $pengguna = User::where('email', $data['email'])->first();

        if ($pengguna && $pengguna->status !== 'aktif') {
            return back()->withInput($request->only('email'))
                ->withErrors(['email' => 'Akun Anda sedang dinonaktifkan. Silakan hubungi petugas perpustakaan.']);
        }

        if (! Auth::attempt($data, $request->boolean('ingat'))) {
            return back()->withInput($request->only('email'))
                ->withErrors(['email' => 'Email atau kata sandi tidak sesuai.']);
        }

        $request->session()->regenerate();

        return redirect()->intended(Auth::user()->homeUrl())
            ->with('sukses', 'Selamat datang kembali, '.Auth::user()->name.'!');
    }

    public function formDaftar(): View
    {
        return view('auth.register');
    }

    public function daftar(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:160', 'unique:users,email'],
            'telepon' => ['nullable', 'string', 'max:30'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $pengguna = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'telepon' => $data['telepon'] ?? null,
            'password' => $data['password'],
            'role' => User::ROLE_ANGGOTA,
            'status' => 'aktif',
            'tanggal_bergabung' => today(),
        ]);

        Auth::login($pengguna);
        $request->session()->regenerate();

        return redirect()->route('akun.index')
            ->with('sukses', 'Pendaftaran berhasil. Selamat bergabung di '.config('perpustakaan.nama').'!');
    }

    public function keluar(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('beranda')->with('sukses', 'Anda telah keluar dari akun.');
    }
}
