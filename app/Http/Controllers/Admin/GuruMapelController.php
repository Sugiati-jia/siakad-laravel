<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GuruMapel;
use App\Models\Guru;
use App\Models\Mapel;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class GuruMapelController extends Controller
{
    public function index(): View
    {
        // AUTO CLEANUP: Hapus data yang rusak/kosong akibat bug sebelumnya agar tidak membuat error 500
        \App\Models\GuruMapel::whereNull('id_guru')->orWhereNull('id_mapel')->delete();

        $activeTahunAjaran = TahunAjaran::where('status', 'aktif')->first();
        
        $query = GuruMapel::with('guru', 'mapel', 'kelas', 'tahunAjaran');
        
        if ($activeTahunAjaran) {
            $query->where('id_tahun_ajaran', $activeTahunAjaran->id_tahun_ajaran);
        }
        
        $guruMapels = $query->paginate(10);
        return view('admin.guru-mapel.index', compact('guruMapels'));
    }

    public function create(): View
    {
        $gurus = Guru::whereHas('user', function($query) {
            $query->where('role', 'guru');
        })->get();
        
        $mapels = Mapel::all();
        $kelas = Kelas::all();
        $tahunAjarans = TahunAjaran::all();
        
        return view('admin.guru-mapel.create', compact('gurus', 'mapels', 'kelas', 'tahunAjarans'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'guru_id' => 'required|exists:guru,id_guru',
            'mapel_id' => 'required|exists:mapel,id_mapel',
            'id_kelas' => 'required|exists:kelas,id_kelas',
            'id_tahun_ajaran' => 'required|exists:tahun_ajaran,id_tahun_ajaran',
        ]);

        GuruMapel::create([
            'id_guru'         => $request->guru_id,
            'id_mapel'        => $request->mapel_id,
            'id_kelas'        => $request->id_kelas,
            'id_tahun_ajaran' => $request->id_tahun_ajaran,
            'status'          => 'aktif'
        ]);

        return redirect()->route('admin.guru-mapel.index')
            ->with('success', 'Penugasan guru mapel berhasil ditambahkan');
    }

    public function show(GuruMapel $guruMapel): View
    {
        $guruMapel->load('guru', 'mapel', 'kelas', 'tahunAjaran', 'nilaiTps.siswa');
        return view('admin.guru-mapel.show', compact('guruMapel'));
    }

    public function edit(GuruMapel $guruMapel): View
    {
        $gurus = Guru::whereHas('user', function($query) {
            $query->where('role', 'guru');
        })->get();
        
        $mapels = Mapel::all();
        $kelas = Kelas::all();
        $tahunAjarans = TahunAjaran::all();
        
        return view('admin.guru-mapel.edit', compact('guruMapel', 'gurus', 'mapels', 'kelas', 'tahunAjarans'));
    }

    public function update(Request $request, GuruMapel $guruMapel): RedirectResponse
    {
        $request->validate([
            'guru_id' => 'required|exists:guru,id_guru',
            'mapel_id' => 'required|exists:mapel,id_mapel',
            'id_kelas' => 'required|exists:kelas,id_kelas',
            'id_tahun_ajaran' => 'required|exists:tahun_ajaran,id_tahun_ajaran',
        ]);

        $guruMapel->update([
            'id_guru'         => $request->guru_id,
            'id_mapel'        => $request->mapel_id,
            'id_kelas'        => $request->id_kelas,
            'id_tahun_ajaran' => $request->id_tahun_ajaran,
        ]);

        return redirect()->route('admin.guru-mapel.index')
            ->with('success', 'Penugasan guru mapel berhasil diperbarui');
    }

    public function destroy(GuruMapel $guruMapel): RedirectResponse
    {
        $guruMapel->delete();

        return redirect()->route('admin.guru-mapel.index')
            ->with('success', 'Penugasan guru mapel berhasil dihapus');
    }
}
