<?php

namespace App\Imports;

use App\Models\TujuanPembelajaran;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;

class TpImport implements ToCollection, WithCalculatedFormulas
{
    protected $id_mapel;
    protected $tingkat;
    protected $semester;
    public $importedCount = 0;
    public $processedCount = 0;

    public function __construct($id_mapel, $tingkat, $semester)
    {
        $this->id_mapel = $id_mapel;
        $this->tingkat = $tingkat;
        $this->semester = $semester;
    }

    public function collection(Collection $rows)
    {
        $currentCount = TujuanPembelajaran::where('id_mapel', $this->id_mapel)
            ->where('tingkat', $this->tingkat)
            ->where('id_semester', $this->semester)
            ->count();

        // Default fallbacks
        $kodeColIndex = 0;
        $descColIndex = 1;
        $hasHeader = false;
        $headerRowIndex = -1;

        // Scan rows to find header column indexes dynamically
        foreach ($rows as $index => $row) {
            $rowArray = $row instanceof Collection ? $row->toArray() : (array) $row;
            
            $tempKodeIdx = -1;
            $tempDescIdx = -1;
            
            foreach ($rowArray as $colIdx => $cell) {
                if ($cell === null) continue;
                $cellStr = strtolower(trim((string)$cell));
                if (str_contains($cellStr, 'kode') || str_contains($cellStr, 'kd') || $cellStr === 'no' || $cellStr === 'no.') {
                    $tempKodeIdx = $colIdx;
                }
                if (str_contains($cellStr, 'deskripsi') || str_contains($cellStr, 'tujuan') || str_contains($cellStr, 'pembelajaran')) {
                    $tempDescIdx = $colIdx;
                }
            }
            if ($tempKodeIdx !== -1 && $tempDescIdx !== -1) {
                $kodeColIndex = $tempKodeIdx;
                $descColIndex = $tempDescIdx;
                $hasHeader = true;
                $headerRowIndex = $index;
                break; // Found header row
            }
        }

        if (!$hasHeader) {
            throw new \Exception("Format file tidak dikenali. Pastikan terdapat kolom dengan judul 'Kode TP' dan 'Deskripsi' pada file Excel Anda.");
        }

        $lastParsedCode = 0;
        $stopNextRow = false;

        foreach ($rows as $index => $row) {
            if ($stopNextRow) break;
            
            $rowArray = $row instanceof Collection ? $row->toArray() : (array) $row;
            
            // Skip the header row and any rows before it
            if ($hasHeader && $index <= $headerRowIndex) {
                continue;
            }
            
            $kode_tp = isset($rowArray[$kodeColIndex]) ? trim((string)$rowArray[$kodeColIndex]) : '';
            $deskripsi = isset($rowArray[$descColIndex]) ? trim((string)$rowArray[$descColIndex]) : '';

            // Skip empty or header rows
            if ($kode_tp === '' || $deskripsi === '') {
                continue;
            }
            
            // Check if we hit the student names section (stop on NEXT row so we don't skip TP on this row)
            $fullRowText = preg_replace('/[^a-z]/', '', strtolower(implode('', $rowArray)));
            if (str_contains($fullRowText, 'namasiswa') || str_contains($fullRowText, 'namapeserta')) {
                $stopNextRow = true;
            }
            
            // Immediate stop if the specific cell contains Nama Siswa
            $cleanCodeAlpha = preg_replace('/[^a-z]/', '', strtolower((string)$kode_tp));
            $cleanDescAlpha = preg_replace('/[^a-z]/', '', strtolower((string)$deskripsi));
            if (in_array($cleanCodeAlpha, ['no', 'nomor']) || str_contains($cleanDescAlpha, 'namasiswa') || str_contains($cleanDescAlpha, 'namapeserta')) {
                break;
            }
            
            if (strtolower($kode_tp) === 'kode tp' || strtolower($kode_tp) === 'kd' || strtolower($deskripsi) === 'deskripsi' || strtolower($deskripsi) === 'tujuan pembelajaran') {
                continue;
            }

            // Clean code format (e.g. "TP 1" -> "1")
            $cleanCode = preg_replace('/[^0-9]/', '', $kode_tp);
            
            // Break if we hit a student list (e.g. number restarts from 1 after reaching 3 or 4)
            if ($cleanCode !== '' && (int)$cleanCode <= $lastParsedCode && $lastParsedCode >= 2) {
                break;
            }

            if ($cleanCode === '') {
                // If it doesn't contain any numbers, try to generate one based on current processed count
                $cleanCode = (string)($this->processedCount + 1);
            }
            
            $lastParsedCode = (int)$cleanCode;

            $this->processedCount++;

            // Check if this TP already exists for this mapel/tingkat/semester
            $existingTp = TujuanPembelajaran::where('id_mapel', $this->id_mapel)
                ->where('tingkat', $this->tingkat)
                ->where('id_semester', $this->semester)
                ->where('kode_tp', $cleanCode)
                ->first();

            if ($existingTp) {
                // Update description if it has changed
                if (trim($existingTp->deskripsi) !== trim($deskripsi)) {
                    $existingTp->update([
                        'deskripsi' => $deskripsi,
                    ]);
                    $this->importedCount++;
                }
            } else {
                // Create if limit of 15 not reached
                if ($currentCount < 15) {
                    $maxUrutan = TujuanPembelajaran::where('id_mapel', $this->id_mapel)
                        ->where('tingkat', $this->tingkat)
                        ->where('id_semester', $this->semester)
                        ->max('urutan');

                    TujuanPembelajaran::create([
                        'id_mapel' => $this->id_mapel,
                        'tingkat' => $this->tingkat,
                        'id_semester' => $this->semester,
                        'kode_tp' => $cleanCode,
                        'deskripsi' => $deskripsi,
                        'urutan' => $maxUrutan ? $maxUrutan + 1 : 1,
                    ]);
                    $currentCount++;
                    $this->importedCount++;
                }
            }
        }
    }
}
