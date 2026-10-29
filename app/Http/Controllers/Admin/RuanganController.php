<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ruangan;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class RuanganController extends Controller
{
    public function index(): View
    {
        $ruangans = Ruangan::orderBy('nama_ruangan')->paginate(15);
        return view('admin.ruangan.index', compact('ruangans'));
    }

    public function create(): View
    {
        return view('admin.ruangan.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nama_ruangan' => 'required|string|max:50|unique:ruangan,nama_ruangan',
            'kode_ruangan' => 'nullable|string|max:20|unique:ruangan,kode_ruangan',
        ]);

        Ruangan::create([
            'nama_ruangan' => $request->nama_ruangan,
            'kode_ruangan' => $request->kode_ruangan,
        ]);

        return redirect()->route('admin.ruangan.index')
            ->with('success', 'Ruangan berhasil ditambahkan.');
    }

    public function edit(Ruangan $ruangan): View
    {
        return view('admin.ruangan.edit', compact('ruangan'));
    }

    public function update(Request $request, Ruangan $ruangan): RedirectResponse
    {
        $request->validate([
            'nama_ruangan' => 'required|string|max:50|unique:ruangan,nama_ruangan,' . $ruangan->id_ruangan . ',id_ruangan',
            'kode_ruangan' => 'nullable|string|max:20|unique:ruangan,kode_ruangan,' . $ruangan->id_ruangan . ',id_ruangan',
        ]);

        $ruangan->update([
            'nama_ruangan' => $request->nama_ruangan,
            'kode_ruangan' => $request->kode_ruangan,
        ]);

        return redirect()->route('admin.ruangan.index')
            ->with('success', 'Ruangan berhasil diperbarui.');
    }

    public function destroy(Ruangan $ruangan): RedirectResponse
    {
        // Cek apakah ruangan sedang dipakai di jadwal
        if ($ruangan->jadwal()->count() > 0) {
            return back()->with('error', 'Ruangan tidak dapat dihapus karena masih digunakan di jadwal pelajaran.');
        }

        $ruangan->delete();

        return redirect()->route('admin.ruangan.index')
            ->with('success', 'Ruangan berhasil dihapus.');
    }
}
