<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Buku;
use App\Models\Kategori;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BukuController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));
        $kategoriId = $request->query('kategori');
        $stok = (string) $request->query('stok', '');

        $buku = Buku::with('kategori')
            ->cari($q)
            ->when($kategoriId, fn ($query) => $query->where('kategori_id', $kategoriId))
            ->when($stok === 'habis', fn ($query) => $query->where('tersedia', 0))
            ->when($stok === 'tersedia', fn ($query) => $query->where('tersedia', '>', 0))
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        $kategori = Kategori::orderBy('nama')->get();

        return view('admin.buku.index', compact('buku', 'kategori', 'q', 'kategoriId', 'stok'));
    }

    public function create(): View
    {
        return view('admin.buku.form', [
            'buku' => new Buku(['jumlah_stok' => 1, 'bahasa' => 'Indonesia', 'kode' => Buku::kodeBerikutnya()]),
            'kategori' => Kategori::orderBy('nama')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validasi($request);

        if ($request->hasFile('sampul')) {
            $data['sampul'] = $request->file('sampul')->store('sampul', 'public');
        }

        $data['kode'] = $data['kode'] ?: Buku::kodeBerikutnya();
        $data['tersedia'] = $data['jumlah_stok'];

        $buku = Buku::create($data);

        return redirect()->route('admin.buku.index')
            ->with('sukses', "Buku \"{$buku->judul}\" berhasil ditambahkan.");
    }

    public function edit(Buku $buku): View
    {
        return view('admin.buku.form', [
            'buku' => $buku,
            'kategori' => Kategori::orderBy('nama')->get(),
        ]);
    }

    public function update(Request $request, Buku $buku): RedirectResponse
    {
        $data = $this->validasi($request, $buku);

        $dipinjam = $buku->dipinjam;

        if ($data['jumlah_stok'] < $dipinjam) {
            return back()->withInput()->withErrors([
                'jumlah_stok' => "Jumlah stok tidak boleh kurang dari {$dipinjam} eksemplar yang sedang dipinjam.",
            ]);
        }

        if ($request->boolean('hapus_sampul') && $buku->sampul) {
            Storage::disk('public')->delete($buku->sampul);
            $data['sampul'] = null;
        }

        if ($request->hasFile('sampul')) {
            if ($buku->sampul) {
                Storage::disk('public')->delete($buku->sampul);
            }
            $data['sampul'] = $request->file('sampul')->store('sampul', 'public');
        }

        $data['kode'] = $data['kode'] ?: $buku->kode;
        $data['tersedia'] = $data['jumlah_stok'] - $dipinjam;

        $buku->update($data);

        return redirect()->route('admin.buku.index')
            ->with('sukses', "Data buku \"{$buku->judul}\" berhasil diperbarui.");
    }

    public function destroy(Buku $buku): RedirectResponse
    {
        if ($buku->peminjaman()->exists()) {
            return back()->with('error', 'Buku ini memiliki riwayat peminjaman sehingga tidak dapat dihapus.');
        }

        if ($buku->sampul) {
            Storage::disk('public')->delete($buku->sampul);
        }

        $judul = $buku->judul;
        $buku->delete();

        return redirect()->route('admin.buku.index')->with('sukses', "Buku \"{$judul}\" telah dihapus.");
    }

    private function validasi(Request $request, ?Buku $buku = null): array
    {
        return $request->validate([
            'kode' => ['nullable', 'string', 'max:30', Rule::unique('buku', 'kode')->ignore($buku?->id)],
            'isbn' => ['nullable', 'string', 'max:30'],
            'judul' => ['required', 'string', 'max:255'],
            'penulis' => ['required', 'string', 'max:255'],
            'penerbit' => ['nullable', 'string', 'max:255'],
            'tahun_terbit' => ['nullable', 'integer', 'between:1000,'.(now()->year + 1)],
            'kategori_id' => ['nullable', 'exists:kategori,id'],
            'jumlah_stok' => ['required', 'integer', 'min:1', 'max:9999'],
            'halaman' => ['nullable', 'integer', 'min:1', 'max:20000'],
            'bahasa' => ['required', 'string', 'max:40'],
            'lokasi_rak' => ['nullable', 'string', 'max:30'],
            'sinopsis' => ['nullable', 'string', 'max:5000'],
            'sampul' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);
    }
}
