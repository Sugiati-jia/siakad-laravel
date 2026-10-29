@extends('layouts.app')

@section('title', "E-Rapor Siswa")

@section('content')
<div class="container py-4">
    {{-- Status badge --}}
    <div class="ot-status-row">
        @if(isset($isPublished) && $isPublished)
            <span class="ot-status-chip chip-green"><i class="bi bi-patch-check-fill me-1"></i>Rapor Diterbitkan</span>
        @else
            <span class="ot-status-chip chip-gray" style="background: var(--custom-header-bg, #f1f5f9); color: #64748b;"><i class="bi bi-hourglass-split me-1"></i>Belum Diterbitkan</span>
        @endif
    </div>

    {{-- Rapor info --}}
    <div class="ot-card" style="margin-bottom:14px;">
        <div class="ot-row-item">
            <span class="ot-row-label">Wali Kelas</span>
            <span class="ot-row-val">{{ $siswa->kelas->waliKelas ? $siswa->kelas->waliKelas->nama : '-' }}</span>
        </div>
        <div class="ot-row-item">
            <span class="ot-row-label">Tahun Ajaran</span>
            <span class="ot-row-val">{{ $activeTahunAjaran->tahun_ajaran }}</span>
        </div>
        <div class="ot-row-item" style="border:0;">
            <span class="ot-row-label">Semester</span>
            <span class="ot-row-val">{{ $activeTahunAjaran->id_semester == 1 ? '1 (Ganjil)' : '2 (Genap)' }}</span>
        </div>
    </div>

    {{-- CTA --}}
    @if(isset($isPublished) && $isPublished)
        <a href="{{ route('siswa.rapor.view') }}" class="ot-cta-btn">
            <i class="bi bi-eye-fill me-2"></i> Lihat E-Rapor
        </a>
        <p class="ot-cta-hint">Ketuk untuk melihat berkas rapor lengkap.</p>
    @else
        <div class="p-3 text-center text-muted mt-3" style="font-size: 0.85rem;">
            Wali kelas belum menerbitkan E-Rapor untuk semester ini.
        </div>
    @endif
</div>

<style>
.ot-status-row { margin-bottom: 14px; }
.ot-status-chip {
    display: inline-flex; align-items: center;
    padding: 6px 14px; border-radius: 20px; font-size: .78rem; font-weight: 700;
}
.chip-green { background: #dcfce7; color: #16a34a; }
.chip-gray { background: var(--custom-header-bg, #f1f5f9); color: #64748b; }

.ot-card { background: var(--custom-card-bg, #ffffff); border-radius: 16px; box-shadow: 0 2px 10px rgba(0,0,0,.06); overflow: hidden; }
.ot-row-item {
    display: flex; justify-content: space-between; align-items: center;
    gap: 12px; padding: 13px 16px; border-bottom: 1px solid #f1f5f9;
}
.ot-row-label { font-size: .8rem; color: #94a3b8; }
.ot-row-val   { font-size: .85rem; font-weight: 600; color: #1e293b; text-align: right; }

.ot-cta-btn {
    display: flex; align-items: center; justify-content: center;
    background: linear-gradient(135deg, #16a34a, #15803d);
    color: #fff; border-radius: 16px; padding: 16px;
    font-size: 1rem; font-weight: 700; text-decoration: none;
    box-shadow: 0 4px 14px rgba(22,163,74,.3);
    transition: opacity .15s;
}
.ot-cta-btn:hover { opacity: .92; }
.ot-cta-hint { text-align: center; font-size: .75rem; color: #94a3b8; margin-top: 10px; }
</style>
@endsection
