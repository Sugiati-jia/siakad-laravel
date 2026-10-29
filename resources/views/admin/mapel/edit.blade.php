@extends('layouts.app')

@section('title', 'Edit Mata Pelajaran')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Card (Flat Modern Slate Gray - No Gradients) -->
    <div class="card mb-4 border-0 shadow-sm" style="border-radius: 12px; background-color: #1e293b;">
        <div class="card-body p-4 text-white">
            <div class="d-flex align-items-center">
                <a href="{{ route('admin.mapel.index') }}" class="btn btn-outline-light rounded-pill px-3 py-2 me-3">
                    <i class="bi bi-arrow-left me-1"></i> Kembali
                </a>
                <div>
                    <h3 class="fw-bold mb-1"><i class="bi bi-pencil-square me-2 text-primary"></i> Edit Mata Pelajaran</h3>
                    <p class="mb-0 text-white-50">Ubah rincian kurikulum untuk mata pelajaran: <strong>{{ $mapel->nama_mapel }}</strong>.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Form Card -->
    <div class="card border-0 shadow-sm" style="border-radius: 12px; max-width: 800px;">
        <div class="card-header bg-white py-3 border-0">
            <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-pencil text-primary me-1"></i> Formulir Perubahan</h5>
        </div>
        <div class="card-body p-4">
            <form action="{{ route('admin.mapel.update', $mapel->id_mapel) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark">Kode Mapel <span class="text-danger">*</span></label>
                            <input type="text" name="kode_mapel" class="form-control rounded-3 @error('kode_mapel') is-invalid @enderror" placeholder="Contoh: BIO-X" value="{{ old('kode_mapel', $mapel->kode_mapel) }}" required>
                            @error('kode_mapel')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text fs-8 text-muted">Kode mapel harus unik di seluruh sistem.</div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark">Nama Mata Pelajaran <span class="text-danger">*</span></label>
                            <input type="text" name="nama_mapel" class="form-control rounded-3 @error('nama_mapel') is-invalid @enderror" placeholder="Contoh: Biologi Otomotif" value="{{ old('nama_mapel', $mapel->nama_mapel) }}" required>
                            @error('nama_mapel')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark">Kategori / Jenis Mapel <span class="text-danger">*</span></label>
                            <select name="jenis_mapel" class="form-select rounded-3 @error('jenis_mapel') is-invalid @enderror" required>
                                <option value="Wajib" {{ old('jenis_mapel', $mapel->jenis_mapel) == 'Wajib' ? 'selected' : '' }}>Nasional (Wajib)</option>
                                <option value="Peminatan" {{ old('jenis_mapel', $mapel->jenis_mapel) == 'Peminatan' ? 'selected' : '' }}>Kejuruan (Peminatan)</option>
                                <option value="Bakat Minat" {{ old('jenis_mapel', $mapel->jenis_mapel) == 'Bakat Minat' ? 'selected' : '' }}>Muatan Lokal (Bakat Minat)</option>
                            </select>
                            @error('jenis_mapel')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark">Kelompok Mapel <span class="text-danger">*</span></label>
                            <select name="kelompok_mapel" class="form-select rounded-3 @error('kelompok_mapel') is-invalid @enderror" required>
                                <option value="A" {{ old('kelompok_mapel', $mapel->kelompok_mapel) == 'A' ? 'selected' : '' }}>Kelompok A (Umum)</option>
                                <option value="B" {{ old('kelompok_mapel', $mapel->kelompok_mapel) == 'B' ? 'selected' : '' }}>Kelompok B (Umum)</option>
                                <option value="C" {{ old('kelompok_mapel', $mapel->kelompok_mapel) == 'C' ? 'selected' : '' }}>Kelompok C (Kejuruan)</option>
                                <option value="Mata Pelajaran Umum" {{ old('kelompok_mapel', $mapel->kelompok_mapel) == 'Mata Pelajaran Umum' ? 'selected' : '' }}>Mata Pelajaran Umum</option>
                            </select>
                            @error('kelompok_mapel')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mb-4" style="max-width: 380px;">
                    <label class="form-label fw-semibold text-dark">Urutan di Rapor <span class="text-danger">*</span></label>
                    <input type="number" name="urutan" class="form-control rounded-3 @error('urutan') is-invalid @enderror" placeholder="Contoh: 1" min="1" value="{{ old('urutan', $mapel->urutan) }}" required>
                    @error('urutan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text fs-8 text-muted">Tentukan nomor urut mapel saat dicetak di rapor.</div>
                </div>

                <div class="mb-4" style="max-width: 380px;">
                    <label class="form-label fw-semibold text-dark">Jam Pelajaran Per Minggu <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="number" name="jam_mengajar_per_minggu" class="form-control rounded-3 @error('jam_mengajar_per_minggu') is-invalid @enderror" placeholder="Contoh: 2" min="1" max="40" value="{{ old('jam_mengajar_per_minggu', $mapel->jam_mengajar_per_minggu ?: 2) }}" required>
                        <span class="input-group-text bg-light text-dark">Jam</span>
                        @error('jam_mengajar_per_minggu')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-text fs-8 text-muted">Beban mengajar mata pelajaran dalam seminggu (1 s.d. 40 jam).</div>
                </div>

                <hr class="text-muted mb-4">

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.mapel.index') }}" class="btn btn-light rounded-pill px-4">Batal</a>
                    <button type="submit" class="btn btn-primary text-white rounded-pill px-4 fw-bold">
                        <i class="bi bi-save me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
