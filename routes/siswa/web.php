<?php

use App\Http\Controllers\Siswa\DashboardController;
use App\Http\Controllers\Siswa\NilaiController;
use App\Http\Controllers\Siswa\RaporController;

Route::middleware(['auth', 'role:siswa'])->prefix('siswa')->name('siswa.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Grade View Routes
    Route::get('/nilai', [NilaiController::class, 'index'])->name('nilai.index');
    Route::get('/nilai/detail/{mapel_id}', [NilaiController::class, 'detail'])->name('nilai.detail');
    
    // E-Rapor Routes
    Route::get('/rapor', [RaporController::class, 'index'])->name('rapor.index');
    Route::get('/rapor/view', [RaporController::class, 'view'])->name('rapor.view');
    
    // Notification Routes
    Route::get('/notifications', [DashboardController::class, 'notifications'])->name('notifications');
    Route::post('/notifications/read/{id}', [DashboardController::class, 'markAsRead'])->name('notifications.read');
});
