<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Buku extends Model
{
    protected $table = 'buku';

    protected $fillable = [
        'kode',
        'isbn',
        'judul',
        'penulis',
        'penerbit',
        'tahun_terbit',
        'kategori_id',
        'jumlah_stok',
        'tersedia',
        'halaman',
        'bahasa',
        'lokasi_rak',
        'sinopsis',
        'sampul',
    ];

    protected function casts(): array
    {
        return [
            'tahun_terbit' => 'integer',
            'jumlah_stok' => 'integer',
            'tersedia' => 'integer',
            'halaman' => 'integer',
        ];
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }

    public function peminjaman(): HasMany
    {
        return $this->hasMany(Peminjaman::class, 'buku_id');
    }

    public function scopeCari(Builder $query, ?string $kata): Builder
    {
        if (! $kata) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($kata) {
            $q->where('judul', 'like', "%{$kata}%")
                ->orWhere('penulis', 'like', "%{$kata}%")
                ->orWhere('penerbit', 'like', "%{$kata}%")
                ->orWhere('isbn', 'like', "%{$kata}%")
                ->orWhere('kode', 'like', "%{$kata}%");
        });
    }

    public function scopeTersedia(Builder $query): Builder
    {
        return $query->where('tersedia', '>', 0);
    }

    public function getSampulUrlAttribute(): ?string
    {
        return $this->sampul ? asset('storage/'.$this->sampul) : null;
    }

    public function getDipinjamAttribute(): int
    {
        return max(0, $this->jumlah_stok - $this->tersedia);
    }

    /** Buat kode buku berikutnya, mis. BK-0042. */
    public static function kodeBerikutnya(): string
    {
        $nomor = (int) static::max('id') + 1;

        do {
            $kode = 'BK-'.str_pad((string) $nomor, 4, '0', STR_PAD_LEFT);
            $nomor++;
        } while (static::where('kode', $kode)->exists());

        return $kode;
    }
}
