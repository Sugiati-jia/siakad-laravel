<?php

namespace App\Http\Controllers\WaliKelas;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\NilaiTp;
use App\Models\NilaiEkstrakurikuler;
use App\Models\CatatanWaliKelas;
use App\Models\PushNotification;
use App\Models\UserNotification;
use App\Models\User;
use App\Services\GradeCalculationService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class RaporController extends Controller
{
    protected $gradeService;

    public function __construct(GradeCalculationService $gradeService)
    {
        $this->gradeService = $gradeService;
    }

    public function selectKelas(): View
    {
        $waliKelas = Auth::user()->guru;
        $kelas = Kelas::where('id_wali_kelas', $waliKelas->id_guru)->get();

        $activeTahunAjaran = \App\Models\TahunAjaran::activeOrAbort();

        return view('walikelas.rapor.select-kelas', compact('kelas', 'activeTahunAjaran'));
    }

    public function index($id_kelas): View
    {
        $waliKelas = Auth::user()->guru;
        $kelas = Kelas::where('id_wali_kelas', $waliKelas->id_guru)
            ->where('id_kelas', $id_kelas)
            ->firstOrFail();

        $siswas = Siswa::with('user')
            ->where('id_kelas', $id_kelas)
            ->orderBy('nama')
            ->get();

        $activeTahunAjaran = \App\Models\TahunAjaran::activeOrAbort();

        $siswaIds = $siswas->pluck('id_siswa');

        // Get total subjects taught in this class for the active semester
        $totalMapel = \App\Models\GuruMapel::where('id_kelas', $id_kelas)
            ->where('id_tahun_ajaran', $activeTahunAjaran->id_tahun_ajaran)
            ->where(function($q) use ($activeTahunAjaran) {
                $q->where('id_semester', $activeTahunAjaran->id_semester)
                  ->orWhereNull('id_semester');
            })
            ->count();

        // Check completeness status for each student (must have grades for ALL mapels)
        $hasGrades = \App\Models\NilaiTp::whereIn('id_siswa', $siswaIds)
            ->whereNotNull('nilai_akhir_tp')
            ->whereHas('guruMapel', function($q) use ($activeTahunAjaran) {
                $q->where('id_tahun_ajaran', $activeTahunAjaran->id_tahun_ajaran)
                  ->where(function($q2) use ($activeTahunAjaran) {
                      $q2->where('id_semester', $activeTahunAjaran->id_semester)
                         ->orWhereNull('id_semester');
                  });
            })
            ->get()
            ->groupBy('id_siswa')
            ->map(function($item) use ($totalMapel) {
                // Count unique mapels that have grades
                $uniqueMapels = $item->pluck('id_mapel')->unique()->count();
                return $uniqueMapels >= $totalMapel && $totalMapel > 0;
            });

        // Check if grades are ALL locked per student
        $hasLockedGrades = \App\Models\NilaiTp::whereIn('id_siswa', $siswaIds)
            ->where('status_nilai', 'locked')
            ->get()
            ->groupBy('id_siswa')
            ->map(fn($item) => $item->count() > 0);

        // Check for any unlocked (non-locked) grades
        $hasUnlockedGrades = \App\Models\NilaiTp::whereIn('id_siswa', $siswaIds)
            ->whereNotNull('nilai_akhir_tp')
            ->where('status_nilai', '!=', 'locked')
            ->exists();

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

        return view('walikelas.rapor.index', compact(
            'kelas',
            'siswas',
            'raporStatuses',
            'activeTahunAjaran',
            'hasGrades',
            'hasLockedGrades',
            'hasUnlockedGrades',
            'hasEkskul',
            'hasCatatan',
            'hasKokurikuler'
        ));
    }

    public function view($id_siswa): View
    {
        $user = Auth::user();
        $isWaliKelas = false;
        if ($user->guru) {
            $isWaliKelas = \App\Models\Kelas::where('id_wali_kelas', $user->guru->id_guru)->exists();
        }

        if ($user->role !== 'wali_kelas' && $user->role !== 'kurikulum' && $user->role !== 'admin' && !$isWaliKelas) {
            abort(403, 'Akses ditolak. Fitur ini hanya untuk Wali Kelas, Kurikulum, dan Admin.');
        }

        if ($user->role === 'admin' || $user->role === 'kurikulum') {
            $siswa = Siswa::with(['kelas', 'user'])->findOrFail($id_siswa);
        } else {
            $waliKelas = $user->guru;
            $siswa = Siswa::with(['kelas', 'user'])
                ->whereHas('kelas', function($query) use ($waliKelas) {
                    $query->where('id_wali_kelas', $waliKelas->id_guru);
                })
                ->findOrFail($id_siswa);
        }

        $activeTahunAjaran = \App\Models\TahunAjaran::activeOrAbort();

        // Get all grades grouped by subject for active year & semester
        $nilaiTps = NilaiTp::with(['mapel', 'tp'])
            ->where('id_siswa', $id_siswa)
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
            ->where('id_siswa', $id_siswa)
            ->where('id_tahun_ajaran', $activeTahunAjaran->id_tahun_ajaran)
            ->where('id_semester', $activeTahunAjaran->id_semester)
            ->get();

        // Get notes from wali kelas
        $catatanWaliKelas = CatatanWaliKelas::where('id_siswa', $id_siswa)
            ->where('id_tahun_ajaran', $activeTahunAjaran->id_tahun_ajaran)
            ->where('id_semester', $activeTahunAjaran->id_semester)
            ->first(); // catatan wali kelas per semester adalah unique/single row

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
        $user = Auth::user();
        $isWaliKelas = false;
        if ($user->guru) {
            $isWaliKelas = \App\Models\Kelas::where('id_wali_kelas', $user->guru->id_guru)->exists();
        }

        if ($user->role !== 'wali_kelas' && $user->role !== 'kurikulum' && $user->role !== 'admin' && !$isWaliKelas) {
            abort(403, 'Akses ditolak. Fitur ini hanya untuk Wali Kelas, Kurikulum, dan Admin.');
        }

        if ($user->role === 'admin' || $user->role === 'kurikulum') {
            $siswa = Siswa::with(['kelas', 'user'])->findOrFail($id_siswa);
        } else {
            $waliKelas = $user->guru;
            $siswa = Siswa::with(['kelas', 'user'])
                ->whereHas('kelas', function($query) use ($waliKelas) {
                    $query->where('id_wali_kelas', $waliKelas->id_guru);
                })
                ->findOrFail($id_siswa);
        }

        $activeTahunAjaran = \App\Models\TahunAjaran::activeOrAbort();

        // Get all grades grouped by subject for active year & semester
        $nilaiTps = NilaiTp::with(['mapel', 'tp'])
            ->where('id_siswa', $id_siswa)
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
            $pdf->getDomPDF()->set_option('isPhpEnabled', true);
            $filename = 'Rapor_' . str_replace(' ', '_', $siswa->nama) . '_' . $activeTahunAjaran->tahun_ajaran . '_Semester_' . $activeTahunAjaran->id_semester . '.pdf';
            $pdfContent = base64_encode($pdf->output());
            return view('walikelas.rapor.pdf-viewer', compact('pdfContent', 'filename'));
        } catch (\Exception $e) {
            return response('Terjadi Error saat memuat PDF: ' . $e->getMessage(), 500);
        }
    }

    public function kirim(Request $request): RedirectResponse
    {
        $request->validate([
            'id_siswa' => 'required|exists:siswa,id_siswa',
            'status' => 'required|in:draft,dikirim,diverifikasi',
        ]);

        $activeTahunAjaran = \App\Models\TahunAjaran::activeOrAbort();

        // Check: nilai harus sudah locked sebelum bisa dikirim
        if ($request->status === 'dikirim') {
            $hasUnlocked = NilaiTp::where('id_siswa', $request->id_siswa)
                ->whereNotNull('nilai_akhir_tp')
                ->where('status_nilai', '!=', 'locked')
                ->exists();

            if ($hasUnlocked) {
                return back()->with('error',
                    'Tidak dapat mengirim rapor. Nilai siswa ini belum sepenuhnya dikunci. ' .
                    'Silakan kunci semua nilai terlebih dahulu di halaman Rekap Nilai.'
                );
            }
        }

        $dataUpdate = [
            'status' => $request->status,
            'dikirim_oleh' => Auth::user()->name,
        ];

        if ($request->status === 'dikirim') {
            $dataUpdate['tanggal_kirim'] = now();
        }

        \App\Models\RaporStatus::updateOrCreate(
            [
                'id_siswa' => $request->id_siswa,
                'id_semester' => $activeTahunAjaran->id_semester,
            ],
            $dataUpdate
        );

        $statusText = match($request->status) {
            'dikirim' => 'Rapor berhasil dikirim ke Orang Tua & Kurikulum',
            'diverifikasi' => 'Rapor berhasil diverifikasi oleh Kurikulum',
            default => 'Status rapor dikembalikan ke Draft',
        };

        // ── Kirim notifikasi in-app ke orang tua siswa ────────────────────
        if ($request->status === 'dikirim') {
            $siswa = Siswa::find($request->id_siswa);
            $this->sendRaporNotification(
                $siswa ? $siswa->nama : 'Siswa',
                Auth::user()->name,
                false,
                [$request->id_siswa]
            );
        }

        return back()->with('success', $statusText);
    }

    public function kirimSemua(Request $request, $id_kelas): RedirectResponse
    {
        $waliKelas = Auth::user()->guru;
        $kelas = Kelas::where('id_wali_kelas', $waliKelas->id_guru)
            ->where('id_kelas', $id_kelas)
            ->firstOrFail();

        $siswas = Siswa::where('id_kelas', $id_kelas)->get();
        if ($siswas->isEmpty()) {
            return back()->with('error', 'Tidak ada siswa di kelas ini.');
        }

        $activeTahunAjaran = \App\Models\TahunAjaran::activeOrAbort();
        $siswaIds = $siswas->pluck('id_siswa');

        // Check: nilai harus sudah locked sebelum bisa dikirim massal
        $hasUnlockedGrades = \App\Models\NilaiTp::whereIn('id_siswa', $siswaIds)
            ->whereNotNull('nilai_akhir_tp')
            ->where('status_nilai', '!=', 'locked')
            ->exists();

        if ($hasUnlockedGrades) {
            return back()->with('error', 'Tidak dapat mengirim rapor massal. Masih ada nilai siswa di kelas ini yang belum dikunci. Silakan kunci semua nilai terlebih dahulu di halaman Rekap Nilai.');
        }

        // Get total subjects taught in this class for the active semester
        $totalMapel = \App\Models\GuruMapel::where('id_kelas', $id_kelas)
            ->where('id_tahun_ajaran', $activeTahunAjaran->id_tahun_ajaran)
            ->where('id_semester', $activeTahunAjaran->id_semester)
            ->count();

        // Check completeness status for each student
        $hasGrades = \App\Models\NilaiTp::whereIn('id_siswa', $siswaIds)
            ->whereNotNull('nilai_akhir_tp')
            ->whereHas('guruMapel', function($q) use ($activeTahunAjaran) {
                $q->where('id_tahun_ajaran', $activeTahunAjaran->id_tahun_ajaran)
                  ->where('id_semester', $activeTahunAjaran->id_semester);
            })
            ->get()
            ->groupBy('id_siswa')
            ->map(function($item) use ($totalMapel) {
                $uniqueMapels = $item->pluck('id_mapel')->unique()->count();
                return $uniqueMapels >= $totalMapel && $totalMapel > 0;
            });

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

        // Verify that 100% of students have complete data
        $incompleteSiswaNames = [];
        foreach ($siswas as $siswa) {
            $isAkademikOk = $hasGrades->get($siswa->id_siswa, false);
            $isEkskulOk = $hasEkskul->get($siswa->id_siswa, false);
            $isCatatanOk = isset($hasCatatan[$siswa->id_siswa]);
            $isKokurikulerOk = $hasKokurikuler->get($siswa->id_siswa, false);

            if (!$isAkademikOk || !$isEkskulOk || !$isCatatanOk || !$isKokurikulerOk) {
                $incompleteSiswaNames[] = $siswa->nama;
            }
        }

        if (!empty($incompleteSiswaNames)) {
            $namesStr = implode(', ', array_slice($incompleteSiswaNames, 0, 3));
            if (count($incompleteSiswaNames) > 3) {
                $namesStr .= ' dan ' . (count($incompleteSiswaNames) - 3) . ' siswa lainnya';
            }
            return back()->with('error', 'Tidak dapat mengirim rapor. Data siswa berikut belum lengkap (nilai/ekskul/catatan/P5): ' . $namesStr);
        }

        // Bulk update/create status
        foreach ($siswas as $siswa) {
            \App\Models\RaporStatus::updateOrCreate(
                [
                    'id_siswa' => $siswa->id_siswa,
                    'id_semester' => $activeTahunAjaran->id_semester,
                ],
                [
                    'status' => 'dikirim',
                    'dikirim_oleh' => Auth::user()->name,
                    'tanggal_kirim' => now(),
                ]
            );
        }

        // ── Kirim notifikasi in-app ke semua orang tua di kelas ini ────────────────────
        $this->sendRaporNotification('semua siswa kelas ' . $kelas->nama_kelas, Auth::user()->name, false, $siswaIds->toArray());

        return back()->with('success', 'Semua e-rapor kelas ini berhasil dikirim ke Orang Tua & Kurikulum!');
    }

    public function downloadAll($id_kelas)
    {
        $user = Auth::user();
        $isWaliKelas = false;
        if ($user->guru) {
            $isWaliKelas = \App\Models\Kelas::where('id_wali_kelas', $user->guru->id_guru)->exists();
        }

        if ($user->role !== 'wali_kelas' && $user->role !== 'kurikulum' && $user->role !== 'admin' && !$isWaliKelas) {
            abort(403, 'Akses ditolak. Fitur ini hanya untuk Wali Kelas, Kurikulum, dan Admin.');
        }

        if ($user->role === 'admin' || $user->role === 'kurikulum') {
            $kelas = Kelas::findOrFail($id_kelas);
        } else {
            $waliKelas = $user->guru;
            $kelas = Kelas::where('id_wali_kelas', $waliKelas->id_guru)
                ->where('id_kelas', $id_kelas)
                ->firstOrFail();
        }

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

            // Get all grades grouped by subject for active year & semester
            $nilaiTps = NilaiTp::with(['mapel', 'tp'])
                ->where('id_siswa', $id_siswa)
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

            // Ukuran kertas F4 dalam point (215 x 330 mm = 609.448 x 935.433 pt)
            $pdf = Pdf::loadHTML($html)->setPaper([0, 0, 609.448, 935.433], 'portrait');
            $pdf->getDomPDF()->set_option('isPhpEnabled', true);
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
    /**
     * Kirim notifikasi in-app ke semua akun orang_tua.
     * Pesan standar: "Hasil belajar siswa telah tersedia dan dapat diakses melalui sistem."
     */
    private function sendRaporNotification(string $namaLabel, string $sentBy, bool $broadcastAll = false, array $siswaIds = []): void
    {
        $title = '🔔 E-Rapor Telah Tersedia';
        $body = 'Hasil belajar siswa telah tersedia dan dapat diakses melalui sistem.';
        $url = url('/orangtua/rapor');

        $notif = \App\Models\PushNotification::create([
            'title'       => $title,
            'body'        => $body,
            'icon'        => $icon,
            'url'         => $url,
            'target_role' => 'orang_tua',
            'sent_by'     => $sentBy,
        ]);

        // Kirim ke akun orang_tua yang sesuai (in-app inbox)
        // Note: Karena tabel users tidak berelasi langsung dengan siswa (id_siswa/nisn tidak konsisten),
        // maka untuk saat ini notifikasi di-broadcast ke semua orang tua.
        $orangTuaUsers = \App\Models\User::where('role', 'orang_tua')->get();
        $count = 0;
        foreach ($orangTuaUsers as $user) {
            UserNotification::create([
                'user_id'         => $user->id,
                'notification_id' => $notif->id,
                'is_read'         => false,
            ]);
            $count++;
        }
        $notif->update(['delivered_count' => $count]);

        // Kirim ke FCM (Background Push)
        $orangTuaUserIds = $orangTuaUsers->pluck('id')->toArray();
        $fcmTokens = \App\Models\PushSubscription::whereIn('user_id', $orangTuaUserIds)
            ->where('is_active', true)
            ->pluck('endpoint')
            ->filter()
            ->toArray();

        if (!empty($fcmTokens)) {
            $fcmService = new \App\Services\FCMService();
            // Due to Firebase tokens limit per multicast (500), chunking might be needed for large scale, but fine for now
            $chunks = array_chunk($fcmTokens, 500);
            foreach ($chunks as $chunk) {
                $fcmService->sendMulticast($chunk, $title, $body, $icon, $url);
            }
        }
    }

    public function downloadRapor($id)
    {
    return 'Controller jalan';}

    public function generateDeskripsi(Request $request): RedirectResponse
    {
        $request->validate([
            'id_siswa' => 'required|exists:siswa,id_siswa',
            'mapel_id' => 'required|exists:mapel,id_mapel'
        ]);

        $waliKelas = Auth::user()->guru;
        $siswa = Siswa::whereHas('kelas', function($query) use ($waliKelas) {
                $query->where('id_wali_kelas', $waliKelas->id_guru);
            })
            ->findOrFail($request->id_siswa);

        $description = $this->gradeService->generateDescription(
            $request->id_siswa,
            $request->mapel_id
        );

        return back()->with('description', $description);
    }
}
