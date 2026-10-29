@extends('layouts.app')

@section('title', "Edit Wali Kelas")

@section('content')
<div class="container-fluid py-4">
    <!-- Header Card (Flat Modern Slate Gray - No Gradients) -->
    <div class="card mb-4 border-0 shadow-sm" style="border-radius: 12px; background-color: #1e293b;">
        <div class="card-body p-4 text-white">
            <div class="d-flex align-items-center">
                <a href="{{ route('admin.wali-kelas.index') }}" class="btn btn-outline-light rounded-circle me-3 p-2 d-inline-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                    <i class="bi bi-arrow-left fs-5"></i>
                </a>
                <div>
                    <h3 class="fw-bold mb-1">Edit Penugasan Wali Kelas</h3>
                    <p class="mb-0 text-white-50">Sesuaikan kelas yang akan dipimpin oleh Wali Kelas ini.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Card -->
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm" style="border-radius: 16px;">
                <div class="card-body p-4">
                    <form action="{{ route('admin.wali-kelas.update', $guru->id_guru) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark"><i class="bi bi-person me-1 text-primary"></i> Wali Kelas</label>
                            <input type="text" class="form-control rounded-3 p-3 bg-light text-muted fw-bold" value="{{ $guru->nama }} (NIP. {{ $guru->nip ?: '-' }})" readonly disabled>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark"><i class="bi bi-house-door me-1 text-warning"></i> Kelas yang Dipimpin</label>
                            <span class="d-block text-muted small mb-2">Pilih kelas yang akan dipimpin. Hanya kelas yang belum memiliki Wali Kelas, atau kelas yang sedang dipimpin saat ini yang akan ditampilkan.</span>
                            <select name="id_kelas" class="form-select rounded-3 p-3 @error('id_kelas') is-invalid @enderror">
                                <option value="">-- Tanpa Kelas (Hanya Mengubah Peran) --</option>
                                @foreach($kelas as $kls)
                                    <option value="{{ $kls->id_kelas }}" {{ (old('id_kelas') ?? ($guru->kelasWali?->id_kelas ?? '')) == $kls->id_kelas ? 'selected' : '' }}>
                                        Kelas {{ $kls->nama_kelas }} (Tingkat {{ $kls->tingkat }})
                                    </option>
                                @endforeach
                            </select>
                            @error('id_kelas')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr class="my-4 text-muted">

                        <div class="d-flex justify-content-between align-items-center flex-wrap">
                            <a href="{{ route('admin.wali-kelas.index') }}" class="btn btn-light rounded-pill px-4 py-2 fw-semibold text-muted">
                                Batal
                            </a>
                            <button type="submit" class="btn btn-primary text-white rounded-pill px-4 py-2 fw-bold shadow-sm">
                                <i class="bi bi-check-circle me-1"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
