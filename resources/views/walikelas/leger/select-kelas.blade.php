@extends('layouts.app')

@section('title', "Pilih Kelas - Leger Nilai")

@section('content')
<div class="container-fluid py-3">
    <div class="d-flex align-items-center mb-4">
        @if(Auth::user()->role === 'wali_kelas')
            <a href="{{ route('walikelas.dashboard') }}" class="btn btn-outline-secondary btn-sm me-3">
                <i class="bi bi-arrow-left"></i>
            </a>
        @else
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm me-3">
                <i class="bi bi-arrow-left"></i>
            </a>
        @endif
        <div>
            <h4 class="mb-0">Pilih Kelas Leger Nilai</h4>
            <small class="text-muted">Tahun Pelajaran: <strong>{{ $activeTahunAjaran->tahun_ajaran }} - Semester {{ $activeTahunAjaran->id_semester }}</strong></small>
        </div>
    </div>

    @if($kelas->count() > 0)
        <div class="row g-3">
            @foreach($kelas as $kls)
                <div class="col-12 col-sm-6 col-md-4">
                    <div class="card h-100 border-0 shadow-sm" style="border-radius: 12px; background-color: var(--custom-card-bg, #ffffff);">
                        <div class="card-body p-4 text-center">
                            <div class="rounded-circle bg-primary bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width: 56px; height: 56px;">
                                <i class="bi bi-grid-3x3-gap text-primary fs-3"></i>
                            </div>
                            <h5 class="fw-bold text-dark mb-1">{{ $kls->nama_kelas }}</h5>
                            <div class="mb-4">
                                <span class="badge bg-secondary-subtle text-secondary px-3 py-1 rounded-pill small">
                                    Tingkat {{ $kls->tingkat ?: 'X' }}
                                </span>
                            </div>
                            
                            <div class="d-grid gap-2 mt-2">
                                <a href="{{ route(Auth::user()->role === 'wali_kelas' ? 'walikelas.leger.index' : 'admin.leger.index', $kls->id_kelas) }}" class="btn btn-outline-primary rounded-pill px-3 py-2 fw-bold text-start">
                                    <i class="bi bi-eye-fill me-2"></i> Lihat Leger Nilai
                                </a>
                                <a href="{{ route(Auth::user()->role === 'wali_kelas' ? 'walikelas.leger.excel' : 'admin.leger.excel', $kls->id_kelas) }}" class="btn btn-outline-success rounded-pill px-3 py-2 fw-bold text-start">
                                    <i class="bi bi-file-earmark-excel-fill me-2"></i> Unduh Excel
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
                <i class="bi bi-house-x" style="font-size: 3rem; color: #ccc;"></i>
                <h5 class="text-muted mt-3">Belum Ada Kelas</h5>
                <p class="text-muted">Anda belum terdaftar sebagai Wali Kelas di kelas manapun.</p>
            </div>
        </div>
    @endif
</div>
@endsection
