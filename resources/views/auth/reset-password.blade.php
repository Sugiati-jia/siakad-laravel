@extends('layouts.guest')
@section('title', 'Reset Kata Sandi')
@section('content')
<div class="login-card">
    <div class="login-logo">
        <h3>SIAKAD</h3>
        <p>Sistem Informasi Akademik</p>
        <p>SMK Sunan Giri</p>
    </div>
    <form method="POST" action="{{ route('password.update') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email', session('reset_email')) }}" required placeholder="Masukkan email Anda" readonly>
            </div>
            @error('email')
                <div class="invalid-feedback d-block"><i class="bi bi-exclamation-circle me-1"></i> {{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password Baru</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required placeholder="Masukkan password baru">
                <button type="button" class="btn btn-outline-secondary" id="togglePasswordReset"><i class="bi bi-eye-slash"></i></button>
            </div>
            @error('password')
                <div class="invalid-feedback d-block"><i class="bi bi-exclamation-circle me-1"></i> {{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                <input id="password_confirmation" type="password" class="form-control" name="password_confirmation" required placeholder="Ulangi password baru">
            </div>
        </div>
        <div class="d-grid">
            <button type="submit" class="btn btn-primary btn-login"><i class="bi bi-key me-2"></i>Reset Password</button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const togglePassword = document.querySelector('#togglePasswordReset');
        const password = document.querySelector('#password');
        const passwordConfirm = document.querySelector('#password_confirmation');
        
        if (togglePassword && password) {
            togglePassword.addEventListener('click', function (e) {
                // Toggle the type attribute for both password fields
                const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                password.setAttribute('type', type);
                if (passwordConfirm) {
                    passwordConfirm.setAttribute('type', type);
                }
                
                // Toggle the icon
                const icon = this.querySelector('i');
                if (type === 'text') {
                    icon.classList.remove('bi-eye-slash');
                    icon.classList.add('bi-eye');
                } else {
                    icon.classList.remove('bi-eye');
                    icon.classList.add('bi-eye-slash');
                }
            });
        }
    });
</script>
@endpush
@endsection
