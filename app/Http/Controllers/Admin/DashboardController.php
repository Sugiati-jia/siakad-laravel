<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\NilaiTp;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalGuru = Guru::count();
        $totalSiswa = Siswa::count();
        $totalKelas = Kelas::count();
        $totalMapel = Mapel::count();
        
        $activeTahunAjaran = TahunAjaran::active();

        // Top 10 students across all classes (leaderboard)
        $topStudentIds = NilaiTp::whereNotNull('nilai_akhir_tp')
            ->select('id_siswa')
            ->selectRaw('ROUND(AVG(nilai_akhir_tp), 1) as rata_rata')
            ->groupBy('id_siswa')
            ->orderByDesc('rata_rata')
            ->limit(10)
            ->get()
            ->pluck('rata_rata', 'id_siswa');

        $topStudents = Siswa::with('kelas')
            ->whereIn('id_siswa', $topStudentIds->keys())
            ->get()
            ->map(function($siswa) use ($topStudentIds) {
                $siswa->rata_rata = $topStudentIds[$siswa->id_siswa] ?? 0;
                return $siswa;
            })
            ->sortByDesc('rata_rata')
            ->values();
        
        return view('admin.dashboard', compact(
            'totalGuru',
            'totalSiswa', 
            'totalKelas',
            'totalMapel',
            'activeTahunAjaran',
            'topStudents'
        ));
    }

    public function setActiveSemester(Request $request)
    {
        $request->validate([
            'id_tahun_ajaran' => 'required|exists:tahun_ajaran,id_tahun_ajaran',
            'id_semester' => 'required|in:1,2'
        ]);

        // Reset all tahun ajaran status
        TahunAjaran::query()->update(['status' => 'non-aktif']);

        // Set active tahun ajaran
        $tahunAjaran = TahunAjaran::find($request->id_tahun_ajaran);
        
        // FIX: Tambahkan error handling jika data tidak ditemukan (misal dihapus dari database)
        if (!$tahunAjaran) {
            return back()->with('error', 'Tahun ajaran tidak ditemukan.');
        }

        $tahunAjaran->update([
            'status' => 'aktif',
            'id_semester' => $request->id_semester
        ]);

        return back()->with('success', 'Semester aktif berhasil diatur');
    }

    public function setActiveTahunAjaran(Request $request)
    {
        // FIX: Ubah 'id' menjadi 'id_tahun_ajaran' karena tabel tidak memiliki kolom 'id'
        $request->validate([
            'id_tahun_ajaran' => 'required|exists:tahun_ajaran,id_tahun_ajaran'
        ]);

        // Reset all tahun ajaran status
        TahunAjaran::query()->update(['status' => 'non-aktif']);

        // Set active tahun ajaran
        $tahunAjaran = TahunAjaran::find($request->id_tahun_ajaran);
        
        // FIX: Tambahkan error handling jika data tidak ditemukan
        if (!$tahunAjaran) {
            return back()->with('error', 'Tahun ajaran tidak ditemukan.');
        }

        $tahunAjaran->update(['status' => 'aktif']);

        return back()->with('success', 'Tahun ajaran aktif berhasil diatur');
    }
}
