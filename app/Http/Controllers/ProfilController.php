<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ProfilController extends Controller
{
    public function edit(Request $request): View
    {
        return view('profil.edit', ['pengguna' => $request->user()]);
    }

    public function update(Request $request): RedirectResponse
    {
        $pengguna = $request->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:160', Rule::unique('users', 'email')->ignore($pengguna->id)],
            'telepon' => ['nullable', 'string', 'max:30'],
            'alamat' => ['nullable', 'string', 'max:500'],
            'password_lama' => ['nullable', 'required_with:password', 'current_password'],
            'password' => ['nullable', 'confirmed', Password::min(8)],
        ]);

        $pengguna->fill([
            'name' => $data['name'],
            'email' => $data['email'],
            'telepon' => $data['telepon'] ?? null,
            'alamat' => $data['alamat'] ?? null,
        ]);

        if (! empty($data['password'])) {
            $pengguna->password = $data['password'];
        }

        $pengguna->save();

        return back()->with('sukses', 'Profil berhasil diperbarui.');
    }
}
