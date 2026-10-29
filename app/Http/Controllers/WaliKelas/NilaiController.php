<?php

namespace App\Http\Controllers\WaliKelas;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\NilaiTp;
use App\Models\GuruMapel;
use App\Models\TahunAjaran;
use App\Services\GradeCalculationService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NilaiController extends Controller
{
    /**
     * Show grade summary per subject + student list for a class.
     */
    public function index($id_kelas): View
    {
        $waliKelas = Auth::user()->guru;
        $kelas = Kelas::where('id_wali_kelas', $waliKelas->id_guru)
            ->where('id_kelas', $id_kelas)
            ->firstOrFail();

        $activeTahunAjaran = TahunAjaran::activeOrAbort();

        // Get all subjects taught in this class (eager load)
        $guruMapels = GuruMapel::with(['mapel', 'guru'])
            ->where('id_kelas', $id_kelas)
            ->where('id_tahun_ajaran', $activeTahunAjaran->id)
            ->get();

        $siswas = Siswa::where('id_kelas', $id_kelas)->get();
        $totalStudents = $siswas->count();

        // Grade summary per subject using grouped queries to avoid N+1
        $mapelIds = $guruMapels->pluck('id_mapel');
        $siswaIds = $siswas->pluck('id_siswa');
        
        $nilaiTps = NilaiTp::whereIn('id_mapel', $mapelIds)
            ->whereIn('id_siswa', $siswaIds)
            ->get();

        $gradeSummaries = [];
        foreach ($guruMapels as $guruMapel) {
            $mapelNilai = $nilaiTps->where('id_mapel', $guruMapel->id_mapel);

            $submittedCount = $mapelNilai->whereIn('status_nilai', ['submitted', 'locked'])->unique('id_siswa')->count();
            $lockedCount = $mapelNilai->where('status_nilai', 'locked')->unique('id_siswa')->count();
            $hasGradeCount = $mapelNilai->whereNotNull('nilai_akhir_tp')->unique('id_siswa')->count();

            $gradeSummaries[$guruMapel->id] = [
                'total'                 => $totalStudents,
                'submitted'             => $submittedCount,
                'locked'                => $lockedCount,
                'has_grade'             => $hasGradeCount,
                'completion_percentage' => $totalStudents > 0 ? round(($hasGradeCount / $totalStudents) * 100, 2) : 0,
                'lock_percentage' => $totalStudents > 0 ? round(($lockedCount / $totalStudents) * 100, 2) : 0,
                'is_fully_locked' => $lockedCount >= $totalStudents && $totalStudents > 0,
                'is_all_submitted' => $submittedCount >= $totalStudents && $totalStudents > 0,
            ];
        }

        // Student list for this class with grade count
        $siswaList = Siswa::where('id_kelas', $id_kelas)
            ->orderBy('nama')
            ->get()
            ->map(function ($siswa) use ($activeTahunAjaran) {
                $siswa->mapel_count = NilaiTp::where('id_siswa', $siswa->id_siswa)
                    ->distinct('id_mapel')
                    ->count('id_mapel');
                return $siswa;
            });

        // Check if ALL mapels are fully locked (for rapor unlock check)
        $totalMapels = $guruMapels->count();
        $lockedMapels = collect($gradeSummaries)->filter(fn($s) => $s['is_fully_locked'])->count();
        $allNilaiLocked = $totalMapels > 0 && $lockedMapels === $totalMapels;

        return view('walikelas.nilai.index', compact(
            'kelas',
            'guruMapels',
            'gradeSummaries',
            'activeTahunAjaran',
            'siswaList',
            'allNilaiLocked'
        ));
    }

    /**
     * Show detailed grades for a single student.
     * Returns a partial view for AJAX requests, full view otherwise.
     */
    public function detail(Request $request, $id_siswa, GradeCalculationService $gradeService)
    {
        $waliKelas = Auth::user()->guru;
        $siswa = Siswa::with(['kelas', 'user'])
            ->whereHas('kelas', function($query) use ($waliKelas) {
                $query->where('id_wali_kelas', $waliKelas->id_guru);
            })
            ->findOrFail($id_siswa);

        $activeTahunAjaran = TahunAjaran::activeOrAbort();

        // Get all grades for this student, grouped by mapel
        $nilaiTps = NilaiTp::with(['guruMapel.mapel', 'guruMapel.guru', 'tp'])
            ->where('id_siswa', $id_siswa)
            ->whereHas('guruMapel', function($query) use ($activeTahunAjaran) {
                $query->where('id_semester', $activeTahunAjaran->id_semester)
                      ->where('id_tahun_ajaran', $activeTahunAjaran->id_tahun_ajaran);
            })
            ->get()
            ->groupBy('id_mapel');

        // Generate descriptions for each subject
        $descriptions = [];
        foreach ($nilaiTps as $mapelId => $grades) {
            $descriptions[$mapelId] = $gradeService->generateDescription($id_siswa, $mapelId);
        }

        // For AJAX requests, return only the partial HTML
        if ($request->ajax()) {
            return view('walikelas.nilai.detail-partial', compact(
                'siswa',
                'nilaiTps',
                'activeTahunAjaran',
                'descriptions'
            ))->render();
        }

        // For regular requests, return full page
        return view('walikelas.nilai.detail', compact(
            'siswa',
            'nilaiTps',
            'activeTahunAjaran',
            'descriptions'
        ));
    }

    /**
     * Show detailed grades of all students for a single subject (guru_mapel).
     * Returns a partial view for AJAX requests.
     */
    public function detailMapel(Request $request, $guru_mapel_id, GradeCalculationService $gradeService)
    {
        $waliKelas = Auth::user()->guru;
        $guruMapel = GuruMapel::with(['mapel', 'guru', 'kelas'])
            ->whereHas('kelas', function($query) use ($waliKelas) {
                $query->where('id_wali_kelas', $waliKelas->id_guru);
            })
            ->findOrFail($guru_mapel_id);

        $activeTahunAjaran = TahunAjaran::activeOrAbort();

        $siswas = Siswa::where('id_kelas', $guruMapel->id_kelas)
            ->orderBy('nama')
            ->get();

        $summaryData = [];
        foreach ($siswas as $siswa) {
            $siswaId = $siswa->id_siswa;

            // Get all NilaiTp for this student + mapel
            $nilaiTps = NilaiTp::where('id_siswa', $siswaId)
                ->where('id_mapel', $guruMapel->id_mapel)
                ->where('id_semester', $guruMapel->id_semester)
                ->get();

            // Hitung NA sesuai rumus Excel: AVERAGE(rerata_TH, rerata_UH, AS)
            $nilaiAkhir = $nilaiTps->isNotEmpty()
                ? $gradeService->hitungNilaiAkhirSiswa($nilaiTps)
                : null;

            // Generate description
            $deskripsi = $nilaiTps->isNotEmpty()
                ? $gradeService->generateDescription($siswaId, $guruMapel->id_mapel)
                : 'Belum ada nilai yang diinput.';

            // Check status
            $status = $nilaiTps->first()?->status_nilai ?? 'belum diinput';

            $summaryData[] = [
                'siswa'      => $siswa,
                'nilai_akhir'=> $nilaiAkhir,
                'deskripsi'  => $deskripsi,
                'status'     => $status,
                'jumlah_tp'  => $nilaiTps->count(),
            ];
        }

        if ($request->ajax()) {
            return view('walikelas.nilai.detail-mapel-partial', compact(
                'guruMapel',
                'summaryData',
                'activeTahunAjaran'
            ))->render();
        }

        return abort(404);
    }

    /**
     * Lock grades for a single subject (guru_mapel) — only locks submitted/draft grades.
     * After lock, guru cannot edit anymore until wali kelas unlocks.
     */
    public function lock(Request $request): RedirectResponse
    {
        $request->validate([
            'guru_mapel_id' => 'required|exists:guru_mapel,id_guru_mapel',
        ]);

        $waliKelas = Auth::user()->guru;

        $guruMapel = GuruMapel::whereHas('kelas', function($query) use ($waliKelas) {
                $query->where('id_wali_kelas', $waliKelas->id_guru);
            })
            ->findOrFail($request->guru_mapel_id);

        $updated = NilaiTp::where('id_mapel', $guruMapel->id_mapel)
            ->whereIn('id_siswa', function($query) use ($guruMapel) {
                $query->select('id_siswa')->from('siswa')->where('id_kelas', $guruMapel->id_kelas);
            })
            ->whereIn('status_nilai', ['draft', 'submitted', 'approved'])
            ->update(['status_nilai' => 'locked']);

        if ($updated === 0) {
            return back()->with('error', 'Tidak ada nilai yang dapat dikunci. Mungkin sudah terkunci sebelumnya.');
        }

        return back()->with('success', 'Nilai ' . ($guruMapel->mapel->nama_mapel ?? '') . ' berhasil dikunci. Guru tidak dapat mengubah nilai lagi.');
    }

    /**
     * Lock ALL submitted/draft grades across all subjects in this class.
     */
    public function lockAll(Request $request): RedirectResponse
    {
        $request->validate([
            'id_kelas' => 'required|exists:kelas,id_kelas',
        ]);

        $waliKelas = Auth::user()->guru;

        // Verify this wali kelas manages this class
        $kelas = Kelas::where('id_wali_kelas', $waliKelas->id_guru)
            ->where('id_kelas', $request->id_kelas)
            ->firstOrFail();

        $updated = NilaiTp::whereIn('id_siswa', function($query) use ($kelas) {
                $query->select('id_siswa')->from('siswa')->where('id_kelas', $kelas->id_kelas);
            })
            ->whereIn('status_nilai', ['draft', 'submitted', 'approved'])
            ->update(['status_nilai' => 'locked']);

        if ($updated === 0) {
            return back()->with('error', 'Tidak ada nilai yang dapat dikunci. Semua nilai mungkin sudah terkunci atau belum ada nilai sama sekali.');
        }

        return back()->with('success', "Semua nilai kelas {$kelas->nama_kelas} berhasil dikunci ($updated data). Guru tidak dapat mengubah nilai lagi.");
    }

    /**
     * Unlock grades for a single subject and return them to draft (so guru can edit again).
     */
    public function unlock(Request $request): RedirectResponse
    {
        $request->validate([
            'guru_mapel_id' => 'required|exists:guru_mapel,id_guru_mapel',
        ]);

        $waliKelas = Auth::user()->guru;

        $guruMapel = GuruMapel::with('mapel')
            ->whereHas('kelas', function($query) use ($waliKelas) {
                $query->where('id_wali_kelas', $waliKelas->id_guru);
            })
            ->findOrFail($request->guru_mapel_id);

        $updated = NilaiTp::where('id_mapel', $guruMapel->id_mapel)
            ->whereIn('id_siswa', function($query) use ($guruMapel) {
                $query->select('id_siswa')->from('siswa')->where('id_kelas', $guruMapel->id_kelas);
            })
            ->where('status_nilai', 'locked')
            ->update(['status_nilai' => 'draft']);

        if ($updated === 0) {
            return back()->with('error', 'Tidak ada nilai terkunci yang dapat dibuka untuk mata pelajaran ini.');
        }

        return back()->with('success', 'Nilai ' . ($guruMapel->mapel->nama_mapel ?? '') . ' berhasil dibuka. Guru dapat mengubah nilai kembali.');
    }

    /**
     * Unlock ALL locked grades across all subjects in this class.
     */
    public function unlockAll(Request $request): RedirectResponse
    {
        $request->validate([
            'id_kelas' => 'required|exists:kelas,id_kelas',
        ]);

        $waliKelas = Auth::user()->guru;

        // Verify this wali kelas manages this class
        $kelas = Kelas::where('id_wali_kelas', $waliKelas->id_guru)
            ->where('id_kelas', $request->id_kelas)
            ->firstOrFail();

        $updated = NilaiTp::whereIn('id_siswa', function($query) use ($kelas) {
                $query->select('id_siswa')->from('siswa')->where('id_kelas', $kelas->id_kelas);
            })
            ->where('status_nilai', 'locked')
            ->update(['status_nilai' => 'draft']);

        if ($updated === 0) {
            return back()->with('error', 'Tidak ada nilai terkunci yang dapat dibuka.');
        }

        return back()->with('success', "Semua nilai kelas {$kelas->nama_kelas} berhasil dibuka ($updated data). Guru dapat mengubah nilai kembali.");
    }
}
