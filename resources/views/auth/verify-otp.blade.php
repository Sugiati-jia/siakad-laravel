@extends('layouts.guest')
@section('title', 'Verifikasi OTP')
@section('content')
<div class="login-card">
    <div class="login-logo">
        <h3>Verifikasi OTP</h3>
        <p>Kode OTP 6 digit telah dikirim ke email Anda.</p>
    </div>
    
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form method="POST" action="{{ route('password.otp.verify') }}">
        @csrf
        <div class="mb-3">
            <label for="otp" class="form-label text-center d-block">Kode OTP</label>
            <input id="otp" type="text" class="form-control text-center fs-4 fw-bold letter-spacing-2 @error('otp') is-invalid @enderror" name="otp" required autofocus placeholder="------" maxlength="6" autocomplete="one-time-code">
            @error('otp')
                <div class="invalid-feedback d-block text-center"><i class="bi bi-exclamation-circle me-1"></i> {{ $message }}</div>
            @enderror
        </div>
        <div class="d-grid mt-4">
            <button type="submit" class="btn btn-primary btn-login"><i class="bi bi-check2-circle me-2"></i>Verifikasi Kode</button>
        </div>
    </form>
    
    <div class="text-center mt-3">
        <a href="{{ route('password.request') }}" class="text-decoration-none"><i class="bi bi-arrow-left me-1"></i>Kirim ulang email OTP</a>
    </div>
</div>
<style>
.letter-spacing-2 { letter-spacing: 0.5rem; }
</style>
@endsection
