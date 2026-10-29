<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\NilaiTp;
use App\Models\NilaiEkstrakurikuler;
use App\Models\CatatanWaliKelas;
use App\Services\GradeCalculationService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class RaporController extends Controller
{
    protected $gradeService;

    public function __construct(GradeCalculationService $gradeService)
    {
        $this->gradeService = $gradeService;
    }

    public function index(): View
    {
        $siswa = Auth::user()->siswa;
        $activeTahunAjaran = \App\Models\TahunAjaran::activeOrAbort();

        // Check RaporStatus
        $raporStatusObj = \App\Models\RaporStatus::where('id_siswa', $siswa->id_siswa ?? $siswa->id)
            ->where('id_tahun_ajaran', $activeTahunAjaran->id_tahun_ajaran ?? $activeTahunAjaran->id)
            ->where('id_semester', $activeTahunAjaran->id_semester)
            ->first();
        $isPublished = $raporStatusObj && in_array($raporStatusObj->status, ['dikirim', 'diverifikasi']);

        if ($isPublished) {
            // Get all grades grouped by subject
            $nilaiTps = NilaiTp::with(['guruMapel.mapel'])
                ->where('id_siswa', $siswa->id_siswa ?? $siswa->id)
                ->whereHas('guruMapel', function($query) use ($activeTahunAjaran) {
                    $query->where('id_tahun_ajaran', $activeTahunAjaran->id_tahun_ajaran ?? $activeTahunAjaran->id)
                          ->where(function($q2) use ($activeTahunAjaran) {
                              $q2->where('id_semester', $activeTahunAjaran->id_semester)
                                 ->orWhereNull('id_semester');
                          });
                })->whereNotNull('nilai_akhir_tp')
                ->get()
                ->groupBy('guruMapel.mapel_id');

            // Calculate average for each subject
            $subjectAverages = [];
            foreach ($nilaiTps as $mapelId => $grades) {
                $subjectAverages[$mapelId] = $grades->avg('nilai_tp');
            }
        } else {
            $nilaiTps = collect();
            $subjectAverages = [];
        }

        return view('siswa.rapor.index', compact(
            'siswa',
            'nilaiTps',
            'subjectAverages',
            'activeTahunAjaran',
            'isPublished'
        ));
    }

    public function view(): View
    {
        $siswa = Auth::user()->siswa;
        $activeTahunAjaran = \App\Models\TahunAjaran::activeOrAbort();

        // Check RaporStatus
        $raporStatusObj = \App\Models\RaporStatus::where('id_siswa', $siswa->id_siswa ?? $siswa->id)
            ->where('id_tahun_ajaran', $activeTahunAjaran->id_tahun_ajaran ?? $activeTahunAjaran->id)
            ->where('id_semester', $activeTahunAjaran->id_semester)
            ->first();
        $isPublished = $raporStatusObj && in_array($raporStatusObj->status, ['dikirim', 'diverifikasi']);

        if (!$isPublished) {
            return redirect()->route('siswa.rapor.index')
                ->with('error', 'E-Rapor belum diterbitkan oleh Wali Kelas.');
        }

        // Get all grades grouped by subject
        $nilaiTps = NilaiTp::with(['guruMapel.mapel', 'guruMapel.guru', 'tp'])
            ->where('id_siswa', $siswa->id)
            ->where('id_semester', $activeTahunAjaran->id_semester)
            ->where('id_tahun_ajaran', $activeTahunAjaran->id)
            ->get()
            ->groupBy('guruMapel.mapel_id');

        // Get extracurricular grades
        $nilaiEkstrakurikulers = NilaiEkstrakurikuler::with('ekstrakurikuler')
            ->where('id_siswa', $siswa->id)
            ->where('id_semester', $activeTahunAjaran->id_semester)
            ->where('id_tahun_ajaran', $activeTahunAjaran->id)
            ->get();

        // Get notes from wali kelas
        $catatanWaliKelas = CatatanWaliKelas::where('id_siswa', $siswa->id)
            ->where('id_semester', $activeTahunAjaran->id_semester)
            ->where('id_tahun_ajaran', $activeTahunAjaran->id)
            ->get();

        // Generate descriptions for each subject
        $descriptions = [];
        foreach ($nilaiTps as $mapelId => $grades) {
            $descriptions[$mapelId] = $this->gradeService->generateDescription(
                $siswa->id, 
                $mapelId, 
                $activeTahunAjaran->id_semester, 
                $activeTahunAjaran->id
            );
        }

        return view('siswa.rapor.view', compact(
            'siswa',
            'nilaiTps',
            'nilaiEkstrakurikulers',
            'catatanWaliKelas',
            'descriptions',
            'activeTahunAjaran'
        ));
    }
}
