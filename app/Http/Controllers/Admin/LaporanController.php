<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LaporanController extends Controller
{
    public function index(Request $request): View
    {
        [$dari, $sampai, $status] = $this->filter($request);

        $query = $this->query($dari, $sampai, $status);

        $ringkasan = [
            'total' => (clone $query)->count(),
            'dipinjam' => (clone $query)->where('status', Peminjaman::DIPINJAM)->count(),
            'dikembalikan' => (clone $query)->where('status', Peminjaman::DIKEMBALIKAN)->count(),
            'denda' => (int) (clone $query)->sum('denda'),
        ];

        $data = $query->paginate(15)->withQueryString();

        return view('admin.laporan.index', compact('data', 'ringkasan', 'dari', 'sampai', 'status'));
    }

    public function ekspor(Request $request): StreamedResponse
    {
        [$dari, $sampai, $status] = $this->filter($request);

        $query = $this->query($dari, $sampai, $status);
        $nama = 'laporan-peminjaman-'.$dari->format('Ymd').'-'.$sampai->format('Ymd').'.csv';

        return response()->streamDownload(function () use ($query) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // BOM agar Excel membaca UTF-8 dengan benar
            fputcsv($out, ['Kode', 'Anggota', 'No. Anggota', 'Judul Buku', 'Tgl Pinjam', 'Jatuh Tempo', 'Tgl Kembali', 'Status', 'Denda (Rp)', 'Denda Lunas']);

            foreach ($query->lazy(200) as $p) {
                fputcsv($out, [
                    $p->kode,
                    $p->anggota?->name,
                    $p->anggota?->kode_anggota,
                    $p->buku?->judul,
                    $p->tanggal_pinjam->format('Y-m-d'),
                    $p->jatuh_tempo->format('Y-m-d'),
                    $p->tanggal_kembali?->format('Y-m-d'),
                    $p->status_tampil['label'],
                    $p->dendaSaatIni(),
                    $p->denda > 0 ? ($p->denda_lunas ? 'Ya' : 'Belum') : '-',
                ]);
            }

            fclose($out);
        }, $nama, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /** @return array{0: Carbon, 1: Carbon, 2: string} */
    private function filter(Request $request): array
    {
        $request->validate([
            'dari' => ['nullable', 'date'],
            'sampai' => ['nullable', 'date'],
            'status' => ['nullable', 'in:dipinjam,terlambat,dikembalikan,denda'],
        ]);

        $dari = $request->filled('dari') ? Carbon::parse($request->query('dari'))->startOfDay() : now()->startOfMonth();
        $sampai = $request->filled('sampai') ? Carbon::parse($request->query('sampai'))->startOfDay() : today();

        if ($sampai->lt($dari)) {
            [$dari, $sampai] = [$sampai, $dari];
        }

        return [$dari, $sampai, (string) $request->query('status', '')];
    }

    private function query(Carbon $dari, Carbon $sampai, string $status): Builder
    {
        return Peminjaman::with(['anggota', 'buku'])
            ->whereBetween('tanggal_pinjam', [$dari->toDateString(), $sampai->toDateString()])
            ->when($status === 'dipinjam', fn ($q) => $q->aktif())
            ->when($status === 'terlambat', fn ($q) => $q->terlambat())
            ->when($status === 'dikembalikan', fn ($q) => $q->where('status', Peminjaman::DIKEMBALIKAN))
            ->when($status === 'denda', fn ($q) => $q->dendaBelumLunas())
            ->orderBy('tanggal_pinjam')
            ->orderBy('id');
    }
}
