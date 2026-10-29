@extends('layouts.app')

@section('title', "Edit Jurusan")

@section('content')
<div class="container-fluid py-4">
    <!-- Header Card -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; background-color: #1e293b; color: white;">
        <div class="card-body p-4">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h2 class="fw-bold mb-1"><i class="bi bi-pencil-square text-info me-2"></i> Edit Jurusan / Konsentrasi Keahlian</h2>
                    <p class="mb-0 text-white-50">Perbarui informasi kompetensi keahlian / jurusan.</p>
                </div>
                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    <a href="{{ route('admin.konsentrasi-keahlian.index') }}" class="btn btn-outline-light rounded-pill px-4 btn-sm">
                        <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Card -->
    <div class="card border-0 shadow-sm" style="border-radius: 12px;">
        <div class="card-body p-4">
            <form action="{{ route('admin.konsentrasi-keahlian.update', $konsentrasiKeahlian->id_konsentrasi_keahlian) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row mb-4">
                    <!-- Program Keahlian -->
                    <div class="col-md-6 mb-3">
                        <label for="program_keahlian_id" class="form-label fw-bold text-dark">Program Keahlian</label>
                        <select name="program_keahlian_id" id="program_keahlian_id" class="form-select @error('program_keahlian_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Program Keahlian --</option>
                            @foreach($programKeahlians as $pk)
                                <option value="{{ $pk->id_program_keahlian }}" {{ old('program_keahlian_id', $konsentrasiKeahlian->id_program_keahlian) == $pk->id_program_keahlian ? 'selected' : '' }}>
                                    [{{ $pk->kode_program }}] {{ $pk->nama_program }} - ({{ $pk->bidangKeahlian?->nama_bidang }})
                                </option>
                            @endforeach
                        </select>
                        @error('program_keahlian_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Kode Konsentrasi -->
                    <div class="col-md-6 mb-3">
                        <label for="kode_konsentrasi" class="form-label fw-bold text-dark">Kode Jurusan / Konsentrasi</label>
                        <input type="text" name="kode_konsentrasi" id="kode_konsentrasi" class="form-control @error('kode_konsentrasi') is-invalid @enderror" placeholder="Contoh: RPL, TKJ" value="{{ old('kode_konsentrasi', $konsentrasiKeahlian->kode_konsentrasi) }}" required>
                        @error('kode_konsentrasi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-4">
                    <!-- Nama Konsentrasi -->
                    <div class="col-md-12 mb-3">
                        <label for="nama_konsentrasi" class="form-label fw-bold text-dark">Nama Konsentrasi Keahlian (Jurusan)</label>
                        <input type="text" name="nama_konsentrasi" id="nama_konsentrasi" class="form-control @error('nama_konsentrasi') is-invalid @enderror" placeholder="Contoh: Rekayasa Perangkat Lunak" value="{{ old('nama_konsentrasi', $konsentrasiKeahlian->nama_konsentrasi) }}" required>
                        @error('nama_konsentrasi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-4">
                    <!-- Deskripsi -->
                    <div class="col-md-12 mb-3">
                        <label for="deskripsi" class="form-label fw-bold text-dark">Deskripsi</label>
                        <textarea name="deskripsi" id="deskripsi" class="form-control @error('deskripsi') is-invalid @enderror" rows="4" placeholder="Keterangan singkat mengenai kompetensi jurusan...">{{ old('deskripsi', $konsentrasiKeahlian->deskripsi) }}</textarea>
                        @error('deskripsi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="{{ route('admin.konsentrasi-keahlian.index') }}" class="btn btn-light rounded-pill px-4">Batal</a>
                    <button type="submit" class="btn btn-info text-white rounded-pill px-5 fw-bold">Perbarui Jurusan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
