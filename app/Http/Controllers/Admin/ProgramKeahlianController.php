<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProgramKeahlian;
use App\Models\BidangKeahlian;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ProgramKeahlianController extends Controller
{
    public function index(): View
    {
        $programKeahlians = ProgramKeahlian::with('bidangKeahlian', 'konsentrasiKeahlians')->paginate(10);
        return view('admin.program-keahlian.index', compact('programKeahlians'));
    }

    public function create(): View
    {
        $bidangKeahlians = BidangKeahlian::all();
        return view('admin.program-keahlian.create', compact('bidangKeahlians'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'id_bidang_keahlian' => 'required|exists:bidang_keahlian,id_bidang_keahlian',
            'nama_program' => 'required|string|max:255'
        ]);

        ProgramKeahlian::create([
            'id_bidang_keahlian' => $request->id_bidang_keahlian,
            'nama_program' => $request->nama_program
        ]);

        return redirect()->route('admin.program-keahlian.index')
            ->with('success', 'Program keahlian berhasil ditambahkan');
    }

    public function show(ProgramKeahlian $programKeahlian): View
    {
        $programKeahlian->load('bidangKeahlian', 'konsentrasiKeahlians');
        return view('admin.program-keahlian.show', compact('programKeahlian'));
    }

    public function edit(ProgramKeahlian $programKeahlian): View
    {
        $bidangKeahlians = BidangKeahlian::all();
        return view('admin.program-keahlian.edit', compact('programKeahlian', 'bidangKeahlians'));
    }

    public function update(Request $request, ProgramKeahlian $programKeahlian): RedirectResponse
    {
        $request->validate([
            'id_bidang_keahlian' => 'required|exists:bidang_keahlian,id_bidang_keahlian',
            'nama_program' => 'required|string|max:255'
        ]);

        $programKeahlian->update([
            'id_bidang_keahlian' => $request->id_bidang_keahlian,
            'nama_program' => $request->nama_program
        ]);

        return redirect()->route('admin.program-keahlian.index')
            ->with('success', 'Program keahlian berhasil diperbarui');
    }

    public function destroy(ProgramKeahlian $programKeahlian): RedirectResponse
    {
        $programKeahlian->delete();

        return redirect()->route('admin.program-keahlian.index')
            ->with('success', 'Program keahlian berhasil dihapus');
    }
}
