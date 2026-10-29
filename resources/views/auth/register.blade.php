@extends('layouts.guest')

@section('title', 'Registrasi Ditutup - SIAKAD SMK Sunan Giri')

@section('content')
<div class="login-card text-center">
    <!-- Logo Icon & Title -->
    <div class="login-logo mb-4">
        <div class="login-logo-icon bg-warning text-dark mb-3 mx-auto" style="width: 70px; height: 70px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2rem; box-shadow: 0 4px 15px rgba(255, 193, 7, 0.3);">
            <i class="bi bi-info-circle-fill"></i>
        </div>
        <h3>Registrasi Akun</h3>
    </div>

    <div class="alert alert-info py-4 px-3 mb-4" style="border-radius: 12px; border-left: 5px solid #0dcaf0;">
        <h5 class="alert-heading fw-bold mb-3 text-dark"><i class="bi bi-exclamation-triangle-fill me-2 text-warning"></i>Informasi Pendaftaran</h5>
        <p class="mb-0 text-start text-dark" style="font-size: 0.95rem; line-height: 1.6;">
            Silakan hubungi <strong>pihak Tata Usaha atau Administrator Sekolah</strong> untuk pembuatan akun baru. <br><br>
            Setiap Guru, Pegawai, maupun Siswa harus terdaftar secara resmi di sekolah terlebih dahulu. Data akses dan informasi login akan diberikan langsung oleh pihak sekolah.
        </p>
    </div>

    <!-- Links -->
    <div class="d-grid mt-4">
        <a href="{{ route('login') }}" class="btn btn-primary py-2 fw-bold" style="border-radius: 10px; background-color: #4361ee; border: none; box-shadow: 0 4px 10px rgba(67, 97, 238, 0.3);">
            <i class="bi bi-arrow-left me-2"></i>Kembali ke Halaman Login
        </a>
    </div>
</div>
@endsection
