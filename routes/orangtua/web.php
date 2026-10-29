<?php

use App\Http\Controllers\OrangTua\DashboardController;
use App\Http\Controllers\OrangTua\NilaiController;
use App\Http\Controllers\OrangTua\RaporController;
use App\Http\Controllers\OrangTua\NotificationController;

Route::middleware(['auth', 'role:orang_tua'])->prefix('orangtua')->name('orangtua.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Children Selection
    Route::get('/select-anak', [DashboardController::class, 'selectAnak'])->name('select-anak');
    Route::post('/set-anak', [DashboardController::class, 'setAnak'])->name('set-anak');
    
    // Grade View Routes
    Route::get('/nilai', [NilaiController::class, 'index'])->name('nilai.index');
    Route::get('/nilai/detail/{mapel_id}', [NilaiController::class, 'detail'])->name('nilai.detail');
    
    // E-Rapor Routes
    Route::get('/rapor', [RaporController::class, 'index'])->name('rapor.index');
    Route::get('/rapor/view', [RaporController::class, 'view'])->name('rapor.view');
    
    // Notification Routes
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/subscribe', [NotificationController::class, 'subscribe'])->name('notifications.subscribe');
    Route::post('/notifications/unsubscribe', [NotificationController::class, 'unsubscribe'])->name('notifications.unsubscribe');
    Route::post('/notifications/read/{id}', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.read-all');
    Route::post('/notifications/clear-all', [NotificationController::class, 'clearAll'])->name('notifications.clear-all');
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount'])->name('notifications.unread-count');
});
