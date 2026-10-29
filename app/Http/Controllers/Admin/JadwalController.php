<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JadwalPelajaran;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\Guru;
use App\Models\TahunAjaran;
use App\Models\Semester;
use App\Models\Ruangan;
use App\Models\JamPelajaran;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Validator;

class JadwalController extends Controller
{
    public function index(): View
    {
        $activeTahunAjaran = TahunAjaran::where('status', 'aktif')->first();
        
        $query = JadwalPelajaran::with(['kelas', 'mapel', 'guru', 'tahunAjaran', 'jam', 'ruangan']);
        
        if ($activeTahunAjaran) {
            $query->where('id_tahun_ajaran', $activeTahunAjaran->id_tahun_ajaran);
        }
        
        $jadwals = $query->paginate(10);
        return view('admin.jadwal.index', compact('jadwals'));
    }

    public function create(): View
    {
        $kelases = Kelas::all();
        $mapels = Mapel::all();
        $gurus = Guru::all();
        $tahuns = TahunAjaran::all();
        $semesters = Semester::all();
        $activeTahun = TahunAjaran::active();
        $ruangans = Ruangan::all();
        $jamPelajarans = JamPelajaran::orderBy('urutan')->get();

        return view('admin.jadwal.create', compact('kelases', 'mapels', 'gurus', 'tahuns', 'semesters', 'activeTahun', 'ruangans', 'jamPelajarans'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'id_kelas'        => 'required|exists:kelas,id_kelas',
            'id_mapel'        => 'required|exists:mapel,id_mapel',
            'id_guru'         => 'required|exists:guru,id_guru',
            'id_tahun_ajaran' => 'required|exists:tahun_ajaran,id_tahun_ajaran',
            'hari'            => 'required|in:Senin,Selasa,Rabu,Kamis,Sabtu,Minggu',
            'id_jam'          => 'required|exists:jam_pelajaran,id_jam',
            'id_ruangan'      => 'nullable|exists:ruangan,id_ruangan',
            'status'          => 'nullable|in:aktif,non-aktif',
        ], [
            'hari.in' => 'Hari Jumat adalah libur sekolah. Jadwal hanya boleh di hari Senin-Kamis atau Sabtu-Minggu.',
        ]);

        if (!isset($validated['status'])) {
            $validated['status'] = 'aktif';
        }

        // Cek Bentrok Guru: guru + hari + jam + tahun ajaran yang sama
        $bentrok = JadwalPelajaran::where('id_guru', $request->id_guru)
            ->where('hari', $request->hari)
            ->where('id_tahun_ajaran', $request->id_tahun_ajaran)
            ->where('id_jam', $request->id_jam)
            ->where('status', 'aktif')
            ->first();

        if ($bentrok) {
            $namaKelas = $bentrok->kelas->nama_kelas ?? 'kelas lain';
            return back()->withInput()->with('error',
                'KRES/BENTROK! Guru ini sudah mengajar ' . $namaKelas . ' pada hari ' . $request->hari . ' di jam yang sama.');
        }

        // Cek Bentrok Kelas
        $bentrokKelas = JadwalPelajaran::where('id_kelas', $request->id_kelas)
            ->where('hari', $request->hari)
            ->where('id_tahun_ajaran', $request->id_tahun_ajaran)
            ->where('id_jam', $request->id_jam)
            ->where('status', 'aktif')
            ->first();

        if ($bentrokKelas) {
            return back()->withInput()->with('error',
                'KRES/BENTROK! Kelas ini sudah memiliki jadwal pada hari ' . $request->hari . ' di jam yang sama.');
        }

        // Cek Bentrok Ruangan
        if ($request->id_ruangan) {
            $bentrokRuangan = JadwalPelajaran::where('id_ruangan', $request->id_ruangan)
                ->where('hari', $request->hari)
                ->where('id_tahun_ajaran', $request->id_tahun_ajaran)
                ->where('id_jam', $request->id_jam)
                ->where('status', 'aktif')
                ->first();

            if ($bentrokRuangan) {
                $namaRuangan = $bentrokRuangan->ruangan->nama_ruangan ?? 'ruangan';
                return back()->withInput()->with('error',
                    'KRES/BENTROK! Ruangan ' . $namaRuangan . ' sudah dipakai pada hari ' . $request->hari . ' di jam yang sama.');
            }
        }

        JadwalPelajaran::create($validated);

        return redirect()->route('admin.jadwal.index')
            ->with('success', 'Jadwal pelajaran berhasil ditambahkan');
    }

