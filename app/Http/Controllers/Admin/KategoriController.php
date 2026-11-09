<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use App\Support\Warna;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class KategoriController extends Controller
{
    public function index(): View
    {
        $kategori = Kategori::withCount('buku')->orderBy('nama')->get();

        return view('admin.kategori.index', compact('kategori'));
    }

    public function create(): View
    {
        return view('admin.kategori.form', ['kategori' => new Kategori(['warna' => 'indigo'])]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validasi($request);
        $data['slug'] = $this->buatSlug($data['nama']);

        Kategori::create($data);

        return redirect()->route('admin.kategori.index')->with('sukses', 'Kategori berhasil ditambahkan.');
    }

    public function edit(Kategori $kategori): View
    {
        return view('admin.kategori.form', compact('kategori'));
    }

    public function update(Request $request, Kategori $kategori): RedirectResponse
    {
        $data = $this->validasi($request, $kategori);
        $data['slug'] = $this->buatSlug($data['nama'], $kategori->id);

        $kategori->update($data);

        return redirect()->route('admin.kategori.index')->with('sukses', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Kategori $kategori): RedirectResponse
    {
        if ($kategori->buku()->exists()) {
            return back()->with('error', 'Kategori masih memiliki buku. Pindahkan atau hapus bukunya terlebih dahulu.');
        }

        $kategori->delete();

        return redirect()->route('admin.kategori.index')->with('sukses', 'Kategori telah dihapus.');
    }

    private function validasi(Request $request, ?Kategori $kategori = null): array
    {
        return $request->validate([
            'nama' => ['required', 'string', 'max:120', Rule::unique('kategori', 'nama')->ignore($kategori?->id)],
            'deskripsi' => ['nullable', 'string', 'max:500'],
            'warna' => ['required', Rule::in(array_keys(Warna::DAFTAR))],
        ]);
    }

    private function buatSlug(string $nama, ?int $abaikanId = null): string
    {
        $dasar = Str::slug($nama) ?: 'kategori';
        $slug = $dasar;
        $n = 2;

        while (Kategori::where('slug', $slug)->when($abaikanId, fn ($q) => $q->where('id', '!=', $abaikanId))->exists()) {
            $slug = $dasar.'-'.$n++;
        }

        return $slug;
    }
}
