@extends('layouts.app')

@section('title', "Master Mata Pelajaran")

@section('content')
<div class="container-fluid py-4">
    <!-- Header Card (Flat Modern Slate Gray - No Gradients) -->
    <div class="card mb-4 border-0 shadow-sm" style="border-radius: 12px; background-color: #1e293b;">
        <div class="card-body p-4 text-white">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <h3 class="fw-bold mb-1"><i class="bi bi-book me-2 text-info"></i> Mata Pelajaran</h3>
                    <p class="mb-0 text-white-50">Master data kurikulum dan beban jam mata pelajaran sekolah.</p>
                </div>
                <div class="mt-3 mt-md-0">
                    <button class="btn btn-info fw-bold text-white px-4 py-2 rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#createMapelModal">
                        <i class="bi bi-plus-lg me-1"></i> Tambah Mapel Baru
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Table (Minimalist & Flat Grid) -->
    <div class="card border-0 shadow-sm" style="border-radius: 12px;">
        <div class="card-header bg-white py-3 border-0">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-journal-check me-1 text-info"></i> Daftar Mata Pelajaran</h5>
                <div class="mt-2 mt-md-0">
                    <form action="" method="GET" class="d-flex">
                        <input type="text" name="search" class="form-control form-control-sm me-2 rounded-pill px-3" placeholder="Cari mapel..." value="{{ request('search') }}">
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
                            <th class="py-3" style="width: 180px;">Kode Mapel</th>
                            <th class="py-3">Nama Mata Pelajaran</th>
                            <th class="py-3 text-center" style="width: 200px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($mapels as $index => $mapel)
                            <tr>
                                <td class="px-4 text-center text-muted fw-bold">{{ $mapels->firstItem() + $index }}</td>
                                <td>
                                    <span class="badge bg-light text-dark border fw-bold px-3 py-1 rounded-pill">{{ $mapel->kode_mapel }}</span>
                                </td>
                                <td>
                                    <h6 class="mb-0 fw-bold text-dark">{{ $mapel->nama_mapel }}</h6>
                                    <small class="text-muted">Kategori: {{ $mapel->jenis_mapel ?: 'Muatan Kejuruan' }}</small>
                                </td>
                                <td class="px-4">
                                    <div class="action-group">
                                        <button class="btn-action btn-action-view" data-bs-toggle="modal" data-bs-target="#detailMapelModal{{ $mapel->id_mapel }}" title="Lihat Detail">
                                            <i class="bi bi-eye-fill"></i>
                                        </button>
                                        <button class="btn-action btn-action-edit" data-bs-toggle="modal" data-bs-target="#editMapelModal{{ $mapel->id_mapel }}" title="Edit Data">
                                            <i class="bi bi-pencil-fill"></i>
                                        </button>
                                        <form action="{{ route('admin.mapel.destroy', $mapel->id_mapel) }}" method="POST" data-confirm="Hapus mata pelajaran {{ $mapel->nama_mapel }}?" class="d-inline">
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
                                <td colspan="4" class="text-center py-5 text-muted">
                                    <i class="bi bi-journal-x display-4 mb-3 d-block text-muted"></i>
                                    Belum ada data mata pelajaran terdaftar.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="d-flex justify-content-end p-4">
                {{ $mapels->links() }}
            </div>
        </div>
    </div>
</div>

