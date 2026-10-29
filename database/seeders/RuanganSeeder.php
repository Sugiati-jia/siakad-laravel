<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RuanganSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('ruangan')->insert([
            ['nama_ruangan' => 'Ruang Kelas 1',    'kode_ruangan' => 'R-01', 'created_at' => now(), 'updated_at' => now()],
            ['nama_ruangan' => 'Ruang Kelas 2',    'kode_ruangan' => 'R-02', 'created_at' => now(), 'updated_at' => now()],
            ['nama_ruangan' => 'Ruang Kelas 3',    'kode_ruangan' => 'R-03', 'created_at' => now(), 'updated_at' => now()],
            ['nama_ruangan' => 'Ruang Kelas 4',    'kode_ruangan' => 'R-04', 'created_at' => now(), 'updated_at' => now()],
            ['nama_ruangan' => 'Ruang Kelas 5',    'kode_ruangan' => 'R-05', 'created_at' => now(), 'updated_at' => now()],
            ['nama_ruangan' => 'Ruang Kelas 6',    'kode_ruangan' => 'R-06', 'created_at' => now(), 'updated_at' => now()],
            ['nama_ruangan' => 'Lab Komputer 1',   'kode_ruangan' => 'LAB-1', 'created_at' => now(), 'updated_at' => now()],
            ['nama_ruangan' => 'Lab Komputer 2',   'kode_ruangan' => 'LAB-2', 'created_at' => now(), 'updated_at' => now()],
            ['nama_ruangan' => 'Lab IPA',           'kode_ruangan' => 'LAB-IPA', 'created_at' => now(), 'updated_at' => now()],
            ['nama_ruangan' => 'Aula Sekolah',     'kode_ruangan' => 'AULA', 'created_at' => now(), 'updated_at' => now()],
            ['nama_ruangan' => 'Perpustakaan',     'kode_ruangan' => 'PERP', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
