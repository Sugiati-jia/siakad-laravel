@extends('layouts.app')

@section('title', "Edit Tahun Ajaran")

@section('content')
<div class="container-fluid py-4">
    <!-- Header Card -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; background-color: #1e293b; color: white;">
        <div class="card-body p-4">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h2 class="fw-bold mb-1"><i class="bi bi-pencil-square text-info me-2"></i> Edit Tahun Ajaran</h2>
                    <p class="mb-0 text-white-50">Ubah detail periode tahun akademik dan semester aktif.</p>
                </div>
                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    <a href="{{ route('admin.tahun-ajaran.index') }}" class="btn btn-outline-light rounded-pill px-4 btn-sm">
                        <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Card -->
    <div class="card border-0 shadow-sm" style="border-radius: 12px;">
        <div class="card-body p-4">
            <form action="{{ route('admin.tahun-ajaran.update', $tahunAjaran->id_tahun_ajaran) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row mb-4">
                    <!-- Tahun Ajaran -->
                    <div class="col-md-6 mb-3">
                        <label for="tahun_ajaran" class="form-label fw-bold text-dark">Tahun Ajaran</label>
                        <input type="text" name="tahun_ajaran" id="tahun_ajaran" class="form-control @error('tahun_ajaran') is-invalid @enderror" placeholder="Contoh: 2023/2024" value="{{ old('tahun_ajaran', $tahunAjaran->tahun_ajaran) }}" required>
                        <div class="form-text">Maksimal 9 karakter. Gunakan format seperti 2023/2024.</div>
                        @error('tahun_ajaran')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Semester Aktif -->
                    <div class="col-md-6 mb-3">
                        <label for="id_semester" class="form-label fw-bold text-dark">Semester Aktif</label>
                        <select name="id_semester" id="id_semester" class="form-select @error('id_semester') is-invalid @enderror" required>
                            <option value="">-- Pilih Semester --</option>
                            <option value="1" {{ old('id_semester', $tahunAjaran->id_semester) == '1' ? 'selected' : '' }}>Semester 1 (Ganjil)</option>
                            <option value="2" {{ old('id_semester', $tahunAjaran->id_semester) == '2' ? 'selected' : '' }}>Semester 2 (Genap)</option>
                        </select>
                        @error('id_semester')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-4">
                    <!-- Tanggal Mulai -->
                    <div class="col-md-6 mb-3">
                        <label for="tanggal_mulai" class="form-label fw-bold text-dark">Tanggal Mulai</label>
                        <input type="date" name="tanggal_mulai" id="tanggal_mulai" class="form-control @error('tanggal_mulai') is-invalid @enderror" value="{{ old('tanggal_mulai', $tahunAjaran->tanggal_mulai?->format('Y-m-d')) }}" required>
                        @error('tanggal_mulai')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Tanggal Selesai -->
                    <div class="col-md-6 mb-3">
                        <label for="tanggal_selesai" class="form-label fw-bold text-dark">Tanggal Selesai</label>
                        <input type="date" name="tanggal_selesai" id="tanggal_selesai" class="form-control @error('tanggal_selesai') is-invalid @enderror" value="{{ old('tanggal_selesai', $tahunAjaran->tanggal_selesai?->format('Y-m-d')) }}" required>
                        @error('tanggal_selesai')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="{{ route('admin.tahun-ajaran.index') }}" class="btn btn-light rounded-pill px-4">Batal</a>
                    <button type="submit" class="btn btn-info text-white rounded-pill px-5 fw-bold">Perbarui Tahun Ajaran</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
