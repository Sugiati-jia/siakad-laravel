@extends('layouts.app')

@section('title', "Nilai Hasil Belajar")

@section('content')
<div class="ot-wrap">

    {{-- Header --}}
    <div class="ot-page-header">
        <div class="ot-page-icon" style="background:#dbeafe;color:#2563eb;"><i class="bi bi-journal-check"></i></div>
        <div>
            <div class="ot-page-title">Nilai Capaian</div>
            <div class="ot-page-sub">{{ $siswa->nama }} · Smt {{ $activeTahunAjaran->id_semester }} / {{ $activeTahunAjaran->tahun_ajaran }}</div>
        </div>
    </div>

    {{-- List Nilai --}}
    @forelse($nilaiTps as $mapelId => $grades)
        @php
            $mapel = $grades->first()->mapel;
            $avgGrade = round($grades->avg('nilai_akhir_tp'), 1);
            $isCompetent = $avgGrade >= 75;
        @endphp
        <a href="{{ route('orangtua.nilai.detail', $mapelId) }}" class="ot-nilai-card">
            <div class="ot-nilai-left">
                <div class="ot-nilai-icon {{ $isCompetent ? 'icon-green' : 'icon-amber' }}">
                    <i class="bi bi-book"></i>
                </div>
                <div>
                    <div class="ot-nilai-mapel">{{ $mapel->nama }}</div>
                    <div class="ot-nilai-kode">{{ $mapel->kode_mapel }}</div>
                </div>
            </div>
            <div class="ot-nilai-right">
                <div class="ot-nilai-score {{ $isCompetent ? 'score-green' : 'score-amber' }}">{{ $avgGrade }}</div>
                <div class="ot-nilai-label {{ $isCompetent ? 'label-green' : 'label-amber' }}">
                    {{ $isCompetent ? 'Kompeten' : 'Cukup' }}
                </div>
            </div>
            <i class="bi bi-chevron-right ot-chevron"></i>
        </a>
    @empty
        <div class="ot-empty">
            <i class="bi bi-journal-x"></i>
            <p>Belum ada data nilai yang dipublikasikan untuk semester ini.</p>
        </div>
    @endforelse
</div>

<style>
.ot-wrap { padding: 12px 12px 80px; max-width: 600px; margin: 0 auto; }

/* Header */
.ot-page-header {
    display: flex; align-items: center; gap: 14px;
    margin-bottom: 16px;
}
.ot-page-icon {
    width: 48px; height: 48px; border-radius: 14px; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center; font-size: 1.3rem;
}
.ot-page-title { font-weight: 700; font-size: 1.1rem; color: #0f172a; }
.ot-page-sub   { font-size: .78rem; color: #64748b; margin-top: 2px; }

/* Nilai card */
.ot-nilai-card {
    display: flex; align-items: center; gap: 12px;
    background: var(--custom-card-bg, #ffffff); border-radius: 16px; padding: 14px 12px;
    margin-bottom: 10px; text-decoration: none;
    box-shadow: 0 2px 8px rgba(0,0,0,.06);
    transition: transform .15s;
}
.ot-nilai-card:active { transform: scale(.98); }
.ot-nilai-left  { display: flex; align-items: center; gap: 12px; flex: 1; min-width: 0; }
.ot-nilai-icon  { width: 42px; height: 42px; border-radius: 12px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; }
.icon-green { background: #dcfce7; color: #16a34a; }
.icon-amber { background: #fef9c3; color: #ca8a04; }
.ot-nilai-mapel { font-weight: 700; font-size: .88rem; color: #1e293b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 180px; }
.ot-nilai-kode  { font-size: .72rem; color: #94a3b8; margin-top: 2px; }
.ot-nilai-right { flex-shrink: 0; text-align: center; }
.ot-nilai-score { font-weight: 800; font-size: 1.1rem; line-height: 1; }
.score-green { color: #16a34a; }
.score-amber { color: #ca8a04; }
.ot-nilai-label { font-size: .68rem; font-weight: 600; margin-top: 3px; }
.label-green { color: #16a34a; }
.label-amber { color: #ca8a04; }
.ot-chevron { color: #cbd5e1; font-size: .85rem; flex-shrink: 0; }

/* Empty */
.ot-empty { text-align: center; padding: 48px 0; color: #94a3b8; }
.ot-empty i { font-size: 3rem; display: block; margin-bottom: 12px; }
.ot-empty p { font-size: .9rem; margin: 0; }
</style>
@endsection
