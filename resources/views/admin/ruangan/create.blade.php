@extends('layouts.app')
@section('title', 'Tambah Ruangan')
@section('content')
<div class="container-fluid py-4">
    <div class="card border-0 shadow-sm mb-4" style="border-radius:12px; background:#1e293b; color:white;">
        <div class="card-body p-4 d-flex align-items-center justify-content-between">
            <div>
                <h2 class="fw-bold mb-1"><i class="bi bi-door-open text-info me-2"></i>Tambah Ruangan</h2>
                <p class="mb-0 text-white-50">Tambahkan data ruangan atau laboratorium baru.</p>
            </div>
            <a href="{{ route('admin.ruangan.index') }}" class="btn btn-outline-light rounded-pill px-4 btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius:12px;">
        <div class="card-body p-4">
            <form action="{{ route('admin.ruangan.store') }}" method="POST">
                @csrf
                <div class="row g-3">
                    <div class="col-md-8">
                        <label for="nama_ruangan" class="form-label fw-bold text-dark">Nama Ruangan <span class="text-danger">*</span></label>
                        <input type="text" name="nama_ruangan" id="nama_ruangan"
                               class="form-control @error('nama_ruangan') is-invalid @enderror"
                               placeholder="Contoh: Lab Komputer 1, Ruang Kelas A, Aula"
                               value="{{ old('nama_ruangan') }}" required>
                        @error('nama_ruangan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label for="kode_ruangan" class="form-label fw-bold text-dark">Kode Ruangan <span class="text-muted fw-normal">(Opsional)</span></label>
                        <input type="text" name="kode_ruangan" id="kode_ruangan"
                               class="form-control @error('kode_ruangan') is-invalid @enderror"
                               placeholder="Contoh: LAB-1, R-01"
                               value="{{ old('kode_ruangan') }}">
                        @error('kode_ruangan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="{{ route('admin.ruangan.index') }}" class="btn btn-light rounded-pill px-4">Batal</a>
                    <button type="submit" class="btn btn-info text-white rounded-pill px-5 fw-bold">
                        <i class="bi bi-save me-1"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
