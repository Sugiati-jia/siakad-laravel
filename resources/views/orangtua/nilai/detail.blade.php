@extends('layouts.app')

@section('title', "Rincian Nilai – {{ $mapel->nama }}")

@section('content')
<div class="ot-wrap">

    {{-- Header --}}
    <div class="ot-page-header">
        <a href="{{ route('orangtua.nilai.index') }}" class="ot-back-btn"><i class="bi bi-arrow-left"></i></a>
        <div class="ot-page-icon" style="background:#dbeafe;color:#2563eb;"><i class="bi bi-book-half"></i></div>
        <div style="min-width:0;">
            <div class="ot-page-title" style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $mapel->nama }}</div>
            <div class="ot-page-sub">{{ $siswa->nama }}</div>
        </div>
    </div>

    {{-- Cards per TP --}}
    @forelse($nilaiTps as $nilai)
    @php $ok = ($nilai->nilai_akhir_tp ?? 0) >= 75; @endphp
    <div class="ot-tp-card">
        <div class="ot-tp-head">
            <span class="ot-tp-kode">{{ $nilai->tp->kode_tp }}</span>
            <span class="ot-tp-score {{ $ok ? 'score-g' : 'score-a' }}">{{ $nilai->nilai_akhir_tp ?? '-' }}</span>
        </div>
        <p class="ot-tp-desc">{{ $nilai->tp->deskripsi }}</p>
        <div class="ot-tp-grid">
            <div class="ot-tp-cell"><div class="ot-tp-cell-label">TH 1</div><div class="ot-tp-cell-val">{{ $nilai->nilai_th1 ?? '-' }}</div></div>
            <div class="ot-tp-cell"><div class="ot-tp-cell-label">TH 2</div><div class="ot-tp-cell-val">{{ $nilai->nilai_th2 ?? '-' }}</div></div>
            <div class="ot-tp-cell ot-tp-cell-hl"><div class="ot-tp-cell-label">Rerata TH</div><div class="ot-tp-cell-val">{{ $nilai->nilai_th ?? '-' }}</div></div>
            <div class="ot-tp-cell"><div class="ot-tp-cell-label">UH 1</div><div class="ot-tp-cell-val">{{ $nilai->nilai_uh1 ?? '-' }}</div></div>
            <div class="ot-tp-cell"><div class="ot-tp-cell-label">UH 2</div><div class="ot-tp-cell-val">{{ $nilai->nilai_uh2 ?? '-' }}</div></div>
            <div class="ot-tp-cell ot-tp-cell-hl"><div class="ot-tp-cell-label">Rerata UH</div><div class="ot-tp-cell-val">{{ $nilai->nilai_uh ?? '-' }}</div></div>
            <div class="ot-tp-cell"><div class="ot-tp-cell-label">Sumatif AS</div><div class="ot-tp-cell-val">{{ $nilai->nilai_as ?? '-' }}</div></div>
        </div>
    </div>
    @empty
    <div class="ot-empty">
        <i class="bi bi-file-earmark-x"></i>
        <p>Rincian nilai belum tersedia untuk mata pelajaran ini.</p>
    </div>
    @endforelse
</div>

<style>
.ot-wrap { padding: 12px 12px 80px; max-width: 600px; margin: 0 auto; }

.ot-page-header { display: flex; align-items: center; gap: 12px; margin-bottom: 16px; }
.ot-back-btn {
    width: 38px; height: 38px; border-radius: 12px; background: var(--custom-header-bg, #f1f5f9);
    display: flex; align-items: center; justify-content: center;
    color: #475569; font-size: 1rem; text-decoration: none; flex-shrink: 0;
}
.ot-page-icon {
    width: 42px; height: 42px; border-radius: 12px; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center; font-size: 1.2rem;
}
.ot-page-title { font-weight: 700; font-size: .95rem; color: #0f172a; }
.ot-page-sub   { font-size: .75rem; color: #64748b; margin-top: 2px; }

/* TP Card */
.ot-tp-card {
    background: var(--custom-card-bg, #ffffff); border-radius: 16px; padding: 16px;
    margin-bottom: 12px; box-shadow: 0 2px 8px rgba(0,0,0,.06);
}
.ot-tp-head {
    display: flex; justify-content: space-between; align-items: center;
    margin-bottom: 6px;
}
.ot-tp-kode { font-weight: 700; font-size: .88rem; color: #1e293b; }
.ot-tp-score {
    font-weight: 800; font-size: 1.1rem; padding: 2px 12px;
    border-radius: 20px;
}
.score-g { background: #dcfce7; color: #16a34a; }
.score-a { background: #fef9c3; color: #92400e; }

.ot-tp-desc {
    font-size: .78rem; color: #64748b; margin-bottom: 12px; line-height: 1.5;
}
.ot-tp-grid {
    display: grid; grid-template-columns: repeat(3,1fr); gap: 8px;
}
.ot-tp-cell {
    background: var(--custom-header-bg, #f8fafc); border-radius: 10px; padding: 8px;
    text-align: center;
}
.ot-tp-cell-hl { background: #eff6ff; }
.ot-tp-cell-label { font-size: .68rem; color: #94a3b8; font-weight: 600; text-transform: uppercase; letter-spacing: .5px; }
.ot-tp-cell-val   { font-size: .95rem; font-weight: 700; color: #1e293b; margin-top: 4px; }

.ot-empty { text-align: center; padding: 48px 0; color: #94a3b8; }
.ot-empty i { font-size: 3rem; display: block; margin-bottom: 12px; }
.ot-empty p { font-size: .9rem; margin: 0; }
</style>
@endsection
