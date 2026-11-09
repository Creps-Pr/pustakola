<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class Peminjaman extends Model
{
    public const DIPINJAM = 'dipinjam';
    public const DIKEMBALIKAN = 'dikembalikan';

    protected $table = 'peminjaman';

    protected $fillable = [
        'kode',
        'user_id',
        'buku_id',
        'petugas_id',
        'tanggal_pinjam',
        'jatuh_tempo',
        'tanggal_kembali',
        'status',
        'denda',
        'denda_lunas',
        'perpanjangan',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_pinjam' => 'date',
            'jatuh_tempo' => 'date',
            'tanggal_kembali' => 'date',
            'denda' => 'integer',
            'denda_lunas' => 'boolean',
            'perpanjangan' => 'integer',
        ];
    }

    /* ------------------------------------------------------------------ */
    /* Relasi                                                              */
    /* ------------------------------------------------------------------ */

    public function anggota(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function buku(): BelongsTo
    {
        return $this->belongsTo(Buku::class, 'buku_id');
    }

    public function petugas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }

    /* ------------------------------------------------------------------ */
    /* Scope                                                               */
    /* ------------------------------------------------------------------ */

    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('status', self::DIPINJAM);
    }

    public function scopeTerlambat(Builder $query): Builder
    {
        return $query->where('status', self::DIPINJAM)->whereDate('jatuh_tempo', '<', today());
    }

    public function scopeDendaBelumLunas(Builder $query): Builder
    {
        return $query->where('status', self::DIKEMBALIKAN)
            ->where('denda', '>', 0)
            ->where('denda_lunas', false);
    }

    /* ------------------------------------------------------------------ */
    /* Logika bisnis                                                       */
    /* ------------------------------------------------------------------ */

    public function sedangDipinjam(): bool
    {
        return $this->status === self::DIPINJAM;
    }

    public function sudahTerlambat(): bool
    {
        return $this->sedangDipinjam() && $this->jatuh_tempo->lt(today());
    }

    /** Jumlah hari keterlambatan (berjalan bila belum dikembalikan). */
    public function hariTerlambat(): int
    {
        $akhir = $this->tanggal_kembali ?? Carbon::today();

        if ($akhir->lte($this->jatuh_tempo)) {
            return 0;
        }

        return (int) $this->jatuh_tempo->diffInDays($akhir);
    }

    public static function hitungDenda(int $hari): int
    {
        return max(0, $hari) * (int) config('perpustakaan.denda_per_hari');
    }

    /** Denda yang berlaku saat ini: tersimpan bila sudah kembali, berjalan bila belum. */
    public function dendaSaatIni(): int
    {
        if ($this->sedangDipinjam()) {
            return static::hitungDenda($this->hariTerlambat());
        }

        return $this->denda;
    }

    public function bisaDiperpanjang(): bool
    {
        return $this->sedangDipinjam()
            && ! $this->sudahTerlambat()
            && $this->perpanjangan < (int) config('perpustakaan.maks_perpanjang');
    }

    /** Label & warna status untuk tampilan. */
    public function getStatusTampilAttribute(): array
    {
        if ($this->sudahTerlambat()) {
            return ['label' => 'Terlambat', 'warna' => 'rose'];
        }

        if ($this->sedangDipinjam()) {
            return ['label' => 'Dipinjam', 'warna' => 'sky'];
        }

        if ($this->denda > 0 && ! $this->denda_lunas) {
            return ['label' => 'Denda belum lunas', 'warna' => 'amber'];
        }

        return ['label' => 'Dikembalikan', 'warna' => 'emerald'];
    }

    public static function buatKode(): string
    {
        do {
            $kode = 'TRX-'.now()->format('ymd').'-'.Str::upper(Str::random(4));
        } while (static::where('kode', $kode)->exists());

        return $kode;
    }
}
