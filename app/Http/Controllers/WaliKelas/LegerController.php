<?php

namespace App\Http\Controllers\WaliKelas;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Mapel;
use App\Models\GuruMapel;
use App\Models\NilaiTp;
use App\Models\TahunAjaran;
use App\Services\GradeCalculationService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class LegerController extends Controller
{
    protected $gradeService;

    public function __construct(GradeCalculationService $gradeService)
    {
        $this->gradeService = $gradeService;
    }

    public function selectKelas(): View
    {
        $user = Auth::user();
        $activeTahunAjaran = \App\Models\TahunAjaran::activeOrAbort();

        if ($user->role === 'wali_kelas') {
            $waliKelas = $user->guru;
            $kelas = Kelas::where('id_wali_kelas', $waliKelas->id_guru)->get();
        } else {
            // Admin or Kurikulum
            $kelas = Kelas::orderBy('tingkat')->orderBy('nama')->get();
        }

        return view('walikelas.leger.select-kelas', compact('kelas', 'activeTahunAjaran'));
    }

    public function index($id_kelas): View
    {
        $user = Auth::user();
        if ($user->role === 'wali_kelas') {
            $waliKelas = $user->guru;
            $kelas = Kelas::where('id_wali_kelas', $waliKelas->id_guru)
                ->where('id_kelas', $id_kelas)
                ->firstOrFail();
        } else {
            $kelas = Kelas::findOrFail($id_kelas);
        }

        $activeTahunAjaran = TahunAjaran::activeOrAbort();

        $data = $this->getLegerData($id_kelas, $activeTahunAjaran);

        // Paginate studentsList per 10 items
        $page = request()->get('page', 1);
        $perPage = 10;
        $offset = ($page - 1) * $perPage;
        $items = array_slice($data['studentsList'], $offset, $perPage, true);
        
        $data['studentsList'] = new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            count($data['studentsList']),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('walikelas.leger.index', array_merge([
            'kelas' => $kelas,
            'activeTahunAjaran' => $activeTahunAjaran
        ], $data));
    }

    public function pdf($id_kelas)
    {
        $user = Auth::user();
        if ($user->role === 'wali_kelas') {
            $waliKelas = $user->guru;
            $kelas = Kelas::where('id_wali_kelas', $waliKelas->id_guru)
                ->where('id_kelas', $id_kelas)
                ->firstOrFail();
        } else {
            $kelas = Kelas::findOrFail($id_kelas);
        }

        $activeTahunAjaran = TahunAjaran::activeOrAbort();

        $data = $this->getLegerData($id_kelas, $activeTahunAjaran);

        $pdf = Pdf::loadView('walikelas.leger.pdf', array_merge([
            'kelas' => $kelas,
            'activeTahunAjaran' => $activeTahunAjaran
        ], $data))->setPaper('a4', 'landscape');

        $filename = 'Leger_Nilai_' . str_replace(' ', '_', $kelas->nama_kelas) . '.pdf';
        return $pdf->download($filename);
    }

    public function excel($id_kelas)
    {
        $user = Auth::user();
        if ($user->role === 'wali_kelas') {
            $waliKelas = $user->guru;
            $kelas = Kelas::where('id_wali_kelas', $waliKelas->id_guru)
                ->where('id_kelas', $id_kelas)
                ->firstOrFail();
        } else {
            $kelas = Kelas::findOrFail($id_kelas);
        }

        $activeTahunAjaran = TahunAjaran::activeOrAbort();

        $data = $this->getLegerData($id_kelas, $activeTahunAjaran);

        // Render clean HTML table and set headers for Excel download
        $filename = 'Leger_Nilai_' . str_replace(' ', '_', $kelas->nama_kelas) . '.xls';
        
        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Pragma: no-cache");
        header("Expires: 0");

        return view('walikelas.leger.excel', array_merge([
            'kelas' => $kelas,
            'activeTahunAjaran' => $activeTahunAjaran
        ], $data));
    }

    private function getLegerData($id_kelas, $activeTahunAjaran): array
    {
        $siswas = Siswa::where('id_kelas', $id_kelas)
            ->orderBy('nama')
            ->get();

        // Get subjects in this class
        $guruMapels = GuruMapel::with('mapel')
            ->where('id_kelas', $id_kelas)
            ->where('id_tahun_ajaran', $activeTahunAjaran->id_tahun_ajaran)
            ->get();

        $mapels = $guruMapels->map(fn($gm) => $gm->mapel)
            ->unique('id_mapel')
            ->sort(function($a, $b) {
                $kelompokOrder = [
                    'Mata Pelajaran Umum' => 1, 'Muatan Nasional' => 1, 'A' => 1, 'Kelompok A' => 1, 
                    'Kejuruan' => 2, 'B' => 2, 'Kelompok B' => 2, 
                    'C' => 3, 'Kelompok C' => 3, 'Mata Pelajaran Pilihan' => 3
                ];
                $orderA = $kelompokOrder[$a->kelompok_mapel] ?? 2;
                $orderB = $kelompokOrder[$b->kelompok_mapel] ?? 2;
                
                if ($orderA != $orderB) return $orderA <=> $orderB;
                
                $urutanA = $a->urutan ?? 999;
                $urutanB = $b->urutan ?? 999;
                
                if ($urutanA == $urutanB) return strcmp($a->nama_mapel, $b->nama_mapel);
                return $urutanA <=> $urutanB;
            })->values();

        // Fetch all grades based on the guruMapels (which are correctly filtered by year)
        $allNilaiTps = \App\Models\NilaiTp::whereIn('id_siswa', $siswas->pluck('id_siswa'))
            ->whereIn('id_guru_mapel', $guruMapels->pluck('id_guru_mapel'))
            ->get();

        // Group by siswa and mapel
        $groupedNilai = $allNilaiTps->groupBy(['id_siswa', 'id_mapel']);

        // Map grades by student_id and mapel_id using GradeCalculationService to match Rapor exactly
        $gradesMatrix = [];
        foreach ($groupedNilai as $siswaId => $mapelGroup) {
            foreach ($mapelGroup as $mapelId => $nilaiTps) {
                $na = $this->gradeService->hitungNilaiAkhirSiswa($nilaiTps);
                if ($na !== null) {
                    $gradesMatrix[$siswaId][$mapelId] = $na;
                }
            }
        }

        // Calculate student aggregates (average across all mapels, and ranking)
        $studentsList = [];
        
        // Fetch CatatanWaliKelas (Attendance)
        $catatanWaliKelas = \App\Models\CatatanWaliKelas::where('id_kelas', $id_kelas)
            ->where('id_tahun_ajaran', $activeTahunAjaran->id_tahun_ajaran)
            ->where('id_semester', $activeTahunAjaran->id_semester)
            ->get()->keyBy('id_siswa');

        // Fetch all Ekstrakurikuler for this class
        $allNilaiEkstra = \App\Models\NilaiEkstrakurikuler::with('ekstrakurikuler')
            ->whereIn('id_siswa', $siswas->pluck('id_siswa'))
            ->where('id_tahun_ajaran', $activeTahunAjaran->id_tahun_ajaran)
            ->where('id_semester', $activeTahunAjaran->id_semester)
            ->get();

        // Get unique extracurriculars as ordered list (by id)
        $ekstraList = $allNilaiEkstra
            ->filter(fn($n) => $n->ekstrakurikuler)
            ->sortBy('id_ekstrakurikuler')
            ->unique('id_ekstrakurikuler')
            ->values()
            ->map(fn($n) => $n->ekstrakurikuler);  // Ekstrakurikuler model uses 'nama' field

        // Group nilai ekstra by siswa_id => [id_ekstrakurikuler => keterangan]
        $ekstraMatrix = [];
        foreach ($allNilaiEkstra as $ne) {
            $ekstraMatrix[$ne->id_siswa][$ne->id_ekstrakurikuler] = $ne->keterangan ?? $ne->nilai ?? '';
        }

        foreach ($siswas as $siswa) {
            $studentGrades = $gradesMatrix[$siswa->id_siswa] ?? [];
            $totalMarks = 0;
            $gradedCount = 0;

            foreach ($mapels as $mapel) {
                $mark = $studentGrades[$mapel->id_mapel] ?? null;
                if ($mark !== null) {
                    $totalMarks += $mark;
                    $gradedCount++;
                }
            }

            $overallAvg = $gradedCount > 0 ? round($totalMarks / $gradedCount, 2) : 0;

            // Kehadiran
            $catatan = $catatanWaliKelas->get($siswa->id_siswa);
            $kehadiran = [
                'sakit' => $catatan ? ($catatan->sakit ?? 0) : 0,
                'izin'  => $catatan ? ($catatan->izin ?? 0) : 0,
                'alpa'  => $catatan ? ($catatan->alpa ?? 0) : 0,
            ];

            $studentsList[] = [
                'siswa'    => $siswa,
                'grades'   => $studentGrades,
                'total'    => $totalMarks,
                'average'  => $overallAvg,
                'ekstra'   => $ekstraMatrix[$siswa->id_siswa] ?? [],  // [id_ekstra => ket]
                'kehadiran'=> $kehadiran
            ];
        }

        // Sort by overall total descending to compute rankings
        usort($studentsList, function($a, $b) {
            // Students with grades come first, then students without grades
            if ($a['total'] == 0 && $b['total'] == 0) return strcmp($a['siswa']->nama, $b['siswa']->nama);
            if ($a['total'] == 0) return 1;  // a goes after b
            if ($b['total'] == 0) return -1; // b goes after a
            return $b['total'] <=> $a['total']; // higher total = better rank
        });

        // Inject rankings — only for students with actual grades
        $rankCounter = 1;
        foreach ($studentsList as $index => &$item) {
            if ($item['total'] > 0) {
                $item['rank'] = $rankCounter++;
            } else {
                $item['rank'] = null; // No rank if no grades
            }
        }

        return [
            'mapels'       => $mapels,
            'ekstraList'   => $ekstraList,
            'studentsList' => $studentsList
        ];
    }
}
