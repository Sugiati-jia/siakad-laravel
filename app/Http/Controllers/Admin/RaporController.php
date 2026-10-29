<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\NilaiTp;
use App\Models\NilaiEkstrakurikuler;
use App\Models\CatatanWaliKelas;
use App\Services\GradeCalculationService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class RaporController extends Controller
{
    protected $gradeService;

    public function __construct(GradeCalculationService $gradeService)
    {
        $this->gradeService = $gradeService;
    }

    public function index(): View
    {
        $kelas = Kelas::with(['waliKelas', 'jurusan', 'tahunAjaran', 'siswas'])->get()->sortBy('nama_kelas');
        $activeTahunAjaran = \App\Models\TahunAjaran::activeOrAbort();

        return view('admin.rapor.index', compact('kelas', 'activeTahunAjaran'));
    }

    public function kelas($id_kelas): View
    {
        $kelas = Kelas::with(['waliKelas', 'jurusan', 'tahunAjaran'])->findOrFail($id_kelas);
        $siswas = Siswa::with('user')
            ->where('id_kelas', $id_kelas)
            ->orderBy('nama')
            ->get();

        $activeTahunAjaran = \App\Models\TahunAjaran::activeOrAbort();

        // Check completeness status for each student
        $siswaIds = $siswas->pluck('id_siswa');

        $raporStatuses = \App\Models\RaporStatus::where('id_semester', $activeTahunAjaran->id_semester)
            ->whereIn('id_siswa', $siswaIds)
            ->get()
            ->keyBy('id_siswa');

        $hasGrades = \App\Models\NilaiTp::whereIn('id_siswa', $siswaIds)
            ->whereNotNull('nilai_akhir_tp')
            ->get()
            ->groupBy('id_siswa')
            ->map(fn($item) => $item->count() > 0);

        $hasEkskul = \App\Models\NilaiEkstrakurikuler::whereIn('id_siswa', $siswaIds)
            ->where('id_semester', $activeTahunAjaran->id_semester)
            ->where('id_tahun_ajaran', $activeTahunAjaran->id_tahun_ajaran)
            ->get()
            ->groupBy('id_siswa')
            ->map(fn($item) => $item->count() > 0);

        $hasCatatan = \App\Models\CatatanWaliKelas::whereIn('id_siswa', $siswaIds)
            ->where('id_semester', $activeTahunAjaran->id_semester)
            ->where('id_tahun_ajaran', $activeTahunAjaran->id_tahun_ajaran)
            ->whereNotNull('catatan')
            ->where('catatan', '!=', '')
            ->get()
            ->keyBy('id_siswa');

        $hasKokurikuler = \App\Models\Kokurikuler::whereIn('id_siswa', $siswaIds)
            ->where('id_semester', $activeTahunAjaran->id_semester)
            ->where('id_tahun_ajaran', $activeTahunAjaran->id_tahun_ajaran)
            ->get()
            ->groupBy('id_siswa')
            ->map(fn($item) => $item->count() > 0);

        return view('admin.rapor.kelas', compact(
            'kelas',
            'siswas',
            'raporStatuses',
            'activeTahunAjaran',
            'hasGrades',
            'hasEkskul',
            'hasCatatan',
            'hasKokurikuler'
        ));
    }

    public function view($id_siswa): View
    {
        $siswa = Siswa::with(['kelas', 'user'])->findOrFail($id_siswa);
        $activeTahunAjaran = \App\Models\TahunAjaran::activeOrAbort();

        // Get all grades grouped by subject for active year & semester
        $nilaiTps = NilaiTp::with(['mapel', 'tp'])
            ->where('id_siswa', $id_siswa)
            ->whereHas('guruMapel', function($query) use ($activeTahunAjaran) {
                $query->where('id_tahun_ajaran', $activeTahunAjaran->id_tahun_ajaran)
                      ->where(function($q2) use ($activeTahunAjaran) {
                          $q2->where('id_semester', $activeTahunAjaran->id_semester)
                             ->orWhereNull('id_semester');
                      });
            })
            ->get()
            ->groupBy('id_mapel');

        // Get extracurricular grades
        $nilaiEkstrakurikulers = NilaiEkstrakurikuler::with('ekstrakurikuler')
            ->where('id_siswa', $id_siswa)
            ->where('id_tahun_ajaran', $activeTahunAjaran->id_tahun_ajaran)
            ->where('id_semester', $activeTahunAjaran->id_semester)
            ->get();

        // Get notes from wali kelas
        $catatanWaliKelas = CatatanWaliKelas::where('id_siswa', $id_siswa)
            ->where('id_tahun_ajaran', $activeTahunAjaran->id_tahun_ajaran)
            ->where('id_semester', $activeTahunAjaran->id_semester)
            ->first();

        // Get P5 Kokurikuler
        $kokurikuler = \App\Models\Kokurikuler::where('id_siswa', $id_siswa)
            ->where('id_tahun_ajaran', $activeTahunAjaran->id_tahun_ajaran)
            ->where('id_semester', $activeTahunAjaran->id_semester)
            ->first();

        // Generate descriptions for each subject
        $descriptions = [];
        foreach ($nilaiTps as $mapelId => $grades) {
            $descriptions[$mapelId] = $this->gradeService->generateDescription(
                $id_siswa, 
                $mapelId
            );
        }

        return view('walikelas.rapor.view', compact(
            'siswa',
            'nilaiTps',
            'nilaiEkstrakurikulers',
            'catatanWaliKelas',
            'kokurikuler',
            'descriptions',
            'activeTahunAjaran'
        ));
    }


    public function pdf($id_siswa)
    {
        $siswa = Siswa::with(['kelas', 'user'])->findOrFail($id_siswa);
        $activeTahunAjaran = \App\Models\TahunAjaran::activeOrAbort();

        // Get all grades grouped by subject for active year & semester
        $nilaiTps = NilaiTp::with(['mapel', 'tp'])
            ->where('id_siswa', $id_siswa)
            ->whereHas('guruMapel', function($query) use ($activeTahunAjaran) {
                $query->where('id_tahun_ajaran', $activeTahunAjaran->id_tahun_ajaran)
                      ->where(function($q2) use ($activeTahunAjaran) {
                          $q2->where('id_semester', $activeTahunAjaran->id_semester)
                             ->orWhereNull('id_semester');
                      });
            })
            ->get()
            ->groupBy('id_mapel');

        // Get extracurricular grades
        $nilaiEkstrakurikulers = NilaiEkstrakurikuler::with('ekstrakurikuler')
            ->where('id_siswa', $id_siswa)
            ->where('id_tahun_ajaran', $activeTahunAjaran->id_tahun_ajaran)
            ->where('id_semester', $activeTahunAjaran->id_semester)
            ->get();

        // Get notes from wali kelas
        $catatanWaliKelas = CatatanWaliKelas::where('id_siswa', $id_siswa)
            ->where('id_tahun_ajaran', $activeTahunAjaran->id_tahun_ajaran)
            ->where('id_semester', $activeTahunAjaran->id_semester)
            ->first();

        // Get P5 Kokurikuler
        $kokurikuler = \App\Models\Kokurikuler::where('id_siswa', $id_siswa)
            ->where('id_tahun_ajaran', $activeTahunAjaran->id_tahun_ajaran)
            ->where('id_semester', $activeTahunAjaran->id_semester)
            ->first();

        // Generate descriptions for each subject
        $descriptions = [];
        foreach ($nilaiTps as $mapelId => $grades) {
            $descriptions[$mapelId] = $this->gradeService->generateDescription(
                $id_siswa, 
                $mapelId
            );
        }

        $html = view('walikelas.rapor.pdf', compact(
            'siswa',
            'nilaiTps',
            'nilaiEkstrakurikulers',
            'catatanWaliKelas',
            'kokurikuler',
            'descriptions',
            'activeTahunAjaran'
        ))->render();

        \App\Models\RaporStatus::updateOrCreate(
            [
                'id_siswa' => $id_siswa,
                'id_semester' => $activeTahunAjaran->id_semester,
            ],
            [
                'tanggal_cetak' => now(),
            ]
        );

        try {
            // Ukuran kertas F4 dalam point (215 x 330 mm = 609.448 x 935.433 pt)
            $pdf = Pdf::loadHTML($html)->setPaper([0, 0, 609.448, 935.433], 'portrait');
            $filename = 'Rapor_' . str_replace(' ', '_', $siswa->nama) . '_' . $activeTahunAjaran->tahun_ajaran . '_Semester_' . $activeTahunAjaran->id_semester . '.pdf';
            $pdfContent = base64_encode($pdf->output());
            return view('walikelas.rapor.pdf-viewer', compact('pdfContent', 'filename'));
        } catch (\Exception $e) {
            return response('Terjadi Error saat memuat PDF: ' . $e->getMessage(), 500);
        }
    }

    public function downloadAll($id_kelas)
    {
        $kelas = Kelas::findOrFail($id_kelas);
        $siswas = Siswa::where('id_kelas', $id_kelas)->get();
        if ($siswas->isEmpty()) {
            return back()->with('error', 'Tidak ada siswa di kelas ini.');
        }

        $activeTahunAjaran = \App\Models\TahunAjaran::activeOrAbort();

        $zip = new \ZipArchive();
        $tahunAjaranSafe = str_replace(['/', '\\'], '-', $activeTahunAjaran->tahun_ajaran);
        $zipFilename = 'Rapor_Semua_Siswa_' . str_replace(' ', '_', $kelas->nama_kelas) . '_' . $tahunAjaranSafe . '.zip';
        $zipPath = tempnam(sys_get_temp_dir(), 'zip') . '.zip';

        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            return back()->with('error', 'Gagal membuat file ZIP.');
        }

        foreach ($siswas as $siswa) {
            $id_siswa = $siswa->id_siswa;

            $nilaiTps = NilaiTp::with(['mapel', 'tp'])
                ->where('id_siswa', $id_siswa)
                ->whereHas('guruMapel', function($query) use ($activeTahunAjaran) {
                    $query->where('id_tahun_ajaran', $activeTahunAjaran->id_tahun_ajaran)
                          ->where(function($q2) use ($activeTahunAjaran) {
                              $q2->where('id_semester', $activeTahunAjaran->id_semester)
                                 ->orWhereNull('id_semester');
                          });
                })
                ->get()
                ->groupBy('id_mapel');

            $nilaiEkstrakurikulers = NilaiEkstrakurikuler::with('ekstrakurikuler')
                ->where('id_siswa', $id_siswa)
                ->where('id_tahun_ajaran', $activeTahunAjaran->id_tahun_ajaran)
                ->where('id_semester', $activeTahunAjaran->id_semester)
                ->get();

            $catatanWaliKelas = CatatanWaliKelas::where('id_siswa', $id_siswa)
                ->where('id_tahun_ajaran', $activeTahunAjaran->id_tahun_ajaran)
                ->where('id_semester', $activeTahunAjaran->id_semester)
                ->first();

            $kokurikuler = \App\Models\Kokurikuler::where('id_siswa', $id_siswa)
                ->where('id_tahun_ajaran', $activeTahunAjaran->id_tahun_ajaran)
                ->where('id_semester', $activeTahunAjaran->id_semester)
                ->first();

            $descriptions = [];
            foreach ($nilaiTps as $mapelId => $grades) {
                $descriptions[$mapelId] = $this->gradeService->generateDescription(
                    $id_siswa, 
                    $mapelId
                );
            }

            $html = view('walikelas.rapor.pdf', compact(
                'siswa',
                'nilaiTps',
                'nilaiEkstrakurikulers',
                'catatanWaliKelas',
                'kokurikuler',
                'descriptions',
                'activeTahunAjaran'
            ))->render();

            \App\Models\RaporStatus::updateOrCreate(
                [
                    'id_siswa' => $id_siswa,
                    'id_semester' => $activeTahunAjaran->id_semester,
                ],
                [
                    'tanggal_cetak' => now(),
                ]
            );

            $pdf = Pdf::loadHTML($html)->setPaper([0, 0, 609.448, 935.433], 'portrait');
            $pdfContent = $pdf->output();
            $individualFilename = 'Rapor_' . str_replace([' ', '/', '\\', ':', '*', '?', '"', '<', '>', '|'], '_', $siswa->nama) . '.pdf';
            $zip->addFromString($individualFilename, $pdfContent);
        }

        $zip->close();

        $zipContent = base64_encode(file_get_contents($zipPath));
        @unlink($zipPath);

        return view('walikelas.rapor.zip-downloader', [
            'zipContent' => $zipContent,
            'filename' => $zipFilename
        ]);
    }
}


