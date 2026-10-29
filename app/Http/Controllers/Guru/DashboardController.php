<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\GuruMapel;
use App\Models\NilaiTp;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index(): View
    {
        $guru = Auth::user()->guru;

        abort_if(!$guru, 404, 'Profil guru tidak ditemukan untuk akun ini.');

        /** @var GuruMapel $assignmentsQuery */
        $assignmentsQuery = GuruMapel::with('mapel', 'kelas')->where('guru_id', $guru->getKey());

        // Tabel beberapa skema tidak punya kolom status penugasan; kolom tersebut opsional di sini.
        if (Schema::hasColumn((new GuruMapel)->getTable(), 'status')) {
            $assignmentsQuery->where('status', 'aktif');
        }

        $guruMapels = $assignmentsQuery->get();

        $totalGrades = NilaiTp::whereHas('guruMapel', function ($query) use ($guru) {
            $query->where('guru_id', $guru->getKey());
        })->count();

        /** Skema seperti siakad-nilai bisa belum memiliki kolom workflow status_nilai */
        $nilaiUsesStatus = Schema::hasColumn((new NilaiTp)->getTable(), 'status_nilai');
        $draftGrades = $nilaiUsesStatus
            ? NilaiTp::whereHas('guruMapel', function ($query) use ($guru) {
                $query->where('guru_id', $guru->getKey());
            })->draft()->count()
            : 0;

        $submittedGrades = $nilaiUsesStatus
            ? NilaiTp::whereHas('guruMapel', function ($query) use ($guru) {
                $query->where('guru_id', $guru->getKey());
            })->submitted()->count()
            : 0;

        $lockedGrades = $nilaiUsesStatus
            ? NilaiTp::whereHas('guruMapel', function ($query) use ($guru) {
                $query->where('guru_id', $guru->getKey());
            })->locked()->count()
            : 0;

        // Today's teaching schedule
        $today = date('l');
        $dayMap = [
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
            'Sunday' => 'Minggu',
        ];
        $hariIni = $dayMap[$today] ?? 'Senin';

        $hasIdJam = \Illuminate\Support\Facades\Schema::hasColumn('jadwal_pelajaran', 'id_jam');
        $orderByCol = $hasIdJam ? 'id_jam' : 'jam_mulai';

        $schedulesToday = \App\Models\JadwalPelajaran::with(['kelas', 'mapel', 'jam', 'ruangan'])
            ->where('id_guru', $guru->id_guru)
            ->where('hari', $hariIni)
            ->orderBy($orderByCol)
            ->get();

        // Get full weekly schedule grouped by day (Sabtu s.d. Kamis)
        $weeklySchedules = \App\Models\JadwalPelajaran::with(['kelas', 'mapel', 'jam', 'ruangan'])
            ->where('id_guru', $guru->id_guru)
            ->orderByRaw("FIELD(hari, 'Sabtu', 'Ahad', 'Senin', 'Selasa', 'Rabu', 'Kamis')")
            ->orderBy($orderByCol)
            ->get()
            ->groupBy('hari');

        $totalKelasMengajar = $guruMapels->pluck('id_kelas')->unique()->count();
        
        $totalSiswaMengajar = \App\Models\Siswa::whereIn('id_kelas', $guruMapels->pluck('id_kelas')->unique())->count();

        // Get rankings for classes taught by this guru with dynamic competition ranking (tie-break)
        $kelasIds = $guruMapels->pluck('id_kelas')->unique();
        
        $siswaAverages = \App\Models\NilaiTp::whereNotNull('nilai_akhir_tp')
            ->whereIn('id_siswa', function($query) use ($kelasIds) {
                $query->select('id_siswa')->from('siswa')->whereIn('id_kelas', $kelasIds);
            })
            ->select('id_siswa')
            ->selectRaw('ROUND(AVG(nilai_akhir_tp), 1) as rata_rata')
            ->groupBy('id_siswa')
            ->get()
            ->pluck('rata_rata', 'id_siswa');

        $allRankedSiswa = \App\Models\Siswa::with('kelas')
            ->whereIn('id_kelas', $kelasIds)
            ->get()
            ->map(function($siswa) use ($siswaAverages) {
                $siswa->rata_rata = $siswaAverages[$siswa->id_siswa] ?? 0;
                return $siswa;
            })
            ->sortBy('nama') // Stable alphabetical fallback for same grades
            ->sortByDesc('rata_rata') // Primary sort by average grade
            ->values();

        // Calculate rankings with tie handling (Competition ranking)
        $currentRank = 1;
        $prevAverage = null;
        foreach ($allRankedSiswa as $index => $siswa) {
            if ($prevAverage !== null && $siswa->rata_rata < $prevAverage) {
                $currentRank = $index + 1;
            }
            $siswa->calculated_rank = $currentRank;
            $prevAverage = $siswa->rata_rata;
        }

        $rankings = $allRankedSiswa->take(5);

        return view('guru.dashboard', compact(
            'guruMapels',
            'totalGrades',
            'draftGrades',
            'submittedGrades',
            'lockedGrades',
            'schedulesToday',
            'weeklySchedules',
            'hariIni',
            'totalKelasMengajar',
            'totalSiswaMengajar',
            'rankings'
        ));
    }
}
