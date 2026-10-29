<?php

namespace App\Http\Controllers\Siswa;

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
        $siswa = Auth::user()->siswa;
        $activeTahunAjaran = TahunAjaran::activeOrAbort();

        // Check RaporStatus
        $raporStatusObj = \App\Models\RaporStatus::where('id_siswa', $siswa->id_siswa ?? $siswa->id)
            ->where('id_tahun_ajaran', $activeTahunAjaran->id_tahun_ajaran ?? $activeTahunAjaran->id)
            ->where('id_semester', $activeTahunAjaran->id_semester)
            ->first();
        $isPublished = $raporStatusObj && in_array($raporStatusObj->status, ['dikirim', 'diverifikasi']);

        if ($isPublished) {
            // Get grades grouped by subject
            $nilaiTps = NilaiTp::with(['guruMapel.mapel', 'guruMapel.guru', 'tp'])
                ->where('id_siswa', $siswa->id)
                ->where('id_semester', $activeTahunAjaran->id_semester)
                ->where('id_tahun_ajaran', $activeTahunAjaran->id)
                ->whereNotNull('nilai_tp')
                ->get()
                ->groupBy('guruMapel.mapel_id');
        } else {
            $nilaiTps = collect();
        }

        return view('siswa.nilai.index', compact(
            'siswa',
            'nilaiTps',
            'activeTahunAjaran'
        ));
    }

    public function detail($mapel_id): View
    {
        $siswa = Auth::user()->siswa;
        $activeTahunAjaran = TahunAjaran::activeOrAbort();

        // Check RaporStatus
        $raporStatusObj = \App\Models\RaporStatus::where('id_siswa', $siswa->id_siswa ?? $siswa->id)
            ->where('id_tahun_ajaran', $activeTahunAjaran->id_tahun_ajaran ?? $activeTahunAjaran->id)
            ->where('id_semester', $activeTahunAjaran->id_semester)
            ->first();
        $isPublished = $raporStatusObj && in_array($raporStatusObj->status, ['dikirim', 'diverifikasi']);

        if (!$isPublished) {
            return redirect()->route('siswa.nilai.index')
                ->with('error', 'Nilai belum dipublikasikan oleh Wali Kelas.');
        }

        // Get detailed grades for specific subject
        $nilaiTps = NilaiTp::with(['guruMapel.mapel', 'guruMapel.guru', 'tp'])
            ->where('id_siswa', $siswa->id)
            ->whereHas('guruMapel', function($query) use ($mapel_id) {
                $query->where('mapel_id', $mapel_id);
            })
            ->where('id_semester', $activeTahunAjaran->id_semester)
            ->where('id_tahun_ajaran', $activeTahunAjaran->id)
            ->orderBy('tp.kode_tp')
            ->get();

        $mapel = \App\Models\Mapel::findOrFail($mapel_id);

        return view('siswa.nilai.detail', compact(
            'siswa',
            'nilaiTps',
            'mapel',
            'activeTahunAjaran'
        ));
    }
}
