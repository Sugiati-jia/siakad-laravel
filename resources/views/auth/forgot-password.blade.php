@extends('layouts.guest')
@section('title', 'Lupa Kata Sandi')
@section('content')
<div class="login-card">
    <div class="login-logo">
        <h3>SIAKAD</h3>
        <p>Sistem Informasi Akademik</p>
        <p>SMK Sunan Giri</p>
    </div>
    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        @if(session('success'))
            <div class="alert alert-success d-flex align-items-center">
                <i class="bi bi-check-circle-fill me-2"></i>
                <div>{{ session('success') }}</div>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger d-flex align-items-center">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <div>{{ session('error') }}</div>
            </div>
        @endif
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" required placeholder="Masukkan email Anda">
            </div>
            @error('email')
                <div class="invalid-feedback d-block"><i class="bi bi-exclamation-circle me-1"></i> {{ $message }}</div>
            @enderror
        </div>
        <div class="d-grid">
            <button type="submit" class="btn btn-primary btn-login"><i class="bi bi-envelope-arrow-up me-2"></i>Kirim Link Reset</button>
        </div>
    </form>
</div>
@endsection
