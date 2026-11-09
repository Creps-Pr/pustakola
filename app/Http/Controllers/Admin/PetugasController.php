<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class PetugasController extends Controller
{
    public function index(): View
    {
        $petugas = User::staf()->orderByRaw("CASE role WHEN 'admin' THEN 0 ELSE 1 END")->orderBy('name')->get();

        return view('admin.petugas.index', compact('petugas'));
    }

    public function create(): View
    {
        return view('admin.petugas.form', ['petugas' => new User(['role' => User::ROLE_PETUGAS, 'status' => 'aktif'])]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:160', 'unique:users,email'],
            'telepon' => ['nullable', 'string', 'max:30'],
            'role' => ['required', Rule::in([User::ROLE_ADMIN, User::ROLE_PETUGAS])],
            'status' => ['required', Rule::in(['aktif', 'nonaktif'])],
            'password' => ['required', Password::min(8)],
        ]);

        User::create($data + ['tanggal_bergabung' => today()]);

        return redirect()->route('admin.petugas.index')->with('sukses', 'Akun staf berhasil dibuat.');
    }

    public function edit(User $petugas): View
    {
        return view('admin.petugas.form', compact('petugas'));
    }

    public function update(Request $request, User $petugas): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:160', Rule::unique('users', 'email')->ignore($petugas->id)],
            'telepon' => ['nullable', 'string', 'max:30'],
            'role' => ['required', Rule::in([User::ROLE_ADMIN, User::ROLE_PETUGAS])],
            'status' => ['required', Rule::in(['aktif', 'nonaktif'])],
            'password' => ['nullable', Password::min(8)],
        ]);

        // Jangan sampai admin mengunci dirinya sendiri.
        if ($petugas->id === $request->user()->id && ($data['role'] !== User::ROLE_ADMIN || $data['status'] !== 'aktif')) {
            return back()->withInput()->withErrors([
                'role' => 'Anda tidak dapat menurunkan peran atau menonaktifkan akun Anda sendiri.',
            ]);
        }

        if (empty($data['password'])) {
            unset($data['password']);
        }

        $petugas->update($data);

        return redirect()->route('admin.petugas.index')->with('sukses', 'Akun staf berhasil diperbarui.');
    }

    public function destroy(Request $request, User $petugas): RedirectResponse
    {
        if ($petugas->id === $request->user()->id) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $petugas->delete(); // petugas_id pada peminjaman otomatis menjadi null

        return redirect()->route('admin.petugas.index')->with('sukses', 'Akun staf telah dihapus.');
    }
}
