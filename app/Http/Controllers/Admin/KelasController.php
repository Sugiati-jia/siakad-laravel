<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Guru;
use App\Models\KonsentrasiKeahlian;
use App\Models\TahunAjaran;
use App\Imports\KelasImport;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class KelasController extends Controller
{
    public function index(): View
    {
        $kelas = Kelas::with('waliKelas', 'jurusan', 'tahunAjaran', 'siswas')->paginate(10);
        
        // Ambil nama jurusan (karena sekolah hanya 1 jurusan, ambil yang pertama)
        $jurusanUtama = KonsentrasiKeahlian::first();
        $namaJurusan = $jurusanUtama ? $jurusanUtama->nama_konsentrasi : 'Kelas Sekolah';

        return view('admin.kelas.index', compact('kelas', 'namaJurusan'));
    }

    public function create(): View
    {
        $gurus = Guru::whereHas('user', function($query) {
            $query->where('role', 'wali_kelas');
        })->get();
        
        $jurusans = KonsentrasiKeahlian::all();
        $tahunAjarans = TahunAjaran::all();
        
        return view('admin.kelas.create', compact('gurus', 'jurusans', 'tahunAjarans'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nama_kelas' => 'required|string|max:255',
            'tingkat' => 'required|integer|min:10|max:12',
            'jurusan_id' => 'required|exists:konsentrasi_keahlian,id_konsentrasi_keahlian',
            'wali_id_kelas' => 'nullable|exists:guru,id_guru',
            'id_tahun_ajaran' => 'required|exists:tahun_ajaran,id_tahun_ajaran'
        ]);

        $mapTingkat = [10 => 'X', 11 => 'XI', 12 => 'XII'];
        $tingkatRomawi = $mapTingkat[$request->tingkat] ?? 'X';

        $tingkatModel = \App\Models\TingkatKelas::where('nama_tingkat', $tingkatRomawi)->first();

        Kelas::create([
            'nama' => $request->nama_kelas,
            'tingkat' => $tingkatRomawi,
            'id_tingkat' => $tingkatModel ? $tingkatModel->id_tingkat : null,
            'id_konsentrasi_keahlian' => $request->jurusan_id,
            'id_wali_kelas' => $request->wali_id_kelas,
            'id_tahun_ajaran' => $request->id_tahun_ajaran
        ]);

        return redirect()->route('admin.kelas.index')
            ->with('success', 'Data kelas berhasil ditambahkan');
    }

    public function show(Kelas $kelas): View
    {
        $kelas->load('waliKelas', 'jurusan', 'tahunAjaran', 'siswas.user', 'guruMapels.guru', 'guruMapels.mapel');
        return view('admin.kelas.show', compact('kelas'));
    }

    public function edit(Kelas $kelas): View
    {
        $gurus = Guru::whereHas('user', function($query) {
            $query->where('role', 'wali_kelas');
        })->get();
        
        $jurusans = KonsentrasiKeahlian::all();
        $tahunAjarans = TahunAjaran::all();
        
        return view('admin.kelas.edit', compact('kelas', 'gurus', 'jurusans', 'tahunAjarans'));
    }

    public function update(Request $request, Kelas $kelas): RedirectResponse
    {
        $request->validate([
            'nama_kelas' => 'required|string|max:255',
            'tingkat' => 'required|integer|min:10|max:12',
            'jurusan_id' => 'required|exists:konsentrasi_keahlian,id_konsentrasi_keahlian',
            'wali_id_kelas' => 'nullable|exists:guru,id_guru',
            'id_tahun_ajaran' => 'required|exists:tahun_ajaran,id_tahun_ajaran'
        ]);

        $mapTingkat = [10 => 'X', 11 => 'XI', 12 => 'XII'];
        $tingkatRomawi = $mapTingkat[$request->tingkat] ?? 'X';

        $tingkatModel = \App\Models\TingkatKelas::where('nama_tingkat', $tingkatRomawi)->first();

        $kelas->update([
            'nama' => $request->nama_kelas,
            'tingkat' => $tingkatRomawi,
            'id_tingkat' => $tingkatModel ? $tingkatModel->id_tingkat : null,
            'id_konsentrasi_keahlian' => $request->jurusan_id,
            'id_wali_kelas' => $request->wali_id_kelas,
            'id_tahun_ajaran' => $request->id_tahun_ajaran
        ]);

        return redirect()->route('admin.kelas.index')
            ->with('success', 'Data kelas berhasil diperbarui');
    }

    public function destroy(Kelas $kelas): RedirectResponse
    {
        $kelas->delete();

        return redirect()->route('admin.kelas.index')
            ->with('success', 'Data kelas berhasil dihapus');
    }

    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048'
        ]);

        try {
            Excel::import(new KelasImport, $request->file('file'));
            return back()->with('success', 'Data kelas berhasil diimport');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengimport data: ' . $e->getMessage());
        }
    }

    public function downloadTemplate(): BinaryFileResponse
    {
        $headers = [
            'nama_kelas', 'tingkat', 'kapasitas', 'id_jurusan', 
            'id_tahun_ajaran', 'id_wali_kelas'
        ];

        $callback = function() use ($headers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);
            fputcsv($file, ['X RPL 1', '10', '36', '1', '1', '1']);
            fclose($file);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="template_kelas.csv"',
        ]);
    }
}
