@extends('layouts.app')

@section('title', 'Pilih Mata Pelajaran - Import Excel')

@section('content')
<style>
    .mapel-card {
        border-radius: 16px;
        border: none;
        box-shadow: 0 2px 12px rgba(0,0,0,0.07);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        background-color: var(--custom-card-bg, #ffffff);
        overflow: hidden;
        text-decoration: none;
        display: block;
    }
    .mapel-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.12);
    }
    .mapel-icon-wrap {
        width: 56px;
        height: 56px;
        border-radius: 50%;
        background: #ecfeff;
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
    .mapel-card-footer-strip {
        background: #0891b2;
        color: white;
        text-align: center;
        padding: 10px;
        font-size: 0.82rem;
        font-weight: 600;
    }

    html.dark-mode .mapel-card { background-color: #1e293b !important; box-shadow: 0 2px 12px rgba(0,0,0,0.3); }
    html.dark-mode .mapel-card-title { color: #f8fafc !important; }
    html.dark-mode .mapel-icon-wrap { background: rgba(6,182,212,0.15) !important; }

    @media (max-width: 576px) {
        .mapel-card-title { font-size: 0.9rem; }
    }
</style>

<div class="container-fluid py-3">
    <div class="d-flex align-items-center mb-4 gap-3">
        <a href="{{ route('guru.import.select-kelas') }}" class="btn btn-outline-secondary btn-sm flex-shrink-0">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h4 class="mb-0 fw-bold">Import Excel - Pilih Mata Pelajaran</h4>
            <small class="text-muted">Kelas: <strong>{{ $kelas->nama_kelas ?? '-' }}</strong></small>
        </div>
    </div>

    @if($guruMapels->count() > 0)
        <div class="row g-3">
            @foreach($guruMapels as $gm)
                @if($gm->mapel)
                    <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                        <a href="{{ route('guru.import.import', [$kelas->id_kelas, $gm->mapel->id_mapel]) }}"
                           class="mapel-card h-100 d-flex flex-column">
                            <div class="card-body p-3 text-center flex-grow-1 d-flex flex-column align-items-center justify-content-center">
                                <div class="mapel-icon-wrap">
                                    <i class="bi bi-file-earmark-excel text-info fs-4"></i>
                                </div>
                                <div class="mapel-card-title mb-1">{{ $gm->mapel->nama_mapel }}</div>
                                @if($gm->mapel->kode_mapel ?? false)
                                    <span class="badge bg-secondary-subtle text-secondary rounded-pill small px-2 mt-1">
                                        {{ $gm->mapel->kode_mapel }}
                                    </span>
                                @endif
                            </div>
                            <div class="mapel-card-footer-strip">
                                <i class="bi bi-file-earmark-excel me-1"></i> Import Excel
                            </div>
                        </a>
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
