<?php

namespace App\Services;

use App\Models\NilaiTp;
use App\Models\TujuanPembelajaran;
use Illuminate\Support\Facades\DB;

class GradeCalculationService
{
    /**
     * Hitung ranking siswa dalam satu kelas berdasarkan rata-rata nilai_akhir_tp.
     */
    public function calculateRanking(int $kelasId, ?int $semester = null, ?int $tahunAjaranId = null)
    {
        $query = DB::table('nilai_tp as nt')
            ->select([
                'nt.id_siswa',
                's.nama',
                's.nisn',
                DB::raw('AVG(nt.nilai_akhir_tp) as average'),
                DB::raw('AVG(nt.nilai_akhir_tp) as rata_rata'),
                DB::raw('RANK() OVER (ORDER BY AVG(nt.nilai_akhir_tp) DESC) as ranking'),
            ])
            ->join('siswa as s', 'nt.id_siswa', '=', 's.id_siswa')
            ->where('s.id_kelas', $kelasId)
            ->whereNotNull('nt.nilai_akhir_tp')
            ->groupBy('nt.id_siswa', 's.nama', 's.nisn');

        if ($semester !== null) {
            // Tidak ada kolom semester di nilai_tp asli — skip jika tidak ada
        }

        if ($tahunAjaranId !== null) {
            // Tidak ada kolom id_tahun_ajaran di nilai_tp asli — skip
        }

        return $query->orderBy('ranking')->get();
    }

    /**
     * Hitung rata-rata nilai akhir per mapel untuk satu siswa.
     */
    public function getNilaiPerMapel(int $siswaId, int $kelasId): array
    {
        $rows = DB::table('nilai_tp as nt')
            ->select([
                'm.id_mapel',
                'm.nama as nama_mapel',
                DB::raw('AVG(nt.nilai_akhir_tp) as rata_rata'),
                DB::raw('MIN(nt.nilai_akhir_tp) as nilai_min'),
                DB::raw('MAX(nt.nilai_akhir_tp) as nilai_max'),
            ])
            ->join('mapel as m', 'nt.id_mapel', '=', 'm.id_mapel')
            ->where('nt.id_siswa', $siswaId)
            ->whereNotNull('nt.nilai_akhir_tp')
            ->groupBy('m.id_mapel', 'm.nama')
            ->get();

        return $rows->keyBy('id_mapel')->toArray();
    }

    /**
     * Generate deskripsi capaian kompetensi otomatis berdasarkan nilai Tugas Harian (TH)
     * tertinggi & terendah. Nilai Ulangan Harian (UH) TIDAK ikut dalam penentuan deskripsi.
     */
    public function generateDescription(int $siswaId, int $mapelId): string
    {
        // Ambil semua nilai TP untuk siswa + mapel ini yang memiliki nilai Tugas Harian
        $nilaiTps = NilaiTp::with('tp')
            ->where('id_siswa', $siswaId)
            ->where('id_mapel', $mapelId)
            ->where(function ($q) {
                $q->whereNotNull('nilai_th1')
                  ->orWhereNotNull('nilai_th2');
            })
            ->get();

        if ($nilaiTps->isEmpty()) {
            return 'Belum ada data capaian kompetensi untuk mata pelajaran ini.';
        }

        // Hitung rata-rata TH (Tugas Harian) per TP — UH tidak ikut
        $nilaiTpsWithTh = $nilaiTps->map(function ($nilaiTp) {
            $th1 = $nilaiTp->nilai_th1 !== null ? (float) $nilaiTp->nilai_th1 : null;
            $th2 = $nilaiTp->nilai_th2 !== null ? (float) $nilaiTp->nilai_th2 : null;

            $thValues = array_filter([$th1, $th2], fn($v) => $v !== null);
            $rerataTh = count($thValues) > 0
                ? round(array_sum($thValues) / count($thValues), 2)
                : null;

            return [
                'nilaiTp'   => $nilaiTp,
                'rerata_th' => $rerataTh,
            ];
        })->filter(fn($item) => $item['rerata_th'] !== null);

        if ($nilaiTpsWithTh->isEmpty()) {
            return 'Belum ada data capaian kompetensi untuk mata pelajaran ini.';
        }

        // Temukan TP dengan nilai TH tertinggi dan terendah
        $highest = $nilaiTpsWithTh->sortByDesc('rerata_th')->first();
        $lowest  = $nilaiTpsWithTh->sortBy('rerata_th')->first();

        $deskripsiHighest = rtrim($highest['nilaiTp']?->tp?->deskripsi ?? '', '.');
        $deskripsiLowest  = rtrim($lowest['nilaiTp']?->tp?->deskripsi ?? '', '.');

        $desc = "Menunjukkan penguasaan yang baik dalam "
              . lcfirst($deskripsiHighest);

        // Tambah saran peningkatan jika TP terendah berbeda dari tertinggi
        if ($lowest && $lowest['nilaiTp']->id_nilai_tp !== $highest['nilaiTp']->id_nilai_tp) {
            $desc .= ', dan perlu peningkatan dalam ' . lcfirst($deskripsiLowest);
        }

        return $desc . '.';
    }

