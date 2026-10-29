@extends('layouts.app')

@section('title', 'Ekstrakurikuler - ' . ($kelas->nama_kelas ?? ''))

@section('content')
<div class="container-fluid py-3">
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('walikelas.dashboard') }}" class="btn btn-outline-secondary btn-sm me-3">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h4 class="mb-0">Nilai Ekstrakurikuler</h4>
            <small class="text-muted">Kelas: <strong>{{ $kelas->nama_kelas ?? '-' }}</strong></small>
        </div>
    </div>

    {{-- Form Input Nilai Ekstrakurikuler --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white">
            <i class="bi bi-plus-circle me-2"></i>Tambah Nilai Ekstrakurikuler
        </div>
        <div class="card-body">
            <form action="{{ route('walikelas.ekstrakurikuler.store') }}" method="POST">
                @csrf
                <input type="hidden" name="id_semester" value="{{ $activeTahunAjaran->id_semester ?? 1 }}">
                <input type="hidden" name="id_tahun_ajaran" value="{{ $activeTahunAjaran->id_tahun_ajaran ?? '' }}">

                <div class="row g-3 align-items-start">
                    <div class="col-12 col-md-6 col-xl-3">
                        <label class="form-label fw-semibold">Siswa</label>
                        <select name="id_siswa" class="form-select" required>
                            <option value="">-- Pilih Siswa --</option>
                            @foreach($siswas as $siswa)
                                <option value="{{ $siswa->id_siswa }}">{{ $siswa->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-md-6 col-xl-3">
                        <label class="form-label fw-semibold">Ekstrakurikuler</label>
                        <select name="id_ekstrakurikuler" class="form-select" required>
                            <option value="">-- Pilih Ekskul --</option>
                            @foreach($ekstrakurikulers as $ekskul)
                                <option value="{{ $ekskul->id_ekstrakurikuler }}">{{ $ekskul->nama ?? $ekskul->nama_ekstrakurikuler ?? '-' }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-md-4 col-xl-2">
                        <label class="form-label fw-semibold">Nilai</label>
                        <input type="number" name="nilai" class="form-control" min="0" max="100" required placeholder="0-100">
                        <div class="form-text text-muted" style="font-size: 0.75rem;">Auto: Keterangan</div>
                    </div>
                    <div class="col-12 col-md-8 col-xl-3">
                        <label class="form-label fw-semibold">Deskripsi <small class="text-muted">(opsional)</small></label>
                        <input type="text" name="deskripsi" class="form-control" placeholder="Deskripsi singkat">
                    </div>
                    <div class="col-12 col-xl-1 d-flex">
                        <label class="form-label d-none d-xl-block">&nbsp;</label>
                        <button type="submit" class="btn btn-primary w-100 mt-auto mb-xl-4 h-auto" style="min-height: 38px;">
                            <i class="bi bi-save"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Daftar Nilai Ekstrakurikuler --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white">
            <i class="bi bi-trophy me-2"></i>Daftar Nilai Ekstrakurikuler
        </div>
        <div class="card-body p-0">
            @if($existingGrades->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 40px;">No</th>
                                <th>Siswa</th>
                                <th>Ekstrakurikuler</th>
                                <th class="text-center">Keterangan</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $no = 1; @endphp
                            @foreach($existingGrades as $grade)
                                <tr>
                                    <td>{{ $no++ }}</td>
                                    <td>{{ $siswas->firstWhere('id_siswa', $grade->id_siswa)->nama ?? '-' }}</td>
                                    <td>{{ $ekstrakurikulers->firstWhere('id_ekstrakurikuler', $grade->id_ekstrakurikuler)->nama ?? $ekstrakurikulers->firstWhere('id_ekstrakurikuler', $grade->id_ekstrakurikuler)->nama_ekstrakurikuler ?? '-' }}</td>
                                    <td class="text-center">
                                        @php
                                            $ketBadge = match($grade->keterangan) {
                                                'Sangat Baik' => 'bg-success',
                                                'Baik' => 'bg-info',
                                                'Cukup' => 'bg-warning text-dark',
                                                default => 'bg-danger'
                                            };
                                        @endphp
                                        <span class="badge {{ $ketBadge }} px-3 py-1 rounded-pill">
                                            {{ $grade->keterangan ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="action-group justify-content-center">
                                            <button class="btn-action btn-action-edit"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#editModal{{ $grade->id }}"
                                                    title="Edit Nilai">
                                                <i class="bi bi-pencil-fill"></i>
                                            </button>
                                            <form action="{{ route('walikelas.ekstrakurikuler.destroy', $grade->id) }}" method="POST" data-confirm="Hapus nilai ekstrakurikuler ini?" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn-action btn-action-delete" title="Hapus Nilai">
                                                    <i class="bi bi-trash-fill"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-trophy" style="font-size: 3rem; color: #ccc;"></i>
                    <h5 class="text-muted mt-3">Belum Ada Data</h5>
                    <p class="text-muted">Belum ada nilai ekstrakurikuler yang diinput.</p>
                </div>
            @endif
        </div>
    </div>
</div>

@foreach($existingGrades as $grade)
    {{-- Edit Modal --}}
    <div class="modal fade" id="editModal{{ $grade->id }}" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('walikelas.ekstrakurikuler.update', $grade->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Nilai Ekstrakurikuler</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nilai (0-100)</label>
                            <input type="number" name="nilai" class="form-control" value="{{ $grade->nilai }}" min="0" max="100" required>
                            <div class="form-text text-muted">Keterangan akan otomatis dihasilkan: ≥85 Sangat Baik, ≥70 Baik, ≥60 Cukup, &lt;60 Kurang</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Deskripsi <small class="text-muted">(opsional)</small></label>
                            <textarea name="deskripsi" class="form-control" rows="3">{{ $grade->deskripsi }}</textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach
@endsection
