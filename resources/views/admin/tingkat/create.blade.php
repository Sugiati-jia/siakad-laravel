@extends('layouts.app')
@section('title', 'Tambah Tingkat Kelas')
@section('content')
<div class="container-fluid py-4">
    <div class="card border-0 shadow-sm mb-4" style="border-radius:12px; background:#1e293b; color:white;">
        <div class="card-body p-4 d-flex align-items-center justify-content-between">
            <div>
                <h2 class="fw-bold mb-1"><i class="bi bi-layers text-warning me-2"></i>Tambah Tingkat Kelas</h2>
                <p class="mb-0 text-white-50">Tambahkan tingkat kelas baru (misal: X, XI, atau XII).</p>
            </div>
            <a href="{{ route('admin.tingkat.index') }}" class="btn btn-outline-light rounded-pill px-4 btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius:12px;">
        <div class="card-body p-4">
            <form action="{{ route('admin.tingkat.store') }}" method="POST">
                @csrf
                <div class="col-md-4">
                    <label for="nama_tingkat" class="form-label fw-bold text-dark">Nama Tingkat <span class="text-danger">*</span></label>
                    <select name="nama_tingkat" id="nama_tingkat" class="form-select @error('nama_tingkat') is-invalid @enderror" required>
                        <option value="">-- Pilih Tingkat --</option>
                        <option value="X" {{ old('nama_tingkat') === 'X' ? 'selected' : '' }}>X (Sepuluh)</option>
                        <option value="XI" {{ old('nama_tingkat') === 'XI' ? 'selected' : '' }}>XI (Sebelas)</option>
                        <option value="XII" {{ old('nama_tingkat') === 'XII' ? 'selected' : '' }}>XII (Dua Belas)</option>
                    </select>
                    <div class="form-text text-muted">Tingkat sesuai jenjang SMK: X, XI, atau XII.</div>
                    @error('nama_tingkat')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="{{ route('admin.tingkat.index') }}" class="btn btn-light rounded-pill px-4">Batal</a>
                    <button type="submit" class="btn btn-warning rounded-pill px-5 fw-bold">
                        <i class="bi bi-save me-1"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
