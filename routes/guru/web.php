<?php

use App\Http\Controllers\Guru\DashboardController;
use App\Http\Controllers\Guru\NilaiController;
use App\Http\Controllers\Guru\ImportController;
use App\Http\Controllers\Guru\TpController;

Route::middleware(['auth', 'role:guru,wali_kelas'])->prefix('guru')->name('guru.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Grade Input Routes
    Route::get('/nilai/select-kelas', [NilaiController::class, 'selectKelas'])->name('nilai.select-kelas');
    Route::get('/nilai/select-mapel/{kelas_id}', [NilaiController::class, 'selectMapel'])->name('nilai.select-mapel');
    Route::get('/nilai/input/{kelas_id}/{mapel_id}', [NilaiController::class, 'input'])->name('nilai.input');
    Route::get('/nilai/table-partial/{kelas_id}/{mapel_id}', [NilaiController::class, 'tablePartial'])->name('nilai.table-partial');
    Route::post('/nilai/store', [NilaiController::class, 'store'])->name('nilai.store');
    Route::get('/nilai/summary/{kelas_id}/{mapel_id}', [NilaiController::class, 'summary'])->name('nilai.summary');
    Route::delete('/nilai/student/{siswa_id}/{mapel_id}', [NilaiController::class, 'destroyStudentGrades'])->name('nilai.destroy-student');
    Route::delete('/nilai/destroy-all/{kelas_id}/{mapel_id}', [NilaiController::class, 'destroyAllGrades'])->name('nilai.destroy-all');
    Route::post('/nilai/update-status', [NilaiController::class, 'updateStatus'])->name('nilai.update-status');
    
    // TP Routes
    Route::get('/tp/select-kelas', [TpController::class, 'selectKelas'])->name('tp.select-kelas');
    Route::get('/tp/select-mapel/{kelas_id}', [TpController::class, 'selectMapel'])->name('tp.select-mapel');
    Route::get('/tp/input/{kelas_id}/{mapel_id}', [TpController::class, 'input'])->name('tp.input');
    Route::post('/tp/store', [TpController::class, 'store'])->name('tp.store');
    Route::post('/tp/store-ajax', [TpController::class, 'storeAjax'])->name('tp.store-ajax');
    Route::put('/tp/{id}', [TpController::class, 'update'])->name('tp.update');
    Route::delete('/tp/{id}', [TpController::class, 'destroy'])->name('tp.destroy');
    Route::delete('/tp/destroy-all/{kelas_id}/{mapel_id}', [TpController::class, 'destroyAll'])->name('tp.destroy-all');
    Route::delete('/tp/destroy-ajax/{id}', [TpController::class, 'destroyAjax'])->name('tp.destroy-ajax');
    Route::get('/tp/export/{kelas_id}/{mapel_id}', [TpController::class, 'export'])->name('tp.export');
    Route::post('/tp/import/{kelas_id}/{mapel_id}', [TpController::class, 'import'])->name('tp.import');
    
    // Import Routes
    Route::get('/import/select-kelas', [ImportController::class, 'selectKelas'])->name('import.select-kelas');
    Route::get('/import/select-mapel/{kelas_id}', [ImportController::class, 'selectMapel'])->name('import.select-mapel');
    Route::get('/import/{kelas_id}/{mapel_id}', [ImportController::class, 'import'])->name('import.import');
    Route::post('/import/process', [ImportController::class, 'process'])->name('import.process');
    Route::get('/import/template/{kelas_id}/{mapel_id}', [ImportController::class, 'template'])->name('import.template');
});
