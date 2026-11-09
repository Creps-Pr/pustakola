<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    public const ROLE_ADMIN = 'admin';
    public const ROLE_PETUGAS = 'petugas';
    public const ROLE_ANGGOTA = 'anggota';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'kode_anggota',
        'telepon',
        'alamat',
        'status',
        'tanggal_bergabung',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'tanggal_bergabung' => 'date',
        ];
    }

    protected static function booted(): void
    {
        // Beri nomor anggota otomatis (AGT-00001) untuk anggota baru.
        static::created(function (User $user) {
            if ($user->role === self::ROLE_ANGGOTA && ! $user->kode_anggota) {
                $user->forceFill([
                    'kode_anggota' => 'AGT-'.str_pad((string) $user->id, 5, '0', STR_PAD_LEFT),
                ])->saveQuietly();
            }
        });
    }

    /* ------------------------------------------------------------------ */
    /* Relasi                                                              */
    /* ------------------------------------------------------------------ */

    public function peminjaman(): HasMany
    {
        return $this->hasMany(Peminjaman::class, 'user_id');
    }

    /* ------------------------------------------------------------------ */
    /* Scope                                                               */
    /* ------------------------------------------------------------------ */

    public function scopeAnggota(Builder $query): Builder
    {
        return $query->where('role', self::ROLE_ANGGOTA);
    }

    public function scopeStaf(Builder $query): Builder
    {
        return $query->whereIn('role', [self::ROLE_ADMIN, self::ROLE_PETUGAS]);
    }

    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('status', 'aktif');
    }

    public function scopeCari(Builder $query, ?string $kata): Builder
    {
        if (! $kata) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($kata) {
            $q->where('name', 'like', "%{$kata}%")
                ->orWhere('email', 'like', "%{$kata}%")
                ->orWhere('kode_anggota', 'like', "%{$kata}%")
                ->orWhere('telepon', 'like', "%{$kata}%");
        });
    }

    /* ------------------------------------------------------------------ */
    /* Peran & helper                                                      */
    /* ------------------------------------------------------------------ */

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isStaf(): bool
    {
        return in_array($this->role, [self::ROLE_ADMIN, self::ROLE_PETUGAS], true);
    }

    public function isAnggota(): bool
    {
        return $this->role === self::ROLE_ANGGOTA;
    }

    public function isAktif(): bool
    {
        return $this->status === 'aktif';
    }

    public function homeUrl(): string
    {
        return $this->isStaf() ? route('admin.dashboard') : route('akun.index');
    }

    public function getInisialAttribute(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->filter()
            ->take(2)
            ->map(fn ($kata) => Str::upper(Str::substr($kata, 0, 1)))
            ->implode('');
    }

    public function getLabelPeranAttribute(): string
    {
        return match ($this->role) {
            self::ROLE_ADMIN => 'Administrator',
            self::ROLE_PETUGAS => 'Petugas',
            default => 'Anggota',
        };
    }

    /** Jumlah buku yang sedang dipinjam. */
    public function jumlahPinjamAktif(): int
    {
        return $this->peminjaman()->where('status', 'dipinjam')->count();
    }

    /** Apakah anggota memiliki peminjaman yang melewati jatuh tempo? */
    public function punyaKeterlambatan(): bool
    {
        return $this->peminjaman()
            ->where('status', 'dipinjam')
            ->whereDate('jatuh_tempo', '<', today())
            ->exists();
    }

    /** Total denda yang tercatat dan belum dibayar. */
    public function dendaBelumLunas(): int
    {
        return (int) $this->peminjaman()
            ->where('status', 'dikembalikan')
            ->where('denda_lunas', false)
            ->sum('denda');
    }
}
