<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseCleanerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        // 1. Truncate all tables
        $tables = [
            'nilai_tp',
            'catatan_wali_kelas',
            'nilai_ekstrakurikuler',
            'kokurikuler',
            'tujuan_pembelajaran',
            'rapor_status',
            'jadwal_pelajaran',
            'ekstrakurikuler',
            'guru_mapel',
            'siswa',
            'guru',
            'kelas',
            'mapel',
            'activity_logs',
            'error_logs',
            'users',
            'tahun_ajaran',
            'semester',
            'settings',
            'push_notifications',
            'push_subscriptions',
            'user_notifications',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                DB::table($table)->truncate();
            }
        }

        // 2. Re-seed Semesters
        DB::table('semester')->insert([
            [
                'id_semester' => 1,
                'nama' => '1 (SATU)',
                'created_at' => now(),
            ],
            [
                'id_semester' => 2,
                'nama' => '2 (DUA)',
                'created_at' => now(),
            ]
        ]);

        // 3. Re-seed Default Active Academic Year
        DB::table('tahun_ajaran')->insert([
            'id_tahun_ajaran' => 1,
            'tahun_ajaran' => '2025/2026',
            'semester_aktif' => 1,
            'tanggal_mulai' => '2025-07-15',
            'tanggal_selesai' => '2026-06-20',
            'status' => 'aktif',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 4. Re-seed Settings
        $settings = [
            ['key' => 'nama_sekolah', 'value' => 'SMK SUNAN GIRI'],
            ['key' => 'alamat_sekolah', 'value' => 'Menganti, Gresik'],
            ['key' => 'kepala_sekolah_nama', 'value' => 'Sillahturohman, S.Pd., M.Pd'],
            ['key' => 'kepala_sekolah_nip', 'value' => '-'],
        ];

        foreach ($settings as $setting) {
            DB::table('settings')->insert([
                'key' => $setting['key'],
                'value' => $setting['value'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 5. Seed Admin and Superadmin users
        DB::table('users')->insert([
            [
                'name' => 'Administrator',
                'email' => 'admin@smksunangiri.sch.id',
                'username' => 'admin',
                'role' => 'admin',
                'password' => Hash::make('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Super Administrator',
                'email' => 'superadmin@smksunangiri.sch.id',
                'username' => 'superadmin',
                'role' => 'superadmin',
                'password' => Hash::make('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);

        Schema::enableForeignKeyConstraints();
    }
}
