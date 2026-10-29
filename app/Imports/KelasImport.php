<?php

namespace App\Imports;

use App\Models\Kelas;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class KelasImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Skip empty rows
            if (!isset($row['nama_kelas']) || trim($row['nama_kelas']) === '') {
                continue;
            }

            $namaKelas = trim($row['nama_kelas']);

            // Check if class already exists
            if (Kelas::where('nama', $namaKelas)->exists()) {
                continue; // Skip existing
            }

            Kelas::create([
                'nama' => $namaKelas,
                'tingkat' => isset($row['tingkat']) ? (int) $row['tingkat'] : 10,
                'kapasitas' => isset($row['kapasitas']) ? (int) $row['kapasitas'] : 36,
                'id_konsentrasi_keahlian' => isset($row['id_jurusan']) ? (int) $row['id_jurusan'] : null,
                'id_tahun_ajaran' => isset($row['id_tahun_ajaran']) ? (int) $row['id_tahun_ajaran'] : null,
                'id_wali_kelas' => isset($row['id_wali_kelas']) ? (int) $row['id_wali_kelas'] : null,
                'status' => 'aktif'
            ]);
        }
    }
}
