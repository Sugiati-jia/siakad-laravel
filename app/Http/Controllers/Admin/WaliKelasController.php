<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class WaliKelasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $activeTahunAjaran = \App\Models\TahunAjaran::where('status', 'aktif')->first();

        $query = Guru::whereHas('user', function ($q) {
            $q->where('role', 'wali_kelas');
        })->where(function($q) use ($activeTahunAjaran) {
            if ($activeTahunAjaran) {
                $q->whereHas('kelasWali', function($q2) use ($activeTahunAjaran) {
                    $q2->where('id_tahun_ajaran', $activeTahunAjaran->id_tahun_ajaran);
                })->orWhereDoesntHave('kelasWali');
            }
        })->with(['kelasWali' => function($q) use ($activeTahunAjaran) {
            if ($activeTahunAjaran) {
                $q->where('id_tahun_ajaran', $activeTahunAjaran->id_tahun_ajaran);
            }
        }, 'user']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $waliKelas = $query->paginate(10)->withQueryString();

        return view('admin.wali-kelas.index', compact('waliKelas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        // Get only teachers who have role 'guru'
        $gurus = Guru::whereHas('user', function ($q) {
            $q->where('role', 'guru');
        })->orderBy('nama')->get();

        // Get classes that don't have a Wali Kelas yet
        $kelas = Kelas::whereNull('id_wali_kelas')->orderBy('nama')->get();

        return view('admin.wali-kelas.create', compact('gurus', 'kelas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'guru_id' => 'required|exists:guru,id_guru',
            'id_kelas' => 'nullable|exists:kelas,id_kelas',
        ]);

        DB::beginTransaction();
        try {
            $guru = Guru::findOrFail($request->guru_id);
            
            // 1. Update User's role to 'wali_kelas'
            $guru->user->update(['role' => 'wali_kelas']);

            // 2. If a class is selected, assign this Wali Kelas
            if ($request->filled('id_kelas')) {
                Kelas::where('id_kelas', $request->id_kelas)->update([
                    'id_wali_kelas' => $guru->id_guru
                ]);
            }

            DB::commit();

            return redirect()->route('admin.wali-kelas.index')
                ->with('success', 'Wali Kelas berhasil ditambahkan dan ditugaskan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): RedirectResponse
    {
        return redirect()->route('admin.wali-kelas.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id): View
    {
        $guru = Guru::with('user', 'kelasWali')->findOrFail($id);

        // Get classes that either have no Wali Kelas, OR are currently assigned to this teacher
        $kelas = Kelas::where(function ($query) use ($guru) {
            $query->whereNull('id_wali_kelas')
                  ->orWhere('id_wali_kelas', $guru->id_guru);
        })->orderBy('nama')->get();

        return view('admin.wali-kelas.edit', compact('guru', 'kelas'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        $request->validate([
            'id_kelas' => 'nullable|exists:kelas,id_kelas',
        ]);

        DB::beginTransaction();
        try {
            $guru = Guru::findOrFail($id);

            // 1. Unassign Guru from their current class (if any)
            Kelas::where('id_wali_kelas', $guru->id_guru)->update([
                'id_wali_kelas' => null
            ]);

            // 2. Assign to new class if selected
            if ($request->filled('id_kelas')) {
                Kelas::where('id_kelas', $request->id_kelas)->update([
                    'id_wali_kelas' => $guru->id_guru
                ]);
            }

            DB::commit();

            return redirect()->route('admin.wali-kelas.index')
                ->with('success', 'Penugasan Wali Kelas berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): RedirectResponse
    {
        DB::beginTransaction();
        try {
            $guru = Guru::findOrFail($id);

            // 1. Unassign from class
            Kelas::where('id_wali_kelas', $guru->id_guru)->update([
                'id_wali_kelas' => null
            ]);

            // 2. Revert role to 'guru'
            $guru->user->update(['role' => 'guru']);

            DB::commit();

            return redirect()->route('admin.wali-kelas.index')
                ->with('success', 'Status Wali Kelas telah dicabut dan Guru dikembalikan ke peran biasa.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
