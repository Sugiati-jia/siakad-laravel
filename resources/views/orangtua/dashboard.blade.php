@extends('layouts.app')

@section('title', "Dashboard Orang Tua")

@section('content')
<div class="ot-wrap">

    {{-- ── BANNER RAPOR ── --}}
    @if(session('trigger_erapor_notif'))
    <div id="rapor-banner" class="ot-banner">
        <div class="ot-banner-icon"><i class="bi bi-bell-fill"></i></div>
        <div class="ot-banner-body">
            <div class="ot-banner-title">🔔 E-Rapor Telah Terbit!</div>
            <div class="ot-banner-desc">Hasil belajar semester aktif putra/putri Anda sudah diterbitkan.</div>
            <a href="{{ route('orangtua.rapor.index') }}" class="ot-banner-btn">Buka E-Rapor</a>
        </div>
        <button class="ot-banner-close" onclick="document.getElementById('rapor-banner').remove()">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>
    @endif

    {{-- ── HERO ANAK ── --}}
    @if($selectedChild)
    <div class="ot-hero">
        <div class="ot-hero-avatar">{{ strtoupper(substr($selectedChild->nama,0,1)) }}</div>
        <div class="ot-hero-info">
            <div class="ot-hero-name">{{ $selectedChild->nama }}</div>
            <div class="ot-hero-meta">
                NISN {{ $selectedChild->nisn }}
                &nbsp;·&nbsp;
                <span class="ot-chip">{{ $selectedChild->kelas->nama_kelas }}</span>
                @if(isset($rank) && $rank <= 3)
                    &nbsp;<span class="ot-chip chip-gold">🏆 Rank {{ $rank }}</span>
                @endif
            </div>
        </div>
        <a href="{{ route('orangtua.select-anak') }}" class="ot-switch-btn" title="Ganti Anak">
            <i class="bi bi-arrow-left-right"></i>
        </a>
    </div>
    @else
    <div class="ot-hero ot-hero-empty">
        <i class="bi bi-person-circle" style="font-size:2rem;opacity:.6;"></i>
        <div style="flex:1">
            <div class="ot-hero-name" style="font-size:.95rem;">Profil anak belum dipilih</div>
        </div>
        <a href="{{ route('orangtua.select-anak') }}" class="ot-switch-btn">Pilih</a>
    </div>
    @endif

    {{-- ── QUICK MENU ── --}}
    <div class="ot-menu-grid">
        <a href="{{ route('orangtua.nilai.index') }}" class="ot-menu-card">
            <div class="ot-menu-icon" style="background:#dbeafe;color:#2563eb;"><i class="bi bi-journal-check"></i></div>
            <div class="ot-menu-label">Nilai</div>
        </a>
        <a href="{{ route('orangtua.rapor.index') }}" class="ot-menu-card">
            <div class="ot-menu-icon" style="background:#dcfce7;color:#16a34a;"><i class="bi bi-file-earmark-pdf"></i></div>
            <div class="ot-menu-label">E-Rapor</div>
        </a>
        <a href="{{ route('orangtua.notifications.index') }}" class="ot-menu-card">
            <div class="ot-menu-icon" style="background:#fef9c3;color:#ca8a04;position:relative;">
                <i class="bi bi-bell"></i>
                @php $unread = \App\Models\UserNotification::where('user_id', auth()->id())->where('is_read', false)->count(); @endphp
                @if($unread > 0)<span class="ot-badge">{{ $unread }}</span>@endif
            </div>
            <div class="ot-menu-label">Notifikasi</div>
        </a>
        <a href="{{ route('orangtua.select-anak') }}" class="ot-menu-card">
            <div class="ot-menu-icon" style="background:#f3e8ff;color:#9333ea;"><i class="bi bi-people"></i></div>
            <div class="ot-menu-label">Profil Anak</div>
        </a>
    </div>

    {{-- ── BIODATA ── --}}
    @if($selectedChild)
    <div class="ot-section-title">Biodata Siswa</div>
    <div class="ot-card">
        <div class="ot-row-item">
            <span class="ot-row-label">Nama Lengkap</span>
            <span class="ot-row-val">{{ $selectedChild->nama }}</span>
        </div>
        <div class="ot-row-item">
            <span class="ot-row-label">NIS / NISN</span>
            <span class="ot-row-val">{{ $selectedChild->nis }} / {{ $selectedChild->nisn }}</span>
        </div>
        <div class="ot-row-item">
            <span class="ot-row-label">Jenis Kelamin</span>
            <span class="ot-row-val">{{ $selectedChild->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</span>
        </div>
        <div class="ot-row-item">
            <span class="ot-row-label">Tempat, Tgl Lahir</span>
            <span class="ot-row-val">{{ $selectedChild->tempat_lahir }}, {{ $selectedChild->tanggal_lahir?->format('d F Y') }}</span>
        </div>
        <div class="ot-row-item">
            <span class="ot-row-label">Nama Ayah</span>
            <span class="ot-row-val">{{ $selectedChild->nama_ayah ?: '-' }}</span>
        </div>
        <div class="ot-row-item">
            <span class="ot-row-label">Nama Ibu</span>
            <span class="ot-row-val">{{ $selectedChild->nama_ibu ?: '-' }}</span>
        </div>
        <div class="ot-row-item">
            <span class="ot-row-label">Alamat</span>
            <span class="ot-row-val">{{ $selectedChild->alamat ?: '-' }}</span>
        </div>
        <div class="ot-row-item" style="border:0;">
            <span class="ot-row-label">No. HP Ortu</span>
            <span class="ot-row-val">{{ $selectedChild->no_hp_ortu ?: '-' }}</span>
        </div>
    </div>
    @endif
</div>

<style>
/* ── Global wrap ── */
.ot-wrap { padding: 12px 12px 80px; max-width: 600px; margin: 0 auto; }

/* ── Banner ── */
.ot-banner {
    display: flex; gap: 12px; align-items: flex-start;
    background: #0f172a; color: #fff;
    border-radius: 16px; padding: 16px; margin-bottom: 14px;
    position: relative;
}
.ot-banner-icon {
    width: 44px; height: 44px; border-radius: 12px;
    background: #1e40af; display: flex; align-items: center;
    justify-content: center; font-size: 1.3rem; color: #facc15; flex-shrink: 0;
    animation: ringbell 2s infinite;
}
@keyframes ringbell {
    0%,100%{transform:rotate(0)} 10%{transform:rotate(15deg)} 20%{transform:rotate(-10deg)} 30%{transform:rotate(8deg)} 40%{transform:rotate(-5deg)} 50%{transform:rotate(3deg)}
}
.ot-banner-body { flex: 1; }
.ot-banner-title { font-weight: 700; font-size: .95rem; margin-bottom: 4px; }
.ot-banner-desc  { font-size: .8rem; color: rgba(255,255,255,.6); margin-bottom: 10px; }
.ot-banner-btn   {
    display: inline-block; background: #2563eb; color: #fff;
    border-radius: 20px; padding: 6px 16px; font-size: .8rem;
    font-weight: 600; text-decoration: none;
}
.ot-banner-close {
    background: none; border: 0; color: rgba(255,255,255,.5);
    padding: 0; line-height: 1; cursor: pointer; flex-shrink: 0;
}

/* ── Hero ── */
.ot-hero {
    display: flex; align-items: center; gap: 14px;
    background: var(--custom-card-bg, #ffffff); border-radius: 18px;
    padding: 16px; margin-bottom: 14px;
    box-shadow: 0 2px 12px rgba(0,0,0,.07);
}
.ot-hero-empty { opacity: .7; }
.ot-hero-avatar {
    width: 52px; height: 52px; border-radius: 14px; flex-shrink: 0;
    background: linear-gradient(135deg,#3b82f6,#1d4ed8);
    color: #fff; font-weight: 800; font-size: 1.4rem;
    display: flex; align-items: center; justify-content: center;
}
.ot-hero-info { flex: 1; min-width: 0; }
.ot-hero-name { font-weight: 700; font-size: 1rem; color: #0f172a; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.ot-hero-meta { font-size: .78rem; color: #64748b; margin-top: 4px; display: flex; align-items: center; flex-wrap: wrap; gap: 4px; }
.ot-chip { background: var(--custom-header-bg, #f1f5f9); color: #475569; border-radius: 20px; padding: 2px 8px; font-size: .72rem; font-weight: 600; }
.ot-chip.chip-gold { background: #fef9c3; color: #854d0e; }
.ot-switch-btn {
    flex-shrink: 0; background: var(--custom-header-bg, #f1f5f9); border-radius: 10px;
    padding: 8px 12px; color: #475569; font-size: .8rem; font-weight: 600;
    text-decoration: none; border: 0; display: flex; align-items: center; gap: 4px;
}

/* ── Quick Menu ── */
.ot-menu-grid {
    display: grid; grid-template-columns: repeat(4,1fr);
    gap: 10px; margin-bottom: 18px;
}
.ot-menu-card {
    display: flex; flex-direction: column; align-items: center;
    gap: 8px; background: var(--custom-card-bg, #ffffff); border-radius: 16px;
    padding: 16px 8px; text-decoration: none;
    box-shadow: 0 2px 10px rgba(0,0,0,.06);
    transition: transform .15s, box-shadow .15s;
}
.ot-menu-card:active { transform: scale(.96); }
.ot-menu-icon {
    width: 48px; height: 48px; border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.3rem; position: relative;
}
.ot-badge {
    position: absolute; top: -4px; right: -4px;
    background: #ef4444; color: #fff; border-radius: 20px;
    font-size: .65rem; font-weight: 700; padding: 1px 5px;
    min-width: 18px; text-align: center;
}
.ot-menu-label { font-size: .72rem; font-weight: 600; color: #475569; }

/* ── Section title ── */
.ot-section-title {
    font-size: .8rem; font-weight: 700; color: #94a3b8;
    text-transform: uppercase; letter-spacing: .8px;
    margin: 0 0 8px; padding: 0 4px;
}

/* ── Card (rows) ── */
.ot-card {
    background: var(--custom-card-bg, #ffffff); border-radius: 16px;
    box-shadow: 0 2px 10px rgba(0,0,0,.06);
    overflow: hidden; margin-bottom: 14px;
}
.ot-row-item {
    display: flex; justify-content: space-between; align-items: flex-start;
    gap: 12px; padding: 12px 16px;
    border-bottom: 1px solid #f1f5f9;
}
.ot-row-label { font-size: .8rem; color: #94a3b8; flex-shrink: 0; }
.ot-row-val   { font-size: .85rem; font-weight: 600; color: #1e293b; text-align: right; }
</style>

<script>
function dismissInAppNotification() {
    const el = document.getElementById('rapor-banner');
    if (el) el.remove();
}
document.addEventListener('DOMContentLoaded', function() {
    @if(session('trigger_erapor_notif'))
    if ('Notification' in window && Notification.permission === 'granted') {
        new Notification('🔔 E-Rapor Kurikulum Merdeka Terbit!', {
            body: 'Silakan klik menu E-Rapor untuk melihat hasil belajar.',
            icon: 'https://cdn-icons-png.flaticon.com/512/2997/2997300.png',
            tag: 'e-rapor-notification',
            renotify: true
        });
    }
    @endif
});
</script>
@endsection

<style>
/* Dark Mode overrides for OT Dashboard */
html.dark-mode .ot-banner,
html.dark-mode .ot-hero,
html.dark-mode .ot-menu,
html.dark-mode .ot-panel,
html.dark-mode .ot-row-item {
    background: #1e293b !important;
    color: #f8fafc !important;
    border-color: rgba(255,255,255,0.1) !important;
}
html.dark-mode .ot-hero-name, 
html.dark-mode .ot-menu-label, 
html.dark-mode .ot-panel-title, 
html.dark-mode .ot-row-label {
    color: #f8fafc !important;
}
html.dark-mode .ot-chip {
    background: #334155 !important; 
    color: #cbd5e1 !important;
}
html.dark-mode .ot-menu-icon {
    background: #334155 !important;
}
</style>

