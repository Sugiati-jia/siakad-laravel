@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')
<style>
    html.dark-mode .prof-card { background: #1e293b !important; border-color: rgba(255,255,255,0.07) !important; }
    html.dark-mode .prof-divider { border-color: rgba(255,255,255,0.08) !important; }
    html.dark-mode .prof-label { color: #94a3b8 !important; }
    html.dark-mode .prof-input {
        background: #0f172a !important;
        border-color: rgba(255,255,255,0.1) !important;
        color: #f8fafc !important;
    }
    html.dark-mode .prof-input:focus { border-color: #3b82f6 !important; }
    html.dark-mode .prof-section-title { color: #f8fafc !important; }
    html.dark-mode .prof-section-sub { color: #64748b !important; }
    html.dark-mode .prof-hint { color: #475569 !important; }
    html.dark-mode .btn-prof-ghost {
        background: rgba(255,255,255,0.07) !important;
        color: #94a3b8 !important;
        border-color: rgba(255,255,255,0.1) !important;
    }
    html.dark-mode .btn-prof-ghost:hover { background: rgba(255,255,255,0.12) !important; color: #f8fafc !important; }
    html.dark-mode .prof-avatar-bg { border-color: rgba(255,255,255,0.12) !important; }

    .prof-input {
        width: 100%;
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        padding: 10px 14px;
        font-size: 0.875rem;
        color: #0f172a;
        background: #fff;
        transition: border-color .15s, box-shadow .15s;
        outline: none;
    }
    .prof-input:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59,130,246,.1);
    }

    .prof-label {
        font-size: 0.78rem;
        font-weight: 600;
        color: #64748b;
        display: block;
        margin-bottom: 5px;
        letter-spacing: .02em;
    }

    .prof-hint { font-size: .72rem; color: #94a3b8; margin-top: 4px; }

    .btn-prof-primary {
        background: linear-gradient(135deg,#1e293b,#334155);
        color:#fff; border:none; border-radius:10px;
        padding:10px 26px; font-size:.875rem; font-weight:700;
        cursor:pointer; transition:all .15s;
        display:inline-flex; align-items:center; gap:7px;
    }
    .btn-prof-primary:hover { transform:translateY(-1px); box-shadow:0 6px 20px rgba(30,41,59,.3); color:#fff; }
    html.dark-mode .btn-prof-primary { background:linear-gradient(135deg,#3b82f6,#2563eb) !important; }

    .btn-prof-ghost {
        background:#f8fafc; color:#475569;
        border:1.5px solid #e2e8f0; border-radius:10px;
        padding:9px 20px; font-size:.875rem; font-weight:600;
        cursor:pointer; transition:all .15s;
        display:inline-flex; align-items:center; gap:7px;
    }
    .btn-prof-ghost:hover { background:#e2e8f0; color:#1e293b; }

    .btn-prof-sm {
        background:#f1f5f9; color:#475569;
        border:1.5px solid #e2e8f0; border-radius:8px;
        padding:7px 16px; font-size:.8rem; font-weight:600;
        cursor:pointer; transition:all .15s;
        display:inline-flex; align-items:center; gap:6px;
    }
    .btn-prof-sm:hover { background:#e2e8f0; }
    html.dark-mode .btn-prof-sm {
        background: rgba(255,255,255,0.07) !important;
        color: #94a3b8 !important;
        border-color: rgba(255,255,255,0.1) !important;
    }
    html.dark-mode .btn-prof-sm:hover { background: rgba(255,255,255,0.13) !important; color: #f8fafc !important; }

    .prof-divider {
        border: none;
        border-top: 1px solid #f1f5f9;
        margin: 24px 0;
    }

    .prof-avatar-bg {
        width:80px; height:80px;
        border-radius:50%;
        border:3px solid #e2e8f0;
        object-fit:cover;
        flex-shrink:0;
    }
    .prof-avatar-placeholder {
        width:80px; height:80px;
        border-radius:50%;
        background:linear-gradient(135deg,#3b82f6,#6366f1);
        display:flex; align-items:center; justify-content:center;
        font-size:2rem; font-weight:800; color:#fff;
        flex-shrink:0;
    }

    .prof-pwd-body { display:none; margin-top:16px; }
    .prof-pwd-body.show { display:block; }

    @media(max-width:600px){
        .prof-grid-2 { grid-template-columns:1fr!important; }
    }
</style>

<div class="container-fluid py-4">

    {{-- ── HEADER CARD (sama gaya dengan dashboard) ── --}}
    <div class="card border-0 shadow-sm mb-4" style="border-radius:16px; background-color:#1e293b; color:white; overflow:hidden; position:relative;">
        {{-- grid bg --}}
        <div style="position:absolute;inset:0;background-image:linear-gradient(rgba(255,255,255,.03) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.03) 1px,transparent 1px);background-size:30px 30px;pointer-events:none;"></div>
        {{-- deco circle --}}
        <div style="position:absolute;top:-50px;right:-50px;width:160px;height:160px;border-radius:50%;border:35px solid rgba(59,130,246,.12);pointer-events:none;"></div>

        <div class="card-body p-4" style="position:relative;z-index:1;">
            <div class="d-flex align-items-center gap-4 flex-wrap">
                {{-- Avatar --}}
                <div id="headerAvatarWrap" style="flex-shrink:0;">
                    @if($user->foto)
                        <img src="{{ asset('storage/'.$user->foto) }}" id="headerAvatar"
                            style="width:72px;height:72px;border-radius:50%;object-fit:cover;border:3px solid rgba(255,255,255,.25);" alt="Foto">
                    @else
                        <div id="headerAvatar"
                            style="width:72px;height:72px;border-radius:50%;background:linear-gradient(135deg,#3b82f6,#6366f1);display:flex;align-items:center;justify-content:center;font-size:1.8rem;font-weight:800;color:#fff;border:3px solid rgba(255,255,255,.2);">
                            {{ strtoupper(substr($user->name,0,1)) }}
                        </div>
                    @endif
                </div>
                {{-- Info --}}
                <div>
                    @php
                        $roleLabels = ['admin'=>'Administrator','guru'=>'Guru','wali_kelas'=>'Wali Kelas','siswa'=>'Siswa','orangtua'=>'Orang Tua','kurikulum'=>'Kurikulum'];
                    @endphp
                    <span class="badge bg-info mb-2 px-3 py-1 rounded-pill fw-bold text-uppercase" style="font-size:.7rem;">{{ $roleLabels[$user->role] ?? ucfirst($user->role) }}</span>
                    <h4 class="fw-bold mb-1" style="letter-spacing:-.3px;">{{ $user->name }}</h4>
                    <p class="mb-0" style="color:rgba(255,255,255,.55);font-size:.83rem;">
                        <i class="bi bi-envelope me-1"></i>{{ $user->email }}
                        &nbsp;·&nbsp;
                        <i class="bi bi-person-badge me-1"></i>{{ $user->username }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- ── FORM — SATU CARD ── --}}
    @if(session('success'))
        <div class="alert alert-success border-0 rounded-3 mb-3 d-flex align-items-center gap-2" style="font-size:.875rem;">
            <i class="bi bi-check-circle-fill text-success"></i> {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger border-0 rounded-3 mb-3" style="font-size:.875rem;">
            <ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <div class="card border-0 shadow-sm prof-card" style="border-radius:16px;">
        <div class="card-body p-4">

            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- FOTO --}}
                <div class="d-flex align-items-center gap-4 flex-wrap">
                    <div id="avatarWrap">
                        @if($user->foto)
                            <img src="{{ asset('storage/'.$user->foto) }}" class="prof-avatar-bg" id="avatarPreview" alt="Foto">
                        @else
                            <div class="prof-avatar-placeholder" id="avatarPreview">{{ strtoupper(substr($user->name,0,1)) }}</div>
                        @endif
                    </div>
                    <div>
                        <p class="fw-bold prof-section-title mb-1" style="font-size:.95rem;">Foto Profil</p>
                        <p class="prof-hint mb-2">Format JPG / PNG · Maks. 2MB</p>
                        <label for="foto" class="btn-prof-sm" style="cursor:pointer;">
                            <i class="bi bi-cloud-upload"></i> Unggah Foto
                        </label>
                        <input type="file" id="foto" name="foto" class="d-none" accept="image/*">
                    </div>
                </div>

                <hr class="prof-divider">

                {{-- DATA DIRI --}}
                <p class="fw-bold prof-section-title mb-1" style="font-size:.95rem;">Data Diri</p>
                <p class="prof-hint mb-3">Nama, username, dan email yang digunakan untuk login.</p>

                <div class="prof-grid-2" style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
                    <div>
                        <label class="prof-label" for="name">Nama Lengkap</label>
                        <input type="text" id="name" name="name" class="prof-input"
                            value="{{ old('name',$user->name) }}" required placeholder="Nama lengkap">
                    </div>
                    <div>
                        <label class="prof-label" for="username">Username</label>
                        <input type="text" id="username" name="username" class="prof-input"
                            value="{{ old('username',$user->username) }}" required placeholder="Username login">
                    </div>
                </div>
                <div>
                    <label class="prof-label" for="email">Email</label>
                    <input type="email" id="email" name="email" class="prof-input"
                        value="{{ old('email',$user->email) }}" required placeholder="Alamat email">
                    <p class="prof-hint"><i class="bi bi-lock me-1"></i>Digunakan untuk login ke akun Anda</p>
                </div>

                <hr class="prof-divider">

                {{-- KATA SANDI --}}
                <div class="d-flex align-items-start justify-content-between gap-3">
                    <div>
                        <p class="fw-bold prof-section-title mb-1" style="font-size:.95rem;">Kata Sandi</p>
                        <p class="prof-hint mb-0">Kosongkan jika tidak ingin mengubah kata sandi.</p>
                    </div>
                    <button type="button" class="btn-prof-sm flex-shrink-0" id="togglePwdBtn" onclick="togglePwd()">
                        <i class="bi bi-pencil-fill"></i> Ubah
                    </button>
                </div>

                <div class="prof-pwd-body" id="pwdForm">
                    <div class="prof-grid-2 mt-3" style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                        <div>
                            <label class="prof-label" for="password">Kata Sandi Baru</label>
                            <input type="password" id="password" name="password" class="prof-input"
                                placeholder="Minimal 8 karakter">
                        </div>
                        <div>
                            <label class="prof-label" for="password_confirmation">Konfirmasi Kata Sandi</label>
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                class="prof-input" placeholder="Ketik ulang kata sandi baru">
                        </div>
                    </div>
                </div>

                <hr class="prof-divider">

                {{-- ACTION BUTTONS --}}
                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ url()->previous() }}" class="btn-prof-ghost">
                        <i class="bi bi-x-lg"></i> Batal
                    </a>
                    <button type="submit" class="btn-prof-primary">
                        <i class="bi bi-check-lg"></i> Simpan Perubahan
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

<script>
function togglePwd() {
    const form = document.getElementById('pwdForm');
    const btn  = document.getElementById('togglePwdBtn');
    const show = form.classList.toggle('show');
    btn.innerHTML = show ? '<i class="bi bi-x-lg"></i> Tutup' : '<i class="bi bi-pencil-fill"></i> Ubah';
}

@if($errors->has('password') || $errors->has('password_confirmation'))
    document.addEventListener('DOMContentLoaded', () => {
        document.getElementById('pwdForm').classList.add('show');
        document.getElementById('togglePwdBtn').innerHTML = '<i class="bi bi-x-lg"></i> Tutup';
    });
@endif

// Preview foto & update header avatar
document.getElementById('foto').addEventListener('change', function(e) {
    if (!e.target.files?.[0]) return;
    const reader = new FileReader();
    reader.onload = ev => {
        // preview kecil
        document.getElementById('avatarWrap').innerHTML =
            `<img src="${ev.target.result}" class="prof-avatar-bg" id="avatarPreview" alt="Foto">`;
        // update avatar di header
        const ha = document.getElementById('headerAvatarWrap');
        ha.innerHTML = `<img src="${ev.target.result}" id="headerAvatar"
            style="width:72px;height:72px;border-radius:50%;object-fit:cover;border:3px solid rgba(255,255,255,.25);" alt="Foto">`;
    };
    reader.readAsDataURL(e.target.files[0]);
});
</script>
@endsection
