<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class TahunAjaranController extends Controller
{
    public function index(): View
    {
        $tahunAjarans = TahunAjaran::paginate(10);
        return view('admin.tahun-ajaran.index', compact('tahunAjarans'));
    }

    public function create(): View
    {
        return view('admin.tahun-ajaran.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'tahun_ajaran' => 'required|string|max:9|unique:tahun_ajaran,tahun_ajaran',
            'id_semester' => 'required|in:1,2',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai'
        ]);

        TahunAjaran::create([
            'tahun_ajaran' => $request->tahun_ajaran,
            'id_semester' => $request->id_semester,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'status' => 'non-aktif'
        ]);

        return redirect()->route('admin.tahun-ajaran.index')
            ->with('success', 'Tahun ajaran berhasil ditambahkan');
    }

    public function show(TahunAjaran $tahunAjaran): View
    {
        $tahunAjaran->load('kelas', 'guruMapels.guru', 'guruMapels.mapel', 'guruMapels.kelas');
        return view('admin.tahun-ajaran.show', compact('tahunAjaran'));
    }

    public function edit(TahunAjaran $tahunAjaran): View
    {
        return view('admin.tahun-ajaran.edit', compact('tahunAjaran'));
    }

    public function update(Request $request, TahunAjaran $tahunAjaran): RedirectResponse
    {
        $request->validate([
            'tahun_ajaran' => 'required|string|max:9|unique:tahun_ajaran,tahun_ajaran,' . $tahunAjaran->id_tahun_ajaran . ',id_tahun_ajaran',
            'id_semester' => 'required|in:1,2',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai'
        ]);

        $tahunAjaran->update([
            'tahun_ajaran' => $request->tahun_ajaran,
            'id_semester' => $request->id_semester,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai
        ]);

        return redirect()->route('admin.tahun-ajaran.index')
            ->with('success', 'Tahun ajaran berhasil diperbarui');
    }

    public function destroy(TahunAjaran $tahunAjaran): RedirectResponse
    {
        if ($tahunAjaran->status === 'aktif') {
            return back()->with('error', 'Tidak dapat menghapus tahun ajaran yang sedang aktif');
        }

        $tahunAjaran->delete();

        return redirect()->route('admin.tahun-ajaran.index')
            ->with('success', 'Tahun ajaran berhasil dihapus');
    }

    public function setActive(TahunAjaran $tahunAjaran): RedirectResponse
    {
        // Reset all tahun ajaran status
        TahunAjaran::query()->update(['status' => 'non-aktif']);

        // Set active tahun ajaran
        $tahunAjaran->update(['status' => 'aktif']);

        return back()->with('success', 'Tahun ajaran berhasil diaktifkan');
    }
}
