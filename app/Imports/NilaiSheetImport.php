<?php

namespace App\Imports;

use App\Models\GuruMapel;
use App\Models\NilaiTp;
use App\Models\Siswa;
use App\Models\TujuanPembelajaran;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;

class NilaiSheetImport implements ToCollection, WithCalculatedFormulas
{
    public function __construct(
        protected int $guruMapelId,
        protected string|int $semester,
        protected int $tahunAjaranId
    ) {}

    public function collection(Collection $rows): void
    {
        $guruMapel = GuruMapel::findOrFail($this->guruMapelId);
        $kelasId = $guruMapel->id_kelas;

        // ── 1. Dynamic Layout Detection ──────────────────────────────────
        $tpHeaderIndex = -1;
        $tpDescColIndex = -1;
        $tpCodeColIndex = -1;
        $studentHeaderIndex = -1;
        $studentNameColIndex = -1;
        $studentNoColIndex = -1;

        foreach ($rows as $index => $row) {
            $rowArray = $row instanceof Collection ? $row->toArray() : (array) $row;
            
            // Check for TP Header
            if ($tpHeaderIndex === -1) {
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
            }
            
            // Check for Student Header
            foreach ($rowArray as $colIndex => $cell) {
                if ($cell !== null) {
                    $cleanCell = preg_replace('/[^a-z]/', '', strtolower((string)$cell));
                    if ($cleanCell !== '' && (str_contains($cleanCell, 'namapesertadidik') || str_contains($cleanCell, 'namasiswa') || $cleanCell === 'nama')) {
                        $studentHeaderIndex = $index;
                        $this->studentNameColIndex = $colIndex;
                    }
                    if (in_array($cleanCell, ['no', 'nomor'])) {
                        $studentNoColIndex = $colIndex;
                    }
                }
            }
            if ($studentHeaderIndex !== -1 && $tpHeaderIndex !== -1 && $index > $studentHeaderIndex) {
                break; // Stop scanning headers once both are found
            }
        }

        // ── 2. Automatic TP Creation from Sheet (if present in this sheet)
        if ($tpHeaderIndex !== -1 && $studentHeaderIndex !== -1) {
            for ($i = $tpHeaderIndex + 1; $i < $studentHeaderIndex; $i++) {
                if (!isset($rows[$i])) continue;
                $rowArray = $rows[$i] instanceof Collection ? $rows[$i]->toArray() : (array)$rows[$i];
                $codeColIndex = $tpCodeColIndex !== -1 ? $tpCodeColIndex : max(0, $tpDescColIndex - 1);
                $code = isset($rowArray[$codeColIndex]) ? trim((string)$rowArray[$codeColIndex]) : '';
                $desc = isset($rowArray[$tpDescColIndex]) ? trim((string)$rowArray[$tpDescColIndex]) : '';
                
                $cleanCodeAlpha = preg_replace('/[^a-z]/', '', strtolower((string)$code));
                $cleanDescAlpha = preg_replace('/[^a-z]/', '', strtolower((string)$desc));
                
                if (in_array($cleanCodeAlpha, ['no', 'nomor']) || str_contains($cleanDescAlpha, 'namasiswa') || str_contains($cleanDescAlpha, 'namapeserta') || $cleanDescAlpha === 'nama') {
                    break;
                }
                
                if ($desc !== '') {
                    // Skip header-like rows in the TP section
                    if (strtolower($desc) === 'tujuan pembelajaran' || strtolower($desc) === 'deskripsi') {
                        continue;
                    }
                    
                    static $autoNum = 1;
                    $cleanCode = preg_replace('/[^0-9]/', '', $code);
                    
                    if ($cleanCode === '') {
                        if (preg_match('/^([0-9]+)[\.\-\s]/', $desc, $matches)) {
                            $cleanCode = $matches[1];
                        } else {
                            $cleanCode = (string)$autoNum;
                        }
                    } else {
                        $autoNum = (int)$cleanCode;
                    }
                    $autoNum++;

                    // Check if this TP already exists for this mapel/kelas/semester
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

        // ── 3. Fetch TPs (including newly created ones) ──────────────────
        $tps = TujuanPembelajaran::query()
            ->where('id_mapel', $guruMapel->mapel_id)
            ->where('tingkat', $guruMapel->kelas->tingkat)
            ->where('id_semester', $this->semester)
            ->orderBy('kode_tp')
            ->get();

        $tpsKeyed = $tps->keyBy(fn (TujuanPembelajaran $tp) => (string) $tp->kode_tp);

        $currentSiswaId = null;
        $studentKdRowIndex = 0;

        foreach ($rows as $index => $row) {
            $rowArray = $row instanceof Collection ? $row->toArray() : (array) $row;

            // Skip header rows dynamically based on detected student header index
            if ($studentHeaderIndex !== -1 && $index <= $studentHeaderIndex) {
                continue;
            } elseif ($studentHeaderIndex === -1 && $index < 12) {
                // Fallback if no student header detected
                continue;
            }

            // Gunakan deteksi kolom dinamis
            $nameColIndex = $this->studentNameColIndex ?? 1;
            $noColIndex = isset($studentNoColIndex) && $studentNoColIndex !== -1 ? $studentNoColIndex : max(0, $nameColIndex - 1);
            $kdColIndex = $nameColIndex + 1;
            
            $no = isset($rowArray[$noColIndex]) ? trim((string)$rowArray[$noColIndex]) : '';
            $nama = isset($rowArray[$nameColIndex]) ? trim((string) $rowArray[$nameColIndex]) : '';
            $kd = isset($rowArray[$kdColIndex]) ? trim((string) $rowArray[$kdColIndex]) : '';

            // Jika kolom 'No' ada isinya (angka), berarti ini adalah blok siswa baru.
            // Jika namanya kosong, kita harus mereset currentSiswaId agar tidak menimpa siswa sebelumnya.
            $isNewStudentBlock = is_numeric($no) && (int)$no > 0;

            // Jika ada nama, cari siswa dan update currentSiswaId
            if ($nama !== '' && strtolower($nama) !== 'nama peserta didik' && strtolower($nama) !== 'nama siswa' && strtolower($nama) !== 'nama') {
                $siswa = Siswa::query()
                    ->where('id_kelas', $kelasId)
                    ->where('nama', 'like', '%' . $nama . '%')
                    ->first();

                if ($siswa) {
                    $currentSiswaId = $siswa->id_siswa ?? $siswa->id;
                    $studentKdRowIndex = 0; // reset for new student
                } else {
                    $currentSiswaId = null;
                }
            } elseif ($isNewStudentBlock) {
                // Ini blok siswa baru tapi namanya kosong (slot sisa di template Excel)
                $currentSiswaId = null;
            }

            // Jika tidak ada siswa yang valid untuk baris ini, atau baris bukan baris nilai (KD kosong/teks), lewati
            if (!$currentSiswaId || $kd === '' || strtolower($kd) === 'kd') {
                continue;
            }

            // Skip non-KD rows like "Nilai Penugasan Harian" or summaries
            if (is_numeric($kd) || str_starts_with(strtolower($kd), 'tp') || preg_match('/^[0-9]+(\.[0-9]+)*$/', $kd) || strtolower($kd) === 'tugas harian' || strtolower($kd) === 'ulangan harian') {
                // Jika KD adalah string literal seperti 'tugas harian', mungkin baris ini bukan nilai, skip saja.
                if(!is_numeric($kd) && !str_starts_with(strtolower($kd), 'tp') && !preg_match('/^[0-9]+(\.[0-9]+)*$/', $kd)){
                    continue;
                }
            } else {
                continue;
            }

            // ── Dynamic TP Matching ──────────────────────────────────────────
            $tp = null;

            // Strategy 1: Exact match
            if ($tpsKeyed->has($kd)) {
                $tp = $tpsKeyed->get($kd);
            }

            // Strategy 2: Numeric clean match (e.g. sheet has "1" and database has "TP 1" or "1.1")
            if (!$tp) {
                $kdNumeric = preg_replace('/[^0-9]/', '', $kd);
                if ($kdNumeric !== '') {
                    foreach ($tps as $item) {
                        $itemNumeric = preg_replace('/[^0-9]/', '', $item->kode_tp);
                        if ($itemNumeric !== '' && $itemNumeric === $kdNumeric) {
                            $tp = $item;
                            break;
                        }
                    }
                }
            }

            // Strategy 3: Index-based fallback (map $i-th excel row of student to $i-th database TP)
            if (!$tp) {
                $tpsArray = $tps->values()->all();
                if (isset($tpsArray[$studentKdRowIndex])) {
                    $tp = $tpsArray[$studentKdRowIndex];
                }
            }

            // Increment row counter for this student
            $studentKdRowIndex++;

            if (!$tp) {
                continue;
            }

            // Gunakan deteksi kolom dinamis untuk nilai
            $nameColIndex = $this->studentNameColIndex ?? 1;
            $kdColIndex = $nameColIndex + 1;
            $th1 = $rowArray[$kdColIndex + 1] ?? null;
            $th2 = $rowArray[$kdColIndex + 2] ?? null;
            $uh1 = $rowArray[$kdColIndex + 4] ?? null; // +4 asumsi ada kolom Rata-rata di +3
            $uh2 = $rowArray[$kdColIndex + 5] ?? null;
            $akhir = $rowArray[$kdColIndex + 7] ?? null; // +7 asumsi J jika C adalah KD

            // Simpan nilai per TP
            $this->saveGrade($currentSiswaId, $tp->id_tujuan_pembelajaran ?? $tp->id, 'th1', $this->parseNumeric($th1));
            $this->saveGrade($currentSiswaId, $tp->id_tujuan_pembelajaran ?? $tp->id, 'th2', $this->parseNumeric($th2));
            $this->saveGrade($currentSiswaId, $tp->id_tujuan_pembelajaran ?? $tp->id, 'uh1', $this->parseNumeric($uh1));
            $this->saveGrade($currentSiswaId, $tp->id_tujuan_pembelajaran ?? $tp->id, 'uh2', $this->parseNumeric($uh2));

            // Simpan nilai akhir di memori jika ada
            if ($this->parseNumeric($akhir) !== null) {
                $this->akhirSemesterValues[$currentSiswaId] = $this->parseNumeric($akhir);
            }
        }

        // Simpan semua nilai akhir semester ke database setelah semua baris diproses
        if (!empty($this->akhirSemesterValues)) {
            foreach ($this->akhirSemesterValues as $siswaId => $asValue) {
                $this->saveAkhirSemester($siswaId, $asValue);
            }
        }
    }

    private array $akhirSemesterValues = [];

    private function parseNumeric($value): ?float
    {
        if ($value === null || $value === '') return null;
        $numeric = is_numeric($value) ? (float) $value : null;
        return $numeric;
    }

    private function saveGrade(int $siswaId, int $tpId, string $gradeField, ?float $value): void
    {
        if ($value === null) return;

        $nilaiTp = NilaiTp::firstOrCreate(
            [
                'id_siswa' => $siswaId,
                'id_guru_mapel' => $this->guruMapelId,
                'id_tujuan_pembelajaran' => $tpId,
                'id_semester' => $this->semester,
            ],
            [
                'id_mapel' => \App\Models\GuruMapel::findOrFail($this->guruMapelId)->id_mapel,
                'status_nilai' => 'draft',
            ]
        );

        // Map gradeField from th1/th2/uh1/uh2 to nilai_th1/nilai_th2/nilai_uh1/nilai_uh2
        $mappedGradeField = 'nilai_' . $gradeField;
        $nilaiTp->{$mappedGradeField} = $value;
        $nilaiTp->save();
    }

    private function saveAkhirSemester(int $siswaId, float $value): void
    {
        NilaiTp::where('id_siswa', $siswaId)
            ->where('id_guru_mapel', $this->guruMapelId)
            ->where('id_semester', $this->semester)
            ->get()
            ->each(function (NilaiTp $nilaiTp) use ($value) {
                $nilaiTp->nilai_as = $value;
                $nilaiTp->save();
            });
    }
}
