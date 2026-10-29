@extends('layouts.app')

@section('title', 'Notifikasi E-Rapor')

@section('content')
<div class="ot-wrap">

    {{-- ── HEADER ── --}}
    <div class="ot-page-header">
        <div class="ot-page-icon" style="background:#fef9c3;color:#ca8a04;">
            <i class="bi bi-bell-fill"></i>
        </div>
        <div style="flex:1;">
            <div class="ot-page-title">Notifikasi
                @if($unreadCount > 0)
                    <span class="ot-badge-inline">{{ $unreadCount }}</span>
                @endif
            </div>
            <div class="ot-page-sub">Kotak masuk pemberitahuan dari sekolah</div>
        </div>
        <div style="display:flex; gap:8px; flex-shrink:0;">
            @if($unreadCount > 0)
            <form method="POST" action="{{ route('orangtua.notifications.read-all') }}" style="margin:0;">
                @csrf
                <button type="submit" class="ot-action-btn" title="Tandai semua dibaca">
                    <i class="bi bi-check2-all"></i>
                </button>
            </form>
            @endif
            
            @if($notifications->count() > 0)
            <form method="POST" action="{{ route('orangtua.notifications.clear-all') }}" style="margin:0;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus semua notifikasi?')">
                @csrf
                <button type="submit" class="ot-action-btn" style="color:#ef4444;" title="Bersihkan semua notifikasi">
                    <i class="bi bi-trash3"></i>
                </button>
            </form>
            @endif
        </div>
    </div>

    {{-- ── STATUS NOTIFIKASI ── --}}
    @if($hasSubscription)
    <div class="ot-status-row" style="margin-bottom:16px;">
        <div class="ot-status-chip chip-green">
            <i class="bi bi-shield-check-fill"></i>
            Notifikasi Aktif
        </div>
        <button id="btn-unsubscribe" class="ot-unsub-btn">
            <i class="bi bi-bell-slash"></i> Nonaktifkan
        </button>
    </div>
    @else
    {{-- PERMISSION CARD --}}
    <div id="notif-permission-card" class="ot-perm-card" style="display:none;">
        <div class="ot-perm-icon"><i class="bi bi-bell-fill"></i></div>
        <div class="ot-perm-body">
            <div class="ot-perm-title">Aktifkan Notifikasi E-Rapor</div>
            <div class="ot-perm-desc">Terima pemberitahuan otomatis saat rapor diterbitkan, bahkan saat browser ditutup.</div>
            <div style="display:flex;gap:8px;margin-top:12px;">
                <button id="btn-aktifkan-notif" class="ot-perm-btn-yes">
                    <i class="bi bi-bell-fill me-1"></i> Aktifkan
                </button>
                <button id="btn-skip-notif" class="ot-perm-btn-skip">Nanti</button>
            </div>
        </div>
        <button onclick="document.getElementById('notif-permission-card').style.display='none'" class="ot-perm-close">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>
    @endif

    {{-- ── LIST NOTIFIKASI ── --}}
    @if($notifications->isEmpty())
        <div class="ot-empty">
            <div style="font-size:3rem;opacity:.3;margin-bottom:12px;">📭</div>
            <div style="font-weight:700;color: #1e293b;margin-bottom:4px;">Belum ada notifikasi</div>
            <div style="font-size:.82rem;color:#94a3b8;">Aktifkan notifikasi agar tidak terlewatkan pemberitahuan dari sekolah.</div>
        </div>
    @else
        @foreach($notifications as $un)
        @php $notif = $un->notification; @endphp
        <div class="ot-notif-item {{ $un->is_read ? '' : 'ot-notif-unread' }} notification-item"
             data-id="{{ $un->id }}" style="cursor:pointer;">
            <div class="ot-notif-icon {{ $un->is_read ? 'icon-gray' : 'icon-blue' }}">
                <i class="bi bi-bell{{ $un->is_read ? '' : '-fill' }}"></i>
            </div>
            <div class="ot-notif-content">
                <div class="ot-notif-row1">
                    <span class="ot-notif-title">
                        {{ $notif->title ?? 'Notifikasi' }}
                        @if(!$un->is_read)<span class="ot-new-dot"></span>@endif
                    </span>
                    <span class="ot-notif-time">{{ $un->created_at->diffForHumans() }}</span>
                </div>
                <div class="ot-notif-body">{{ $notif->body ?? '-' }}</div>
                <div class="ot-notif-actions">
                    @if($notif->url)
                    <a href="{{ $notif->url }}" class="ot-notif-link">
                        <i class="bi bi-arrow-right-circle me-1"></i>Buka
                    </a>
                    @endif
                    @if(!$un->is_read)
                    <button class="ot-notif-read-btn mark-read-btn" data-id="{{ $un->id }}">
                        <i class="bi bi-check2 me-1"></i>Tandai dibaca
                    </button>
                    @else
                    <span class="ot-notif-read-label">
                        <i class="bi bi-check2-all me-1"></i>
                        Dibaca {{ $un->read_at ? $un->read_at->format('d M, H:i') : '' }}
                    </span>
                    @endif
                </div>
            </div>
        </div>
        @endforeach

        <div class="mt-4 d-flex justify-content-center">
            {{ $notifications->links('pagination::bootstrap-5') }}
        </div>
    @endif

    {{-- ── INFO CARA KERJA ── --}}
    <div class="ot-info-card">
        <div class="ot-info-title"><i class="bi bi-info-circle-fill me-1" style="color:#38bdf8;"></i> Cara Kerja Push Notification</div>
        <div class="ot-info-step"><span class="ot-step-num">①</span><div><b>Izinkan Notifikasi</b> – Klik "Aktifkan" dan pilih "Izinkan" pada dialog browser.</div></div>
        <div class="ot-info-step"><span class="ot-step-num">②</span><div><b>Token Tersimpan</b> – Perangkat Anda terdaftar di server Firebase sekolah.</div></div>
        <div class="ot-info-step"><span class="ot-step-num step-green">③</span><div><b>Notifikasi Otomatis</b> – Begitu Wali Kelas kirim rapor, HP/laptop Anda langsung berbunyi.</div></div>
    </div>

