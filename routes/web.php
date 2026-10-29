<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Admin\GuruController;

// Temporary route to fix corrupt data
Route::get('/fix-corrupt-data', function () {
    $deleted = \App\Models\GuruMapel::whereNull('id_guru')->orWhereNull('id_mapel')->delete();
    return "Berhasil menghapus $deleted baris data Guru Mapel yang error (kosong). Silakan buka kembali halaman admin/guru-mapel.";
});

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
    Route::get('/orangtua/login', [AuthenticatedSessionController::class, 'parentLoginView'])->name('orangtua.login-view');
    Route::post('/orangtua/login', [AuthenticatedSessionController::class, 'parentLogin'])->name('orangtua.login');
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);
});

// Forgot Password routes for admin users
Route::get('forgot-password', [\App\Http\Controllers\Auth\ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('forgot-password', [\App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('forgot-password/otp', [\App\Http\Controllers\Auth\ForgotPasswordController::class, 'showOtpForm'])->name('password.otp');
Route::post('forgot-password/otp', [\App\Http\Controllers\Auth\ForgotPasswordController::class, 'verifyOtp'])->name('password.otp.verify');
Route::get('reset-password', [\App\Http\Controllers\Auth\ForgotPasswordController::class, 'showResetForm'])->name('password.reset.form');
Route::post('reset-password', [\App\Http\Controllers\Auth\ForgotPasswordController::class, 'reset'])->name('password.update');

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

// Redirect authenticated users to their dashboard
Route::get('/', function () {
    return auth()->check() ? redirect('/dashboard') : view('welcome');
})->name('home');

// Dashboard redirect based on role
Route::get('/dashboard', function () {
    $user = auth()->user();
    return match ($user->role) {
        'admin', 'superadmin' => redirect('/admin/dashboard'),
        'guru' => redirect('/guru/dashboard'),
        'wali_kelas' => redirect('/walikelas/dashboard'),
        'siswa' => redirect('/siswa/dashboard'),
        'orang_tua' => redirect('/orangtua/dashboard'),
        default => redirect('/login')
    };
})->middleware('auth')->name('dashboard');

// Profile Routes for all authenticated users
Route::middleware('auth')->group(function () {
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
});

// Include role-based routes
require __DIR__ . '/admin/web.php';
require __DIR__ . '/guru/web.php';
require __DIR__ . '/walikelas/web.php';
require __DIR__ . '/siswa/web.php';
require __DIR__ . '/orangtua/web.php';
