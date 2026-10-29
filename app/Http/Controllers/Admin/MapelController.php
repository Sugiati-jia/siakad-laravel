<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mapel;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class MapelController extends Controller
{
    public function index(): View
    {
        $mapels = Mapel::with('tujuanPembelajarans')->paginate(10);
        return view('admin.mapel.index', compact('mapels'));
    }

    public function create(): View
    {
        return view('admin.mapel.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'kode_mapel' => 'required|unique:mapel,kode_mapel',
            'nama_mapel' => 'required|string|max:255',
            'kelompok_mapel' => 'required|in:A,B,C,Mata Pelajaran Umum',
            'urutan' => 'nullable|integer|min:1'
        ]);

        Mapel::create([
            'kode_mapel' => $request->kode_mapel,
            'nama' => $request->nama_mapel,
            'kelompok_mapel' => $request->kelompok_mapel,
            'urutan' => $request->urutan,
            'status' => 'aktif'
        ]);

        return redirect()->route('admin.mapel.index')
            ->with('success', 'Data mata pelajaran berhasil ditambahkan');
    }

    public function show(Mapel $mapel): View
    {
        $mapel->load('tujuanPembelajarans', 'guruMapels.guru', 'guruMapels.kelas');
        return view('admin.mapel.show', compact('mapel'));
    }

    public function edit(Mapel $mapel): View
    {
        return view('admin.mapel.edit', compact('mapel'));
    }

    public function update(Request $request, Mapel $mapel): RedirectResponse
    {
        $request->validate([
            'kode_mapel' => 'required|unique:mapel,kode_mapel,' . $mapel->id_mapel . ',id_mapel',
            'nama_mapel' => 'required|string|max:255',
            'kelompok_mapel' => 'required|in:A,B,C,Mata Pelajaran Umum',
            'urutan' => 'nullable|integer|min:1'
        ]);

        $mapel->update([
            'kode_mapel' => $request->kode_mapel,
            'nama' => $request->nama_mapel,
            'kelompok_mapel' => $request->kelompok_mapel,
            'urutan' => $request->urutan
        ]);

        return redirect()->route('admin.mapel.index')
            ->with('success', 'Data mata pelajaran berhasil diperbarui');
    }

    public function destroy(Mapel $mapel): RedirectResponse
    {
        $mapel->delete();

        return redirect()->route('admin.mapel.index')
            ->with('success', 'Data mata pelajaran berhasil dihapus');
    }
}
