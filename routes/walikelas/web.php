<?php

use App\Http\Controllers\WaliKelas\DashboardController;
use App\Http\Controllers\WaliKelas\NilaiController;
use App\Http\Controllers\WaliKelas\RaporController;
use App\Http\Controllers\WaliKelas\EkstrakurikulerController;
use App\Http\Controllers\WaliKelas\CatatanController;
use App\Http\Controllers\WaliKelas\KokurikulerController;

Route::middleware(['auth', 'role:wali_kelas'])
    ->prefix('walikelas')
    ->name('walikelas.')
    ->group(function () {

        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

        // =========================
        // NILAI
        // =========================
        Route::get('/nilai/{kelas_id}', [NilaiController::class, 'index'])
            ->name('nilai.index');

        Route::get('/nilai/detail/{siswa_id}', [NilaiController::class, 'detail'])
            ->name('nilai.detail');

        Route::get('/nilai/detail-mapel/{guru_mapel_id}', [NilaiController::class, 'detailMapel'])
            ->name('nilai.detail-mapel');

        Route::post('/nilai/lock', [NilaiController::class, 'lock'])
            ->name('nilai.lock');

        Route::post('/nilai/lock-all', [NilaiController::class, 'lockAll'])
            ->name('nilai.lock-all');

        Route::post('/nilai/unlock-all', [NilaiController::class, 'unlockAll'])
            ->name('nilai.unlock-all');

        Route::post('/nilai/unlock', [NilaiController::class, 'unlock'])
            ->name('nilai.unlock');

        // =========================
        // RAPOR
        // =========================
        Route::get('/rapor', [RaporController::class, 'selectKelas'])
            ->name('rapor.select-kelas');

        Route::get('/rapor/{kelas_id}', [RaporController::class, 'index'])
            ->name('rapor.index');

        Route::get('/rapor/view/{siswa_id}', [RaporController::class, 'view'])
            ->name('rapor.view');

        Route::get('/rapor/pdf/{siswa_id}', [RaporController::class, 'pdf'])
            ->name('rapor.pdf');

        Route::post('/rapor/kirim', [RaporController::class, 'kirim'])
            ->name('rapor.kirim');

        Route::post('/rapor/kirim-semua/{kelas_id}', [RaporController::class, 'kirimSemua'])
            ->name('rapor.kirim-semua');

        Route::get('/rapor/download-all/{kelas_id}', [RaporController::class, 'downloadAll'])
            ->name('rapor.download-all');

        Route::post('/rapor/generate-deskripsi', [RaporController::class, 'generateDeskripsi'])
            ->name('rapor.generate-deskripsi');

        // =========================
        // EKSTRAKURIKULER
        // =========================
        Route::get('/ekstrakurikuler/{kelas_id}', [EkstrakurikulerController::class, 'index'])
            ->name('ekstrakurikuler.index');

        Route::post('/ekstrakurikuler/store', [EkstrakurikulerController::class, 'store'])
            ->name('ekstrakurikuler.store');

        Route::put('/ekstrakurikuler/update/{id}', [EkstrakurikulerController::class, 'update'])
            ->name('ekstrakurikuler.update');

        Route::delete('/ekstrakurikuler/delete/{id}', [EkstrakurikulerController::class, 'destroy'])
            ->name('ekstrakurikuler.destroy');

        // =========================
        // KOKURIKULER
        // =========================
        Route::get('/kokurikuler/{kelas_id}', [KokurikulerController::class, 'index'])
            ->name('kokurikuler.index');

        Route::post('/kokurikuler/store', [KokurikulerController::class, 'store'])
            ->name('kokurikuler.store');

        Route::put('/kokurikuler/update/{id}', [KokurikulerController::class, 'update'])
            ->name('kokurikuler.update');

        Route::delete('/kokurikuler/delete/{id}', [KokurikulerController::class, 'destroy'])
            ->name('kokurikuler.destroy');

        // =========================
        // CATATAN
        // =========================
        Route::get('/catatan/{kelas_id}', [CatatanController::class, 'index'])
            ->name('catatan.index');

        Route::post('/catatan/store', [CatatanController::class, 'store'])
            ->name('catatan.store');

        Route::put('/catatan/update/{id}', [CatatanController::class, 'update'])
            ->name('catatan.update');

        Route::delete('/catatan/destroy/{id}', [CatatanController::class, 'destroy'])
            ->name('catatan.destroy');

        // =========================
        // LEGER NILAI
        // =========================
        Route::get('/leger', [\App\Http\Controllers\WaliKelas\LegerController::class, 'selectKelas'])
            ->name('leger.select-kelas');

        Route::get('/leger/{kelas_id}', [\App\Http\Controllers\WaliKelas\LegerController::class, 'index'])
            ->name('leger.index');

        Route::get('/leger/pdf/{kelas_id}', [\App\Http\Controllers\WaliKelas\LegerController::class, 'pdf'])
            ->name('leger.pdf');

        Route::get('/leger/excel/{kelas_id}', [\App\Http\Controllers\WaliKelas\LegerController::class, 'excel'])
            ->name('leger.excel');

        // =========================
        // RANKING
        // =========================
        Route::get('/ranking', [DashboardController::class, 'selectKelasRanking'])
            ->name('ranking.select-kelas');

        Route::get('/ranking/{kelas_id}', [DashboardController::class, 'ranking'])
            ->name('ranking');
    });