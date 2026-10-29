<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TingkatKelas;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class TingkatKelasController extends Controller
{
    public function index(): View
    {
        $tingkats = TingkatKelas::withCount('kelas')->orderBy('nama_tingkat')->paginate(15);
        return view('admin.tingkat.index', compact('tingkats'));
    }

    public function create(): View
    {
        return view('admin.tingkat.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nama_tingkat' => 'required|string|max:20|unique:tingkat_kelas,nama_tingkat',
        ]);

        TingkatKelas::create([
            'nama_tingkat' => $request->nama_tingkat,
        ]);

        return redirect()->route('admin.tingkat.index')
            ->with('success', 'Tingkat kelas berhasil ditambahkan.');
    }

    public function edit(TingkatKelas $tingkat): View
    {
        return view('admin.tingkat.edit', compact('tingkat'));
    }

    public function update(Request $request, TingkatKelas $tingkat): RedirectResponse
    {
        $request->validate([
            'nama_tingkat' => 'required|string|max:20|unique:tingkat_kelas,nama_tingkat,' . $tingkat->id_tingkat . ',id_tingkat',
        ]);

        $tingkat->update([
            'nama_tingkat' => $request->nama_tingkat,
        ]);

        return redirect()->route('admin.tingkat.index')
            ->with('success', 'Tingkat kelas berhasil diperbarui.');
    }

    public function destroy(TingkatKelas $tingkat): RedirectResponse
    {
        if ($tingkat->kelas()->count() > 0) {
            return back()->with('error', 'Tingkat kelas tidak dapat dihapus karena masih digunakan oleh ' . $tingkat->kelas()->count() . ' kelas.');
        }

        $tingkat->delete();

        return redirect()->route('admin.tingkat.index')
            ->with('success', 'Tingkat kelas berhasil dihapus.');
    }
}
