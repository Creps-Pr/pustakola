<?php

namespace Database\Seeders;

use App\Models\Kategori;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class KategoriSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['Fiksi & Sastra', 'indigo', 'Novel, kumpulan cerita, dan karya sastra klasik maupun kontemporer.'],
            ['Sains & Teknologi', 'sky', 'Ilmu pengetahuan alam, pemrograman, dan rekayasa perangkat lunak.'],
            ['Sejarah & Sosial', 'amber', 'Sejarah dunia, sejarah Indonesia, antropologi, dan ilmu sosial.'],
            ['Bisnis & Pengembangan Diri', 'emerald', 'Manajemen, kewirausahaan, keuangan pribadi, dan produktivitas.'],
            ['Filsafat & Pemikiran', 'violet', 'Filsafat, cara berpikir, dan pemikiran kritis.'],
            ['Anak & Remaja', 'rose', 'Bacaan ringan dan inspiratif untuk pembaca muda.'],
            ['Seni & Desain', 'teal', 'Desain, kreativitas, dan pengalaman pengguna.'],
        ];

        foreach ($data as [$nama, $warna, $deskripsi]) {
            Kategori::create([
                'nama' => $nama,
                'slug' => Str::slug($nama),
                'warna' => $warna,
                'deskripsi' => $deskripsi,
            ]);
        }
    }
}
