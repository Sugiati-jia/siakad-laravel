<?php

namespace App\Http\Controllers\WaliKelas;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Kokurikuler;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class KokurikulerController extends Controller
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

        $activeTahunAjaran = TahunAjaran::activeOrAbort();

        // Get existing notes
        $existingNotes = Kokurikuler::where('id_semester', $activeTahunAjaran->id_semester)
            ->where('id_tahun_ajaran', $activeTahunAjaran->id_tahun_ajaran)
            ->get()
            ->keyBy('id_siswa');

        return view('walikelas.kokurikuler.index', compact(
            'kelas',
            'siswas',
            'existingNotes',
            'activeTahunAjaran'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'id_siswa' => 'required|exists:siswa,id_siswa',
            'id_kelas' => 'required|exists:kelas,id_kelas',
            'id_semester' => 'required|in:1,2',
            'id_tahun_ajaran' => 'required|exists:tahun_ajaran,id_tahun_ajaran',
            'deskripsi' => 'required|string',
        ]);

        $waliKelas = Auth::user()->guru;
        
        // Verify access
        $kelas = Kelas::where('id_wali_kelas', $waliKelas->id_guru)
            ->findOrFail($request->id_kelas);

        $siswa = Siswa::where('id_kelas', $request->id_kelas)
            ->findOrFail($request->id_siswa);

        Kokurikuler::updateOrCreate(
            [
                'id_siswa' => $request->id_siswa,
                'id_semester' => $request->id_semester,
                'id_tahun_ajaran' => $request->id_tahun_ajaran,
            ],
            [
                'deskripsi' => $request->deskripsi,
            ]
        );

        return back()->with('success', 'Nilai kokurikuler P5 berhasil disimpan');
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'deskripsi' => 'required|string',
        ]);

        $waliKelas = Auth::user()->guru;
        
        $kokurikuler = Kokurikuler::findOrFail($id);

        // Verify class access
        $siswa = Siswa::findOrFail($kokurikuler->id_siswa);
        $kelas = Kelas::where('id_wali_kelas', $waliKelas->id_guru)
            ->findOrFail($siswa->id_kelas);

        $kokurikuler->update([
            'deskripsi' => $request->deskripsi
        ]);

        return back()->with('success', 'Nilai kokurikuler P5 berhasil diperbarui');
    }

    public function destroy($id): RedirectResponse
    {
        $waliKelas = Auth::user()->guru;
        
        $kokurikuler = Kokurikuler::findOrFail($id);

        // Verify class access
        $siswa = Siswa::findOrFail($kokurikuler->id_siswa);
        $kelas = Kelas::where('id_wali_kelas', $waliKelas->id_guru)
            ->findOrFail($siswa->id_kelas);

        $kokurikuler->delete();

        return back()->with('success', 'Nilai kokurikuler P5 berhasil dihapus');
    }
}
