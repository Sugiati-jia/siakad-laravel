@extends('layouts.app')

@section('title', 'Input Tujuan Pembelajaran (TP)')

@section('content')
<div class="container-fluid py-3">
    <!-- Header Info -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex align-items-center mb-3">
                <a href="{{ route('guru.tp.select-mapel', $kelas->id_kelas) }}" class="btn btn-outline-secondary btn-sm me-3">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <h5 class="mb-0 text-primary">
                    <i class="bi bi-card-list me-2"></i>
                    TUJUAN PEMBELAJARAN (TP)
                </h5>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td width="150"><strong>Mata Pelajaran</strong></td>
                            <td width="20">:</td>
                            <td>{{ $mapel->nama_mapel }}</td>
                        </tr>
                        <tr>
                            <td><strong>Kelas</strong></td>
                            <td>:</td>
                            <td>{{ $kelas->nama_kelas }}</td>
                        </tr>
                        <tr>
                            <td><strong>Tingkat</strong></td>
                            <td>:</td>
                            <td>{{ $kelas->tingkat }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td width="150"><strong>Semester</strong></td>
                            <td width="20">:</td>
                            <td>{{ $guruMapel->id_semester == 1 ? '1 (Satu)' : '2 (Dua)' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Guru Mapel</strong></td>
                            <td>:</td>
                            <td>{{ Auth::user()->guru->nama }}</td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <div class="mt-4 d-flex flex-wrap justify-content-end gap-2">
                <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#importModal">
                    <i class="bi bi-upload me-1"></i>Import TP
                </button>
                <a href="{{ route('guru.tp.export', [$kelas->id_kelas, $mapel->id_mapel]) }}" class="btn btn-outline-success">
                    <i class="bi bi-download me-1"></i>Export TP
                </a>
                @if(count($tujuanPembelajarans) > 0)
                <form action="{{ route('guru.tp.destroy-all', [$kelas->id_kelas, $mapel->id_mapel]) }}" method="POST" class="m-0" data-confirm="Hapus SEMUA TP untuk mapel ini? Semua nilai yang terkait dengan TP ini akan hilang.">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger">
                        <i class="bi bi-trash-fill me-1"></i>Hapus Semua TP
                    </button>
                </form>
                @endif
                <a href="{{ route('guru.nilai.input', [$kelas->id_kelas, $mapel->id_mapel]) }}" class="btn btn-success shadow-sm">
                    <i class="bi bi-pencil-square me-1"></i>Lanjut Input Nilai
                </a>
            </div>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <!-- Form Add TP -->
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-plus-circle me-2"></i>Tambah TP Baru</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('guru.tp.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="guru_mapel_id" value="{{ $guruMapel->id_guru_mapel }}">
                        
                        <div class="mb-3">
                            <label class="form-label">Kode TP <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('kode_tp') is-invalid @enderror" name="kode_tp" placeholder="Contoh: 1, 2, atau 1.1" value="{{ old('kode_tp') }}" required>
                            @error('kode_tp')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Deskripsi Tujuan Pembelajaran <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('deskripsi') is-invalid @enderror" name="deskripsi" rows="5" placeholder="Masukkan deskripsi tujuan pembelajaran..." required>{{ old('deskripsi') }}</textarea>
                            @error('deskripsi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Usahakan deskripsi jelas dan singkat (maksimal 500 karakter).</div>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Simpan TP</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- List TP -->
        <div class="col-md-8 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-list-ul me-2"></i>Daftar Tujuan Pembelajaran</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="10%" class="text-center">No</th>
                                    <th width="15%" class="text-center">Kode TP</th>
                                    <th width="60%">Deskripsi</th>
                                    <th width="15%" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tujuanPembelajarans as $index => $tp)
                                    <tr>
                                        <td class="text-center align-middle">{{ $index + 1 }}</td>
                                        <td class="text-center align-middle">
                                            <span class="badge bg-secondary">TP {{ $tp->kode_tp }}</span>
                                        </td>
                                        <td class="align-middle">{{ $tp->deskripsi_tp ?? $tp->deskripsi }}</td>
                                        <td class="align-middle">
                                            <div class="action-group justify-content-center">
                                                <button type="button" class="btn-action btn-action-edit" title="Edit TP" data-bs-toggle="modal" data-bs-target="#editModal{{ $tp->id_tujuan_pembelajaran }}">
                                                    <i class="bi bi-pencil-fill"></i>
                                                </button>
                                                <form action="{{ route('guru.tp.destroy', $tp->id_tujuan_pembelajaran) }}" method="POST" class="m-0" data-confirm="Hapus TP ini? Data nilai yang terkait mungkin akan hilang.">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn-action btn-action-delete" title="Hapus TP">
                                                        <i class="bi bi-trash-fill"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">
                                            Belum ada Tujuan Pembelajaran yang ditambahkan untuk kelas ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('guru.tp.import', [$kelas->id_kelas, $mapel->id_mapel]) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="importModalLabel">Import Tujuan Pembelajaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info small">
                        <strong>Panduan:</strong><br>
                        1. Gunakan tombol "Export TP" terlebih dahulu untuk mendapatkan format file (atau salin format dari TP yang sudah ada).<br>
                        2. Kolom yang dibutuhkan: <code>Kode TP</code> dan <code>Deskripsi</code>.<br>
                        3. Batas maksimal TP adalah 15 per kelas/mapel/semester.
                    </div>
                    <div class="mb-3">
                        <label for="file" class="form-label">File Excel (.xlsx, .xls, .csv) <span class="text-danger">*</span></label>
                        <input class="form-control" type="file" id="file" name="file" accept=".xlsx,.xls,.csv" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-upload me-1"></i> Import Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

@foreach($tujuanPembelajarans as $tp)
<!-- Edit Modal -->
<div class="modal fade" id="editModal{{ $tp->id_tujuan_pembelajaran }}" tabindex="-1" aria-labelledby="editModalLabel{{ $tp->id_tujuan_pembelajaran }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('guru.tp.update', $tp->id_tujuan_pembelajaran) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel{{ $tp->id_tujuan_pembelajaran }}">Edit Tujuan Pembelajaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Kode TP <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="kode_tp" value="{{ $tp->kode_tp }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi Tujuan Pembelajaran <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="deskripsi" rows="5" required>{{ $tp->deskripsi_tp ?? $tp->deskripsi }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
@endsection
