<?php

namespace App\Http\Controllers\OrangTua;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\NilaiTp;
use App\Models\NilaiEkstrakurikuler;
use App\Models\CatatanWaliKelas;
use App\Services\GradeCalculationService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;

class RaporController extends Controller
{
    protected $gradeService;

    public function __construct(GradeCalculationService $gradeService)
    {
        $this->gradeService = $gradeService;
    }

    public function index(): View|RedirectResponse
    {
        $selectedChildId = session('selected_child_id');
        
        if (!$selectedChildId) {
            return redirect()->route('orangtua.select-anak')
                ->with('error', 'Silakan pilih anak terlebih dahulu');
        }

        $siswa = Siswa::findOrFail($selectedChildId);
        $activeTahunAjaran = \App\Models\TahunAjaran::activeOrAbort();

        // Check RaporStatus
        $raporStatusObj = \App\Models\RaporStatus::where('id_siswa', $siswa->id_siswa)
            ->where('id_tahun_ajaran', $activeTahunAjaran->id_tahun_ajaran)
            ->where('id_semester', $activeTahunAjaran->id_semester)
            ->first();
        $isPublished = $raporStatusObj && in_array($raporStatusObj->status, ['dikirim', 'diverifikasi']);

        if ($isPublished) {
            // Get all grades grouped by subject
            $nilaiTps = NilaiTp::with(['guruMapel.mapel'])
                ->where('id_siswa', $siswa->id_siswa)
                ->whereHas('guruMapel', function($query) use ($activeTahunAjaran) {
                    $query->where('id_tahun_ajaran', $activeTahunAjaran->id_tahun_ajaran)
                          ->where(function($q2) use ($activeTahunAjaran) {
                              $q2->where('id_semester', $activeTahunAjaran->id_semester)
                                 ->orWhereNull('id_semester');
                          });
                })
                ->whereNotNull('nilai_akhir_tp')
                ->get()
                ->groupBy('id_mapel');

            // Calculate average for each subject
            $subjectAverages = [];
            foreach ($nilaiTps as $mapelId => $grades) {
                $subjectAverages[$mapelId] = $grades->avg('nilai_akhir_tp');
            }
        } else {
            $nilaiTps = collect();
            $subjectAverages = [];
        }

        return view('orangtua.rapor.index', compact(
            'siswa',
            'nilaiTps',
            'subjectAverages',
            'activeTahunAjaran',
            'isPublished'
        ));
    }

    public function view(): View|RedirectResponse
    {
        $selectedChildId = session('selected_child_id');
        
        if (!$selectedChildId) {
            return redirect()->route('orangtua.select-anak')
                ->with('error', 'Silakan pilih anak terlebih dahulu');
        }

        $siswa = Siswa::findOrFail($selectedChildId);
        $activeTahunAjaran = \App\Models\TahunAjaran::activeOrAbort();

        // Check RaporStatus
        $raporStatusObj = \App\Models\RaporStatus::where('id_siswa', $siswa->id_siswa)
            ->where('id_tahun_ajaran', $activeTahunAjaran->id_tahun_ajaran)
            ->where('id_semester', $activeTahunAjaran->id_semester)
            ->first();
        $isPublished = $raporStatusObj && in_array($raporStatusObj->status, ['dikirim', 'diverifikasi']);

        if (!$isPublished) {
            return redirect()->route('orangtua.rapor.index')
                ->with('error', 'E-Rapor belum diterbitkan oleh Wali Kelas.');
        }

        // Get all grades grouped by subject
        $nilaiTps = NilaiTp::with(['guruMapel.mapel', 'guruMapel.guru', 'tp'])
            ->where('id_siswa', $siswa->id_siswa)
            ->whereHas('guruMapel', function($query) use ($activeTahunAjaran) {
                $query->where('id_tahun_ajaran', $activeTahunAjaran->id_tahun_ajaran)
                      ->where(function($q) use ($activeTahunAjaran) {
                          $q->where('id_semester', $activeTahunAjaran->id_semester)
                            ->orWhereNull('id_semester');
                      });
            })
            ->get()
            ->groupBy('id_mapel');

        // Get extracurricular grades
        $nilaiEkstrakurikulers = NilaiEkstrakurikuler::with('ekstrakurikuler')
            ->where('id_siswa', $siswa->id_siswa)
            ->where('id_semester', $activeTahunAjaran->id_semester)
            ->where('id_tahun_ajaran', $activeTahunAjaran->id_tahun_ajaran)
            ->get();

        // Get notes from wali kelas
        $catatanWaliKelas = CatatanWaliKelas::where('id_siswa', $siswa->id_siswa)
            ->where('id_semester', $activeTahunAjaran->id_semester)
            ->where('id_tahun_ajaran', $activeTahunAjaran->id_tahun_ajaran)
            ->first();

        // Get P5 Kokurikuler
        $kokurikuler = \App\Models\Kokurikuler::where('id_siswa', $siswa->id_siswa)
            ->where('id_semester', $activeTahunAjaran->id_semester)
            ->where('id_tahun_ajaran', $activeTahunAjaran->id_tahun_ajaran)
            ->first();

        // Generate descriptions for each subject
        $descriptions = [];
        foreach ($nilaiTps as $mapelId => $grades) {
            $descriptions[$mapelId] = $this->gradeService->generateDescription(
                $siswa->id_siswa, 
                $mapelId
            );
        }

        return view('orangtua.rapor.view', compact(
            'siswa',
            'nilaiTps',
            'nilaiEkstrakurikulers',
            'catatanWaliKelas',
            'kokurikuler',
            'descriptions',
            'activeTahunAjaran'
        ));
    }
}
