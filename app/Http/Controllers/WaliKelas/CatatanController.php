<?php

namespace App\Http\Controllers\WaliKelas;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\CatatanWaliKelas;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class CatatanController extends Controller
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
        $existingNotes = CatatanWaliKelas::where('id_semester', $activeTahunAjaran->id_semester)
            ->where(function($q) use ($activeTahunAjaran) {
                $q->where('id_tahun_ajaran', $activeTahunAjaran->id_tahun_ajaran)
                  ->orWhere('id_tahun_ajaran', $activeTahunAjaran->id_tahun_ajaran);
            })
            ->get()
            ->keyBy(function($item) {
                return $item->id_siswa ?? $item->id_siswa;
            });

        return view('walikelas.catatan.index', compact(
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
            'catatan' => 'required|string',
            'jenis_catatan' => 'required|in:prestasi,pelanggaran,umum',
            'sakit' => 'nullable|integer|min:0',
            'izin' => 'nullable|integer|min:0',
            'alpa' => 'nullable|integer|min:0',
            'status_kenaikan' => 'nullable|in:naik,tinggal',
        ]);

        $waliKelas = Auth::user()->guru;
        
        // Verify this wali kelas has access to this class
        $kelas = Kelas::where('id_wali_kelas', $waliKelas->id_guru)
            ->findOrFail($request->id_kelas);

        // Verify student belongs to this class
        $siswa = Siswa::where('id_kelas', $request->id_kelas)
            ->findOrFail($request->id_siswa);

        CatatanWaliKelas::updateOrCreate(
            [
                'id_siswa' => $request->id_siswa,
                'id_semester' => $request->id_semester,
                'id_tahun_ajaran' => $request->id_tahun_ajaran,
            ],
            [
                'id_kelas' => $request->id_kelas,
                'catatan' => $request->catatan,
                'jenis_catatan' => $request->jenis_catatan,
                'sakit' => $request->sakit ?? 0,
                'izin' => $request->izin ?? 0,
                'alpa' => $request->alpa ?? 0,
                'status_kenaikan' => $request->status_kenaikan,
                'status' => 'aktif'
            ]
        );

        return back()->with('success', 'Catatan wali kelas & absensi berhasil disimpan');
    }

    public function update(Request $request, $id): RedirectResponse|\Illuminate\Http\JsonResponse
    {
        $request->validate([
            'catatan' => 'required|string',
            'jenis_catatan' => 'required|in:prestasi,pelanggaran,umum',
            'sakit' => 'nullable|integer|min:0',
            'izin' => 'nullable|integer|min:0',
            'alpa' => 'nullable|integer|min:0',
            'status_kenaikan' => 'nullable|in:naik,tinggal',
        ]);

        $waliKelas = Auth::user()->guru;
        
        $catatan = CatatanWaliKelas::whereHas('siswa.kelas', function($query) use ($waliKelas) {
                $query->where('id_wali_kelas', $waliKelas->id_guru);
            })
            ->findOrFail($id);

        $catatan->update([
            'catatan' => $request->catatan,
            'jenis_catatan' => $request->jenis_catatan,
            'sakit' => $request->sakit ?? 0,
            'izin' => $request->izin ?? 0,
            'alpa' => $request->alpa ?? 0,
            'status_kenaikan' => $request->status_kenaikan,
        ]);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Catatan wali kelas & absensi berhasil diperbarui']);
        }

        return back()->with('success', 'Catatan wali kelas & absensi berhasil diperbarui');
    }

    public function destroy(Request $request, $id): RedirectResponse|\Illuminate\Http\JsonResponse
    {
        $waliKelas = Auth::user()->guru;
        
        $catatan = CatatanWaliKelas::whereHas('siswa.kelas', function($query) use ($waliKelas) {
                $query->where('id_wali_kelas', $waliKelas->id_guru);
            })
            ->findOrFail($id);

        $catatan->delete();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Catatan wali kelas & absensi berhasil dihapus']);
        }

        return back()->with('success', 'Catatan wali kelas & absensi berhasil dihapus');
    }
}
