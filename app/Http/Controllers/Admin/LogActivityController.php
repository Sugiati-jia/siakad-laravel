<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LogActivityController extends Controller
{
    public function index(): View
    {
        $query = ActivityLog::with('user')->orderBy('created_at', 'desc');

        // Hide superadmin logs from non-superadmin users
        if (auth()->user()->role !== 'superadmin') {
            $query->whereHas('user', function ($q) {
                $q->where('role', '!=', 'superadmin');
            });
        }

        $logs = $query->paginate(20);
        return view('admin.log-activity.index', compact('logs'));
    }
}
