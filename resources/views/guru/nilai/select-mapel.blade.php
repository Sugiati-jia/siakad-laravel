@extends('layouts.app')

@section('title', 'Pilih Mata Pelajaran - ' . ($kelas->nama_kelas ?? ''))

@section('content')
<style>
    .mapel-card {
        border-radius: 16px;
        border: none;
        box-shadow: 0 2px 12px rgba(0,0,0,0.07);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        background-color: var(--custom-card-bg, #ffffff);
        overflow: hidden;
    }
    .mapel-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.12);
    }
    .mapel-icon-wrap {
        width: 56px;
        height: 56px;
        border-radius: 50%;
        background: #eff6ff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 0.75rem;
    }
    .mapel-card-title {
        font-size: 1rem;
        font-weight: 700;
        color: #1e293b;
        line-height: 1.3;
        word-break: break-word;
    }
    .mapel-action-strip {
        display: flex;
        border-top: 1px solid #f1f5f9;
    }
    .mapel-action-btn {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 4px;
        padding: 10px 4px;
        text-decoration: none;
        font-size: 0.7rem;
        font-weight: 600;
        color: #64748b;
        transition: background 0.2s, color 0.2s;
    }
    .mapel-action-btn i {
        font-size: 1.1rem;
    }
    .mapel-action-btn:not(:last-child) {
        border-right: 1px solid #f1f5f9;
    }
    .mapel-action-btn.action-input:hover  { background: #eff6ff; color: #2563eb; }
    .mapel-action-btn.action-import:hover { background: #ecfeff; color: #0891b2; }
    .mapel-action-btn.action-summary:hover{ background: #f0fdf4; color: #16a34a; }

    html.dark-mode .mapel-card { background-color: #1e293b !important; box-shadow: 0 2px 12px rgba(0,0,0,0.3); }
    html.dark-mode .mapel-card-title { color: #f8fafc !important; }
    html.dark-mode .mapel-icon-wrap { background: rgba(59,130,246,0.15) !important; }
    html.dark-mode .mapel-action-strip { border-top-color: rgba(255,255,255,0.08) !important; }
    html.dark-mode .mapel-action-btn { color: #94a3b8 !important; }
    html.dark-mode .mapel-action-btn:not(:last-child) { border-right-color: rgba(255,255,255,0.08) !important; }
    html.dark-mode .mapel-action-btn.action-input:hover  { background: rgba(59,130,246,0.15) !important; color: #60a5fa !important; }
    html.dark-mode .mapel-action-btn.action-import:hover { background: rgba(6,182,212,0.15)  !important; color: #67e8f9 !important; }
    html.dark-mode .mapel-action-btn.action-summary:hover{ background: rgba(22,163,74,0.15)  !important; color: #4ade80 !important; }

    @media (max-width: 576px) {
        .mapel-card-title { font-size: 0.95rem; }
        .mapel-action-btn { font-size: 0.68rem; padding: 10px 2px; }
        .mapel-action-btn i { font-size: 1rem; }
    }
</style>

<div class="container-fluid py-3">
    {{-- Page header --}}
    <div class="d-flex align-items-center mb-4 gap-3">
        <a href="{{ route('guru.nilai.select-kelas') }}" class="btn btn-outline-secondary btn-sm flex-shrink-0">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h4 class="mb-0 fw-bold">Pilih Mata Pelajaran</h4>
            <small class="text-muted">Kelas: <strong>{{ $kelas->nama_kelas ?? '-' }}</strong></small>
        </div>
    </div>

    @if($guruMapels->count() > 0)
        <div class="row g-3">
            @foreach($guruMapels as $gm)
                @if($gm->mapel)
                    <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                        <div class="mapel-card h-100 d-flex flex-column">
                            {{-- Card body --}}
                            <div class="card-body p-3 text-center flex-grow-1 d-flex flex-column align-items-center justify-content-center">
                                <div class="mapel-icon-wrap">
                                    <i class="bi bi-book-half text-primary fs-4"></i>
                                </div>
                                <div class="mapel-card-title mb-1">{{ $gm->mapel->nama_mapel }}</div>
                                @if($gm->mapel->kode_mapel)
                                    <span class="badge bg-secondary-subtle text-secondary rounded-pill small px-2 mt-1">
                                        {{ $gm->mapel->kode_mapel }}
                                    </span>
                                @endif
                            </div>

                            {{-- Action strip --}}
                            <div class="mapel-action-strip">
                                <a href="{{ route('guru.nilai.input', [$kelas->id_kelas, $gm->mapel->id_mapel]) }}"
                                   class="mapel-action-btn action-input" title="Input Nilai & TP">
                                    <i class="bi bi-pencil-square"></i>
                                    <span>Input Nilai</span>
                                </a>
                                <a href="{{ route('guru.import.import', [$kelas->id_kelas, $gm->mapel->id_mapel]) }}"
                                   class="mapel-action-btn action-import" title="Import Excel">
                                    <i class="bi bi-file-earmark-excel"></i>
                                    <span>Import Excel</span>
                                </a>
                                <a href="{{ route('guru.nilai.summary', [$kelas->id_kelas, $gm->mapel->id_mapel]) }}"
                                   class="mapel-action-btn action-summary" title="Ringkasan Nilai">
                                    <i class="bi bi-clipboard-data"></i>
                                    <span>Ringkasan</span>
                                </a>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    @else
        <div class="card border-0 shadow-sm" style="border-radius:12px;">
            <div class="card-body text-center py-5">
                <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                <h5 class="text-muted mt-3">Belum Ada Mata Pelajaran</h5>
                <p class="text-muted">Anda belum memiliki penugasan mata pelajaran di kelas ini.</p>
            </div>
        </div>
    @endif
</div>
@endsection