    public function edit($id): View
    {
        $jadwal = JadwalPelajaran::findOrFail($id);
        $kelases = Kelas::all();
        $mapels = Mapel::all();
        $gurus = Guru::all();
        $tahuns = TahunAjaran::all();
        $semesters = Semester::all();
        $ruangans = Ruangan::all();
        $jamPelajarans = JamPelajaran::orderBy('urutan')->get();

        return view('admin.jadwal.edit', compact('jadwal', 'kelases', 'mapels', 'gurus', 'tahuns', 'semesters', 'ruangans', 'jamPelajarans'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $jadwal = JadwalPelajaran::findOrFail($id);

        $validated = $request->validate([
            'id_kelas'        => 'required|exists:kelas,id_kelas',
            'id_mapel'        => 'required|exists:mapel,id_mapel',
            'id_guru'         => 'required|exists:guru,id_guru',
            'id_tahun_ajaran' => 'required|exists:tahun_ajaran,id_tahun_ajaran',
            'hari'            => 'required|in:Senin,Selasa,Rabu,Kamis,Sabtu,Minggu',
            'id_jam'          => 'required|exists:jam_pelajaran,id_jam',
            'id_ruangan'      => 'nullable|exists:ruangan,id_ruangan',
            'status'          => 'required|in:aktif,non-aktif',
        ], [
            'hari.in' => 'Hari Jumat adalah libur sekolah. Jadwal hanya boleh di hari Senin-Kamis atau Sabtu-Minggu.',
        ]);

        // Cek Bentrok Guru (kecuali jadwal yang sedang diedit)
        if ($validated['status'] === 'aktif') {
            $bentrok = JadwalPelajaran::where('id_guru', $request->id_guru)
                ->where('id_jadwal', '!=', $id)
                ->where('hari', $request->hari)
                ->where('id_tahun_ajaran', $request->id_tahun_ajaran)
                ->where('id_jam', $request->id_jam)
                ->where('status', 'aktif')
                ->first();

            if ($bentrok) {
                $namaKelas = $bentrok->kelas->nama_kelas ?? 'kelas lain';
                return back()->withInput()->with('error',
                    'KRES/BENTROK! Guru ini sudah mengajar ' . $namaKelas . ' pada hari ' . $request->hari . ' di jam yang sama.');
            }

            // Cek Bentrok Kelas
            $bentrokKelas = JadwalPelajaran::where('id_kelas', $request->id_kelas)
                ->where('id_jadwal', '!=', $id)
                ->where('hari', $request->hari)
                ->where('id_tahun_ajaran', $request->id_tahun_ajaran)
                ->where('id_jam', $request->id_jam)
                ->where('status', 'aktif')
                ->first();

            if ($bentrokKelas) {
                return back()->withInput()->with('error',
                    'KRES/BENTROK! Kelas ini sudah memiliki jadwal pada hari ' . $request->hari . ' di jam yang sama.');
            }

            // Cek Bentrok Ruangan
            if ($request->id_ruangan) {
                $bentrokRuangan = JadwalPelajaran::where('id_ruangan', $request->id_ruangan)
                    ->where('id_jadwal', '!=', $id)
                    ->where('hari', $request->hari)
                    ->where('id_tahun_ajaran', $request->id_tahun_ajaran)
                    ->where('id_jam', $request->id_jam)
                    ->where('status', 'aktif')
                    ->first();

                if ($bentrokRuangan) {
                    $namaRuangan = $bentrokRuangan->ruangan->nama_ruangan ?? 'ruangan';
                    return back()->withInput()->with('error',
                        'KRES/BENTROK! Ruangan ' . $namaRuangan . ' sudah dipakai pada hari ' . $request->hari . ' di jam yang sama.');
                }
            }
        }

        $jadwal->update($validated);

        return redirect()->route('admin.jadwal.index')
            ->with('success', 'Jadwal pelajaran berhasil diperbarui');
    }

    public function destroy($id): RedirectResponse
    {
        $jadwal = JadwalPelajaran::findOrFail($id);
        $jadwal->delete();

        return redirect()->route('admin.jadwal.index')
            ->with('success', 'Jadwal pelajaran berhasil dihapus');
    }
}
