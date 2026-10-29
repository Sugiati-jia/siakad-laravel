@extends('layouts.auth-split')
@section('title', 'SIAKAD')

@section('content')
<style>
/* ═══ Form directly on panel — NO card wrapper ═══ */
.lf {
    width: 100%;
    max-width: 370px;
}

/* ── Logo icon at top (small rounded square like reference) ── */
.lf-icon {
    width: 72px; height: 72px;
    background: var(--custom-card-bg, #ffffff);
    border-radius: 20px;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 24px auto;
    box-shadow: 0 6px 24px rgba(108,130,255,.15);
    overflow: hidden;
}
.lf-icon img { width: 54px; height: 54px; object-fit: contain; }
.lf-icon .fb { font-size: 1.8rem; color: #6C82FF; display: none; }

/* ── Heading ── */
.lf-header-text {
    text-align: center;
    margin-bottom: 32px;
}
.lf h3 {
    font-size: 1.7rem; font-weight: 800;
    color: #0f172a; margin-bottom: 8px;
    letter-spacing: -0.5px;
}
.lf .lf-sub {
    font-size: 0.88rem; color: #8898b4; margin: 0;
}

/* ── Alerts ── */
.lf-alert {
    display: flex; align-items: center; gap: 8px;
    border-radius: 8px; padding: 10px 14px;
    font-size: .83rem; font-weight: 500; margin-bottom: 16px;
}
.lf-ok  { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
.lf-err { background: var(--custom-card-bg, #ffffff)1f2; color: #be123c; border: 1px solid #fecdd3; }

/* ═══ Role segmented tabs ═══ */
.lf-seg {
    display: flex;
    border: 1.5px solid #d8dff2;
    border-radius: 10px;
    overflow: hidden;
    margin-bottom: 28px;
}
.lf-seg-btn {
    flex: 1;
    padding: 10px 8px;
    border: none;
    background: transparent;
    cursor: pointer;
    font-family: inherit;
    font-size: .84rem;
    font-weight: 600;
    color: #8898b4;
    transition: all .2s;
    display: flex; align-items: center; justify-content: center; gap: 7px;
}
.lf-seg-btn + .lf-seg-btn {
    border-left: 1.5px solid #d8dff2;
}
.lf-seg-btn:hover { color: #5a6888; background: rgba(108,130,255,.04); }
.lf-seg-btn.active {
    background: rgba(108,130,255,.08);
    color: #6C82FF;
}
.lf-seg-btn .seg-ic { font-size: 1rem; }

/* ═══ Input fields — bottom border like reference ═══ */
.lf-group { margin-bottom: 22px; }

.lf-label {
    display: block;
    font-size: .78rem; font-weight: 600;
    color: #8898b4; margin-bottom: 6px;
    letter-spacing: .02em;
}

.lf-row {
    display: flex; align-items: center;
    border-bottom: 2px solid #e0e6f0;
    transition: border-color .2s;
}
.lf-row:focus-within { border-color: #6C82FF; }

.lf-row .ic {
    color: #6C82FF; font-size: .92rem;
    margin-right: 10px; flex-shrink: 0;
}

.lf-input {
    flex: 1;
    border: none; outline: none;
    background: transparent;
    font-family: inherit;
    font-size: .93rem; color: #1a2340;
    padding: 9px 0;
}
.lf-input::placeholder { color: #b8c4d8; }

.lf-eye {
    background: none; border: none;
    color: #b8c4d8; cursor: pointer;
    font-size: .92rem; padding: 4px;
    flex-shrink: 0; transition: color .2s;
}
.lf-eye:hover { color: #6C82FF; }

.lf-errmsg { font-size: .75rem; color: #e11d48; margin-top: 4px; }
.lf-hint   { font-size: .75rem; color: #8898b4; margin-top: 4px; }

/* ── Remember & Forgot ── */
.lf-meta {
    display: flex; align-items: center;
    justify-content: space-between;
    margin-bottom: 24px;
}
.lf-chk {
    display: flex; align-items: center; gap: 6px;
    font-size: .82rem; color: #8898b4; cursor: pointer;
}
.lf-chk input[type="checkbox"] {
    width: 14px; height: 14px;
    accent-color: #6C82FF; cursor: pointer;
}
.lf-fgt {
    font-size: .82rem; font-weight: 600;
    color: #6C82FF; text-decoration: none;
}
.lf-fgt:hover { text-decoration: underline; }

/* ── Login button ── */
.lf-btn {
    width: 100%; padding: 12px;
    border: none; border-radius: 10px;
    background: #6C82FF;
    color: #fff; font-size: .95rem; font-weight: 700;
    font-family: inherit; letter-spacing: .02em;
    cursor: pointer;
    box-shadow: 0 4px 16px rgba(108,130,255,.35);
    transition: background .2s, transform .15s;
}
.lf-btn:hover { background: #5A6EEB; transform: translateY(-1px); }
.lf-btn:active { transform: translateY(0); }

/* ── Helpdesk ── */
.lf-hd {
    text-align: center; margin-top: 20px;
    font-size: .78rem; color: #8898b4;
}
.lf-hd a { color: #6C82FF; font-weight: 600; text-decoration: none; }
.lf-hd a:hover { text-decoration: underline; }

/* Hide PWA install UI when running in standalone mode (already installed) */
@media all and (display-mode: standalone) {
    #pwaSidePanel, #pwaInstallBanner, #pwaFallbackModal {
        display: none !important;
    }
}

/* ── PWA Install Banner ── */
.pwa-install-banner {
    display: none; /* Hidden by default, shown by JS */
    align-items: center; gap: 12px;
    background: linear-gradient(135deg, #6C82FF 0%, #5A6EEB 100%);
    border-radius: 12px;
    padding: 12px 16px;
    margin-bottom: 20px;
    box-shadow: 0 4px 16px rgba(108,130,255,.3);
    animation: slideDown .4s ease;
}
@keyframes slideDown {
    from { opacity: 0; transform: translateY(-10px); }
    to   { opacity: 1; transform: translateY(0); }
}
.pwa-install-banner.visible { display: flex; }
.pwa-install-icon {
    width: 42px; height: 42px; border-radius: 10px;
    background: rgba(255,255,255,.2);
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.pwa-install-icon img { width: 28px; height: 28px; object-fit: contain; }
.pwa-install-text { flex: 1; }
.pwa-install-text strong { display: block; color: #fff; font-size: .88rem; }
.pwa-install-text span { color: rgba(255,255,255,.8); font-size: .75rem; }
.pwa-install-btn {
    background: #fff; color: #6C82FF;
    border: none; border-radius: 8px;
    padding: 7px 14px; font-size: .8rem; font-weight: 700;
    cursor: pointer; white-space: nowrap;
    transition: transform .15s, box-shadow .15s;
    flex-shrink: 0;
}
.pwa-install-btn:hover { transform: translateY(-1px); box-shadow: 0 3px 10px rgba(0,0,0,.15); }
.pwa-install-close {
    background: none; border: none; color: rgba(255,255,255,.7);
    font-size: 1rem; cursor: pointer; padding: 2px 4px; flex-shrink: 0;
}
.pwa-install-close:hover { color: #fff; }
</style>

<div class="lf">


    {{-- Alerts --}}
    @if(session('success'))
        <div class="lf-alert lf-ok"><i class="bi bi-check-circle-fill"></i> {{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="lf-alert lf-err"><i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}</div>
    @endif

    {{-- Logo icon --}}
    <div class="lf-icon">
        <i class="bi bi-mortarboard-fill" style="font-size: 2.2rem; color: #6C82FF;"></i>
    </div>

    {{-- Heading --}}
    <div class="lf-header-text">
        <h3>Masuk ke Akun</h3>
        <p class="lf-sub">Pilih tipe akun, lalu isi email dan kata sandi Anda</p>
    </div>

    {{-- Role selector --}}
    <div class="lf-seg">
        <button type="button" class="lf-seg-btn active" data-role="pegawai" onclick="setRole('pegawai')">
            <i class="bi bi-person-workspace seg-ic"></i>
            <span>Admin / Guru</span>
        </button>
        <button type="button" class="lf-seg-btn" data-role="orang_tua" onclick="setRole('orang_tua')">
            <i class="bi bi-people-fill seg-ic"></i>
            <span>Orang Tua</span>
        </button>
    </div>

    {{-- Form --}}
    <form method="POST" action="{{ route('login') }}" id="loginForm">
        @csrf
        <input type="hidden" name="login_as" id="login_as" value="">

        {{-- Email --}}
        <div class="lf-group">
            <label class="lf-label" id="emailLabel">Email</label>
            <div class="lf-row">
                <i class="bi bi-envelope ic" id="emailIcon"></i>
                <input id="username_input" type="email" name="email" class="lf-input"
                       value="{{ old('email') ?? old('nisn') }}"
                       required autofocus placeholder="Enter your email address">
            </div>
            @if($errors->has('email') || $errors->has('nisn'))
                <div class="lf-errmsg">{{ $errors->first('email') ?: $errors->first('nisn') }}</div>
            @endif
        </div>

        {{-- Password --}}
        <div class="lf-group">
            <label class="lf-label">Password</label>
            <div class="lf-row">
                <i class="bi bi-lock ic"></i>
                <input id="password" type="password" name="password" class="lf-input"
                       required autocomplete="current-password" placeholder="••••••••••••">
                <button type="button" class="lf-eye" id="togglePassword">
                    <i class="bi bi-eye-slash"></i>
                </button>
            </div>
            @error('password')<div class="lf-errmsg">{{ $message }}</div>@enderror
            <div id="password_hint" class="lf-hint d-none">* Gunakan NISN sebagai kata sandi</div>
        </div>

        {{-- Remember & Forgot --}}
        <div class="lf-meta">
            <label class="lf-chk">
                <input type="checkbox" name="remember">
                Remember me
            </label>
            @if(config('app.env') === 'local')
                <a href="{{ route('password.request') }}" class="lf-fgt">Lupa Password?</a>
            @endif
        </div>

        {{-- Submit --}}
        <button type="submit" class="lf-btn">Login</button>
    </form>

    <div class="lf-hd">
        Butuh bantuan? <a href="#">WA 0812-3456-7890</a>
    </div>
</div>

@section('left-extra')
<div style="background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.25);border-radius:14px;padding:16px;text-align:left;margin-top:16px;">
    <p style="font-size:.86rem;font-weight:700;color:#fff;margin-bottom:12px;">
        <i class="bi bi-info-circle-fill" style="margin-right:6px;"></i> Panduan Instalasi Aplikasi
    </p>
    <div style="display:flex;gap:9px;font-size:.79rem;color:rgba(255,255,255,.82);margin-bottom:10px;">
        <i class="bi bi-phone" style="color:#fff;font-size:1rem;margin-top:-2px;"></i>
        <div><strong style="color:#fff;">Pasang ke Beranda:</strong> Menu browser &rarr; "Tambahkan ke Layar Utama" (Install app).</div>
    </div>
    <div style="display:flex;gap:9px;font-size:.79rem;color:rgba(255,255,255,.82);">
        <i class="bi bi-bell" style="color:#fff;font-size:1rem;margin-top:-2px;"></i>
        <div><strong style="color:#fff;">Aktifkan Notifikasi:</strong> Izinkan notifikasi agar tidak tertinggal info sekolah.</div>
    </div>
</div>

{{-- PWA Install Modal — SINTAK-style with SIAKAD branding --}}
<div id="pwaFallbackModal" style="
    display:none; position:fixed; inset:0; background:rgba(15,23,42,.55);
    z-index:9999; align-items:center; justify-content:center; padding:16px;
    backdrop-filter:blur(3px);
">
    <div id="pwaModalBox" style="
        background:#fff; border-radius:20px; width:100%; max-width:420px;
        box-shadow:0 20px 60px rgba(0,0,0,.3); overflow:hidden;
        animation:modalIn .3s cubic-bezier(.34,1.56,.64,1);
        font-family:inherit;
    ">
        {{-- ── Header ── --}}
        <div style="background:linear-gradient(135deg,#6C82FF,#4f6ef7);padding:20px 20px 18px;position:relative;display:flex;align-items:center;gap:14px;">
            <button onclick="closePwaModal()" style="position:absolute;top:12px;right:12px;background:rgba(255,255,255,.25);border:none;color:#fff;width:28px;height:28px;border-radius:50%;cursor:pointer;font-size:1rem;line-height:1;display:flex;align-items:center;justify-content:center;transition:.2s;" onmouseover="this.style.background='rgba(255,255,255,.4)'" onmouseout="this.style.background='rgba(255,255,255,.25)'">&times;</button>
            <div style="width:50px;height:50px;background:rgba(255,255,255,.22);border-radius:13px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <img src="/images/logo-sekolah.png" style="width:36px;height:36px;object-fit:contain;border-radius:8px;" onerror="this.style.display='none';this.parentElement.innerHTML='<i class=\'bi bi-mortarboard-fill\' style=\'font-size:1.4rem;color:#fff;\'></i>';">
            </div>
            <div>
                <div style="font-size:1.05rem;font-weight:800;color:#fff;line-height:1.2;">SIAKAD</div>
                <div style="font-size:.76rem;color:rgba(255,255,255,.85);margin-top:2px;">Sistem Informasi Akademik SMK Sunan Giri</div>
            </div>
        </div>

        {{-- ── Feature Cards ── --}}
        <div style="padding:18px 16px 6px;">

            {{-- Card 1 --}}
            <div style="background:#f8faff;border:1px solid #e8eeff;border-radius:12px;padding:14px 16px;margin-bottom:10px;display:flex;align-items:flex-start;gap:14px;text-align:left;">
                <div style="width:44px;height:44px;min-width:44px;background:#dde5ff;border-radius:50%;display:flex;align-items:center;justify-content:center;">
                    <i class="bi bi-file-earmark-bar-graph-fill" style="color:#6C82FF;font-size:1.1rem;"></i>
                </div>
                <div>
                    <div style="font-size:.875rem;font-weight:700;color:#1e293b;margin-bottom:4px;">Penilaian Terintegrasi</div>
                    <div style="font-size:.77rem;color:#64748b;line-height:1.55;">Akses mudah untuk melihat rekapitulasi capaian akademik dan e-rapor langsung dari genggaman Anda.</div>
                </div>
            </div>

            {{-- Card 2 --}}
            <div style="background:#fffbf0;border:1px solid #fde9a2;border-radius:12px;padding:14px 16px;margin-bottom:10px;display:flex;align-items:flex-start;gap:14px;text-align:left;">
                <div style="width:44px;height:44px;min-width:44px;background:#fde68a;border-radius:50%;display:flex;align-items:center;justify-content:center;">
                    <i class="bi bi-rocket-takeoff-fill" style="color:#d97706;font-size:1.1rem;"></i>
                </div>
                <div>
                    <div style="font-size:.875rem;font-weight:700;color:#1e293b;margin-bottom:4px;">Praktis Tanpa Ribet</div>
                    <div style="font-size:.77rem;color:#64748b;line-height:1.55;">Akses SIAKAD instan dari layar utama. Hemat waktu tanpa perlu buka browser dan mengetik alamat web.</div>
                </div>
            </div>

            {{-- Card 3 --}}
            <div style="background:#f0fff8;border:1px solid #a7f3d0;border-radius:12px;padding:14px 16px;margin-bottom:4px;display:flex;align-items:flex-start;gap:14px;text-align:left;">
                <div style="width:44px;height:44px;min-width:44px;background:#6ee7b7;border-radius:50%;display:flex;align-items:center;justify-content:center;">
                    <i class="bi bi-ui-radios-grid" style="color:#059669;font-size:1.1rem;"></i>
                </div>
                <div>
                    <div style="font-size:.875rem;font-weight:700;color:#1e293b;margin-bottom:4px;">Antarmuka Lebih Optimal</div>
                    <div style="font-size:.77rem;color:#64748b;line-height:1.55;">Desain yang sangat responsif, memberikan kenyamanan ekstra saat mengakses data penilaian di HP maupun PC.</div>
                </div>
            </div>

        </div>

        {{-- ── Footer ── --}}
        <div style="padding:16px 16px 18px;" id="pwaModalFooter">
            {{-- Default Install Button --}}
            <button id="pwaModalInstallBtn" onclick="startFakeDownload()" style="
                width:100%; padding:14px 20px;
                background:#10b981; /* Green color like reference */
                color:#fff; border:none; border-radius:12px;
                font-size:.95rem; font-weight:800; cursor:pointer;
                display:flex; align-items:center; justify-content:center; gap:10px;
                box-shadow:0 4px 18px rgba(16,185,129,.35);
                transition:transform .15s, box-shadow .15s;
                margin-bottom:12px; letter-spacing:.01em;
            " onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 8px 24px rgba(16,185,129,.45)'" onmouseout="this.style.transform='none';this.style.boxShadow='0 4px 18px rgba(16,185,129,.35)'">
                <i class="bi bi-download" style="font-size:1rem;"></i> Download Sekarang
            </button>

            {{-- Downloading State (Hidden initially) --}}
            <div id="pwaDownloadingState" style="display:none; margin-bottom:12px;">
                <button style="
                    width:100%; padding:14px 20px; background:#10b981; color:#fff;
                    border:none; border-radius:12px; font-size:.95rem; font-weight:800;
                    display:flex; align-items:center; justify-content:center; gap:10px;
                    margin-bottom:16px; cursor:default;
                ">
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="width:1rem;height:1rem;border-width:.15em;"></span>
                    Mengunduh...
                </button>

                <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:16px;">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
                        <div style="display:flex; align-items:center; gap:8px; font-size:.85rem; color:#1e293b; font-weight:600;">
                            <span class="spinner-border spinner-border-sm text-success" role="status" style="width:.9rem;height:.9rem;border-width:.12em;"></span>
                            Sedang mengunduh...
                        </div>
                        <div id="pwaProgressText" style="color:#10b981; font-weight:700; font-size:.85rem;">0%</div>
                    </div>
                    <div style="width:100%; height:8px; background:#e2e8f0; border-radius:4px; overflow:hidden; margin-bottom:8px;">
                        <div id="pwaProgressBar" style="height:100%; width:0%; background:#10b981; border-radius:4px; transition:width .2s linear;"></div>
                    </div>
                    <div style="text-align:center; font-size:.75rem; color:#94a3b8; font-weight:500;">
                        <span id="pwaDownloadedSize">0.0 MB</span> / 28.9 MB
                    </div>
                </div>
            </div>

            {{-- Manual guide box — only shows when no native prompt --}}
            <div id="pwaManualGuide" style="display:none;background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:12px 14px;margin-bottom:12px;text-align:left;">
                <p style="font-size:.76rem;font-weight:700;color:#475569;margin-bottom:7px;"><i class="bi bi-info-circle" style="margin-right:5px;color:#6C82FF;"></i>Install manual via browser:</p>
                <div style="font-size:.74rem;color:#64748b;line-height:1.8;">
                    &bull; <strong style="color:#334155;">Chrome/Edge:</strong> ketuk <strong>⋮</strong> &rarr; <em>"Tambahkan ke layar utama"</em><br>
                    &bull; <strong style="color:#334155;">Safari (iOS):</strong> ketuk <strong>□↑</strong> &rarr; <em>"Add to Home Screen"</em>
                </div>
            </div>

            {{-- Checkbox --}}
            <label id="pwaDontShowContainer" style="display:flex;align-items:center;justify-content:center;gap:8px;font-size:.78rem;color:#94a3b8;cursor:pointer;user-select:none;">
                <input type="checkbox" id="pwaDontShow" style="accent-color:#10b981;width:14px;height:14px;cursor:pointer;" onchange="setPwaDontShow(this.checked)">
                Jangan tampilkan popup ini lagi
            </label>
        </div>
    </div>
</div>

<style>
@keyframes modalIn {
    from { opacity:0; transform:scale(.92) translateY(20px); }
    to   { opacity:1; transform:scale(1) translateY(0); }
}
</style>
@endsection

<script>
function setRole(role) {
    document.querySelectorAll('.lf-seg-btn').forEach(function(el){ el.classList.remove('active'); });
    document.querySelector('.lf-seg-btn[data-role="'+role+'"]').classList.add('active');

    var form    = document.getElementById('loginForm');
    var uInput  = document.getElementById('username_input');
    var hint    = document.getElementById('password_hint');
    var loginAs = document.getElementById('login_as');
    var ic      = document.getElementById('emailIcon');
    var label   = document.getElementById('emailLabel');

    if (role === 'pegawai') {
        form.action = "{{ route('login') }}";
        uInput.name = 'email'; uInput.type = 'email';
        uInput.placeholder = 'Enter your email address';
        if (ic) ic.className = 'bi bi-envelope ic';
        if (label) label.textContent = 'Email';
        hint.classList.add('d-none');
        loginAs.value = '';
    } else {
        form.action = "{{ route('orangtua.login') }}";
        uInput.name = 'nisn'; uInput.type = 'text';
        uInput.placeholder = 'Masukkan NISN siswa';
        if (ic) ic.className = 'bi bi-person-vcard ic';
        if (label) label.textContent = 'NISN';
        hint.classList.remove('d-none');
        loginAs.value = role;
    }
}

document.getElementById('togglePassword').addEventListener('click', function() {
    var pwd = document.getElementById('password');
    var icon = this.querySelector('i');
    if (pwd.type === 'password') { pwd.type = 'text'; icon.className = 'bi bi-eye'; }
    else { pwd.type = 'password'; icon.className = 'bi bi-eye-slash'; }
});

window.addEventListener('DOMContentLoaded', function() {
    @if(old('nisn') || old('login_as'))
        setRole('{{ old("login_as") ?: "orang_tua" }}');
    @else
        setRole('pegawai');
    @endif
});

// ── PWA Install Prompt ──
let deferredPrompt = null;

window.addEventListener('beforeinstallprompt', function(e) {
    e.preventDefault();
    deferredPrompt = e;

    // Show install banner on form side (top of login form)
    const banner = document.getElementById('pwaInstallBanner');
    if (banner) banner.classList.add('visible');

    // Show native install button inside modal
    const modalBtn = document.getElementById('pwaModalInstallBtn');
    if (modalBtn) { modalBtn.style.display = 'flex'; }

    // Hide manual guide — native prompt available
    const manualGuide = document.getElementById('pwaManualGuide');
    if (manualGuide) manualGuide.style.display = 'none';
});

function triggerPwaInstall() {
    // Check "don't show again" preference
    if (localStorage.getItem('pwaDontShow') === '1') {
        if (deferredPrompt) { deferredPrompt.prompt(); return; }
        return;
    }

    const modal = document.getElementById('pwaFallbackModal');
    if (!deferredPrompt) {
        // No native prompt — show modal with manual guide
        const modalBtn = document.getElementById('pwaModalInstallBtn');
        if (modalBtn) modalBtn.style.display = 'none';
        const manualGuide = document.getElementById('pwaManualGuide');
        if (manualGuide) manualGuide.style.display = 'block';
    } else {
        // Native prompt available — show modal with Install button
        const modalBtn = document.getElementById('pwaModalInstallBtn');
        if (modalBtn) modalBtn.style.display = 'flex';
        const manualGuide = document.getElementById('pwaManualGuide');
        if (manualGuide) manualGuide.style.display = 'none';
    }
    
    // Reset downloading state if it was shown previously
    const downloadState = document.getElementById('pwaDownloadingState');
    if (downloadState) downloadState.style.display = 'none';
    const checkboxContainer = document.getElementById('pwaDontShowContainer');
    if (checkboxContainer) checkboxContainer.style.display = 'flex';

    if (modal) modal.style.display = 'flex';
}

function startFakeDownload() {
    const btn = document.getElementById('pwaModalInstallBtn');
    const downloadState = document.getElementById('pwaDownloadingState');
    const checkboxContainer = document.getElementById('pwaDontShowContainer');
    const manualGuide = document.getElementById('pwaManualGuide');
    
    if (btn) btn.style.display = 'none';
    if (checkboxContainer) checkboxContainer.style.display = 'none';
    if (manualGuide) manualGuide.style.display = 'none';
    if (downloadState) downloadState.style.display = 'block';

    let progress = 0;
    const bar = document.getElementById('pwaProgressBar');
    const text = document.getElementById('pwaProgressText');
    const size = document.getElementById('pwaDownloadedSize');
    
    const interval = setInterval(() => {
        progress += Math.floor(Math.random() * 8) + 2; // random increment between 2-10
        if (progress > 100) progress = 100;
        
        if (bar) bar.style.width = progress + '%';
        if (text) text.innerText = progress + '%';
        if (size) size.innerText = (28.9 * (progress/100)).toFixed(1) + ' MB';
        
        if (progress === 100) {
            clearInterval(interval);
            setTimeout(() => {
                triggerNativeInstall();
            }, 300);
        }
    }, 150); // Updates every 150ms
}

function triggerNativeInstall() {
    if (!deferredPrompt) {
        // If they managed to click it without deferredPrompt, just show manual guide
        const downloadState = document.getElementById('pwaDownloadingState');
        if (downloadState) downloadState.style.display = 'none';
        const manualGuide = document.getElementById('pwaManualGuide');
        if (manualGuide) manualGuide.style.display = 'block';
        const checkboxContainer = document.getElementById('pwaDontShowContainer');
        if (checkboxContainer) checkboxContainer.style.display = 'flex';
        return;
    }
    
    deferredPrompt.prompt();
    deferredPrompt.userChoice.then(function(result) {
        closePwaModal();
        if (result.outcome === 'accepted') {
            const banner = document.getElementById('pwaInstallBanner');
            if (banner) banner.classList.remove('visible');
        }
        deferredPrompt = null;
    });
}

function closePwaModal() {
    const modal = document.getElementById('pwaFallbackModal');
    if (modal) modal.style.display = 'none';
}

function setPwaDontShow(checked) {
    localStorage.setItem('pwaDontShow', checked ? '1' : '0');
}

// Close banner ×
const closeBtn = document.getElementById('pwaInstallClose');
if (closeBtn) closeBtn.addEventListener('click', function() {
    const banner = document.getElementById('pwaInstallBanner');
    if (banner) banner.classList.remove('visible');
});

// Banner Install button
const installBtn = document.getElementById('pwaInstallBtn');
if (installBtn) installBtn.addEventListener('click', triggerPwaInstall);

// Hide banner after install
window.addEventListener('appinstalled', function() {
    const banner = document.getElementById('pwaInstallBanner');
    if (banner) banner.classList.remove('visible');
    closePwaModal();
});

// Close modal when clicking backdrop
document.getElementById('pwaFallbackModal').addEventListener('click', function(e) {
    if (e.target === this) closePwaModal();
});

</script>
@endsection
