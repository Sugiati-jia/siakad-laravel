<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\GuruMapel;
use App\Models\Siswa;
use App\Models\TujuanPembelajaran;
use App\Models\NilaiTp;
use App\Models\TahunAjaran;
use App\Services\GradeCalculationService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NilaiController extends Controller
{
    protected GradeCalculationService $gradeService;

    public function __construct(GradeCalculationService $gradeService)
    {
        $this->gradeService = $gradeService;
    }

    public function selectKelas(): View
    {
        $guru = Auth::user()->guru;

        $guruMapels = GuruMapel::with('kelas', 'mapel')
            ->where('id_guru', $guru->id_guru)
            ->where('status', 'aktif')
            ->get()
            ->unique('id_kelas');

        return view('guru.nilai.select-kelas', compact('guruMapels'));
    }

    public function selectMapel($id_kelas): View
    {
        $guru = Auth::user()->guru;
        
        $guruMapels = GuruMapel::with('mapel')
            ->where('id_guru', $guru->id_guru)
            ->where('id_kelas', $id_kelas)
            ->where('status', 'aktif')
            ->get();

        $kelas = \App\Models\Kelas::findOrFail($id_kelas);

        return view('guru.nilai.select-mapel', compact('guruMapels', 'kelas'));
    }

    public function input($id_kelas, $mapel_id): View
    {
        $guru = Auth::user()->guru;
        
        $guruMapel = GuruMapel::where('id_guru', $guru->id_guru)
            ->where('id_kelas', $id_kelas)
            ->where('id_mapel', $mapel_id)
            ->where('status', 'aktif')
            ->firstOrFail();

        $kelas = \App\Models\Kelas::findOrFail($id_kelas);
        $mapel = \App\Models\Mapel::findOrFail($mapel_id);

        $siswas = Siswa::where('id_kelas', $id_kelas)
            ->with('user')
            ->orderBy('nama')
            ->get();

        $tujuanPembelajarans = TujuanPembelajaran::where('id_mapel', $mapel_id)
            ->where('tingkat', $kelas->tingkat)
            ->where('id_semester', $guruMapel->id_semester)
            ->orderBy('kode_tp')
            ->get();

        // Get existing grades
        $existingGrades = NilaiTp::where('id_guru_mapel', $guruMapel->id_guru_mapel)
            ->where('id_semester', $guruMapel->id_semester)
            ->get()
            ->keyBy(function($item) {
                return $item->id_siswa . '_' . $item->id_tujuan_pembelajaran;
            });

        $activeTahunAjaran = TahunAjaran::activeOrAbort();

        $isLocked = NilaiTp::where('id_guru_mapel', $guruMapel->id_guru_mapel)
            ->where('id_semester', $guruMapel->id_semester)
            ->where('status_nilai', 'locked')
            ->exists();

        return view('guru.nilai.input', compact(
            'guruMapel',
            'kelas',
            'mapel',
            'siswas',
            'tujuanPembelajarans',
            'existingGrades',
            'activeTahunAjaran',
            'isLocked'
        ));
    }

    public function tablePartial($id_kelas, $mapel_id)
    {
        $guru = Auth::user()->guru;
        
        $guruMapel = GuruMapel::where('id_guru', $guru->id_guru)
            ->where('id_kelas', $id_kelas)
            ->where('id_mapel', $mapel_id)
            ->where('status', 'aktif')
            ->firstOrFail();

        $kelas = \App\Models\Kelas::findOrFail($id_kelas);
        $mapel = \App\Models\Mapel::findOrFail($mapel_id);

        $siswas = Siswa::where('id_kelas', $id_kelas)
            ->with('user')
            ->orderBy('nama')
            ->get();

        $tujuanPembelajarans = TujuanPembelajaran::where('id_mapel', $mapel_id)
            ->where('tingkat', $kelas->tingkat)
            ->where('id_semester', $guruMapel->id_semester)
            ->orderBy('kode_tp')
            ->get();

        $existingGrades = NilaiTp::where('id_guru_mapel', $guruMapel->id_guru_mapel)
            ->where('id_semester', $guruMapel->id_semester)
            ->get()
            ->keyBy(function($item) {
                return $item->id_siswa . '_' . $item->id_tujuan_pembelajaran;
            });

        $activeTahunAjaran = TahunAjaran::activeOrAbort();

        $isLocked = NilaiTp::where('id_guru_mapel', $guruMapel->id_guru_mapel)
            ->where('id_semester', $guruMapel->id_semester)
            ->where('status_nilai', 'locked')
            ->exists();

        return view('guru.nilai.table-partial', compact(
            'guruMapel',
            'kelas',
            'mapel',
            'siswas',
            'tujuanPembelajarans',
            'existingGrades',
            'activeTahunAjaran',
            'isLocked'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'guru_mapel_id' => 'required|exists:guru_mapel,id_guru_mapel',
            'id_semester' => 'required|in:1,2',
            'grades' => 'required|array',
            'grades.*.id_siswa' => 'required|exists:siswa,id_siswa',
            'grades.*.id_tujuan_pembelajaran' => 'required|exists:tujuan_pembelajaran,id_tujuan_pembelajaran',
            'grades.*.nilai_th1' => 'nullable|numeric|min:0|max:100',
            'grades.*.nilai_th2' => 'nullable|numeric|min:0|max:100',
            'grades.*.nilai_uh1' => 'nullable|numeric|min:0|max:100',
            'grades.*.nilai_uh2' => 'nullable|numeric|min:0|max:100',
            'grades.*.nilai_as' => 'nullable|numeric|min:0|max:100'
        ], [
            'grades.*.nilai_th1.min' => 'Nilai Tugas Harian 1 minimal adalah 0.',
            'grades.*.nilai_th2.min' => 'Nilai Tugas Harian 2 minimal adalah 0.',
            'grades.*.nilai_uh1.min' => 'Nilai Ulangan Harian 1 minimal adalah 0.',
            'grades.*.nilai_uh2.min' => 'Nilai Ulangan Harian 2 minimal adalah 0.',
            'grades.*.nilai_as.min'  => 'Nilai Akhir Semester minimal adalah 0.',
        ]);

        $guru = Auth::user()->guru;
        $guruMapel = GuruMapel::with('kelas', 'mapel')->findOrFail($request->guru_mapel_id);

        // Verify this guru owns this assignment
        if ($guruMapel->id_guru != $guru->id_guru) {
            abort(403, 'Unauthorized action.');
        }

        // Check if grades are locked — guru cannot edit locked grades
        $lockedCount = NilaiTp::where('id_guru_mapel', $guruMapel->id_guru_mapel)
            ->where('status_nilai', 'locked')
            ->count();

        if ($lockedCount > 0) {
            $errorMsg = 'Nilai sudah dikunci oleh Wali Kelas dan tidak dapat diubah. ';
            $errorMsg .= 'Hubungi Wali Kelas jika perlu melakukan perubahan.';
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $errorMsg], 403);
            }
            return back()->with('error', $errorMsg);
        }

        // ── Aturan Pengisian Nilai (Relaxed for Draft Saving) ───────────────────
        // Constraints removed to allow saving partial grades as draft.
        // ─────────────────────────────────────────────────────────────────────────

        DB::beginTransaction();
        try {
            foreach ($request->grades as $gradeData) {
                // Normalize empty strings to null
                $th1 = (isset($gradeData['nilai_th1']) && $gradeData['nilai_th1'] !== '') ? $gradeData['nilai_th1'] : null;
                $th2 = (isset($gradeData['nilai_th2']) && $gradeData['nilai_th2'] !== '') ? $gradeData['nilai_th2'] : null;
                $uh1 = (isset($gradeData['nilai_uh1']) && $gradeData['nilai_uh1'] !== '') ? $gradeData['nilai_uh1'] : null;
                $uh2 = (isset($gradeData['nilai_uh2']) && $gradeData['nilai_uh2'] !== '') ? $gradeData['nilai_uh2'] : null;
                $as  = (isset($gradeData['nilai_as'])  && $gradeData['nilai_as']  !== '') ? $gradeData['nilai_as']  : null;

                // If at least one grade field is filled, update or create
                if ($th1 !== null || $th2 !== null || $uh1 !== null || $uh2 !== null || $as !== null) {
                    $components = array_filter([$th1, $th2, $uh1, $uh2, $as], fn($v) => $v !== null);
                    $nilaiAkhirTp = count($components) > 0 ? array_sum($components) / count($components) : null;

                    NilaiTp::updateOrCreate(
                        [
                            'id_siswa' => $gradeData['id_siswa'],
                            'id_guru_mapel' => $guruMapel->id_guru_mapel,
                            'id_tujuan_pembelajaran' => $gradeData['id_tujuan_pembelajaran'],
                            'id_semester' => $request->id_semester,
                        ],
                        [
                            'id_mapel' => $guruMapel->id_mapel,
                            'nilai_th1' => $th1,
                            'nilai_th2' => $th2,
                            'nilai_uh1' => $uh1,
                            'nilai_uh2' => $uh2,
                            'nilai_as' => $as,
                            'nilai_akhir_tp' => $nilaiAkhirTp,
                            'status_nilai' => 'draft'
                        ]
                    );
                } else {
                    // Delete the record if it exists and all fields are empty
                    NilaiTp::where([
                        'id_siswa' => $gradeData['id_siswa'],
                        'id_guru_mapel' => $guruMapel->id_guru_mapel,
                        'id_tujuan_pembelajaran' => $gradeData['id_tujuan_pembelajaran'],
                        'id_semester' => $request->id_semester,
                    ])->delete();
                }
            }

            DB::commit();

            // Log Activity (only for manual saves to prevent auto-save flooding)
            if (!$request->expectsJson()) {
                \App\Models\ActivityLog::create([
                    'user_id' => Auth::id(),
                    'action' => 'Simpan Nilai',
                    'description' => 'Menyimpan nilai siswa untuk kelas ' . $guruMapel->kelas->nama_kelas . ' - ' . $guruMapel->mapel->nama_mapel,
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent()
                ]);
            }

            // If AJAX request (auto-save), return JSON
            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Nilai berhasil disimpan']);
            }

            return redirect()->route('guru.nilai.summary', [
                'kelas_id' => $guruMapel->id_kelas,
                'mapel_id' => $guruMapel->id_mapel
            ])->with('success', 'Nilai berhasil disimpan!');
        } catch (\Exception $e) {
            DB::rollBack();

            // Log Error to system logs for Admin's Log Error view
            \Illuminate\Support\Facades\Log::error('Save Nilai Exception: ' . $e->getMessage(), [
                'exception' => $e,
                'user_id' => Auth::id(),
                'guru_mapel_id' => $request->guru_mapel_id
            ]);

            if (!$request->expectsJson()) {
                \App\Models\ActivityLog::create([
                    'user_id' => Auth::id(),
                    'action' => 'Simpan Nilai Gagal',
                    'description' => 'Gagal menyimpan nilai siswa: ' . $e->getMessage(),
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent()
                ]);
            }

            throw $e;
        }
    }

    /**
     * Halaman ringkasan nilai: tabel Nama Siswa, Nilai Akhir, Deskripsi + Edit/Delete.
     */
    public function summary($id_kelas, $mapel_id): View
    {
        $guru = Auth::user()->guru;

        $guruMapel = GuruMapel::where('id_guru', $guru->id_guru)
            ->where('id_kelas', $id_kelas)
            ->where('id_mapel', $mapel_id)
            ->where('status', 'aktif')
            ->firstOrFail();

        $kelas = \App\Models\Kelas::findOrFail($id_kelas);
        $mapel = \App\Models\Mapel::findOrFail($mapel_id);

        $siswas = Siswa::where('id_kelas', $id_kelas)
            ->orderBy('nama')
            ->get();

        // Build summary data for each student
        $summaryData = [];
        foreach ($siswas as $siswa) {
            $siswaId = $siswa->id_siswa;

            // Get all NilaiTp for this student + mapel
            $nilaiTps = NilaiTp::where('id_siswa', $siswaId)
                ->where('id_mapel', $mapel_id)
                ->where('id_guru_mapel', $guruMapel->id_guru_mapel)
                ->where('id_semester', $guruMapel->id_semester)
                ->get();

            // Hitung NA sesuai rumus Excel: AVERAGE(rerata_TH, rerata_UH, AS)
            $nilaiAkhir = $nilaiTps->isNotEmpty()
                ? $this->gradeService->hitungNilaiAkhirSiswa($nilaiTps)
                : null;

            // Generate description using GradeCalculationService
            $deskripsi = $nilaiTps->isNotEmpty()
                ? $this->gradeService->generateDescription($siswaId, $mapel_id)
                : 'Belum ada data nilai.';

            // Check status
            $status = $nilaiTps->first()->status_nilai ?? 'draft';

            $summaryData[] = [
                'siswa'      => $siswa,
                'nilai_akhir'=> $nilaiAkhir,
                'deskripsi'  => $deskripsi,
                'status'     => $status,
                'jumlah_tp'  => $nilaiTps->count(),
            ];
        }

        $tpCount = TujuanPembelajaran::where('id_mapel', $mapel_id)
            ->where('tingkat', $kelas->tingkat)
            ->where('id_semester', $guruMapel->id_semester)
            ->count();

        $activeTahunAjaran = TahunAjaran::activeOrAbort();

        $isLocked = NilaiTp::where('id_guru_mapel', $guruMapel->id_guru_mapel)
            ->where('id_semester', $guruMapel->id_semester)
            ->where('status_nilai', 'locked')
            ->exists();

        return view('guru.nilai.summary', compact(
            'guruMapel',
            'kelas',
            'mapel',
            'summaryData',
            'activeTahunAjaran',
            'tpCount',
            'isLocked'
        ));
    }

    /**
     * Hapus semua nilai TP seorang siswa untuk mapel tertentu.
     */
    public function destroyStudentGrades($id_siswa, $mapel_id): RedirectResponse
    {
        $guru = Auth::user()->guru;

        // Verify guru owns this mapel
        $guruMapel = GuruMapel::where('id_guru', $guru->id_guru)
            ->where('id_mapel', $mapel_id)
            ->where('status', 'aktif')
            ->firstOrFail();

        // Check if grades are locked
        $isLocked = NilaiTp::where('id_siswa', $id_siswa)
            ->where('id_mapel', $mapel_id)
            ->where('id_guru_mapel', $guruMapel->id_guru_mapel)
            ->where('status_nilai', 'locked')
            ->exists();

        if ($isLocked) {
            return back()->with('error', 'Nilai sudah dikunci oleh Wali Kelas dan tidak dapat dihapus.');
        }

        $deleted = NilaiTp::where('id_siswa', $id_siswa)
            ->where('id_mapel', $mapel_id)
            ->where('id_guru_mapel', $guruMapel->id_guru_mapel)
            ->delete();

        return back()->with('success', "Nilai siswa berhasil dihapus ($deleted data).");
    }

    /**
     * Hapus semua nilai untuk mapel dan kelas tertentu.
     */
    public function destroyAllGrades($id_kelas, $mapel_id): RedirectResponse
    {
        $guru = Auth::user()->guru;

        // Verify guru owns this mapel
        $guruMapel = GuruMapel::where('id_guru', $guru->id_guru)
            ->where('id_kelas', $id_kelas)
            ->where('id_mapel', $mapel_id)
            ->where('status', 'aktif')
            ->firstOrFail();

        // Check if grades are locked
        $isLocked = NilaiTp::where('id_guru_mapel', $guruMapel->id_guru_mapel)
            ->where('id_semester', $guruMapel->id_semester)
            ->where('status_nilai', 'locked')
            ->exists();

        if ($isLocked) {
            return back()->with('error', 'Nilai sudah dikunci oleh Wali Kelas dan tidak dapat dihapus.');
        }

        $deleted = NilaiTp::where('id_guru_mapel', $guruMapel->id_guru_mapel)
            ->where('id_semester', $guruMapel->id_semester)
            ->delete();

        // Log Activity
        \App\Models\ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'Hapus Semua Nilai',
            'description' => 'Menghapus semua nilai untuk kelas ' . $guruMapel->kelas->nama_kelas . ' - ' . $guruMapel->mapel->nama_mapel,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);

        return back()->with('success', "Seluruh nilai kelas ini berhasil dihapus ($deleted data). Anda dapat mengimpor nilai baru.");
    }

    public function updateStatus(Request $request): RedirectResponse
    {
        $request->validate([
            'guru_mapel_id' => 'required|exists:guru_mapel,id_guru_mapel',
            'id_semester' => 'required|in:1,2',
            'status' => 'required|in:submitted,locked'
        ]);

        $guru = Auth::user()->guru;
        $guruMapel = GuruMapel::with('kelas')->findOrFail($request->guru_mapel_id);

        // Verify this guru owns this assignment
        if ($guruMapel->id_guru != $guru->id_guru) {
            abort(403, 'Unauthorized action.');
        }

        // Check if grades are locked
        $isLocked = NilaiTp::where('id_guru_mapel', $guruMapel->id_guru_mapel)
            ->where('id_semester', $request->id_semester)
            ->where('status_nilai', 'locked')
            ->exists();

        if ($isLocked) {
            return back()->with('error', 'Nilai sudah dikunci oleh Wali Kelas dan tidak dapat diubah statusnya.');
        }

        // Limit Check: [3, 15] TPs required to submit
        $tpCount = TujuanPembelajaran::where('id_mapel', $guruMapel->id_mapel)
            ->where('tingkat', $guruMapel->kelas->tingkat)
            ->where('id_semester', $request->id_semester)
            ->count();

        if ($tpCount < 3) {
            return back()->with('error', 'Gagal mensubmit nilai. Jumlah Tujuan Pembelajaran (TP) untuk semester ini kurang dari batas minimum (minimal 3 TP). Saat ini baru ada ' . $tpCount . ' TP.');
        }

        if ($tpCount > 15) {
            return back()->with('error', 'Gagal mensubmit nilai. Jumlah Tujuan Pembelajaran (TP) untuk semester ini melebihi batas maksimum (maksimal 15 TP). Saat ini ada ' . $tpCount . ' TP.');
        }

        // Student completeness check: Every student in the class must have at least one grade record (since not all TPs are necessarily filled)
        $siswas = Siswa::where('id_kelas', $guruMapel->id_kelas)->get();
        foreach ($siswas as $siswa) {
            $gradesCount = NilaiTp::where('id_siswa', $siswa->id_siswa)
                ->where('id_mapel', $guruMapel->id_mapel)
                ->where('id_guru_mapel', $guruMapel->id_guru_mapel)
                ->where('id_semester', $request->id_semester)
                ->whereNotNull('nilai_akhir_tp')
                ->count();

            if ($gradesCount === 0) {
                return back()->with('error', 'Gagal mensubmit nilai. Siswa "' . $siswa->nama . '" belum memiliki data nilai sama sekali. Semua siswa di kelas ini harus memiliki minimal satu nilai sebelum Anda mensubmit.');
            }
        }

        // Update all grades for this assignment
        NilaiTp::where('id_guru_mapel', $guruMapel->id_guru_mapel)
            ->where('id_semester', $request->id_semester)
            ->update(['status_nilai' => $request->status]);

        return back()->with('success', 'Nilai berhasil disubmit ke Wali Kelas!');
    }
}
