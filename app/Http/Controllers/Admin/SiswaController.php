<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\User;
use App\Models\Kelas;
use App\Imports\SiswaImport;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SiswaController extends Controller
{
    public function index(): View
    {
        $kelas = Kelas::withCount('siswas')->get();
        return view('admin.siswa.index', compact('kelas'));
    }

    public function kelas(Request $request, $id_kelas): View
    {
        $kelas = Kelas::findOrFail($id_kelas);
        
        $query = Siswa::with('user')->where('id_kelas', $id_kelas);
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%");
            });
        }
        
        $siswas = $query->paginate(10)->withQueryString();
        $semuaKelas = Kelas::all(); // Untuk form dropdown create/edit
        return view('admin.siswa.kelas', compact('kelas', 'siswas', 'semuaKelas'));
    }

    public function create(): View
    {
        $kelas = Kelas::all();
        return view('admin.siswa.create', compact('kelas'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nisn'          => 'required|unique:siswa,nisn',
            'nama'          => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email',
            'password'      => 'required|min:6',
            'jenis_kelamin' => 'required|in:L,P',
            'tempat_lahir'  => 'required|string|max:255',
            'tanggal_lahir' => 'required|date',
            'alamat'        => 'required|string',
            'no_telepon'    => 'required|string|max:15',
            'id_kelas'      => 'required|exists:kelas,id_kelas',
        ]);

        $user = User::create([
            'name'     => $request->nama,
            'email'    => $request->email,
            'password' => bcrypt($request->password),
            'role'     => 'siswa',
            'nisn'     => $request->nisn,
        ]);

        // Create siswa record using direct assignment to bypass fillable gaps
        $siswa = new Siswa();
        $siswa->user_id = $user->id;
        $siswa->nisn = $request->nisn;
        $siswa->nama = $request->nama;
        $siswa->jenis_kelamin = $request->jenis_kelamin;
        $siswa->tempat_lahir = $request->tempat_lahir;
        $siswa->tanggal_lahir = $request->tanggal_lahir;
        $siswa->alamat = $request->alamat;
        $siswa->no_hp = $request->no_telepon;
        $siswa->id_kelas = $request->id_kelas;
        $siswa->status = 'aktif';
        $siswa->save();

        return redirect()->route('admin.siswa.kelas', $request->id_kelas)
            ->with('success', 'Data siswa berhasil ditambahkan');
    }

    public function show(Siswa $siswa): View
    {
        $siswa->load('user', 'kelas', 'nilaiTps.tp', 'nilaiEkstrakurikulers.ekstrakurikuler');
        return view('admin.siswa.show', compact('siswa'));
    }

    public function edit(Siswa $siswa): View
    {
        $kelas = Kelas::all();
        return view('admin.siswa.edit', compact('siswa', 'kelas'));
    }

    public function update(Request $request, Siswa $siswa): RedirectResponse
    {
        $request->validate([
            'nisn'          => 'required|unique:siswa,nisn,' . $siswa->id_siswa . ',id_siswa',
            'nama'          => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email,' . $siswa->user_id,
            'password'      => 'nullable|min:6',
            'jenis_kelamin' => 'required|in:L,P',
            'tempat_lahir'  => 'required|string|max:255',
            'tanggal_lahir' => 'required|date',
            'alamat'        => 'required|string',
            'no_telepon'    => 'required|string|max:15',
            'id_kelas'      => 'required|exists:kelas,id_kelas',
        ]);

        $userData = [
            'name'  => $request->nama,
            'email' => $request->email,
            'nisn'  => $request->nisn,
        ];

        if ($request->filled('password')) {
            $userData['password'] = bcrypt($request->password);
        }

        $siswa->user->update($userData);

        // Update siswa record using direct assignment to bypass fillable gaps
        $siswa->nisn = $request->nisn;
        $siswa->nama = $request->nama;
        $siswa->jenis_kelamin = $request->jenis_kelamin;
        $siswa->tempat_lahir = $request->tempat_lahir;
        $siswa->tanggal_lahir = $request->tanggal_lahir;
        $siswa->alamat = $request->alamat;
        $siswa->no_hp = $request->no_telepon;
        $siswa->id_kelas = $request->id_kelas;
        $siswa->save();

        return redirect()->route('admin.siswa.kelas', $siswa->id_kelas)
            ->with('success', 'Data siswa berhasil diperbarui');
    }

    public function destroy(Siswa $siswa): RedirectResponse
    {
        $id_kelas = $siswa->id_kelas;
        $siswa->user->delete();
        $siswa->delete();

        return redirect()->route('admin.siswa.kelas', $id_kelas)
            ->with('success', 'Data siswa berhasil dihapus');
    }

    public function import(Request $request, $id_kelas): RedirectResponse
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048'
        ]);

        try {
            Excel::import(new SiswaImport($id_kelas), $request->file('file'));
            return back()->with('success', 'Data siswa berhasil diimport');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengimport data: ' . $e->getMessage());
        }
    }

    public function downloadTemplate() { return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\SiswaTemplateExport, "template_siswa.xlsx"); }
}

