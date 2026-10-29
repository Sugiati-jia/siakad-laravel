<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TujuanPembelajaran;
use App\Models\Mapel;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class TujuanPembelajaranController extends Controller
{
    public function index(): View
    {
        $tujuanPembelajarans = TujuanPembelajaran::with('mapel')->paginate(10);
        return view('admin.tujuan-pembelajaran.index', compact('tujuanPembelajarans'));
    }

    public function create(): View
    {
        $mapels = Mapel::all();
        return view('admin.tujuan-pembelajaran.create', compact('mapels'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'mapel_id'    => 'required|exists:mapel,id_mapel',
            'kode_tp'     => 'required|string|max:50',
            'deskripsi_tp'=> 'required|string',
            'id_tingkat'  => 'required|exists:tingkat_kelas,id_tingkat',
        ]);

        TujuanPembelajaran::create([
            'id_mapel'  => $request->mapel_id,
            'kode_tp'   => $request->kode_tp,
            'deskripsi' => $request->deskripsi_tp,
            'id_tingkat'=> $request->id_tingkat,
        ]);

        return redirect()->route('admin.tujuan-pembelajaran.index')
            ->with('success', 'Tujuan pembelajaran berhasil ditambahkan');
    }

    public function show(TujuanPembelajaran $tujuanPembelajaran): View
    {
        $tujuanPembelajaran->load('mapel', 'nilaiTps.siswa');
        return view('admin.tujuan-pembelajaran.show', compact('tujuanPembelajaran'));
    }

    public function edit(TujuanPembelajaran $tujuanPembelajaran): View
    {
        $mapels = Mapel::all();
        $tingkats = \App\Models\TingkatKelas::all();
        return view('admin.tujuan-pembelajaran.edit', compact('tujuanPembelajaran', 'mapels', 'tingkats'));
    }

    public function update(Request $request, TujuanPembelajaran $tujuanPembelajaran): RedirectResponse
    {
        $request->validate([
            'mapel_id'    => 'required|exists:mapel,id_mapel',
            'kode_tp'     => 'required|string|max:50',
            'deskripsi_tp'=> 'required|string',
            'id_tingkat'  => 'required|exists:tingkat_kelas,id_tingkat',
        ]);

        $tujuanPembelajaran->update([
            'id_mapel'  => $request->mapel_id,
            'kode_tp'   => $request->kode_tp,
            'deskripsi' => $request->deskripsi_tp,
            'id_tingkat'=> $request->id_tingkat,
        ]);

        return redirect()->route('admin.tujuan-pembelajaran.index')
            ->with('success', 'Tujuan pembelajaran berhasil diperbarui');
    }

    public function destroy(TujuanPembelajaran $tujuanPembelajaran): RedirectResponse
    {
        $tujuanPembelajaran->delete();

        return redirect()->route('admin.tujuan-pembelajaran.index')
            ->with('success', 'Tujuan pembelajaran berhasil dihapus');
    }
}
