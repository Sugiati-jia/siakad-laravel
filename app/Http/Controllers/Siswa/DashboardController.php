<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\NilaiTp;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(): View
    {
        $siswa = Auth::user()->siswa;
        
        // Get grade summary
        $totalGrades = NilaiTp::where('id_siswa', $siswa->id)
            ->whereNotNull('nilai_tp')
            ->count();

        $averageGrade = NilaiTp::where('id_siswa', $siswa->id)
            ->whereNotNull('nilai_tp')
            ->avg('nilai_tp');

        $highestGrade = NilaiTp::where('id_siswa', $siswa->id)
            ->whereNotNull('nilai_tp')
            ->max('nilai_tp');

        $lowestGrade = NilaiTp::where('id_siswa', $siswa->id)
            ->whereNotNull('nilai_tp')
            ->min('nilai_tp');

        return view('siswa.dashboard', compact(
            'siswa',
            'totalGrades',
            'averageGrade',
            'highestGrade',
            'lowestGrade'
        ));
    }

    public function notifications(): View
    {
        $siswa = Auth::user()->siswa;
        
        // Get notifications (this would be from a notifications table)
        $notifications = [];
        
        return view('siswa.notifications', compact('notifications'));
    }

    public function markAsRead($id): \Illuminate\Http\JsonResponse
    {
        // Mark notification as read logic here
        return response()->json(['success' => true]);
    }
}