</div>

<style>
.ot-wrap { padding: 12px 12px 80px; max-width: 600px; margin: 0 auto; }

/* Header */
.ot-page-header { display: flex; align-items: center; gap: 12px; margin-bottom: 16px; }
.ot-page-icon { width: 46px; height: 46px; border-radius: 13px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; }
.ot-page-title { font-weight: 700; font-size: 1.05rem; color: #0f172a; display: flex; align-items: center; gap: 8px; }
.ot-page-sub   { font-size: .76rem; color: #64748b; margin-top: 2px; }
.ot-badge-inline { background: #ef4444; color: #fff; border-radius: 20px; font-size: .7rem; font-weight: 700; padding: 1px 7px; }
.ot-action-btn  { width: 38px; height: 38px; border-radius: 10px; background: var(--custom-header-bg, #f1f5f9); border: 0; color: #475569; font-size: 1rem; display: flex; align-items: center; justify-content: center; cursor: pointer; }

/* Status row */
.ot-status-row { display: flex; align-items: center; justify-content: space-between; gap: 12px; }
.ot-status-chip { display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; border-radius: 20px; font-size: .8rem; font-weight: 700; }
.chip-green { background: #dcfce7; color: #16a34a; }
.ot-unsub-btn { background: #fef2f2; border: 0; color: #ef4444; border-radius: 10px; padding: 6px 12px; font-size: .78rem; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 4px; }

/* Permission card */
.ot-perm-card {
    background: linear-gradient(135deg, #1e293b, #0f4c75);
    border-radius: 18px; padding: 18px; margin-bottom: 16px;
    display: flex; gap: 14px; align-items: flex-start; position: relative;
}
.ot-perm-icon { width: 48px; height: 48px; border-radius: 14px; background: rgba(251,191,36,.2); border: 1px solid rgba(251,191,36,.3); display: flex; align-items: center; justify-content: center; font-size: 1.4rem; color: #fbbf24; flex-shrink: 0; animation: ringbell 2s infinite; }
@keyframes ringbell { 0%,100%{transform:rotate(0)} 10%{transform:rotate(15deg)} 20%{transform:rotate(-10deg)} 30%{transform:rotate(8deg)} 40%{transform:rotate(-5deg)} }
.ot-perm-body { flex: 1; }
.ot-perm-title { font-weight: 700; font-size: .95rem; color: #fff; margin-bottom: 6px; }
.ot-perm-desc  { font-size: .78rem; color: rgba(255,255,255,.6); line-height: 1.5; }
.ot-perm-btn-yes { background: #fbbf24; color: #1e293b; border: 0; border-radius: 20px; padding: 8px 18px; font-size: .82rem; font-weight: 700; cursor: pointer; }
.ot-perm-btn-skip { background: rgba(255,255,255,.1); color: rgba(255,255,255,.7); border: 0; border-radius: 20px; padding: 8px 14px; font-size: .78rem; cursor: pointer; }
.ot-perm-close { position: absolute; top: 12px; right: 12px; background: none; border: 0; color: rgba(255,255,255,.4); font-size: .9rem; cursor: pointer; }

/* Notification items */
.ot-notif-item {
    display: flex; gap: 12px; align-items: flex-start;
    background: var(--custom-card-bg, #ffffff); border-radius: 16px; padding: 14px;
    margin-bottom: 10px; box-shadow: 0 2px 8px rgba(0,0,0,.05);
    border-left: 3px solid transparent;
    transition: transform .12s;
}
.ot-notif-item:active { transform: scale(.98); }
.ot-notif-unread { border-left-color: #0ea5e9; background: #f0f9ff; }
.ot-notif-icon { width: 42px; height: 42px; border-radius: 12px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; font-size: 1rem; }
.icon-blue { background: linear-gradient(135deg,#0ea5e9,#0284c7); color: #fff; }
.icon-gray { background: var(--custom-header-bg, #f1f5f9); color: #94a3b8; }
.ot-notif-content { flex: 1; min-width: 0; }
.ot-notif-row1 { display: flex; align-items: center; gap: 8px; margin-bottom: 4px; }
.ot-notif-title { font-weight: 700; font-size: .87rem; color: #1e293b; flex: 1; min-width: 0; }
.ot-new-dot { display: inline-block; width: 8px; height: 8px; background: #0ea5e9; border-radius: 50%; }
.ot-notif-time { font-size: .7rem; color: #94a3b8; flex-shrink: 0; }
.ot-notif-body { font-size: .8rem; color: #64748b; line-height: 1.5; margin-bottom: 8px; }
.ot-notif-actions { display: flex; align-items: center; gap: 10px; }
.ot-notif-link { font-size: .74rem; font-weight: 700; color: #0ea5e9; text-decoration: none; background: #e0f2fe; padding: 3px 10px; border-radius: 20px; }
.ot-notif-read-btn { background: none; border: 0; color: #94a3b8; font-size: .74rem; padding: 0; cursor: pointer; }
.ot-notif-read-label { font-size: .72rem; color: #94a3b8; }

/* Empty */
.ot-empty { background: var(--custom-card-bg, #ffffff); border-radius: 18px; padding: 48px 24px; text-align: center; margin-bottom: 16px; box-shadow: 0 2px 8px rgba(0,0,0,.05); }

/* Info card */
.ot-info-card { background: var(--custom-header-bg, #f8fafc); border-radius: 16px; padding: 16px; margin-top: 20px; }
.ot-info-title { font-size: .82rem; font-weight: 700; color: #475569; margin-bottom: 14px; }
.ot-info-step { display: flex; gap: 12px; margin-bottom: 10px; font-size: .8rem; color: #64748b; line-height: 1.5; }
.ot-step-num { font-weight: 700; color: #38bdf8; flex-shrink: 0; }
.ot-step-num.step-green { color: #22c55e; }
</style>
@endsection

@section('scripts')
<script type="module">
import { initializeApp, getApps, getApp } from "https://www.gstatic.com/firebasejs/10.7.0/firebase-app.js";
import { getMessaging, getToken, onMessage } from "https://www.gstatic.com/firebasejs/10.7.0/firebase-messaging.js";

const firebaseConfig = {
  apiKey: "AIzaSyDsvRDC2OKIUhyQhE0eEYmCddF92_WNud8",
  authDomain: "siakad-6d044.firebaseapp.com",
  projectId: "siakad-6d044",
  storageBucket: "siakad-6d044.firebasestorage.app",
  messagingSenderId: "65444849835",
  appId: "1:65444849835:web:8f7f0f2d190572de2e2fee",
  measurementId: "G-2X7HDW3CF8"
};

const VAPID = 'BIQdKkVUDZ81IICLCxOdAsXY7DK-lEWIX-wqvE-KnhRAdpuqvdK7UqtdCgKMsNEeRAGMBkzpirJ5p0bm2sIsa64';

const app = !getApps().length ? initializeApp(firebaseConfig) : getApp();
const messaging = getMessaging(app);

// Show in-app toast when app is open/foreground
onMessage(messaging, (payload) => {
    const title = payload.notification?.title || 'Notifikasi Baru';
    const body  = payload.notification?.body  || '';
    showToast('info', `<b>${title}</b><br>${body}`);
    // Reload to refresh the list
    setTimeout(() => location.reload(), 2500);
});

const CSRF = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

async function postJson(url, data = {}) {
    const res = await fetch(url, {
        method : 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body   : JSON.stringify(data)
    });
    return res.json();
}

function getDeviceName() {
    const ua = navigator.userAgent;
    let os = 'Device', browser = 'Browser';
    if (/Android/i.test(ua))      os = 'Android';
    else if (/iPhone|iPad/i.test(ua)) os = 'iOS';
    else if (/Windows/i.test(ua)) os = 'Windows';
    else if (/Mac/i.test(ua))     os = 'Mac';
    if (/Chrome/i.test(ua))       browser = 'Chrome';
    else if (/Firefox/i.test(ua)) browser = 'Firefox';
    else if (/Safari/i.test(ua))  browser = 'Safari';
    else if (/Edge/i.test(ua))    browser = 'Edge';
    return `${browser} on ${os}`;
}

async function getFCMToken() {
    // Ensure firebase-messaging-sw.js is registered
    const reg = await navigator.serviceWorker.register('/firebase-messaging-sw.js', { scope: '/' });
    await navigator.serviceWorker.ready;
    const token = await getToken(messaging, { vapidKey: VAPID, serviceWorkerRegistration: reg });
    return token;
}

// ── AUTO-SYNC: Jika browser SUDAH beri izin tapi token belum tersimpan, simpan otomatis ──
// Ini adalah perbaikan utama untuk bug notifikasi tidak muncul
async function autoSyncToken() {
    if (!('serviceWorker' in navigator)) return;
    if (Notification.permission !== 'granted') return;
    const hasSubscription = {{ $hasSubscription ? 'true' : 'false' }};
    if (hasSubscription) return; // sudah terdaftar

    try {
        const token = await getFCMToken();
        if (!token) return;
        await postJson('{{ route("orangtua.notifications.subscribe") }}', {
            endpoint   : token,
            p256dh_key : null,
            auth_token : null,
            device_name: getDeviceName()
        });
        // Reload to reflect subscribed state
        location.reload();
    } catch (e) {
        console.warn('Auto-sync token gagal:', e.message);
    }
}

window.requestAndSubscribe = async function() {
    if (!('Notification' in window)) { alert('Browser tidak mendukung notifikasi.'); return; }
    if (!('serviceWorker' in navigator)) { alert('Service Worker tidak tersedia.'); return; }

    const permission = await Notification.requestPermission();
    if (permission !== 'granted') {
        showToast('danger', 'Izin notifikasi ditolak. Ubah di pengaturan browser Anda.');
        return;
    }

    try {
        const token = await getFCMToken();
        if (!token) { showToast('danger', 'Gagal mendapatkan token. Coba refresh halaman ini.'); return; }

        const result = await postJson('{{ route("orangtua.notifications.subscribe") }}', {
            endpoint   : token,
            p256dh_key : null,
            auth_token : null,
            device_name: getDeviceName()
        });

        if (result.success) {
            showToast('success', '✅ Notifikasi berhasil diaktifkan!');
            setTimeout(() => location.reload(), 1800);
        }
    } catch (e) {
        console.error('Error FCM Token:', e);
        showToast('danger', 'Gagal mendaftarkan perangkat: ' + e.message);
    }
};

window.unsubscribe = async function() {
    if (!confirm('Nonaktifkan notifikasi di perangkat ini?')) return;
    try {
        const token  = await getFCMToken();
        const result = await postJson('{{ route("orangtua.notifications.unsubscribe") }}', { endpoint: token });
        if (result.success) {
            showToast('info', 'Notifikasi dinonaktifkan.');
            setTimeout(() => location.reload(), 1500);
        }
    } catch (e) {
        showToast('danger', 'Gagal membatalkan langganan.');
    }
};

window.showToast = function(type, msg) {
    const colors = { success:'#16a34a', danger:'#ef4444', info:'#0ea5e9' };
    const toast  = document.createElement('div');
    toast.style.cssText = `position:fixed;bottom:24px;right:16px;left:16px;max-width:400px;margin:0 auto;z-index:9999;background:${colors[type]||'#475569'};color:#fff;border-radius:14px;padding:14px 18px;font-size:.85rem;font-weight:600;box-shadow:0 8px 24px rgba(0,0,0,.15);animation:slideUp .3s ease;`;
    toast.innerHTML = msg;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 4000);
};

// Bind events
document.addEventListener('DOMContentLoaded', () => {
    const permCard = document.getElementById('notif-permission-card');
    const btnAktif = document.getElementById('btn-aktifkan-notif');
    const btnSkip  = document.getElementById('btn-skip-notif');
    const btnUnsub = document.getElementById('btn-unsubscribe');

    @if(!$hasSubscription)
    if (Notification.permission !== 'granted') {
        if (permCard) permCard.style.display = 'flex';
    }
    // ── Auto-sync jika sudah punya izin tapi belum terdaftar ──
    autoSyncToken();
    @endif

    if (btnAktif)  btnAktif.addEventListener('click', window.requestAndSubscribe);
    if (btnSkip)   btnSkip.addEventListener('click', () => { if(permCard) permCard.style.display='none'; });
    if (btnUnsub)  btnUnsub.addEventListener('click', window.unsubscribe);

    // Mark read on click
    document.querySelectorAll('.mark-read-btn').forEach(btn => {
        btn.addEventListener('click', async (e) => {
            e.stopPropagation();
            await postJson(`/orangtua/notifications/read/${btn.dataset.id}`);
            location.reload();
        });
    });

    document.querySelectorAll('.notification-item').forEach(card => {
        card.addEventListener('click', async () => {
            await postJson(`/orangtua/notifications/read/${card.dataset.id}`);
            location.reload();
        });
    });
});
</script>

<style>
@keyframes slideUp {
    from { opacity:0; transform:translateY(20px); }
    to   { opacity:1; transform:translateY(0); }
}
</style>
@endsection
