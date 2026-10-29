<?php

namespace App\Imports;

use App\Models\GuruMapel;
use App\Models\TujuanPembelajaran;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;

class MasterSheetImport implements ToCollection, WithCalculatedFormulas
{
    public function __construct(
        protected int $guruMapelId,
        protected string|int $semester,
        protected int $tahunAjaranId
    ) {}

    public function collection(Collection $rows): void
    {
        $guruMapel = GuruMapel::findOrFail($this->guruMapelId);

        $tpHeaderIndex = -1;
        $tpDescColIndex = -1;
        $tpCodeColIndex = -1;
        foreach ($rows as $index => $row) {
            $rowArray = $row instanceof Collection ? $row->toArray() : (array) $row;
            
            // Cari header TUJUAN PEMBELAJARAN dan NO
            foreach ($rowArray as $colIndex => $cell) {
                if ($cell !== null) {
                    $cleanCell = preg_replace('/[^a-z]/', '', strtolower((string)$cell));
                    if (str_contains($cleanCell, 'tujuanpembelajaran')) {
                        $tpHeaderIndex = $index;
                        $tpDescColIndex = $colIndex;
                    }
                    if (in_array($cleanCell, ['no', 'nomor'])) {
                        $tpCodeColIndex = $colIndex;
                    }
                }
            }
            if ($tpHeaderIndex !== -1) {
                break;
            }
        }

        // Buat TP otomatis jika ada sheet MASTER
        if ($tpHeaderIndex !== -1) {
            $lastParsedCode = 0;
            $stopNextRow = false;
            for ($i = $tpHeaderIndex + 1; $i < count($rows); $i++) {
                if ($stopNextRow) break;
                
                if (!isset($rows[$i])) continue;
                $rowArray = $rows[$i] instanceof Collection ? $rows[$i]->toArray() : (array)$rows[$i];
                
                $codeColIndex = $tpCodeColIndex !== -1 ? $tpCodeColIndex : max(0, $tpDescColIndex - 1);
                $code = isset($rowArray[$codeColIndex]) ? trim((string)$rowArray[$codeColIndex]) : '';
                $desc = isset($rowArray[$tpDescColIndex]) ? trim((string)$rowArray[$tpDescColIndex]) : '';
                
                // Deteksi daftar nama siswa di baris ini
                $fullRowText = preg_replace('/[^a-z]/', '', strtolower(implode('', $rowArray)));
                if (str_contains($fullRowText, 'namasiswa') || str_contains($fullRowText, 'namapeserta')) {
                    $stopNextRow = true;
                }
                
                // Stop jika bertemu bagian daftar nama siswa berdasarkan teks di kode/desc
                $cleanCodeAlpha = preg_replace('/[^a-z]/', '', strtolower((string)$code));
                $cleanDescAlpha = preg_replace('/[^a-z]/', '', strtolower((string)$desc));
                
                if (in_array($cleanCodeAlpha, ['no', 'nomor']) || str_contains($cleanDescAlpha, 'namasiswa') || str_contains($cleanDescAlpha, 'namapeserta')) {
                    break;
                }
                
                // Stop jika numbering me-restart (indikasi daftar siswa dimulai)
                $cleanCodeNumeric = preg_replace('/[^0-9]/', '', $code);
                if ($cleanCodeNumeric !== '' && (int)$cleanCodeNumeric <= $lastParsedCode && $lastParsedCode >= 2) {
                    break;
                }

                // Lanjut jika baris kosong
                if ($code === '' && $desc === '') {
                    continue;
                }

                if ($desc !== '') {
                    // Skip jika mirip header
                    if (strtolower($desc) === 'tujuan pembelajaran' || strtolower($desc) === 'deskripsi') {
                        continue;
                    }
                    
                    static $autoNum = 1;
                    $cleanCode = preg_replace('/[^0-9]/', '', $code);
                    
                    if ($cleanCode === '') {
                        // Coba ekstrak angka dari awal deskripsi (misal: "1. Menjelaskan...")
                        if (preg_match('/^([0-9]+)[\.\-\s]/', $desc, $matches)) {
                            $cleanCode = $matches[1];
                        } else {
                            $cleanCode = (string)$autoNum;
                        }
                    } else {
                        $autoNum = (int)$cleanCode; // Sinkronisasi
                    }
                    $lastParsedCode = (int)$cleanCode;
                    $autoNum++;

                    // Cek jika TP sudah ada
                    $existingTp = TujuanPembelajaran::where('id_mapel', $guruMapel->mapel_id)
                        ->where('tingkat', $guruMapel->kelas->tingkat)
                        ->where('id_semester', $this->semester)
                        ->where(function($query) use ($cleanCode, $desc) {
                            $query->where('kode_tp', $cleanCode)
                                  ->orWhere('deskripsi', $desc);
                        })
                        ->first();
                        
                    if (!$existingTp) {
                        TujuanPembelajaran::create([
                            'id_mapel' => $guruMapel->mapel_id,
                            'kode_tp' => $cleanCode,
                            'deskripsi' => $desc,
                            'tingkat' => $guruMapel->kelas->tingkat,
                            'id_semester' => $this->semester,
                            'urutan' => $cleanCode,
                        ]);
                    }
                }
            }
        }
    }
}
