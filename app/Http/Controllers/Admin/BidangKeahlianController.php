<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BidangKeahlian;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class BidangKeahlianController extends Controller
{
    public function index(): View
    {
        $bidangKeahlians = BidangKeahlian::with('programKeahlians')->paginate(10);
        return view('admin.bidang-keahlian.index', compact('bidangKeahlians'));
    }

    public function create(): View
    {
        return view('admin.bidang-keahlian.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nama_bidang' => 'required|string|max:255'
        ]);

        BidangKeahlian::create([
            'nama_bidang' => $request->nama_bidang
        ]);

        return redirect()->route('admin.bidang-keahlian.index')
            ->with('success', 'Bidang keahlian berhasil ditambahkan');
    }

    public function show(BidangKeahlian $bidangKeahlian): View
    {
        $bidangKeahlian->load('programKeahlians.konsentrasiKeahlians');
        return view('admin.bidang-keahlian.show', compact('bidangKeahlian'));
    }

    public function edit(BidangKeahlian $bidangKeahlian): View
    {
        return view('admin.bidang-keahlian.edit', compact('bidangKeahlian'));
    }

    public function update(Request $request, BidangKeahlian $bidangKeahlian): RedirectResponse
    {
        $request->validate([
            'nama_bidang' => 'required|string|max:255'
        ]);

        $bidangKeahlian->update([
            'nama_bidang' => $request->nama_bidang
        ]);

        return redirect()->route('admin.bidang-keahlian.index')
            ->with('success', 'Bidang keahlian berhasil diperbarui');
    }

    public function destroy(BidangKeahlian $bidangKeahlian): RedirectResponse
    {
        $bidangKeahlian->delete();

        return redirect()->route('admin.bidang-keahlian.index')
            ->with('success', 'Bidang keahlian berhasil dihapus');
    }
}
