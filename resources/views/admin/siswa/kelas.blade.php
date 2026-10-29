@extends('layouts.app')

@section('title', "Data Siswa Kelas " . ($kelas->nama_kelas ?: $kelas->nama))

@section('content')
<div class="container-fluid py-4">
    <!-- Header Card -->
    <div class="card mb-4 border-0 shadow-sm" style="border-radius: 12px; background-color: #1e293b;">
        <div class="card-body p-4 text-white">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <a href="{{ route('admin.siswa.index') }}" class="btn btn-sm btn-outline-light mb-3 rounded-pill px-3">
                        <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Kelas
                    </a>
                    <h3 class="fw-bold mb-1"><i class="bi bi-person-lines-fill me-2 text-info"></i> Siswa Kelas {{ $kelas->nama_kelas ?: $kelas->nama }}</h3>
                    <p class="mb-0 text-white-50">Kelola data siswa yang terdaftar di kelas ini.</p>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-success rounded-pill px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#importSiswaModal">
                        <i class="bi bi-file-earmark-excel me-1"></i> Import
                    </button>
                    <button class="btn btn-info fw-bold text-white px-4 py-2 rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#createSiswaModal">
                        <i class="bi bi-plus-lg me-1"></i> Tambah Siswa
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Table -->
    <div class="card border-0 shadow-sm" style="border-radius: 12px;">
        <div class="card-header bg-white py-3 border-0">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-list-task me-1 text-info"></i> Daftar Siswa</h5>
                <form action="{{ route('admin.siswa.kelas', $kelas->id_kelas) }}" method="GET" class="d-flex mt-3 mt-md-0" style="max-width: 300px;">
                    <div class="input-group shadow-sm rounded-pill overflow-hidden border">
                        <input type="text" name="search" class="form-control border-0 bg-light px-3" placeholder="Cari nama / NISN..." value="{{ request('search') }}">
                        <button type="submit" class="btn btn-light border-0"><i class="bi bi-search text-muted"></i></button>
                    </div>
                </form>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-uppercase fs-7">
                        <tr>
                            <th class="px-4 py-3 text-center" style="width: 70px;">No</th>
                            <th class="py-3">NISN</th>
                            <th class="py-3">Nama Siswa</th>
                            <th class="py-3">L/P</th>
                            <th class="py-3 text-center" style="width: 200px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($siswas as $index => $siswa)
                            <tr>
                                <td class="px-4 text-center text-muted fw-bold">{{ $siswas->firstItem() + $index }}</td>
                                <td>
                                    <span class="badge bg-light text-dark border fw-semibold px-3 py-1 rounded-pill">{{ $siswa->nisn ?: '-' }}</span>
                                </td>
                                <td>
                                    <h6 class="mb-0 fw-bold text-dark">{{ $siswa->nama }}</h6>
                                </td>
                                <td>
                                    {{ $siswa->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}
                                </td>
                                <td class="px-4">
                                    <div class="action-group">
                                        <button class="btn-action btn-action-view" data-bs-toggle="modal" data-bs-target="#detailSiswaModal{{ $siswa->id_siswa }}" title="Lihat Detail">
                                            <i class="bi bi-eye-fill"></i>
                                        </button>
                                        <button class="btn-action btn-action-edit" data-bs-toggle="modal" data-bs-target="#editSiswaModal{{ $siswa->id_siswa }}" title="Edit Data">
                                            <i class="bi bi-pencil-fill"></i>
                                        </button>
                                        <form action="{{ route('admin.siswa.destroy', $siswa->id_siswa) }}" method="POST" data-confirm="Hapus siswa {{ $siswa->nama }}?" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-action btn-action-delete" title="Hapus">
                                                <i class="bi bi-trash-fill"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="bi bi-folder-x display-4 mb-3 d-block text-muted"></i>
                                    Belum ada data siswa di kelas ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="d-flex justify-content-end p-4">
                {{ $siswas->links() }}
            </div>
        </div>
    </div>
</div>

<!-- ==============================================
     MODALS (Diletakkan di luar container card 
     agar terhindar dari bug z-index/overlap HTML)
=============================================== -->

@foreach($siswas as $siswa)
    <!-- Detail Siswa Modal -->
    <div class="modal fade" id="detailSiswaModal{{ $siswa->id_siswa }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold text-dark"><i class="bi bi-person-badge-fill text-info me-1"></i> Rincian Siswa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between py-2">
                            <span class="text-muted fw-semibold">Nama Lengkap:</span>
                            <span class="text-dark fw-bold">{{ $siswa->nama }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between py-2">
                            <span class="text-muted fw-semibold">NISN:</span>
                            <span class="text-dark fw-bold">{{ $siswa->nisn ?: '-' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between py-2">
                            <span class="text-muted fw-semibold">Email Login:</span>
                            <span class="text-dark fw-semibold">{{ $siswa->user?->email ?: '-' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between py-2">
                            <span class="text-muted fw-semibold">Jenis Kelamin:</span>
                            <span class="text-dark fw-semibold">{{ $siswa->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between py-2">
                            <span class="text-muted fw-semibold">No HP:</span>
                            <span class="text-dark fw-semibold">{{ $siswa->no_hp ?: '-' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between py-2">
                            <span class="text-muted fw-semibold">Tempat/Tgl Lahir:</span>
                            <span class="text-dark fw-semibold">{{ $siswa->tempat_lahir }}, {{ $siswa->tanggal_lahir ? \Carbon\Carbon::parse($siswa->tanggal_lahir)->format('d-m-Y') : '-' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between py-2">
                            <span class="text-muted fw-semibold">Alamat:</span>
                            <span class="text-dark fw-semibold text-end" style="max-width: 200px;">{{ $siswa->alamat }}</span>
                        </li>
                    </ul>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Siswa Modal -->
    <div class="modal fade" id="editSiswaModal{{ $siswa->id_siswa }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold text-dark"><i class="bi bi-pencil-square me-1 text-primary"></i> Edit Data Siswa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.siswa.update', $siswa->id_siswa) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body p-4">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">NISN</label>
                                <input type="text" name="nisn" class="form-control rounded-3" value="{{ $siswa->nisn }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Nama Lengkap</label>
                                <input type="text" name="nama" class="form-control rounded-3" value="{{ $siswa->nama }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Email Login</label>
                                <input type="email" name="email" class="form-control rounded-3" value="{{ $siswa->user?->email }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Password Akun (Kosongkan jika tidak diubah)</label>
                                <input type="password" name="password" class="form-control rounded-3" placeholder="Minimal 6 karakter...">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Kelas</label>
                                <select name="id_kelas" class="form-select rounded-3" required>
                                    @foreach($semuaKelas as $k)
                                        <option value="{{ $k->id_kelas }}" {{ $siswa->id_kelas == $k->id_kelas ? 'selected' : '' }}>
                                            {{ $k->nama_kelas ?: $k->nama }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Jenis Kelamin</label>
                                <select name="jenis_kelamin" class="form-select rounded-3" required>
                                    <option value="L" {{ $siswa->jenis_kelamin == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="P" {{ $siswa->jenis_kelamin == 'P' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">No Telepon / HP</label>
                                <input type="text" name="no_telepon" class="form-control rounded-3" value="{{ $siswa->no_hp ?: '-' }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Tempat Lahir</label>
                                <input type="text" name="tempat_lahir" class="form-control rounded-3" value="{{ $siswa->tempat_lahir ?: '-' }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Tanggal Lahir</label>
                                <input type="date" name="tanggal_lahir" class="form-control rounded-3" value="{{ $siswa->tanggal_lahir ? \Carbon\Carbon::parse($siswa->tanggal_lahir)->format('Y-m-d') : '' }}" required>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label fw-semibold">Alamat Lengkap</label>
                                <textarea name="alamat" class="form-control rounded-3" rows="3" required>{{ $siswa->alamat ?: '-' }}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach

<!-- Create Siswa Modal -->
<div class="modal fade" id="createSiswaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-dark"><i class="bi bi-person-plus-fill me-1 text-info"></i> Tambah Siswa Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.siswa.store') }}" method="POST">
                @csrf
                <!-- Set Hidden Input for current class if you want them to stay in the same class by default, 
                     but we will also provide a select dropdown -->
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">NISN</label>
                            <input type="text" name="nisn" class="form-control rounded-3" placeholder="Contoh: 12345678" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Nama Lengkap</label>
                            <input type="text" name="nama" class="form-control rounded-3" placeholder="Contoh: Budi Santoso" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Email Login</label>
                            <input type="email" name="email" class="form-control rounded-3" placeholder="Contoh: siswa@siakad.com" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Password Akun</label>
                            <input type="password" name="password" class="form-control rounded-3" placeholder="Minimal 6 karakter..." required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Kelas</label>
                            <select name="id_kelas" class="form-select rounded-3" required>
                                @foreach($semuaKelas as $k)
                                    <option value="{{ $k->id_kelas }}" {{ $kelas->id_kelas == $k->id_kelas ? 'selected' : '' }}>
                                        {{ $k->nama_kelas ?: $k->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="form-select rounded-3" required>
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">No Telepon / HP</label>
                            <input type="text" name="no_telepon" class="form-control rounded-3" placeholder="Contoh: 0812..." required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Tempat Lahir</label>
                            <input type="text" name="tempat_lahir" class="form-control rounded-3" placeholder="Tempat lahir" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" class="form-control rounded-3" required>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label fw-semibold">Alamat Lengkap</label>
                            <textarea name="alamat" class="form-control rounded-3" rows="3" placeholder="Alamat siswa" required></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-info text-white rounded-pill px-4">Tambah Siswa</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Import Siswa Modal -->
<div class="modal fade" id="importSiswaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-dark"><i class="bi bi-file-earmark-excel text-success me-1"></i> Import Siswa Kelas {{ $kelas->nama_kelas ?: $kelas->nama }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.siswa.import', $kelas->id_kelas) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <div class="alert alert-info border-0 rounded-3" style="font-size: 0.9rem;">
                        <strong>Format File:</strong> Pastikan file Anda menggunakan format Excel (.xlsx, .xls) yang sesuai dengan template. Data siswa akan langsung dimasukkan ke kelas ini.
                        <br><br>
                        <a href="{{ route('admin.siswa.template') }}" class="btn btn-sm btn-outline-info rounded-pill fw-bold">
                            <i class="bi bi-download me-1"></i> Download Template
                        </a>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Pilih File Excel</label>
                        <input type="file" name="file" class="form-control rounded-3" accept=".xlsx, .xls, .csv" required>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4"><i class="bi bi-upload me-1"></i> Upload File</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
