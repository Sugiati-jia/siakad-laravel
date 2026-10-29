<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\GuruController;
use App\Http\Controllers\Admin\SiswaController;
use App\Http\Controllers\Admin\KelasController;
use App\Http\Controllers\Admin\WaliKelasController;
use App\Http\Controllers\Admin\MapelController;
use App\Http\Controllers\Admin\TujuanPembelajaranController;
use App\Http\Controllers\Admin\GuruMapelController;
use App\Http\Controllers\Admin\BidangKeahlianController;
use App\Http\Controllers\Admin\ProgramKeahlianController;
use App\Http\Controllers\Admin\KonsentrasiKeahlianController;
use App\Http\Controllers\Admin\EkstrakurikulerController;
use App\Http\Controllers\Admin\TahunAjaranController;
use App\Http\Controllers\Admin\JadwalController;
use App\Http\Controllers\Admin\RaporController;
use App\Http\Controllers\Admin\KenaikanKelasController;
use App\Http\Controllers\Admin\RuanganController;
use App\Http\Controllers\Admin\TingkatKelasController;

Route::middleware(['auth', 'role:admin,kurikulum,superadmin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Master Data Routes
    Route::post('guru/import', [GuruController::class, 'import'])->name('guru.import');
    Route::get('guru/template', [GuruController::class, 'downloadTemplate'])->name('guru.template');
    Route::resource('guru', GuruController::class);

    Route::post('siswa/kelas/{kelas_id}/import', [SiswaController::class, 'import'])->name('siswa.import');
    Route::get('siswa/template', [SiswaController::class, 'downloadTemplate'])->name('siswa.template');
    Route::get('siswa/kelas/{kelas_id}', [SiswaController::class, 'kelas'])->name('siswa.kelas');
    Route::resource('siswa', SiswaController::class);

    Route::post('kelas/import', [KelasController::class, 'import'])->name('kelas.import');
    Route::get('kelas/template', [KelasController::class, 'downloadTemplate'])->name('kelas.template');
    Route::resource('kelas', KelasController::class)->parameters([
        'kelas' => 'kelas',
    ]);
    Route::resource('wali-kelas', WaliKelasController::class);
    Route::resource('mapel', MapelController::class);
    Route::resource('tujuan-pembelajaran', TujuanPembelajaranController::class);
    Route::resource('guru-mapel', GuruMapelController::class);
    Route::resource('jadwal', JadwalController::class);
    Route::resource('bidang-keahlian', BidangKeahlianController::class);
    Route::resource('program-keahlian', ProgramKeahlianController::class);
    Route::resource('konsentrasi-keahlian', KonsentrasiKeahlianController::class);
    Route::resource('ekstrakurikuler', EkstrakurikulerController::class);
    Route::resource('tahun-ajaran', TahunAjaranController::class)->parameters([
        'tahun-ajaran' => 'tahun_ajaran'
    ]);
    Route::post('tahun-ajaran/{tahun_ajaran}/set-active', [TahunAjaranController::class, 'setActive'])->name('tahun-ajaran.set-active');

    // Logs
    Route::get('log-activity', [\App\Http\Controllers\Admin\LogActivityController::class, 'index'])->name('log-activity.index');
    
    Route::middleware(['role:superadmin'])->group(function () {
        Route::get('log-error', [\App\Http\Controllers\Admin\LogErrorController::class, 'index'])->name('log-error.index');
        Route::post('log-error/clear', [\App\Http\Controllers\Admin\LogErrorController::class, 'clear'])->name('log-error.clear');
    });
    
    // Assignment Routes
    Route::post('/assign-guru-to-kelas', [GuruController::class, 'assignToKelas'])->name('guru.assign-to-kelas');
    Route::post('/assign-guru-to-mapel', [GuruController::class, 'assignToMapel'])->name('guru.assign-to-mapel');
    Route::post('/assign-wali-kelas', [GuruController::class, 'assignWaliKelas'])->name('guru.assign-wali-kelas');
    
    // Settings Routes
    Route::post('/settings/set-active-semester', [DashboardController::class, 'setActiveSemester'])->name('settings.set-active-semester');
    Route::post('/settings/set-active-tahun-ajaran', [DashboardController::class, 'setActiveTahunAjaran'])->name('settings.set-active-tahun-ajaran');
    
    // System Settings Route
    Route::get('/setting', [\App\Http\Controllers\Admin\SettingController::class, 'index'])->name('setting.index');
    Route::put('/setting', [\App\Http\Controllers\Admin\SettingController::class, 'update'])->name('setting.update');

    // E-Rapor Portal Routes (Admin & Kurikulum)
    Route::get('/rapor', [RaporController::class, 'index'])->name('rapor.index');
    Route::get('/rapor/kelas/{kelas_id}', [RaporController::class, 'kelas'])->name('rapor.kelas');
    Route::get('/rapor/view/{siswa_id}', [RaporController::class, 'view'])->name('rapor.view');
    Route::get('/rapor/pdf/{siswa_id}', [RaporController::class, 'pdf'])->name('rapor.pdf');
    Route::get('/rapor/download-all/{kelas_id}', [RaporController::class, 'downloadAll'])->name('rapor.download-all');

    // Leger Nilai
    Route::get('/leger', [\App\Http\Controllers\WaliKelas\LegerController::class, 'selectKelas'])->name('leger.select-kelas');
    Route::get('/leger/{kelas_id}', [\App\Http\Controllers\WaliKelas\LegerController::class, 'index'])->name('leger.index');
    Route::get('/leger/excel/{kelas_id}', [\App\Http\Controllers\WaliKelas\LegerController::class, 'excel'])->name('leger.excel');

    // Kenaikan Kelas & Kelulusan
    Route::get('/kenaikan-kelas', [KenaikanKelasController::class, 'index'])->name('kenaikan-kelas.index');
    Route::post('/kenaikan-kelas/proses', [KenaikanKelasController::class, 'proses'])->name('kenaikan-kelas.proses');

    // Master Ruangan & Tingkat Kelas
    Route::resource('ruangan', RuanganController::class);
    Route::resource('tingkat', TingkatKelasController::class);

});
