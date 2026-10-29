@extends('layouts.app')
@section('title', 'Edit Ruangan')
@section('content')
<div class="container-fluid py-4">
    <div class="card border-0 shadow-sm mb-4" style="border-radius:12px; background:#1e293b; color:white;">
        <div class="card-body p-4 d-flex align-items-center justify-content-between">
            <div>
                <h2 class="fw-bold mb-1"><i class="bi bi-pencil-square text-warning me-2"></i>Edit Ruangan</h2>
                <p class="mb-0 text-white-50">Perbarui data ruangan: <strong class="text-white">{{ $ruangan->nama_ruangan }}</strong></p>
            </div>
            <a href="{{ route('admin.ruangan.index') }}" class="btn btn-outline-light rounded-pill px-4 btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius:12px;">
        <div class="card-body p-4">
            <form action="{{ route('admin.ruangan.update', $ruangan->id_ruangan) }}" method="POST">
                @csrf @method('PUT')
                <div class="row g-3">
                    <div class="col-md-8">
                        <label for="nama_ruangan" class="form-label fw-bold text-dark">Nama Ruangan <span class="text-danger">*</span></label>
                        <input type="text" name="nama_ruangan" id="nama_ruangan"
                               class="form-control @error('nama_ruangan') is-invalid @enderror"
                               value="{{ old('nama_ruangan', $ruangan->nama_ruangan) }}" required>
                        @error('nama_ruangan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label for="kode_ruangan" class="form-label fw-bold text-dark">Kode Ruangan <span class="text-muted fw-normal">(Opsional)</span></label>
                        <input type="text" name="kode_ruangan" id="kode_ruangan"
                               class="form-control @error('kode_ruangan') is-invalid @enderror"
                               value="{{ old('kode_ruangan', $ruangan->kode_ruangan) }}">
                        @error('kode_ruangan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="{{ route('admin.ruangan.index') }}" class="btn btn-light rounded-pill px-4">Batal</a>
                    <button type="submit" class="btn btn-warning rounded-pill px-5 fw-bold">
                        <i class="bi bi-save me-1"></i> Perbarui
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
