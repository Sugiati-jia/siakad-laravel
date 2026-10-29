@extends('layouts.app')

@section('title', "E-Rapor Siswa - Portal Akademik")

@section('content')
<div class="container-fluid py-3">
    <div class="d-flex align-items-center mb-4 flex-wrap gap-3">
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm me-3">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h4 class="mb-0">Portal E-Rapor Kelas</h4>
            <small class="text-muted">Tahun Pelajaran: <strong>{{ $activeTahunAjaran->tahun_ajaran }} - Semester {{ $activeTahunAjaran->id_semester }}</strong></small>
        </div>
    </div>

    @if($kelas->count() > 0)
        <div class="row g-3">
            @foreach($kelas as $kls)
                <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                    <div class="card h-100 border-0 shadow-sm" style="border-radius: 12px; background-color: var(--custom-card-bg, #ffffff);">
                        <div class="card-body p-3 text-center">
                            <div class="rounded-circle bg-info bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-2" style="width: 48px; height: 48px;">
                                <i class="bi bi-door-open-fill text-info fs-4"></i>
                            </div>
                            <h6 class="fw-bold text-dark mb-1 text-truncate">{{ $kls->nama_kelas }}</h6>
                            <div class="mb-3">
                                <span class="badge bg-secondary-subtle text-secondary px-3 py-1 rounded-pill small">
                                    Tingkat {{ $kls->tingkat ?: 'X' }}
                                </span>
                            </div>
                            
                            <div class="text-muted mb-3" style="font-size: 0.75rem;">
                                @if($kls->waliKelas)
                                    <div class="text-truncate" title="{{ $kls->waliKelas->nama }}">
                                        <i class="bi bi-person-badge me-1"></i> {{ $kls->waliKelas->nama }}
                                    </div>
                                @else
                                    <div class="text-danger text-truncate"><i class="bi bi-exclamation-circle me-1"></i> Wali Kelas Kosong</div>
                                @endif
                                <div class="mt-1"><i class="bi bi-people me-1"></i> {{ $kls->siswas->count() }} Siswa</div>
                            </div>
                            
                            <div class="d-grid gap-2 mt-auto">
                                <a href="{{ route('admin.rapor.kelas', $kls->id_kelas) }}" class="btn btn-outline-primary rounded-pill px-2 py-1 fw-bold" style="font-size: 0.75rem;">
                                    <i class="bi bi-eye-fill me-1"></i> Lihat Rapor
                                </a>
                                <a href="{{ route('admin.rapor.download-all', $kls->id_kelas) }}" class="btn btn-outline-secondary rounded-pill px-2 py-1 fw-bold" style="font-size: 0.75rem; color: #475569; border-color: var(--custom-border, #cbd5e1);">
                                    <i class="bi bi-file-earmark-zip-fill me-1"></i> Unduh (ZIP)
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="bi bi-house-x display-4 mb-3 d-block text-muted"></i>
                <h5 class="text-muted mt-3">Belum Ada Kelas</h5>
                <p class="text-muted">Belum ada data kelas terdaftar.</p>
            </div>
        </div>
    @endif
</div>
@endsection
