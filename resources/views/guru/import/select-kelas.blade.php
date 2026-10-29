@extends('layouts.app')

@section('title', 'Import Excel - Pilih Kelas')

@section('content')
<style>
    .kelas-card {
        border-radius: 16px;
        border: none;
        box-shadow: 0 2px 12px rgba(0,0,0,0.07);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        background-color: var(--custom-card-bg, #ffffff);
        overflow: hidden;
        text-decoration: none;
        display: flex;
        flex-direction: column;
    }
    .kelas-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.12);
    }
    .kelas-icon-wrap {
        width: 56px;
        height: 56px;
        border-radius: 50%;
        background: #ecfeff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 0.75rem;
    }
    .kelas-card-title {
        font-size: 1rem;
        font-weight: 700;
        color: #1e293b;
        line-height: 1.3;
        word-break: break-word;
    }
    .kelas-card-footer-strip {
        background: #0891b2;
        color: white;
        text-align: center;
        padding: 10px;
        font-size: 0.85rem;
        font-weight: 600;
    }
    html.dark-mode .kelas-card { background-color: #1e293b !important; box-shadow: 0 2px 12px rgba(0,0,0,0.3); }
    html.dark-mode .kelas-card-title { color: #f8fafc !important; }
    html.dark-mode .kelas-icon-wrap { background: #164e63 !important; }
    @media (max-width: 576px) {
        .kelas-card-title { font-size: 0.95rem; }
        .kelas-card-footer-strip { font-size: 0.8rem; padding: 8px; }
    }
</style>

<div class="container-fluid py-3">
    <div class="d-flex align-items-center mb-4 gap-3">
        <a href="{{ route('guru.dashboard') }}" class="btn btn-outline-secondary btn-sm flex-shrink-0">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h4 class="mb-0 fw-bold">Import Excel - Pilih Kelas</h4>
            <small class="text-muted">Pilih kelas untuk import nilai dari Excel</small>
        </div>
    </div>

    @if($guruMapels->count() > 0)
        <div class="row g-3">
            @foreach($guruMapels as $gm)
                @if($gm->kelas)
                    <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                        <a href="{{ route('guru.import.select-mapel', $gm->kelas->id_kelas) }}" class="kelas-card h-100">
                            <div class="card-body p-3 text-center flex-grow-1 d-flex flex-column align-items-center justify-content-center py-4">
                                <div class="kelas-icon-wrap">
                                    <i class="bi bi-mortarboard-fill text-info fs-4"></i>
                                </div>
                                <div class="kelas-card-title mb-1">{{ $gm->kelas->nama_kelas }}</div>
                                <p class="text-muted mb-0 small">
                                    <i class="bi bi-people me-1"></i>
                                    {{ $gm->kelas->siswas->count() ?? 0 }} Siswa
                                </p>
                            </div>
                            <div class="kelas-card-footer-strip">
                                <i class="bi bi-arrow-right-circle me-1"></i> Import Excel
                            </div>
                        </a>
                    </div>
                @endif
            @endforeach
        </div>
    @else
        <div class="card border-0 shadow-sm" style="border-radius:12px;">
            <div class="card-body text-center py-5">
                <i class="bi bi-inbox fs-1 text-secondary opacity-50"></i>
                <h5 class="text-muted mt-3">Belum Ada Penugasan</h5>
                <p class="text-muted">Anda belum memiliki penugasan mengajar di kelas manapun.</p>
            </div>
        </div>
    @endif
</div>
@endsection
