<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['key' => 'nama_sekolah', 'value' => 'SMK SUNAN GIRI'],
            ['key' => 'alamat_sekolah', 'value' => 'Menganti, Gresik'],
            ['key' => 'kepala_sekolah_nama', 'value' => 'Sillahturohman, S.Pd., M.Pd'],
            ['key' => 'kepala_sekolah_nip', 'value' => '-'],
        ];

        foreach ($settings as $setting) {
            Setting::firstOrCreate(['key' => $setting['key']], ['value' => $setting['value']]);
        }
    }
}
