@extends('layouts.app')

@section('title', "Pilih Profil Anak")

@section('content')
<div class="ot-wrap">

    <div class="ot-section-title" style="margin-top:4px;">Pilih Profil Anak</div>
    <p style="font-size:.83rem;color:#64748b;margin-bottom:16px;">
        Pilih profil putra/putri Anda untuk mengakses E-Rapor dan Nilai.
    </p>

    @forelse($children as $child)
    <div class="ot-child-card">
        <div class="ot-child-avatar">{{ strtoupper(substr($child->nama,0,1)) }}</div>
        <div class="ot-child-info">
            <div class="ot-child-name">{{ $child->nama }}</div>
            <div class="ot-child-meta">NISN: {{ $child->nisn }}</div>
            <div class="ot-child-meta">
                Kelas {{ $child->kelas->nama_kelas }}
                @if($child->kelas->konsentrasiKeahlian)
                    · {{ $child->kelas->konsentrasiKeahlian->nama_konsentrasi }}
                @endif
            </div>
        </div>
        <form action="{{ route('orangtua.set-anak') }}" method="POST">
            @csrf
            <input type="hidden" name="id_siswa" value="{{ $child->id_siswa }}">
            <button type="submit" class="ot-pick-btn">
                <i class="bi bi-check-circle-fill"></i>
            </button>
        </form>
    </div>
    @empty
    <div class="ot-empty">
        <i class="bi bi-person-x"></i>
        <p>Belum ada data siswa aktif terdaftar.</p>
    </div>
    @endforelse
</div>

<style>
.ot-wrap { padding: 12px 12px 80px; max-width: 600px; margin: 0 auto; }
.ot-section-title {
    font-size: .8rem; font-weight: 700; color: #94a3b8;
    text-transform: uppercase; letter-spacing: .8px; margin-bottom: 6px;
}

.ot-child-card {
    display: flex; align-items: center; gap: 14px;
    background: var(--custom-card-bg, #ffffff); border-radius: 18px; padding: 16px;
    margin-bottom: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,.07);
}
.ot-child-avatar {
    width: 50px; height: 50px; border-radius: 14px; flex-shrink: 0;
    background: linear-gradient(135deg,#3b82f6,#1d4ed8);
    color: #fff; font-weight: 800; font-size: 1.3rem;
    display: flex; align-items: center; justify-content: center;
}
.ot-child-info { flex: 1; min-width: 0; }
.ot-child-name { font-weight: 700; font-size: .95rem; color: #0f172a; }
.ot-child-meta { font-size: .78rem; color: #64748b; margin-top: 2px; }

.ot-pick-btn {
    flex-shrink: 0; width: 42px; height: 42px;
    background: #2563eb; color: #fff; border: 0;
    border-radius: 12px; font-size: 1.1rem; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    transition: background .15s;
}
.ot-pick-btn:hover { background: #1d4ed8; }

.ot-empty {
    text-align: center; padding: 48px 0; color: #94a3b8;
}
.ot-empty i { font-size: 3rem; display: block; margin-bottom: 12px; }
.ot-empty p { font-size: .9rem; margin: 0; }
</style>
@endsection
