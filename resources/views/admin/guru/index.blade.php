@extends('layouts.app')

@section('title', "Master Data Guru")

@section('content')
<div class="container-fluid py-4">
    <!-- Header Card (Flat Modern Slate Gray - No Gradients) -->
    <div class="card mb-4 border-0 shadow-sm" style="border-radius: 12px; background-color: #1e293b;">
        <div class="card-body p-4 text-white">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <h3 class="fw-bold mb-1"><i class="bi bi-person-workspace me-2 text-info"></i> Tenaga Pendidik (Guru)</h3>
                    <p class="mb-0 text-white-50">Master data profil guru pengajar dan hak akses akun SIAKAD.</p>
                </div>
                <div class="mt-3 mt-md-0">
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-success rounded-pill px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#importGuruModal">
                            <i class="bi bi-file-earmark-excel me-1"></i> Import
                        </button>
                        <button type="button" class="btn btn-info fw-bold text-white px-4 py-2 rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#createGuruModal">
                            <i class="bi bi-plus-lg me-1"></i> Tambah Guru Baru
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Table (Minimalist & Flat Grid) -->
    <div class="card border-0 shadow-sm" style="border-radius: 12px;">
        <div class="card-header bg-white py-3 border-0">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-list-task me-1 text-info"></i> Daftar Guru</h5>
                <div class="mt-2 mt-md-0">
                    <form action="" method="GET" class="d-flex">
                        <input type="text" name="search" class="form-control form-control-sm me-2 rounded-pill px-3" placeholder="Cari guru..." value="{{ request('search') }}">
                        <button type="submit" class="btn btn-sm btn-secondary rounded-pill px-3">Cari</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-uppercase fs-7">
                        <tr>
                            <th class="px-4 py-3 text-center" style="width: 70px;">No</th>
                            <th class="py-3">NIP / Kode</th>
                            <th class="py-3">Nama Lengkap</th>
                            <th class="py-3">Status Kelas</th>
                            <th class="py-3 text-center" style="width: 200px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($gurus as $index => $guru)
                            <tr>
                                <td class="px-4 text-center text-muted fw-bold">{{ $gurus->firstItem() + $index }}</td>
                                <td>
                                    <span class="badge bg-light text-dark border fw-semibold px-3 py-1 rounded-pill">{{ $guru->nip ?: '-' }}</span>
                                </td>
                                <td>
                                    <h6 class="mb-0 fw-bold text-dark">{{ $guru->nama }}</h6>
                                    <small class="text-muted">{{ $guru->email }}</small>
                                </td>
                                <td>
                                    @if($guru->kelasWali)
                                        <span class="badge bg-success-subtle text-success px-3 py-1 rounded-pill fw-bold">
                                            Wali Kelas {{ $guru->kelasWali->nama_kelas }}
                                        </span>
                                    @else
                                        <span class="text-muted fs-7">Pengampu Mapel</span>
                                    @endif
                                </td>
                                <td class="px-4">
                                    <div class="action-group">
                                        <button class="btn-action btn-action-view" data-bs-toggle="modal" data-bs-target="#detailGuruModal{{ $guru->id_guru }}" title="Lihat Detail">
                                            <i class="bi bi-eye-fill"></i>
                                        </button>
                                        <button class="btn-action btn-action-edit" data-bs-toggle="modal" data-bs-target="#editGuruModal{{ $guru->id_guru }}" title="Edit Data">
                                            <i class="bi bi-pencil-fill"></i>
                                        </button>
                                        <form action="{{ route('admin.guru.destroy', $guru->id_guru) }}" method="POST" data-confirm="Hapus guru {{ $guru->nama }}? Akun loginnya juga akan dihapus." class="d-inline">
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
                                    Belum ada data guru pengajar terdaftar.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="d-flex justify-content-end p-4">
                {{ $gurus->links() }}
            </div>
        </div>
    </div>
</div>

