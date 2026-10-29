<?php

namespace App\Http\Controllers\WaliKelas;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Ekstrakurikuler;
use App\Models\NilaiEkstrakurikuler;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class EkstrakurikulerController extends Controller
{
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

        $ekstrakurikulers = Ekstrakurikuler::all();

        $activeTahunAjaran = TahunAjaran::activeOrAbort();

        // Get existing grades
        $existingGrades = NilaiEkstrakurikuler::where('id_semester', $activeTahunAjaran->id_semester)
            ->where('id_tahun_ajaran', $activeTahunAjaran->id)
            ->get()
            ->keyBy(function($item) {
                return $item->id_siswa . '_' . $item->id_ekstrakurikuler;
            });

        return view('walikelas.ekstrakurikuler.index', compact(
            'kelas',
            'siswas',
            'ekstrakurikulers',
            'existingGrades',
            'activeTahunAjaran'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'id_siswa' => 'required|exists:siswa,id_siswa',
            'id_ekstrakurikuler' => 'required|exists:ekstrakurikuler,id_ekstrakurikuler',
            'id_semester' => 'required|in:1,2',
            'id_tahun_ajaran' => 'required|exists:tahun_ajaran,id_tahun_ajaran',
            'nilai' => 'required|numeric|min:0|max:100',
            'deskripsi' => 'nullable|string'
        ]);

        $waliKelas = Auth::user()->guru;
        
        // Verify this wali kelas has access to this student
        $siswa = Siswa::whereHas('kelas', function($query) use ($waliKelas) {
                $query->where('id_wali_kelas', $waliKelas->id_guru);
            })
            ->findOrFail($request->id_siswa);

        $nilai = $request->nilai;
        $keterangan = match(true) {
            $nilai >= 85 => 'Sangat Baik',
            $nilai >= 70 => 'Baik',
            $nilai >= 60 => 'Cukup',
            default => 'Kurang'
        };

        // Check if grade already exists for this student and extracurricular
        $existing = NilaiEkstrakurikuler::where('id_siswa', $request->id_siswa)
            ->where('id_ekstrakurikuler', $request->id_ekstrakurikuler)
            ->where('id_semester', $request->id_semester)
            ->where('id_tahun_ajaran', $request->id_tahun_ajaran)
            ->exists();

        if ($existing) {
            abort(409, 'Nilai untuk siswa dan ekstrakurikuler ini sudah pernah diinput.');
        }

        NilaiEkstrakurikuler::create([
            'id_siswa' => $request->id_siswa,
            'id_ekstrakurikuler' => $request->id_ekstrakurikuler,
            'id_semester' => $request->id_semester,
            'id_tahun_ajaran' => $request->id_tahun_ajaran,
            'nilai' => $nilai,
            'keterangan' => $keterangan,
            'deskripsi' => $request->deskripsi ?? 'Mengikuti kegiatan dengan ' . strtolower($keterangan),
        ]);

        return back()->with('success', 'Nilai ekstrakurikuler berhasil disimpan');
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'nilai' => 'required|numeric|min:0|max:100',
            'deskripsi' => 'nullable|string'
        ]);

        $waliKelas = Auth::user()->guru;
        
        $nilaiEkstrakurikuler = NilaiEkstrakurikuler::whereHas('siswa.kelas', function($query) use ($waliKelas) {
                $query->where('id_wali_kelas', $waliKelas->id_guru);
            })
            ->findOrFail($id);

        $nilai = $request->nilai;
        $keterangan = match(true) {
            $nilai >= 85 => 'Sangat Baik',
            $nilai >= 70 => 'Baik',
            $nilai >= 60 => 'Cukup',
            default => 'Kurang'
        };

        $nilaiEkstrakurikuler->update([
            'nilai' => $nilai,
            'keterangan' => $keterangan,
            'deskripsi' => $request->deskripsi ?? 'Mengikuti kegiatan dengan ' . strtolower($keterangan)
        ]);

        return back()->with('success', 'Nilai ekstrakurikuler berhasil diperbarui');
    }

    public function destroy($id): RedirectResponse
    {
        $waliKelas = Auth::user()->guru;
        
        $nilaiEkstrakurikuler = NilaiEkstrakurikuler::whereHas('siswa.kelas', function($query) use ($waliKelas) {
                $query->where('id_wali_kelas', $waliKelas->id_guru);
            })
            ->findOrFail($id);

        $nilaiEkstrakurikuler->delete();

        return back()->with('success', 'Nilai ekstrakurikuler berhasil dihapus');
    }
}
