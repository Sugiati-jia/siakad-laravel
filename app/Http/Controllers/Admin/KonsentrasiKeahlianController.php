<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KonsentrasiKeahlian;
use App\Models\ProgramKeahlian;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class KonsentrasiKeahlianController extends Controller
{
    public function index(): View
    {
        $konsentrasiKeahlians = KonsentrasiKeahlian::with('programKeahlian.bidangKeahlian')->paginate(10);
        return view('admin.konsentrasi-keahlian.index', compact('konsentrasiKeahlians'));
    }

    public function create(): View
    {
        $programKeahlians = ProgramKeahlian::with('bidangKeahlian')->get();
        return view('admin.konsentrasi-keahlian.create', compact('programKeahlians'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'program_keahlian_id' => 'required|exists:program_keahlian,id_program_keahlian',
            'nama_konsentrasi' => 'required|string|max:255'
        ]);

        KonsentrasiKeahlian::create([
            'id_program_keahlian' => $request->program_keahlian_id,
            'nama_konsentrasi' => $request->nama_konsentrasi,
            'status' => 'aktif'
        ]);

        return redirect()->route('admin.konsentrasi-keahlian.index')
            ->with('success', 'Konsentrasi keahlian berhasil ditambahkan');
    }

    public function show(KonsentrasiKeahlian $konsentrasiKeahlian): View
    {
        $konsentrasiKeahlian->load('programKeahlian.bidangKeahlian', 'kelas');
        return view('admin.konsentrasi-keahlian.show', compact('konsentrasiKeahlian'));
    }

    public function edit(KonsentrasiKeahlian $konsentrasiKeahlian): View
    {
        $programKeahlians = ProgramKeahlian::with('bidangKeahlian')->get();
        return view('admin.konsentrasi-keahlian.edit', compact('konsentrasiKeahlian', 'programKeahlians'));
    }

    public function update(Request $request, KonsentrasiKeahlian $konsentrasiKeahlian): RedirectResponse
    {
        $request->validate([
            'program_keahlian_id' => 'required|exists:program_keahlian,id_program_keahlian',
            'nama_konsentrasi' => 'required|string|max:255'
        ]);

        $konsentrasiKeahlian->update([
            'id_program_keahlian' => $request->program_keahlian_id,
            'nama_konsentrasi' => $request->nama_konsentrasi
        ]);

        return redirect()->route('admin.konsentrasi-keahlian.index')
            ->with('success', 'Konsentrasi keahlian berhasil diperbarui');
    }

    public function destroy(KonsentrasiKeahlian $konsentrasiKeahlian): RedirectResponse
    {
        $konsentrasiKeahlian->delete();

        return redirect()->route('admin.konsentrasi-keahlian.index')
            ->with('success', 'Konsentrasi keahlian berhasil dihapus');
    }
}
