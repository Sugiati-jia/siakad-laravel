@extends('layouts.app')

@section('title', 'Pengaturan Sistem')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Card -->
    <div class="card mb-4 border-0 shadow-sm" style="border-radius: 12px; background-color: #1e293b;">
        <div class="card-body p-4 text-white">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <h3 class="fw-bold mb-1"><i class="bi bi-gear-fill me-2 text-info"></i> Pengaturan Sistem</h3>
                    <p class="mb-0 text-white-50">Kelola pengaturan nama sekolah, alamat, dan profil kepala sekolah.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="card border-0 shadow-sm" style="border-radius: 12px;">
        <div class="card-body p-4">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show rounded-3" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form action="{{ route('admin.setting.update') }}" method="POST">
                @csrf
                @method('PUT')
                
                <h5 class="fw-bold text-dark border-bottom pb-2 mb-4"><i class="bi bi-building me-2 text-primary"></i> Profil Sekolah</h5>
                
                <div class="row mb-4">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Nama Sekolah</label>
                        <input type="text" name="nama_sekolah" class="form-control rounded-3" value="{{ $settings['nama_sekolah'] ?? '' }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Alamat Sekolah</label>
                        <textarea name="alamat_sekolah" class="form-control rounded-3" rows="2" required>{{ $settings['alamat_sekolah'] ?? '' }}</textarea>
                    </div>
                </div>

                <h5 class="fw-bold text-dark border-bottom pb-2 mb-4 mt-4"><i class="bi bi-person-badge me-2 text-primary"></i> Data Kepala Sekolah</h5>
                
                <div class="row mb-4">
                    <div class="col-md-12 mb-3">
                        <label class="form-label fw-semibold">Pilih Kepala Sekolah</label>
                        <select name="kepala_sekolah_id" class="form-select rounded-3" required>
                            <option value="">-- Pilih Guru --</option>
                            @foreach($gurus as $guru)
                                <option value="{{ $guru->id_guru }}" {{ ($settings['kepala_sekolah_id'] ?? '') == $guru->id_guru ? 'selected' : '' }}>
                                    {{ $guru->nama }} (NIP: {{ $guru->nip ?: '-' }})
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text">Nama dan NIP Kepala Sekolah akan otomatis terisi di rapor berdasarkan data guru yang Anda pilih di sini.</div>
                    </div>
                </div>
                
                <div class="d-flex justify-content-end pt-3">
                    <button type="submit" class="btn btn-primary rounded-pill px-5 shadow-sm"><i class="bi bi-save me-1"></i> Simpan Pengaturan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
