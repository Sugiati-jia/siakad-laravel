@extends('layouts.app')

@section('title', "Tambah Jadwal Pelajaran")

@section('content')
<div class="container-fluid py-4">
    <!-- Header Card -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; background-color: #1e293b; color: white;">
        <div class="card-body p-4">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h2 class="fw-bold mb-1"><i class="bi bi-plus-circle-fill text-info me-2"></i> Tambah Jadwal Pelajaran</h2>
                    <p class="mb-0 text-white-50">Input detail jadwal pelajaran baru ke dalam sistem.</p>
                </div>
                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    <a href="{{ route('admin.jadwal.index') }}" class="btn btn-outline-light rounded-pill px-4 btn-sm">
                        <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Card -->
    <div class="card border-0 shadow-sm" style="border-radius: 12px;">
        <div class="card-body p-4">
            <form action="{{ route('admin.jadwal.store') }}" method="POST">
                @csrf

                <div class="row mb-4">
                    <!-- Kelas -->
                    <div class="col-md-6 mb-3">
                        <label for="id_kelas" class="form-label fw-bold text-dark">Kelas</label>
                        <select name="id_kelas" id="id_kelas" class="form-select @error('id_kelas') is-invalid @enderror" required>
                            <option value="">-- Pilih Kelas --</option>
                            @foreach($kelases as $kelas)
                                <option value="{{ $kelas->id_kelas }}" {{ old('id_kelas') == $kelas->id_kelas ? 'selected' : '' }}>
                                    {{ $kelas->nama_kelas }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_kelas')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Mata Pelajaran -->
                    <div class="col-md-6 mb-3">
                        <label for="id_mapel" class="form-label fw-bold text-dark">Mata Pelajaran</label>
                        <select name="id_mapel" id="id_mapel" class="form-select @error('id_mapel') is-invalid @enderror" required>
                            <option value="">-- Pilih Mata Pelajaran --</option>
                            @foreach($mapels as $mapel)
                                <option value="{{ $mapel->id_mapel }}" {{ old('id_mapel') == $mapel->id_mapel ? 'selected' : '' }}>
                                    [{{ $mapel->kode_mapel ?: '-' }}] {{ $mapel->nama }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_mapel')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-4">
                    <!-- Guru Pengampu -->
                    <div class="col-md-6 mb-3">
                        <label for="id_guru" class="form-label fw-bold text-dark">Guru Pengampu</label>
                        <select name="id_guru" id="id_guru" class="form-select @error('id_guru') is-invalid @enderror" required>
                            <option value="">-- Pilih Guru --</option>
                            @foreach($gurus as $guru)
                                <option value="{{ $guru->id_guru }}" {{ old('id_guru') == $guru->id_guru ? 'selected' : '' }}>
                                    {{ $guru->nama }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_guru')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Tahun Ajaran -->
                    <div class="col-md-6 mb-3">
                        <label for="id_tahun_ajaran" class="form-label fw-bold text-dark">Tahun Ajaran</label>
                        <select name="id_tahun_ajaran" id="id_tahun_ajaran" class="form-select @error('id_tahun_ajaran') is-invalid @enderror" required>
                            <option value="">-- Pilih Tahun Ajaran --</option>
                            @foreach($tahuns as $t)
                                <option value="{{ $t->id_tahun_ajaran }}" 
                                    {{ (old('id_tahun_ajaran') == $t->id_tahun_ajaran || ($activeTahun && $activeTahun->id_tahun_ajaran == $t->id_tahun_ajaran)) ? 'selected' : '' }}>
                                    {{ $t->tahun_ajaran }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_tahun_ajaran')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-4">
                    <!-- Hari -->
                    <div class="col-md-4 mb-3">
                        <label for="hari" class="form-label fw-bold text-dark">Hari</label>
                        <select name="hari" id="hari" class="form-select @error('hari') is-invalid @enderror" required>
                            <option value="">-- Pilih Hari --</option>
                            <option value="Senin" {{ old('hari') == 'Senin' ? 'selected' : '' }}>Senin</option>
                            <option value="Selasa" {{ old('hari') == 'Selasa' ? 'selected' : '' }}>Selasa</option>
                            <option value="Rabu" {{ old('hari') == 'Rabu' ? 'selected' : '' }}>Rabu</option>
                            <option value="Kamis" {{ old('hari') == 'Kamis' ? 'selected' : '' }}>Kamis</option>
                            <option value="Sabtu" {{ old('hari') == 'Sabtu' ? 'selected' : '' }}>Sabtu</option>
                            <option value="Minggu" {{ old('hari') == 'Minggu' ? 'selected' : '' }}>Minggu</option>
                        </select>
                        <div class="form-text text-muted">Hari Jumat libur.</div>
                        @error('hari')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Jam Ke- -->
                    <div class="col-md-8 mb-3">
                        <label for="id_jam" class="form-label fw-bold text-dark">Jam Ke-</label>
                        <select name="id_jam" id="id_jam" class="form-select @error('id_jam') is-invalid @enderror" required>
                            <option value="">-- Pilih Jam --</option>
                            @foreach($jamPelajarans->where('jenis', 'belajar') as $jam)
                                <option value="{{ $jam->id_jam }}" {{ old('id_jam') == $jam->id_jam ? 'selected' : '' }}>
                                    Jam ke-{{ $jam->jam_ke }} &nbsp;({{ substr($jam->waktu_mulai,0,5) }} - {{ substr($jam->waktu_selesai,0,5) }})
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text text-muted">Pilih satu jam mengajar. Setiap baris jadwal mewakili 1 jam pelajaran.</div>
                        @error('id_jam')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-4">
                    <!-- Ruangan -->
                    <div class="col-md-6 mb-3">
                        <label for="id_ruangan" class="form-label fw-bold text-dark">Ruangan (Opsional)</label>
                        <select name="id_ruangan" id="id_ruangan" class="form-select @error('id_ruangan') is-invalid @enderror">
                            <option value="">-- Pilih Ruangan --</option>
                            @foreach($ruangans as $ruangan)
                                <option value="{{ $ruangan->id_ruangan }}" {{ old('id_ruangan') == $ruangan->id_ruangan ? 'selected' : '' }}>
                                    {{ $ruangan->nama_ruangan }} {{ $ruangan->kode_ruangan ? '('.$ruangan->kode_ruangan.')' : '' }}
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text text-muted">Kosongkan jika menggunakan ruangan kelas masing-masing.</div>
                        @error('id_ruangan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="{{ route('admin.jadwal.index') }}" class="btn btn-light rounded-pill px-4">Batal</a>
                    <button type="submit" class="btn btn-info text-white rounded-pill px-5 fw-bold">Simpan Jadwal</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
