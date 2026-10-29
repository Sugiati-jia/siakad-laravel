@extends('layouts.app')

@section('title', "Tambah Kelas")

@section('content')
<div class="container py-4">
    <div class="card shadow-sm border-0" style="border-radius: 12px;">
        <div class="card-header bg-white py-3 border-0">
            <h5 class="mb-0 fw-bold"><i class="bi bi-house-add text-warning me-2"></i>Tambah Data Kelas</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.kelas.store') }}" method="POST">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Nama Kelas <span class="text-danger">*</span></label>
                        <input type="text" name="nama_kelas" class="form-control @error('nama_kelas') is-invalid @enderror" value="{{ old('nama_kelas') }}" placeholder="Contoh: X RPL 1" required>
                        <small class="text-muted" style="font-size: 0.8rem;">Digunakan sebagai identitas unik ruangan belajar.</small>
                        @error('nama_kelas') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Tingkat <span class="text-danger">*</span></label>
                        <select name="tingkat" class="form-select @error('tingkat') is-invalid @enderror" required>
                            <option value="">-- Pilih Tingkat --</option>
                            <option value="10" {{ old('tingkat') == '10' ? 'selected' : '' }}>Kelas 10 (X)</option>
                            <option value="11" {{ old('tingkat') == '11' ? 'selected' : '' }}>Kelas 11 (XI)</option>
                            <option value="12" {{ old('tingkat') == '12' ? 'selected' : '' }}>Kelas 12 (XII)</option>
                        </select>
                        @error('tingkat') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Konsentrasi Keahlian / Jurusan <span class="text-danger">*</span></label>
                        <select name="jurusan_id" class="form-select @error('jurusan_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Jurusan --</option>
                            @foreach($jurusans as $j)
                                <option value="{{ $j->id_konsentrasi_keahlian }}" {{ old('jurusan_id') == $j->id_konsentrasi_keahlian ? 'selected' : '' }}>
                                    {{ $j->nama_konsentrasi }}
                                </option>
                            @endforeach
                        </select>
                        @error('jurusan_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Tahun Ajaran <span class="text-danger">*</span></label>
                        <select name="id_tahun_ajaran" class="form-select @error('id_tahun_ajaran') is-invalid @enderror" required>
                            <option value="">-- Pilih Tahun Ajaran --</option>
                            @foreach($tahunAjarans as $t)
                                <option value="{{ $t->id_tahun_ajaran }}" {{ old('id_tahun_ajaran') == $t->id_tahun_ajaran ? 'selected' : '' }}>
                                    {{ $t->tahun_ajaran }} - Semester {{ $t->id_semester }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_tahun_ajaran') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    
                    <div class="col-12 mt-4">
                        <h6 class="fw-bold text-muted border-bottom pb-2">Penugasan Wali Kelas</h6>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Pilih Wali Kelas (Opsional)</label>
                        <select name="wali_id_kelas" class="form-select @error('wali_id_kelas') is-invalid @enderror">
                            <option value="">-- Belum Ada Wali Kelas --</option>
                            @foreach($gurus as $g)
                                <option value="{{ $g->id_guru }}" {{ old('wali_id_kelas') == $g->id_guru ? 'selected' : '' }}>
                                    {{ $g->nama }} (NIP: {{ $g->nip }})
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted" style="font-size: 0.8rem;">Dapat diisi nanti dari menu Penugasan.</small>
                        @error('wali_id_kelas') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                
                <div class="mt-4 pt-3 border-top">
                    <button type="submit" class="btn btn-warning fw-bold px-4 text-dark"><i class="bi bi-save me-1"></i> Simpan Data</button>
                    <a href="{{ route('admin.kelas.index') }}" class="btn btn-light fw-bold text-secondary border ms-2">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
