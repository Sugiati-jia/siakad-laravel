<?php

namespace App\Imports;

use App\Models\Guru;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;

class GuruImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        DB::transaction(function () use ($rows) {
            foreach ($rows as $row) {
                // Flexible mapping for NIP
                $nip = trim($row['nip'] ?? $row['nomor_induk_pegawai'] ?? $row['id_guru'] ?? '');
                
                // Skip empty rows
                if ($nip === '') {
                    continue;
                }

                // Flexible mapping for Nama
                $namaRaw = trim($row['nama'] ?? $row['nama_guru'] ?? $row['nama_lengkap'] ?? 'Guru Tanpa Nama');

                $email = isset($row['email']) && trim($row['email']) !== '' ? trim($row['email']) : $nip . '@guru.com';

                // Check if NIP already exists
                if (Guru::where('nip', $nip)->exists()) {
                    continue; // Skip existing
                }

                // Check if Email already exists
                if (User::where('email', $email)->exists()) {
                    $email = $nip . rand(10, 99) . '@guru.com';
                }

                // Create User
                $user = User::create([
                    'name' => $namaRaw,
                    'email' => $email,
                    'password' => Hash::make($nip), // Password default NIP
                    'role' => 'guru',
                    'nip' => $nip
                ]);

                // Parse Date (Flexible headers)
                $tglLahirRaw = $row['tanggal_lahir'] ?? $row['tgl_lahir'] ?? $row['tgl'] ?? null;
                $tanggalLahir = null;
                if ($tglLahirRaw) {
                    try {
                        if (is_numeric($tglLahirRaw)) {
                            // Excel date format
                            $tanggalLahir = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($tglLahirRaw)->format('Y-m-d');
                        } else {
                            $tanggalLahir = Carbon::parse($tglLahirRaw)->format('Y-m-d');
                        }
                    } catch (\Exception $e) {
                        $tanggalLahir = null;
                    }
                }

                // Flexible mappings for other columns
                $jkRaw = strtoupper(trim($row['jenis_kelamin'] ?? $row['jk'] ?? $row['l_p'] ?? 'L'));
                $jk = ($jkRaw === 'P' || $jkRaw === 'PEREMPUAN' || $jkRaw === 'W') ? 'P' : 'L';
                
                $tempatLahir = trim($row['tempat_lahir'] ?? $row['tempat'] ?? '-');
                $alamat = trim($row['alamat'] ?? $row['alamat_lengkap'] ?? $row['alamat_guru'] ?? '-');
                $noHp = trim($row['no_telepon'] ?? $row['no_hp'] ?? $row['telepon'] ?? '-');

                // Create Guru
                $guru = new Guru();
                $guru->user_id = $user->id;
                $guru->nip = $nip;
                $guru->nama = $namaRaw;
                $guru->jenis_kelamin = $jk;
                $guru->tempat_lahir = $tempatLahir;
                $guru->tanggal_lahir = $tanggalLahir ?? date('Y-m-d');
                $guru->alamat = $alamat;
                $guru->no_telepon = $noHp;
                $guru->email = $email;
                $guru->status = 'aktif';
                $guru->save();
            }
        });
    }
}
