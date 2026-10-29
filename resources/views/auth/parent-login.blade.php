@extends('layouts.auth-split')

@section('title', 'Login Orang Tua - SIAKAD SMK Sunan Giri')

@section('content')
<div class="login-form-wrapper position-relative">
    <!-- Header -->
    <div class="text-center mb-4">
        <h3 class="fw-bold text-success mb-2" style="font-size: 1.8rem; letter-spacing: -0.5px;">Portal Orang Tua</h3>
        <p class="text-muted" style="font-size: 0.95rem;">Login untuk melihat perkembangan siswa</p>
    </div>

    <!-- Flash Messages -->
    @if ($errors->any())
        <div class="alert alert-danger p-2 small mb-3 text-center" style="border-radius: 8px;">
            <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ $errors->first() }}
        </div>
    @endif

    <style>
        .login-form-wrapper {
            max-width: 400px;
            margin: 0 auto;
            width: 100%;
        }

        /* Input Styles */
        .form-control {
            background-color: #e8f5e9;
            border: none;
            border-radius: 50px;
            padding: 14px 24px;
            font-size: 0.95rem;
            color: #198754;
            box-shadow: none !important;
        }
        .form-control::placeholder {
            color: #81c784;
        }
        .form-control:focus {
            background-color: #c8e6c9;
            color: #146c43;
        }
        .input-group {
            position: relative;
        }
        .input-group .btn-outline-secondary {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            border: none;
            background: transparent;
            color: #198754;
            z-index: 10;
            padding: 0;
        }
        .input-group .btn-outline-secondary:hover {
            color: #146c43;
        }
        
        .form-select {
            background-color: #e8f5e9;
            border: none;
            border-radius: 50px;
            padding: 14px 24px;
            font-size: 0.95rem;
            color: #198754;
            box-shadow: none !important;
        }
        .form-select:focus {
            background-color: #c8e6c9;
            color: #146c43;
        }

        .btn-login {
            background-color: #198754;
            color: #ffffff;
            border: none;
            border-radius: 50px;
            padding: 14px;
            font-weight: 700;
            font-size: 1rem;
            width: 100%;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(25, 135, 84, 0.4);
            margin-top: 15px;
        }
        .btn-login:hover {
            background-color: #146c43;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(25, 135, 84, 0.5);
        }

        .admin-link {
            display: block;
            text-align: center;
            color: #6c757d;
            text-decoration: none;
            font-size: 0.85rem;
            margin-top: 25px;
            transition: color 0.3s;
        }
        .admin-link:hover {
            color: #198754;
            text-decoration: underline;
        }
        
        .orangtua-guide {
            position: absolute;
            right: -280px;
            top: 50%;
            transform: translateY(-50%);
            width: 250px;
            background: rgba(25, 135, 84, 0.05);
            border-left: 4px solid #198754;
            padding: 20px;
            border-radius: 0 15px 15px 0;
        }
        
        @media (max-width: 1100px) {
            .orangtua-guide {
                position: static;
                transform: none;
                width: 100%;
                margin-top: 30px;
                border-radius: 15px;
                border-left: none;
                border-top: 4px solid #198754;
            }
        }
    </style>

    <!-- Login Form -->
    <form action="{{ route('orangtua.login') }}" method="POST">
        @csrf

        <!-- NISN -->
        <div class="mb-3">
            <input type="number" 
                   name="nisn" 
                   class="form-control text-center" 
                   placeholder="NISN............" 
                   value="{{ old('nisn') }}" 
                   required 
                   autocomplete="off">
        </div>

        <!-- Password -->
        <div class="mb-3 input-group">
            <input id="password"
                   type="password"
                   name="password"
                   class="form-control text-center"
                   placeholder="Password (NISN)............"
                   required>
            <button type="button" class="btn btn-outline-secondary" id="togglePassword"><i class="bi bi-eye-slash"></i></button>
        </div>
        <div class="form-text text-muted text-center" style="font-size: 0.75rem; margin-top: -10px; margin-bottom: 10px;">
            * Gunakan NISN sebagai password default
        </div>

        <!-- Role Select -->
        <div class="mb-3">
            <select name="login_as" class="form-select text-center" required>
                <option value="" disabled selected>Login Sebagai...</option>
                <option value="siswa">Siswa</option>
                <option value="orang_tua" selected>Orang Tua / Wali</option>
            </select>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn btn-login">
            LOG IN
        </button>

        <a href="{{ route('login') }}" class="admin-link">Login sebagai Admin/Guru</a>
    </form>

    <!-- Instructions / PWA Guide -->
    <div class="orangtua-guide">
        <div class="fw-bold text-success mb-3" style="font-size: 1rem;">
            <i class="bi bi-info-circle-fill me-1"></i> Panduan Akses
        </div>
        
        <div id="pwaInstallBanner" style="display:none; background:var(--custom-card-bg, #ffffff); border-radius:10px; padding:10px; margin-bottom:15px; box-shadow:0 2px 10px rgba(0,0,0,0.05); border-left:3px solid #198754; align-items:center; justify-content:space-between;">
            <div>
                <strong class="text-dark" style="font-size:0.85rem;">Install Aplikasi</strong>
            </div>
            <button id="btnPwaInstall" class="btn btn-sm btn-success py-1 px-2" style="font-size:0.75rem; font-weight:bold;">Install</button>
        </div>
        
        <div class="d-flex align-items-start mb-3" id="manualInstructions" style="font-size: 0.85rem; color: #475569;">
            <i class="bi bi-phone text-success me-2 mt-1 fs-5"></i>
            <div>
                <strong class="text-dark">Pasang ke Beranda:</strong><br>Klik ikon menu browser lalu pilih "Tambahkan ke Layar Utama".
            </div>
        </div>
        
        <div class="d-flex align-items-start" style="font-size: 0.85rem; color: #475569;">
            <i class="bi bi-bell text-success me-2 mt-1 fs-5"></i>
            <div>
                <strong class="text-dark">Akses Notifikasi:</strong><br>Setelah login, izinkan notifikasi agar Anda mendapat update nilai siswa.
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('togglePassword').addEventListener('click', function () {
    const pwd = document.getElementById('password');
    const type = pwd.getAttribute('type') === 'password' ? 'text' : 'password';
    pwd.setAttribute('type', type);
    this.querySelector('i').classList.toggle('bi-eye');
    this.querySelector('i').classList.toggle('bi-eye-slash');
});

// PWA Installation Scripts
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js')
            .catch(err => console.log('SW failed', err));
    });
}

let deferredPrompt;
const pwaBanner  = document.getElementById('pwaInstallBanner');
const manualInst = document.getElementById('manualInstructions');
const btnInstall = document.getElementById('btnPwaInstall');

window.addEventListener('beforeinstallprompt', (e) => {
    e.preventDefault();
    deferredPrompt = e;
    if (pwaBanner)  pwaBanner.style.display  = 'flex';
    if (manualInst) manualInst.style.display = 'none';
});

if (btnInstall) {
    btnInstall.addEventListener('click', async () => {
        if (!deferredPrompt) return;
        deferredPrompt.prompt();
        const { outcome } = await deferredPrompt.userChoice;
        deferredPrompt = null;
        if (pwaBanner) pwaBanner.style.display = 'none';
    });
}

if (window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone) {
    if (manualInst) manualInst.style.display = 'none';
    if (pwaBanner)  pwaBanner.style.display  = 'none';
}
</script>
@endsection
