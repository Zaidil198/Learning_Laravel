<?php

namespace Database\Seeders;

use App\Models\Mahasiswa;
use Illuminate\Database\Seeder;

class MahasiswaSeeder extends Seeder
{
    public function run(): void
    {
        Mahasiswa::create([
            'nim' => '20260001',
            'nama' => 'Alya Putri',
            'jurusan' => 'Informatika',
            'email' => 'alya@example.test',
            'angkatan' => 2026,
        ]);

        Mahasiswa::create([
            'nim' => '20250012',
            'nama' => 'Bima Pratama',
            'jurusan' => 'Sistem Informasi',
            'email' => 'bima@example.test',
            'angkatan' => 2025,
        ]);

        Mahasiswa::create([
            'nim' => '20260007',
            'nama' => 'Citra Lestari',
            'jurusan' => 'Informatika',
            'email' => 'citra@example.test',
            'angkatan' => 2026,
        ]);

        Mahasiswa::create([
            'nim' => '20240021',
            'nama' => 'Daffa Ramadhan',
            'jurusan' => 'Teknik Komputer',
            'email' => 'daffa@example.test',
            'angkatan' => 2024,
        ]);

        Mahasiswa::create([
            'nim' => '20250032',
            'nama' => 'Elsa Maharani',
            'jurusan' => 'Manajemen Informatika',
            'email' => 'elsa@example.test',
            'angkatan' => 2025,
        ]);
    }
}