    /**
     * Statistik kelas per mapel.
     */
    public function getStatistikKelas(int $kelasId, int $mapelId): object
    {
        return DB::table('nilai_tp as nt')
            ->select([
                DB::raw('COUNT(DISTINCT nt.id_siswa) as total_siswa'),
                DB::raw('ROUND(AVG(nt.nilai_akhir_tp), 2) as rata_rata_kelas'),
                DB::raw('MAX(nt.nilai_akhir_tp) as nilai_tertinggi'),
                DB::raw('MIN(nt.nilai_akhir_tp) as nilai_terendah'),
                DB::raw('SUM(CASE WHEN nt.nilai_akhir_tp >= 90 THEN 1 ELSE 0 END) as sangat_baik'),
                DB::raw('SUM(CASE WHEN nt.nilai_akhir_tp >= 80 AND nt.nilai_akhir_tp < 90 THEN 1 ELSE 0 END) as baik'),
                DB::raw('SUM(CASE WHEN nt.nilai_akhir_tp >= 70 AND nt.nilai_akhir_tp < 80 THEN 1 ELSE 0 END) as cukup'),
                DB::raw('SUM(CASE WHEN nt.nilai_akhir_tp < 70 THEN 1 ELSE 0 END) as kurang'),
            ])
            ->join('siswa as s', 'nt.id_siswa', '=', 's.id_siswa')
            ->where('s.id_kelas', $kelasId)
            ->where('nt.id_mapel', $mapelId)
            ->whereNotNull('nt.nilai_akhir_tp')
            ->first() ?? (object) [];
    }

    /**
     * Hitung Nilai Akhir (NA) siswa untuk satu mapel sesuai rumus Excel:
     *   NA = AVERAGE(rerata_semua_TH_antar_TP, rerata_semua_UH_antar_TP, nilai_AS)
     *
     * Komponen yang null/kosong diabaikan (tidak ikut average).
     *
     * @param  \Illuminate\Support\Collection  $nilaiTps  Collection of NilaiTp records
     * @return float|null
     */
    public function hitungNilaiAkhirSiswa($nilaiTps): ?float
    {
        // Step 1: rerata TH global = rata-rata dari semua per-TP rerata TH
        $thValues = $nilaiTps->map(function ($n) {
            $th1 = $n->nilai_th1 !== null ? (float) $n->nilai_th1 : null;
            $th2 = $n->nilai_th2 !== null ? (float) $n->nilai_th2 : null;
            if ($th1 === null && $th2 === null) return null;
            $vals = array_filter([$th1, $th2], fn($v) => $v !== null);
            return array_sum($vals) / count($vals);
        })->filter(fn($v) => $v !== null);

        $totalRerataTh = $thValues->isNotEmpty() ? $thValues->avg() : null;

        // Step 2: rerata UH global = rata-rata dari semua per-TP rerata UH
        $uhValues = $nilaiTps->map(function ($n) {
            $uh1 = $n->nilai_uh1 !== null ? (float) $n->nilai_uh1 : null;
            $uh2 = $n->nilai_uh2 !== null ? (float) $n->nilai_uh2 : null;
            if ($uh1 === null && $uh2 === null) return null;
            $vals = array_filter([$uh1, $uh2], fn($v) => $v !== null);
            return array_sum($vals) / count($vals);
        })->filter(fn($v) => $v !== null);

        $totalRerataUh = $uhValues->isNotEmpty() ? $uhValues->avg() : null;

        // Step 3: AS (satu nilai per siswa — ambil dari baris pertama yang ada)
        $asValue = null;
        foreach ($nilaiTps as $n) {
            $as = $n->attributes['nilai_as'] ?? null;
            if ($as !== null && $as !== '') {
                $asValue = (float) $as;
                break;
            }
        }

        // Step 4: NA = AVERAGE komponen yang terisi
        $components = array_filter([$totalRerataTh, $totalRerataUh, $asValue], fn($v) => $v !== null);

        if (empty($components)) return null;

        return round(array_sum($components) / count($components), 0);
    }

