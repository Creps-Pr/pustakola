<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AnggotaController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));
        $status = (string) $request->query('status', '');

        $anggota = User::anggota()
            ->cari($q)
            ->when(in_array($status, ['aktif', 'nonaktif'], true), fn ($query) => $query->where('status', $status))
            ->withCount(['peminjaman as pinjam_aktif' => fn ($query) => $query->where('status', 'dipinjam')])
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        return view('admin.anggota.index', compact('anggota', 'q', 'status'));
    }

    public function create(): View
    {
        return view('admin.anggota.form', [
            'anggota' => new User(['status' => 'aktif', 'tanggal_bergabung' => today()]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:160', 'unique:users,email'],
            'telepon' => ['nullable', 'string', 'max:30'],
            'alamat' => ['nullable', 'string', 'max:500'],
            'status' => ['required', Rule::in(['aktif', 'nonaktif'])],
            'password' => ['required', Password::min(8)],
        ]);

        $anggota = User::create($data + [
            'role' => User::ROLE_ANGGOTA,
            'tanggal_bergabung' => today(),
        ]);

        return redirect()->route('admin.anggota.show', $anggota)
            ->with('sukses', "Anggota {$anggota->name} berhasil didaftarkan.");
    }

    public function show(User $anggota): View
    {
        $aktif = $anggota->peminjaman()->with('buku')->aktif()->orderBy('jatuh_tempo')->get();

        $riwayat = $anggota->peminjaman()
            ->with('buku')
            ->where('status', 'dikembalikan')
            ->latest('tanggal_kembali')
            ->latest('id')
            ->paginate(10);

        $ringkasan = [
            'total' => $anggota->peminjaman()->count(),
            'aktif' => $aktif->count(),
            'denda' => $anggota->dendaBelumLunas() + $aktif->sum(fn ($p) => $p->dendaSaatIni()),
        ];

        return view('admin.anggota.show', compact('anggota', 'aktif', 'riwayat', 'ringkasan'));
    }

    public function edit(User $anggota): View
    {
        return view('admin.anggota.form', compact('anggota'));
    }

    public function update(Request $request, User $anggota): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:160', Rule::unique('users', 'email')->ignore($anggota->id)],
            'telepon' => ['nullable', 'string', 'max:30'],
            'alamat' => ['nullable', 'string', 'max:500'],
            'status' => ['required', Rule::in(['aktif', 'nonaktif'])],
            'password' => ['nullable', Password::min(8)],
        ]);

        if (empty($data['password'])) {
            unset($data['password']);
        }

        $anggota->update($data);

        return redirect()->route('admin.anggota.show', $anggota)
            ->with('sukses', 'Data anggota berhasil diperbarui.');
    }

    public function destroy(User $anggota): RedirectResponse
    {
        if ($anggota->peminjaman()->exists()) {
            return back()->with('error', 'Anggota ini memiliki riwayat peminjaman. Nonaktifkan akunnya sebagai gantinya.');
        }

        $nama = $anggota->name;
        $anggota->delete();

        return redirect()->route('admin.anggota.index')->with('sukses', "Anggota {$nama} telah dihapus.");
    }
}
