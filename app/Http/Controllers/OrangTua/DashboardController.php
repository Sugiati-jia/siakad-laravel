<?php

namespace App\Http\Controllers\OrangTua;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Siswa;
use App\Services\GradeCalculationService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;

class DashboardController extends Controller
{
    protected $gradeService;

    public function __construct(GradeCalculationService $gradeService)
    {
        $this->gradeService = $gradeService;
    }

    public function index(): View
    {
        $orangTua = Auth::user();
        
        $children = Siswa::with('user', 'kelas')
            ->where('status', 'aktif')
            ->get();

        $selectedChildId = session('selected_child_id');
        $selectedChild = null;

        if ($selectedChildId) {
            $selectedChild = Siswa::with('kelas')->find($selectedChildId);
        } elseif ($children->isNotEmpty()) {
            $selectedChild = $children->first();
            session(['selected_child_id' => $selectedChild->id_siswa]);
        }

        $rank = null;
        if ($selectedChild) {
            $rankings = $this->gradeService->calculateRanking($selectedChild->id_kelas);
            // Search rankings for the student
            $studentRank = $rankings->firstWhere('id_siswa', $selectedChild->id_siswa);
            if ($studentRank) {
                $rank = $studentRank->ranking;
            }
        }

        return view('orangtua.dashboard', compact(
            'children',
            'selectedChild',
            'rank'
        ));
    }

    public function selectAnak(): View
    {
        $orangTua = Auth::user();
        
        $children = Siswa::with('user', 'kelas')
            ->where('status', 'aktif')
            ->get();

        return view('orangtua.select-anak', compact('children'));
    }

    public function setAnak(Request $request): RedirectResponse
    {
        $request->validate([
            'id_siswa' => 'required|exists:siswa,id_siswa'
        ]);

        session(['selected_child_id' => $request->id_siswa]);

        return redirect()->route('orangtua.dashboard')
            ->with('success', 'Anak berhasil dipilih');
    }

}
