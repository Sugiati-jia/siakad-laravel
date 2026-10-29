@extends('layouts.app')

@section('title', 'Import Excel - ' . ($mapel->nama_mapel ?? '') . ' - ' . ($kelas->nama_kelas ?? ''))

@section('content')
<div class="container-fluid py-3">
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('guru.import.select-mapel', $kelas->id_kelas) }}" class="btn btn-outline-secondary btn-sm me-3">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h4 class="mb-0">Import Excel</h4>
            <small class="text-muted">
                Kelas: <strong>{{ $kelas->nama_kelas ?? '-' }}</strong> | 
                Mapel: <strong>{{ $mapel->nama_mapel ?? '-' }}</strong>
            </small>
        </div>
    </div>

    @if(isset($isLocked) && $isLocked)
    <div class="alert alert-danger d-flex align-items-center mb-4 p-3 border-0 shadow-xs" style="border-left: 4px solid #dc3545 !important; border-radius: 8px; background-color: #f8d7da; color: #842029;">
        <i class="bi bi-lock-fill me-3 fs-4 text-danger"></i>
        <div>
            <strong>Nilai Terkunci:</strong> Nilai mata pelajaran ini sudah dikunci oleh Wali Kelas. Anda tidak dapat mengimpor nilai baru.
        </div>
    </div>
    @endif

    <div class="row g-4">
        {{-- Download Template --}}
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white">
                    <i class="bi bi-download me-2"></i>1. Download Template
                </div>
                <div class="card-body text-center py-5">
                    <div class="rounded-circle bg-success bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                        <i class="bi bi-file-earmark-excel text-success" style="font-size: 2rem;"></i>
                    </div>
                    <h5>Template Excel</h5>
                    <p class="text-muted">Download template Excel yang sudah berisi data siswa dan kolom nilai.</p>
                    <a href="{{ route('guru.import.template', [$kelas->id_kelas, $mapel->id_mapel]) }}" class="btn btn-success">
                        <i class="bi bi-download me-2"></i>Download Template
                    </a>
                </div>
            </div>
        </div>

        {{-- Upload File --}}
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white">
                    <i class="bi bi-upload me-2"></i>2. Upload File Excel
                </div>
                <div class="card-body py-4">
                    <form action="{{ route('guru.import.process') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="guru_mapel_id" value="{{ $guruMapel->id_guru_mapel }}">
                        <input type="hidden" name="id_kelas" value="{{ $kelas->id_kelas }}">
                        <input type="hidden" name="mapel_id" value="{{ $mapel->id_mapel }}">
                        <input type="hidden" name="id_semester" value="{{ $guruMapel->id_semester ?? 1 }}">
                        <input type="hidden" name="id_tahun_ajaran" value="{{ $guruMapel->id_tahun_ajaran }}">

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Pilih File Excel</label>
                            <input type="file" name="excel_file" class="form-control" accept=".xlsx,.xls" required @if(isset($isLocked) && $isLocked) disabled @endif>
                            <div class="form-text">Format: .xlsx atau .xls (maks 10MB)</div>
                        </div>

                        <div class="alert alert-info small">
                            <i class="bi bi-info-circle me-1"></i>
                            <strong>Petunjuk:</strong>
                            <ul class="mb-0 mt-1">
                                <li>Download template terlebih dahulu</li>
                                <li>Isi nilai pada kolom yang tersedia</li>
                                <li>Jangan mengubah urutan kolom atau data siswa</li>
                                <li>Upload file yang sudah terisi</li>
                            </ul>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 mb-3" @if(isset($isLocked) && $isLocked) disabled @endif>
                            <i class="bi bi-upload me-2"></i>Import Nilai
                        </button>
                    </form>

                    @if(!isset($isLocked) || !$isLocked)
                    <hr class="text-muted opacity-25 my-3">
                    <div class="alert alert-warning small py-2 mb-3 border-0" style="background-color: var(--custom-card-bg, #ffffff)3cd; color: #856404; border-radius: 6px;">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i>
                        <strong>Kesulitan menimpa nilai lama?</strong> Jika sel kosong di Excel tidak menghapus nilai lama, Anda dapat mengosongkan tabel nilai terlebih dahulu:
                    </div>
                    <form action="{{ route('guru.nilai.destroy-all', [$kelas->id_kelas ?? $kelas->id, $mapel->id_mapel ?? $mapel->id]) }}" method="POST" data-confirm="Hapus SELURUH NILAI untuk kelas ini? Data akan dikosongkan agar dapat import ulang.">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger w-100" style="border-radius: 6px;">
                            <i class="bi bi-trash-fill me-2"></i>Kosongkan Seluruh Nilai Kelas
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
