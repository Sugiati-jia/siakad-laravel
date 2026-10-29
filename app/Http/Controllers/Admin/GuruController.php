<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\User;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\GuruMapel;
use App\Models\ActivityLog;
use App\Imports\GuruImport;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\StreamedResponse;

class GuruController extends Controller
{
    public function index(Request $request): View
    {
        $query = Guru::with(['user', 'kelasWali', 'guruMapels' => function($q) {
            $q->where('status', 'aktif')->with(['mapel', 'kelas']);
        }]);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $gurus = $query->paginate(10)->withQueryString();
        return view('admin.guru.index', compact('gurus'));
    }

    public function create(): View
    {
        $kelas = Kelas::all();
        return view('admin.guru.create', compact('kelas'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nip'          => 'required|unique:guru,nip',
            'nama'         => 'required|string|max:255',
            'email'        => 'required|email|unique:users,email',
            'password'     => 'required|min:6',
            'jenis_kelamin'=> 'required|in:L,P',
            'tempat_lahir' => 'required|string|max:255',
            'tanggal_lahir'=> 'required|date',
            'alamat'       => 'required|string',
            'no_telepon'   => 'required|string|max:15',
            'role'         => 'required|in:guru,wali_kelas',
        ]);

        $user = User::create([
            'name'     => $request->nama,
            'email'    => $request->email,
            'password' => bcrypt($request->password),
            'role'     => $request->role,
            'nip'      => $request->nip,
        ]);

        // Create guru record
        Guru::create([
            'user_id' => $user->id,
            'nip' => $request->nip,
            'nama' => $request->nama,
            'jenis_kelamin' => $request->jenis_kelamin,
            'tempat_lahir' => $request->tempat_lahir,
            'tanggal_lahir' => $request->tanggal_lahir,
            'alamat' => $request->alamat,
            'no_telepon' => $request->no_telepon,
            'email' => $request->email,
            'status' => 'aktif'
        ]);

        // Log activity for adding guru
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'create_guru',
            'description' => 'Menambahkan guru: ' . $request->nama,
            'ip_address' => request()->ip(),
            'user_agent' => request()->header('User-Agent')
        ]);

        return redirect()->route('admin.guru.index')
            ->with('success', 'Data guru berhasil ditambahkan');
    }

    public function show(Guru $guru): View
    {
        $guru->load('user', 'kelasWali', 'guruMapels.mapel', 'guruMapels.kelas');
        return view('admin.guru.show', compact('guru'));
    }

    public function edit(Guru $guru): View
    {
        $kelas = Kelas::all();
        return view('admin.guru.edit', compact('guru', 'kelas'));
    }

    public function update(Request $request, Guru $guru): RedirectResponse
    {
        $request->validate([
            'nip'          => 'required|unique:guru,nip,' . $guru->id_guru . ',id_guru',
            'nama'         => 'required|string|max:255',
            'email'        => 'required|email|unique:users,email,' . $guru->user_id,
            'password'     => 'nullable|min:6',
            'jenis_kelamin'=> 'required|in:L,P',
            'tempat_lahir' => 'required|string|max:255',
            'tanggal_lahir'=> 'required|date',
            'alamat'       => 'required|string',
            'no_telepon'   => 'required|string|max:15',
            'role'         => 'required|in:guru,wali_kelas',
        ]);

        $userData = [
            'name'  => $request->nama,
            'email' => $request->email,
            'role'  => $request->role,
            'nip'   => $request->nip,
        ];

        if ($request->filled('password')) {
            $userData['password'] = bcrypt($request->password);
        }

        $guru->user->update($userData);

        // Update guru record
        $guru->update([
            'nip' => $request->nip,
            'nama' => $request->nama,
            'jenis_kelamin' => $request->jenis_kelamin,
            'tempat_lahir' => $request->tempat_lahir,
            'tanggal_lahir' => $request->tanggal_lahir,
            'alamat' => $request->alamat,
            'no_telepon' => $request->no_telepon,
            'email' => $request->email
        ]);

        // Log activity for updating guru
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'update_guru',
            'description' => 'Mengubah guru: ' . $guru->nama,
            'ip_address' => request()->ip(),
            'user_agent' => request()->header('User-Agent')
        ]);

        return redirect()->route('admin.guru.index')
            ->with('success', 'Data guru berhasil diperbarui');
    }

    public function destroy(Guru $guru): RedirectResponse
    {
        // Log activity for deleting guru before deletion
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'delete_guru',
            'description' => 'Menghapus guru: ' . $guru->nama,
            'ip_address' => request()->ip(),
            'user_agent' => request()->header('User-Agent')
        ]);

        $guru->user->delete();
        $guru->delete();

        return redirect()->route('admin.guru.index')
            ->with('success', 'Data guru berhasil dihapus');
    }

    public function assignToKelas(Request $request): RedirectResponse
    {
        $request->validate([
            'guru_id' => 'required|exists:guru,id_guru',
            'id_kelas' => 'required|exists:kelas,id_kelas',
            'mapel_id' => 'required|exists:mapel,id_mapel'
        ]);

        $activeTahunAjaran = \App\Models\TahunAjaran::where('status', 'aktif')->first();
        $activeSemester = \App\Models\Semester::where('status', 'aktif')->first();

        // Get from active tahun ajaran & semester, fallback to 1
        $id_tahun_ajaran = $activeTahunAjaran ? $activeTahunAjaran->id_tahun_ajaran : 1;
        $semester = $activeSemester ? $activeSemester->semester : 1;

        \App\Models\GuruMapel::updateOrCreate([
            'guru_id' => $request->guru_id,
            'id_kelas' => $request->id_kelas,
            'mapel_id' => $request->mapel_id
        ], [
            'id_tahun_ajaran' => $id_tahun_ajaran,
            'id_semester' => $semester,
            'status' => 'aktif'
        ]);

        return back()->with('success', 'Guru berhasil ditugaskan ke kelas');
    }

    public function assignWaliKelas(Request $request): RedirectResponse
    {
        $request->validate([
            'guru_id' => 'required|exists:guru,id_guru',
            'id_kelas' => 'required|exists:kelas,id_kelas'
        ]);

        Kelas::where('id_kelas', $request->id_kelas)
            ->update(['wali_id_kelas' => $request->guru_id]);

        return back()->with('success', 'Wali kelas berhasil ditetapkan');
    }

    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048'
        ]);

        try {
            Excel::import(new GuruImport, $request->file('file'));
            
            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'import_guru',
                'description' => 'Mengimport data guru dari Excel',
                'ip_address' => request()->ip(),
                'user_agent' => request()->header('User-Agent')
            ]);

            return back()->with('success', 'Data guru berhasil diimport');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengimport data: ' . $e->getMessage());
        }
    }

    public function downloadTemplate()
    {
        return Excel::download(new \App\Exports\GuruTemplateExport, 'template_guru.xlsx');
    }
}
