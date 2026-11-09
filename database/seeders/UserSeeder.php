<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Akun staf. Kata sandi semua akun contoh: "password"
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@pustakaloka.test',
            'password' => 'password',
            'role' => User::ROLE_ADMIN,
            'telepon' => '0812-3456-7801',
            'status' => 'aktif',
            'tanggal_bergabung' => now()->subYears(2),
        ]);

        User::create([
            'name' => 'Rina Kartika',
            'email' => 'petugas@pustakaloka.test',
            'password' => 'password',
            'role' => User::ROLE_PETUGAS,
            'telepon' => '0812-3456-7802',
            'status' => 'aktif',
            'tanggal_bergabung' => now()->subYear(),
        ]);

        // Anggota contoh.
        $anggota = [
            ['Fino Pratama', 'anggota@pustakaloka.test', '0813-2200-1101', 'Jl. Melati No. 12, Bandung'],
            ['Alya Rahmawati', 'alya.rahmawati@contoh.test', '0813-2200-1102', 'Jl. Kenanga No. 5, Jakarta Selatan'],
            ['Bagas Setiawan', 'bagas.setiawan@contoh.test', '0813-2200-1103', 'Jl. Diponegoro No. 88, Yogyakarta'],
            ['Citra Maharani', 'citra.maharani@contoh.test', '0813-2200-1104', 'Jl. Pemuda No. 21, Semarang'],
            ['Dimas Anggara', 'dimas.anggara@contoh.test', '0813-2200-1105', 'Jl. Gatot Subroto No. 3, Medan'],
            ['Eka Wulandari', 'eka.wulandari@contoh.test', '0813-2200-1106', 'Jl. Veteran No. 14, Surabaya'],
            ['Farhan Hidayat', 'farhan.hidayat@contoh.test', '0813-2200-1107', 'Jl. Sudirman No. 30, Makassar'],
            ['Gita Permatasari', 'gita.permatasari@contoh.test', '0813-2200-1108', 'Jl. Asia Afrika No. 9, Bandung'],
            ['Hendra Wijaya', 'hendra.wijaya@contoh.test', '0813-2200-1109', 'Jl. Ahmad Yani No. 47, Malang'],
            ['Intan Nuraini', 'intan.nuraini@contoh.test', '0813-2200-1110', 'Jl. Cendrawasih No. 2, Denpasar'],
            ['Joko Susilo', 'joko.susilo@contoh.test', '0813-2200-1111', 'Jl. Pahlawan No. 60, Solo'],
            ['Kirana Putri', 'kirana.putri@contoh.test', '0813-2200-1112', 'Jl. Ir. H. Juanda No. 18, Bogor'],
        ];

        foreach ($anggota as $i => [$nama, $email, $telepon, $alamat]) {
            User::create([
                'name' => $nama,
                'email' => $email,
                'password' => 'password',
                'role' => User::ROLE_ANGGOTA,
                'telepon' => $telepon,
                'alamat' => $alamat,
                'status' => 'aktif',
                'tanggal_bergabung' => now()->subDays(40 + $i * 23),
            ]);
        }
    }
}
