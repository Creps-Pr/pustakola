<?php

namespace App\Support;

/**
 * Peta warna statis. Seluruh nama kelas ditulis lengkap di sini agar
 * Tailwind CSS dapat mendeteksinya saat proses build.
 */
class Warna
{
    /** Warna yang boleh dipilih untuk kategori. */
    public const DAFTAR = [
        'indigo' => 'Indigo',
        'sky' => 'Biru Langit',
        'teal' => 'Teal',
        'emerald' => 'Hijau',
        'amber' => 'Kuning',
        'orange' => 'Oranye',
        'rose' => 'Merah Muda',
        'violet' => 'Ungu',
    ];

    /** Kelas untuk lencana (latar lembut + teks + cincin tipis). */
    public static function lencana(string $warna): string
    {
        return match ($warna) {
            'sky' => 'bg-sky-50 text-sky-700 ring-sky-600/20',
            'teal' => 'bg-teal-50 text-teal-700 ring-teal-600/20',
            'emerald' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
            'amber' => 'bg-amber-50 text-amber-800 ring-amber-600/25',
            'orange' => 'bg-orange-50 text-orange-700 ring-orange-600/20',
            'rose' => 'bg-rose-50 text-rose-700 ring-rose-600/20',
            'violet' => 'bg-violet-50 text-violet-700 ring-violet-600/20',
            'slate' => 'bg-slate-100 text-slate-700 ring-slate-500/20',
            default => 'bg-indigo-50 text-indigo-700 ring-indigo-600/20',
        };
    }

    /** Titik warna kecil. */
    public static function titik(string $warna): string
    {
        return match ($warna) {
            'sky' => 'bg-sky-500',
            'teal' => 'bg-teal-500',
            'emerald' => 'bg-emerald-500',
            'amber' => 'bg-amber-500',
            'orange' => 'bg-orange-500',
            'rose' => 'bg-rose-500',
            'violet' => 'bg-violet-500',
            'slate' => 'bg-slate-400',
            default => 'bg-indigo-500',
        };
    }

    /** Latar ikon untuk kartu statistik. */
    public static function ikon(string $warna): string
    {
        return match ($warna) {
            'sky' => 'bg-sky-50 text-sky-600',
            'teal' => 'bg-teal-50 text-teal-600',
            'emerald' => 'bg-emerald-50 text-emerald-600',
            'amber' => 'bg-amber-50 text-amber-600',
            'orange' => 'bg-orange-50 text-orange-600',
            'rose' => 'bg-rose-50 text-rose-600',
            'violet' => 'bg-violet-50 text-violet-600',
            default => 'bg-indigo-50 text-indigo-600',
        };
    }

    /** Gradien sampul buku (dipilih deterministik dari judul). */
    public static function sampul(string $kunci): string
    {
        $gradien = [
            'from-indigo-600 to-violet-700',
            'from-sky-600 to-indigo-700',
            'from-teal-600 to-emerald-700',
            'from-rose-500 to-orange-600',
            'from-slate-700 to-slate-900',
            'from-amber-500 to-rose-600',
            'from-emerald-600 to-teal-800',
            'from-violet-600 to-fuchsia-700',
        ];

        return $gradien[crc32($kunci) % count($gradien)];
    }
}
