<?php

namespace App\Http\Controllers\WaliKelas;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\NilaiTp;
use App\Models\GuruMapel;
use App\Models\JadwalPelajaran;
use App\Services\GradeCalculationService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    protected $gradeService;

    public function __construct(GradeCalculationService $gradeService)
    {
        $this->gradeService = $gradeService;
    }

    public function index(): View
    {
        $waliKelas = Auth::user()->guru;
        $kelas = Kelas::where('id_wali_kelas', $waliKelas->id_guru)->first();

        // ── Jadwal Mengajar (Mingguan) ─────────────────────────────────────
        $today  = date('l');
        $dayMap = [
            'Monday'    => 'Senin',
            'Tuesday'   => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday'  => 'Kamis',
            'Friday'    => 'Jumat',
            'Saturday'  => 'Sabtu',
            'Sunday'    => 'Ahad',
        ];
        $hariIni = $dayMap[$today] ?? 'Senin';

        $hasIdJam = \Illuminate\Support\Facades\Schema::hasColumn('jadwal_pelajaran', 'id_jam');
        $orderByCol = $hasIdJam ? 'id_jam' : 'jam_mulai';

        $weeklySchedules = JadwalPelajaran::with(['kelas', 'mapel', 'jam', 'ruangan'])
            ->where('id_guru', $waliKelas->id_guru)
            ->orderByRaw("FIELD(hari, 'Sabtu', 'Ahad', 'Senin', 'Selasa', 'Rabu', 'Kamis')")
            ->orderBy($orderByCol)
            ->get()
            ->groupBy('hari');

        // ── Penugasan Mengajar ─────────────────────────────────────────────
        $guruMapels = GuruMapel::with('mapel', 'kelas')
            ->where('id_guru', $waliKelas->id_guru)
            ->where('status', 'aktif')
            ->get();

        if (!$kelas) {
            return view('walikelas.dashboard', [
                'kelas'           => null,
                'totalSiswa'      => 0,
                'gradeStats'      => [],
                'gradeDistribution' => [],
                'weeklySchedules' => $weeklySchedules,
                'hariIni'         => $hariIni,
                'guruMapels'      => $guruMapels,
                'raporStats'      => ['dikirim' => 0, 'diverifikasi' => 0, 'draft' => 0],
            ]);
        }

        $totalSiswa = Siswa::where('id_kelas', $kelas->id_kelas)->count();

        $gradeStats      = $this->gradeService->calculateClassStatistics($kelas->id_kelas);
        $gradeDistribution = $this->gradeService->getGradeDistribution($kelas->id_kelas);

        // ── Status Rapor per kelas ────────────────────────────────────────
        $activeTahunAjaran = \App\Models\TahunAjaran::activeOrAbort();
        $raporStatuses = \App\Models\RaporStatus::where('id_semester', $activeTahunAjaran->id_semester)
            ->whereIn('id_siswa', Siswa::where('id_kelas', $kelas->id_kelas)->pluck('id_siswa'))
            ->get();

        $raporStats = [
            'dikirim'     => $raporStatuses->where('status', 'dikirim')->count(),
            'diverifikasi'=> $raporStatuses->where('status', 'diverifikasi')->count(),
            'draft'       => $totalSiswa - $raporStatuses->whereIn('status', ['dikirim','diverifikasi'])->count(),
        ];

        return view('walikelas.dashboard', compact(
            'kelas',
            'totalSiswa',
            'gradeStats',
            'gradeDistribution',
            'weeklySchedules',
            'hariIni',
            'guruMapels',
            'raporStats'
        ));
    }

    public function selectKelasRanking(): View
    {
        $waliKelas = Auth::user()->guru;
        $kelas = Kelas::where('id_wali_kelas', $waliKelas->id_guru)->get();
        $activeTahunAjaran = \App\Models\TahunAjaran::activeOrAbort();

        return view('walikelas.ranking.select-kelas', compact('kelas', 'activeTahunAjaran'));
    }

    public function ranking($id_kelas): View
    {
        $waliKelas = Auth::user()->guru;
        $kelas = Kelas::where('id_wali_kelas', $waliKelas->id_guru)
            ->where('id_kelas', $id_kelas)
            ->firstOrFail();

        $rankings = $this->gradeService->calculateRanking($id_kelas);

        return view('walikelas.ranking', compact('kelas', 'rankings'));
    }
}
