<?php

namespace App\Http\Controllers\OrangTua;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\NilaiTp;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class NilaiController extends Controller
{
    public function index(): View
    {
        $selectedChildId = session('selected_child_id');
        
        if (!$selectedChildId) {
            return redirect()->route('orangtua.select-anak')
                ->with('error', 'Silakan pilih anak terlebih dahulu');
        }

        $siswa = Siswa::findOrFail($selectedChildId);
        $activeTahunAjaran = TahunAjaran::activeOrAbort();

        // Check RaporStatus
        $raporStatusObj = \App\Models\RaporStatus::where('id_siswa', $siswa->id_siswa)
            ->where('id_tahun_ajaran', $activeTahunAjaran->id_tahun_ajaran)
            ->where('id_semester', $activeTahunAjaran->id_semester)
            ->first();
        $isPublished = $raporStatusObj && in_array($raporStatusObj->status, ['dikirim', 'diverifikasi']);

        if ($isPublished) {
            // Get grades grouped by subject
            $nilaiTps = NilaiTp::with(['mapel', 'tp'])
                ->where('id_siswa', $siswa->id_siswa)
                ->whereHas('guruMapel', function($query) use ($activeTahunAjaran) {
                    $query->where('id_semester', $activeTahunAjaran->id_semester)
                          ->where('id_tahun_ajaran', $activeTahunAjaran->id_tahun_ajaran);
                })
                ->whereNotNull('nilai_akhir_tp')
                ->get()
                ->groupBy('id_mapel');
        } else {
            $nilaiTps = collect();
        }

        return view('orangtua.nilai.index', compact(
            'siswa',
            'nilaiTps',
            'activeTahunAjaran'
        ));
    }

    public function detail($mapel_id): View
    {
        $selectedChildId = session('selected_child_id');
        
        if (!$selectedChildId) {
            return redirect()->route('orangtua.select-anak')
                ->with('error', 'Silakan pilih anak terlebih dahulu');
        }

        $siswa = Siswa::findOrFail($selectedChildId);
        $activeTahunAjaran = TahunAjaran::activeOrAbort();

        // Check RaporStatus
        $raporStatusObj = \App\Models\RaporStatus::where('id_siswa', $siswa->id_siswa)
            ->where('id_tahun_ajaran', $activeTahunAjaran->id_tahun_ajaran)
            ->where('id_semester', $activeTahunAjaran->id_semester)
            ->first();
        $isPublished = $raporStatusObj && in_array($raporStatusObj->status, ['dikirim', 'diverifikasi']);

        if (!$isPublished) {
            return redirect()->route('orangtua.nilai.index')
                ->with('error', 'Nilai belum dipublikasikan oleh Wali Kelas.');
        }

        // Get detailed grades for specific subject
        $nilaiTps = NilaiTp::with(['mapel', 'tp'])
            ->where('id_siswa', $siswa->id_siswa)
            ->where('id_mapel', $mapel_id)
            ->whereHas('guruMapel', function($query) use ($activeTahunAjaran) {
                $query->where('id_semester', $activeTahunAjaran->id_semester)
                      ->where('id_tahun_ajaran', $activeTahunAjaran->id_tahun_ajaran);
            })
            ->get();

        $mapel = \App\Models\Mapel::findOrFail($mapel_id);

        return view('orangtua.nilai.detail', compact(
            'siswa',
            'nilaiTps',
            'mapel',
            'activeTahunAjaran'
        ));
    }
}