    /**
     * Hitung nilai akhir mapel (rata-rata semua TP dalam mapel tersebut untuk siswa).
     */
    public function getNilaiAkhirMapel(int $siswaId, int $mapelId): ?float
    {
        $avg = DB::table('nilai_tp')
            ->where('id_siswa', $siswaId)
            ->where('id_mapel', $mapelId)
            ->whereNotNull('nilai_akhir_tp')
            ->avg('nilai_akhir_tp');

        return $avg !== null ? round((float) $avg, 2) : null;
    }

    /**
     * Konversi nilai angka ke predikat huruf.
     */
    public function getPredikat(float $nilai): string
    {
        return match(true) {
            $nilai >= 90 => 'A',
            $nilai >= 80 => 'B',
            $nilai >= 70 => 'C',
            $nilai >= 60 => 'D',
            default      => 'E',
        };
    }

    /**
     * Hitung statistik kelas perwalian secara keseluruhan.
     */
    public function calculateClassStatistics(int $kelasId): array
    {
        $stats = DB::table('nilai_tp as nt')
            ->select([
                DB::raw('AVG(nt.nilai_akhir_tp) as average'),
                DB::raw('MAX(nt.nilai_akhir_tp) as highest'),
                DB::raw('MIN(nt.nilai_akhir_tp) as lowest'),
            ])
            ->join('siswa as s', 'nt.id_siswa', '=', 's.id_siswa')
            ->where('s.id_kelas', $kelasId)
            ->whereNotNull('nt.nilai_akhir_tp')
            ->first();

        return [
            'average' => $stats ? (float) $stats->average : 0.0,
            'highest' => $stats ? (float) $stats->highest : 0.0,
            'lowest'  => $stats ? (float) $stats->lowest  : 0.0,
        ];
    }

    /**
     * Hitung distribusi predikat nilai kelas perwalian.
     */
    public function getGradeDistribution(int $kelasId): array
    {
        $distribution = DB::table('nilai_tp as nt')
            ->select([
                DB::raw('SUM(CASE WHEN nt.nilai_akhir_tp >= 90 THEN 1 ELSE 0 END) as A'),
                DB::raw('SUM(CASE WHEN nt.nilai_akhir_tp >= 80 AND nt.nilai_akhir_tp < 90 THEN 1 ELSE 0 END) as B'),
                DB::raw('SUM(CASE WHEN nt.nilai_akhir_tp >= 70 AND nt.nilai_akhir_tp < 80 THEN 1 ELSE 0 END) as C'),
                DB::raw('SUM(CASE WHEN nt.nilai_akhir_tp >= 60 AND nt.nilai_akhir_tp < 70 THEN 1 ELSE 0 END) as D'),
                DB::raw('SUM(CASE WHEN nt.nilai_akhir_tp < 60 THEN 1 ELSE 0 END) as E'),
            ])
            ->join('siswa as s', 'nt.id_siswa', '=', 's.id_siswa')
            ->where('s.id_kelas', $kelasId)
            ->whereNotNull('nt.nilai_akhir_tp')
            ->first();

        return [
            'A' => $distribution ? (int) $distribution->A : 0,
            'B' => $distribution ? (int) $distribution->B : 0,
            'C' => $distribution ? (int) $distribution->C : 0,
            'D' => $distribution ? (int) $distribution->D : 0,
            'E' => $distribution ? (int) $distribution->E : 0,
        ];
    }
}
