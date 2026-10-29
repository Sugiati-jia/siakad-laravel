<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Admin
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@smksunangiri.sch.id',
            'password' => bcrypt('admin123'),
            'role' => 'admin'
        ]);

        // 2. Create Guru
        User::create([
            'name' => 'Budi Santoso',
            'email' => 'budi.santoso@smksunangiri.sch.id',
            'password' => bcrypt('guru123'),
            'role' => 'guru',
            'nip' => '198501012010011001'
        ]);

        // 3. Create Siswa
        User::create([
            'name' => 'Ahmad Fauzi',
            'email' => 'ahmad.fauzi@smksunangiri.sch.id',
            'password' => bcrypt('siswa123'),
            'role' => 'siswa',
            'nisn' => '0098765432'
        ]);

        // 4. Create Orang Tua
        User::create([
            'name' => 'Pak Fauzi',
            'email' => 'ortu@smksunangiri.sch.id',
            'password' => bcrypt('ortu123'),
            'role' => 'orang_tua'
        ]);

        $this->command->info('Akun berhasil dibuat!');
        $this->command->info('Admin: admin@smksunangiri.sch.id / admin123');
        $this->command->info('Guru: budi.santoso@smksunangiri.sch.id / guru123');
        $this->command->info('Siswa: ahmad.fauzi@smksunangiri.sch.id / siswa123');
        $this->command->info('Orang Tua: ortu@smksunangiri.sch.id / ortu123');
    }
}
