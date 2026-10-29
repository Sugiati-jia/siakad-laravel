<?php

namespace App\Imports;

use App\Models\Siswa;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;

class SiswaImport implements ToCollection, WithHeadingRow
{
    protected $kelasId;

    public function __construct($kelasId)
    {
        $this->kelasId = $kelasId;
    }

    public function collection(Collection $rows)
    {
        DB::transaction(function () use ($rows) {
            foreach ($rows as $row) {
                // Flexible mapping for NISN
                $nisn = trim($row['nisn'] ?? $row['nomor_induk_siswa'] ?? $row['nomor_induk'] ?? '');
                
                // Skip empty rows or rows without NISN
                if ($nisn === '') {
                    continue;
                }

                // Flexible mapping for Nama
                $namaRaw = trim($row['nama'] ?? $row['nama_siswa'] ?? $row['nama_lengkap'] ?? 'Siswa Tanpa Nama');
                
                $email = isset($row['email']) && trim($row['email']) !== '' ? trim($row['email']) : $nisn . '@siswa.com';

                // Check if NISN already exists
                if (Siswa::where('nisn', $nisn)->exists()) {
                    continue; // Skip existing
                }

                // Check if Email already exists
                if (User::where('email', $email)->exists()) {
                    $email = $nisn . rand(10, 99) . '@siswa.com';
                }

                // Create User
                $user = User::create([
                    'name' => $namaRaw,
                    'email' => $email,
                    'password' => Hash::make($nisn), // Password default NISN
                    'role' => 'siswa',
                    'nisn' => $nisn
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
                $nis = trim($row['nis'] ?? $row['nomor_induk_sekolah'] ?? '');
                $jkRaw = strtoupper(trim($row['jenis_kelamin'] ?? $row['jk'] ?? $row['l_p'] ?? 'L'));
                $jk = ($jkRaw === 'P' || $jkRaw === 'PEREMPUAN' || $jkRaw === 'W') ? 'P' : 'L';
                
                $tempatLahir = trim($row['tempat_lahir'] ?? $row['tempat'] ?? '-');
                $alamat = trim($row['alamat'] ?? $row['alamat_lengkap'] ?? $row['alamat_siswa'] ?? '-');
                $noHp = trim($row['no_hp'] ?? $row['no_telepon'] ?? $row['telepon'] ?? '-');
                $agama = trim($row['agama'] ?? 'Islam');
                
                // Create Siswa
                $siswa = new Siswa();
                $siswa->user_id = $user->id;
                $siswa->nisn = $nisn;
                $siswa->nis = $nis ?: null;
                $siswa->nama = $namaRaw;
                $siswa->jenis_kelamin = $jk;
                $siswa->tempat_lahir = $tempatLahir;
                $siswa->tanggal_lahir = $tanggalLahir ?? date('Y-m-d');
                $siswa->alamat = $alamat;
                $siswa->no_hp = $noHp;
                $siswa->agama = $agama;
                $siswa->nama_ayah = trim($row['nama_ayah'] ?? $row['ayah'] ?? '');
                $siswa->nama_ibu = trim($row['nama_ibu'] ?? $row['ibu'] ?? '');
                $siswa->no_hp_ortu = trim($row['no_hp_ortu'] ?? $row['hp_ortu'] ?? '');
                
                $siswa->id_kelas = $this->kelasId;
                $siswa->status = 'aktif';
                $siswa->save();
            }
        });
    }
}