@foreach($gurus as $guru)
    <!-- Detail Guru Modal -->
    <div class="modal fade" id="detailGuruModal{{ $guru->id_guru }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold text-dark"><i class="bi bi-person-badge-fill text-info me-1"></i> Rincian Profil Guru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between py-2">
                            <span class="text-muted fw-semibold">Nama Lengkap:</span>
                            <span class="text-dark fw-bold">{{ $guru->nama }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between py-2">
                            <span class="text-muted fw-semibold">NIP / Kode Guru:</span>
                            <span class="text-dark fw-bold">{{ $guru->nip ?: '-' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between py-2">
                            <span class="text-muted fw-semibold">Email Login:</span>
                            <span class="text-dark fw-semibold">{{ $guru->user?->email ?: $guru->email }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between py-2">
                            <span class="text-muted fw-semibold">No HP:</span>
                            <span class="text-dark fw-semibold">{{ $guru->no_telepon ?: '-' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between py-2">
                            <span class="text-muted fw-semibold">Tempat/Tgl Lahir:</span>
                            <span class="text-dark fw-semibold">{{ $guru->tempat_lahir }}, {{ $guru->tanggal_lahir ? \Carbon\Carbon::parse($guru->tanggal_lahir)->format('d-m-Y') : '-' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between py-2">
                            <span class="text-muted fw-semibold">Alamat:</span>
                            <span class="text-dark fw-semibold text-end" style="max-width: 200px;">{{ $guru->alamat }}</span>
                        </li>
                        <li class="list-group-item py-3">
                            <span class="text-muted fw-semibold mb-2 d-block">Mata Pelajaran yang Diampu:</span>
                            @php
                                $tMpList = $guru->guruMapels;
                            @endphp
                            @forelse($tMpList as $tmp)
                                <span class="badge bg-light text-dark border me-1 mb-1">
                                    {{ $tmp->mapel->nama_mapel }} (Kelas {{ $tmp->kelas->nama_kelas }})
                                </span>
                            @empty
                                <span class="text-muted fs-7">Belum ditugaskan mengajar</span>
                            @endforelse
                        </li>
                    </ul>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Guru Modal -->
    <div class="modal fade" id="editGuruModal{{ $guru->id_guru }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold text-dark"><i class="bi bi-pencil-square me-1 text-primary"></i> Edit Data Guru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.guru.update', $guru->id_guru) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">NIP / Kode Guru</label>
                            <input type="text" name="nip" class="form-control rounded-3" value="{{ $guru->nip }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama Lengkap</label>
                            <input type="text" name="nama" class="form-control rounded-3" value="{{ $guru->nama }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Email Login</label>
                            <input type="email" name="email" class="form-control rounded-3" value="{{ $guru->user?->email ?: $guru->email }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Password Akun (Kosongkan jika tidak diubah)</label>
                            <div class="input-group">
                                <input type="password" name="password" id="editPassword{{ $guru->id_guru }}" class="form-control rounded-start-3" placeholder="Minimal 6 karakter...">
                                <button class="btn btn-outline-secondary rounded-end-3 toggle-password" type="button" data-target="#editPassword{{ $guru->id_guru }}">
                                    <i class="bi bi-eye-slash"></i>
                                </button>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Jenis Kelamin</label>
                                <select name="jenis_kelamin" class="form-select rounded-3" required>
                                    <option value="L" {{ $guru->jenis_kelamin == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="P" {{ $guru->jenis_kelamin == 'P' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">No Telepon</label>
                                <input type="text" name="no_telepon" class="form-control rounded-3" value="{{ $guru->no_telepon ?: '-' }}" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Tempat Lahir</label>
                            <input type="text" name="tempat_lahir" class="form-control rounded-3" value="{{ $guru->tempat_lahir ?: '-' }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" class="form-control rounded-3" value="{{ $guru->tanggal_lahir ? \Carbon\Carbon::parse($guru->tanggal_lahir)->format('Y-m-d') : '' }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Alamat Lengkap</label>
                            <textarea name="alamat" class="form-control rounded-3" rows="3" required>{{ $guru->alamat ?: '-' }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Hak Akses Role</label>
                            <select name="role" class="form-select rounded-3" required>
                                <option value="guru" {{ $guru->user?->role == 'guru' ? 'selected' : '' }}>Guru Biasa</option>
                                <option value="wali_kelas" {{ $guru->user?->role == 'wali_kelas' ? 'selected' : '' }}>Wali Kelas / Guru</option>
                            </select>
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

<!-- Create Guru Modal -->
<div class="modal fade" id="createGuruModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-dark"><i class="bi bi-person-plus-fill me-1 text-info"></i> Tambah Guru Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.guru.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">NIP / Kode Guru</label>
                        <input type="text" name="nip" class="form-control rounded-3" placeholder="Contoh: A1 atau 1990..." required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Lengkap</label>
                        <input type="text" name="nama" class="form-control rounded-3" placeholder="Contoh: Akhmad Baikuni, S.T" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email Login</label>
                        <input type="email" name="email" class="form-control rounded-3" placeholder="Contoh: baikuni@smksunangiri.sch.id" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Password Akun</label>
                        <div class="input-group">
                            <input type="password" name="password" id="createPassword" class="form-control rounded-start-3" placeholder="Minimal 6 karakter..." required>
                            <button class="btn btn-outline-secondary rounded-end-3 toggle-password" type="button" data-target="#createPassword">
                                <i class="bi bi-eye-slash"></i>
                            </button>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="form-select rounded-3" required>
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">No Telepon</label>
                            <input type="text" name="no_telepon" class="form-control rounded-3" placeholder="Contoh: 0812..." required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tempat Lahir</label>
                        <input type="text" name="tempat_lahir" class="form-control rounded-3" placeholder="Tempat lahir..." required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tanggal Lahir</label>
                        <input type="date" name="tanggal_lahir" class="form-control rounded-3" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Alamat Lengkap</label>
                        <textarea name="alamat" class="form-control rounded-3" rows="3" placeholder="Alamat..." required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Hak Akses Role</label>
                        <select name="role" class="form-select rounded-3" required>
                            <option value="guru">Guru Pengampu</option>
                            <option value="wali_kelas">Wali Kelas / Guru</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-info text-white rounded-pill px-4">Tambah Guru</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Import Guru Modal -->
<div class="modal fade" id="importGuruModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-dark"><i class="bi bi-file-earmark-excel text-success me-1"></i> Import Data Guru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.guru.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <div class="alert alert-info border-0 rounded-3" style="font-size: 0.9rem;">
                        <strong>Format File:</strong> Pastikan file Anda menggunakan format Excel (.xlsx, .xls) yang sesuai dengan template.
                        <br><br>
                        <a href="{{ route('admin.guru.template') }}" class="btn btn-sm btn-outline-info rounded-pill fw-bold">
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

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const togglePasswordBtns = document.querySelectorAll('.toggle-password');
        togglePasswordBtns.forEach(btn => {
            btn.addEventListener('click', function () {
                const targetId = this.getAttribute('data-target');
                const passwordInput = document.querySelector(targetId);
                const icon = this.querySelector('i');
                
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    icon.classList.remove('bi-eye-slash');
                    icon.classList.add('bi-eye');
                } else {
                    passwordInput.type = 'password';
                    icon.classList.remove('bi-eye');
                    icon.classList.add('bi-eye-slash');
                }
            });
        });
    });
</script>
@endsection