@foreach($mapels as $mapel)
    <!-- Detail Mapel Modal -->
    <div class="modal fade" id="detailMapelModal{{ $mapel->id_mapel }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold text-dark"><i class="bi bi-journal-text text-info me-1"></i> Rincian Mata Pelajaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between py-2">
                            <span class="text-muted fw-semibold">Kode Mapel:</span>
                            <span class="text-dark fw-bold">{{ $mapel->kode_mapel }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between py-2">
                            <span class="text-muted fw-semibold">Nama Mapel:</span>
                            <span class="text-dark fw-bold text-end" style="max-width: 220px;">{{ $mapel->nama_mapel }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between py-2">
                            <span class="text-muted fw-semibold">Jenis / Muatan:</span>
                            <span class="text-dark fw-bold">{{ $mapel->jenis_mapel ?: 'Kejuruan' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between py-2">
                            <span class="text-muted fw-semibold">Kelompok:</span>
                            <span class="text-dark fw-bold">Kelompok {{ $mapel->kelompok_mapel ?: 'C' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between py-2">
                            <span class="text-muted fw-semibold">Urutan di Rapor:</span>
                            <span class="text-dark fw-bold">{{ $mapel->urutan ?: '-' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between py-2">
                            <span class="text-muted fw-semibold">Jam per Minggu:</span>
                            <span class="badge bg-secondary text-dark fw-bold px-3 py-1 rounded-pill">{{ $mapel->jam_mengajar_per_minggu ?: 2 }} Jam</span>
                        </li>
                        <li class="list-group-item py-3">
                            <span class="text-muted fw-semibold mb-2 d-block">Guru Pengampu Mapel Ini:</span>
                            @php
                                $teachers = \App\Models\GuruMapel::with('guru', 'kelas')
                                    ->where('id_mapel', $mapel->id_mapel)
                                    ->where('status', 'aktif')
                                    ->get();
                            @endphp
                            @forelse($teachers as $tc)
                                <div class="mb-1">
                                    <span class="badge bg-light text-dark border">
                                        <i class="bi bi-person-fill text-muted me-1"></i>
                                        {{ $tc->guru->nama }} &raquo; <strong>{{ $tc->kelas->nama_kelas }}</strong>
                                    </span>
                                </div>
                            @empty
                                <span class="text-muted fs-7">Belum ada guru yang ditugaskan</span>
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

    <!-- Edit Mapel Modal -->
    <div class="modal fade" id="editMapelModal{{ $mapel->id_mapel }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold text-dark"><i class="bi bi-pencil-square me-1 text-primary"></i> Edit Mata Pelajaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.mapel.update', $mapel->id_mapel) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Kode Mapel</label>
                            <input type="text" name="kode_mapel" class="form-control rounded-3" value="{{ $mapel->kode_mapel }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama Mata Pelajaran</label>
                            <input type="text" name="nama_mapel" class="form-control rounded-3" value="{{ $mapel->nama_mapel }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Kategori / Jenis Mapel</label>
                            <select name="jenis_mapel" class="form-select rounded-3" required>
                                <option value="Wajib" {{ $mapel->jenis_mapel == 'Wajib' ? 'selected' : '' }}>Nasional (Wajib)</option>
                                <option value="Peminatan" {{ $mapel->jenis_mapel == 'Peminatan' ? 'selected' : '' }}>Kejuruan (Peminatan)</option>
                                <option value="Bakat Minat" {{ $mapel->jenis_mapel == 'Bakat Minat' ? 'selected' : '' }}>Muatan Lokal (Bakat Minat)</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Kelompok Mapel</label>
                            <select name="kelompok_mapel" class="form-select rounded-3" required>
                                <option value="A" {{ $mapel->kelompok_mapel == 'A' ? 'selected' : '' }}>Kelompok A (Umum)</option>
                                <option value="B" {{ $mapel->kelompok_mapel == 'B' ? 'selected' : '' }}>Kelompok B (Umum)</option>
                                <option value="C" {{ $mapel->kelompok_mapel == 'C' ? 'selected' : '' }}>Kelompok C (Kejuruan)</option>
                                <option value="Mata Pelajaran Umum" {{ $mapel->kelompok_mapel == 'Mata Pelajaran Umum' ? 'selected' : '' }}>Mata Pelajaran Umum</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Urutan di Rapor</label>
                            <input type="number" name="urutan" class="form-control rounded-3" value="{{ $mapel->urutan }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Jam Pelajaran Per Minggu</label>
                            <input type="number" name="jam_mengajar_per_minggu" class="form-control rounded-3" value="{{ $mapel->jam_mengajar_per_minggu ?: 2 }}" required>
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

<!-- Create Mapel Modal -->
<div class="modal fade" id="createMapelModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-dark"><i class="bi bi-journal-plus me-1 text-info"></i> Tambah Mata Pelajaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.mapel.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Kode Mapel</label>
                        <input type="text" name="kode_mapel" class="form-control rounded-3" placeholder="Contoh: BIO-X" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Mata Pelajaran</label>
                        <input type="text" name="nama_mapel" class="form-control rounded-3" placeholder="Contoh: Biologi Otomotif" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Kategori / Jenis Mapel</label>
                        <select name="jenis_mapel" class="form-select rounded-3" required>
                            <option value="Wajib">Nasional (Wajib)</option>
                            <option value="Peminatan">Kejuruan (Peminatan)</option>
                            <option value="Bakat Minat">Muatan Lokal (Bakat Minat)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Kelompok Mapel</label>
                        <select name="kelompok_mapel" class="form-select rounded-3" required>
                            <option value="A">Kelompok A (Umum)</option>
                            <option value="B">Kelompok B (Umum)</option>
                            <option value="C">Kelompok C (Kejuruan)</option>
                            <option value="Mata Pelajaran Umum">Mata Pelajaran Umum</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Urutan di Rapor</label>
                        <input type="number" name="urutan" class="form-control rounded-3" placeholder="Contoh: 1" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Jam Pelajaran Per Minggu</label>
                        <input type="number" name="jam_mengajar_per_minggu" class="form-control rounded-3" placeholder="Contoh: 2" min="1" max="40" required>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-info text-white rounded-pill px-4">Tambah Mapel</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
