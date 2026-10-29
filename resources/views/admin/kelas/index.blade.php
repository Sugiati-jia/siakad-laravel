@extends('layouts.app')

@section('title', "Master Data Kelas")

@section('content')
<div class="container-fluid py-4">
    <!-- Header Card (Flat Modern Slate Gray - No Gradients) -->
    <div class="card mb-4 border-0 shadow-sm" style="border-radius: 12px; background-color: #1e293b;">
        <div class="card-body p-4 text-white">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <h3 class="fw-bold mb-1"><i class="bi bi-house-door me-2 text-info"></i> {{ $namaJurusan }}</h3>
                    <p class="mb-0 text-white-50">Master data kelas, wali kelas, tingkat tingkatan, serta kapasitas siswa.</p>
                </div>
                <div class="mt-3 mt-md-0">
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-info fw-bold text-white px-4 py-2 rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#createKelasModal">
                            <i class="bi bi-plus-lg me-1"></i> Tambah Kelas Baru
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
                <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-door-open me-1 text-info"></i> Daftar Kelas</h5>
                <div class="mt-2 mt-md-0">
                    <form action="" method="GET" class="d-flex">
                        <input type="text" name="search" class="form-control form-control-sm me-2 rounded-pill px-3" placeholder="Cari kelas..." value="{{ request('search') }}">
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
                            <th class="py-3" style="width: 120px;">Tingkat</th>
                            <th class="py-3">Nama Kelas</th>
                            <th class="py-3">Wali Kelas</th>
                            <th class="py-3 text-center" style="width: 200px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($kelas as $index => $kls)
                            <tr>
                                <td class="px-4 text-center text-muted fw-bold">{{ $kelas->firstItem() + $index }}</td>
                                <td>
                                    <span class="badge bg-light text-dark border fw-bold px-3 py-1 rounded-pill">{{ $kls->tingkat ?: 'X' }}</span>
                                </td>
                                <td>
                                    <h6 class="mb-0 fw-bold text-dark">{{ $kls->nama_kelas }}</h6>
                                </td>
                                <td>
                                    @if($kls->waliKelas)
                                        <h6 class="mb-0 text-dark fw-semibold">{{ $kls->waliKelas->nama }}</h6>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger px-3 py-1 rounded-pill fw-bold">Wali Kelas Kosong</span>
                                    @endif
                                </td>
                                <td class="px-4">
                                    <div class="action-group">
                                        <button class="btn-action btn-action-view" data-bs-toggle="modal" data-bs-target="#detailKelasModal{{ $kls->id_kelas }}" title="Lihat Detail">
                                            <i class="bi bi-eye-fill"></i>
                                        </button>
                                        <button class="btn-action btn-action-edit" data-bs-toggle="modal" data-bs-target="#editKelasModal{{ $kls->id_kelas }}" title="Edit Data">
                                            <i class="bi bi-pencil-fill"></i>
                                        </button>
                                        <form action="{{ route('admin.kelas.destroy', $kls->id_kelas) }}" method="POST" data-confirm="Hapus kelas {{ $kls->nama_kelas }}? Semua data terkait kelas ini mungkin akan terpengaruh." class="d-inline">
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
                                    <i class="bi bi-house-x display-4 mb-3 d-block text-muted"></i>
                                    Belum ada data kelas terdaftar.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-end p-4">
                {{ $kelas->links() }}
            </div>
        </div>
    </div>
</div>

@foreach($kelas as $kls)
    <!-- Detail Kelas Modal -->
    <div class="modal fade" id="detailKelasModal{{ $kls->id_kelas }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold text-dark"><i class="bi bi-house-door text-info me-1"></i> Rincian Informasi Kelas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between py-2">
                            <span class="text-muted fw-semibold">Nama Kelas:</span>
                            <span class="text-dark fw-bold">{{ $kls->nama_kelas }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between py-2">
                            <span class="text-muted fw-semibold">Tingkatan:</span>
                            <span class="text-dark fw-bold">Tingkat {{ $kls->tingkat }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between py-2">
                            <span class="text-muted fw-semibold">Wali Kelas:</span>
                            <span class="text-dark fw-bold">{{ $kls->waliKelas ? $kls->waliKelas->nama : 'Belum ditetapkan' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between py-2">
                            <span class="text-muted fw-semibold">Jurusan Keahlian:</span>
                            <span class="text-dark fw-semibold text-end" style="max-width: 220px;">
                                {{ $kls->konsentrasiKeahlian ? $kls->konsentrasiKeahlian->nama_konsentrasi : ($kls->jurusan ? $kls->jurusan->nama_konsentrasi : 'Umum') }}
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between py-2">
                            <span class="text-muted fw-semibold">Tahun Ajaran:</span>
                            <span class="text-dark fw-semibold">{{ $kls->tahunAjaran ? $kls->tahunAjaran->tahun_ajaran : '2025-2026' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between py-2">
                            <span class="text-muted fw-semibold">Jumlah Murid Terdaftar:</span>
                            <span class="badge bg-secondary text-dark fw-bold px-3 py-1 rounded-pill">{{ $kls->siswas->count() }} Siswa</span>
                        </li>
                    </ul>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Kelas Modal -->
    <div class="modal fade" id="editKelasModal{{ $kls->id_kelas }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold text-dark"><i class="bi bi-pencil-square me-1 text-primary"></i> Edit Data Kelas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.kelas.update', $kls->id_kelas) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama Kelas</label>
                            <input type="text" name="nama_kelas" class="form-control rounded-3" value="{{ $kls->nama_kelas }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Tingkatan</label>
                            <select name="tingkat" class="form-select rounded-3" required>
                                <option value="10" {{ $kls->tingkat == 'X' || $kls->tingkat == 10 ? 'selected' : '' }}>Tingkat X</option>
                                <option value="11" {{ $kls->tingkat == 'XI' || $kls->tingkat == 11 ? 'selected' : '' }}>Tingkat XI</option>
                                <option value="12" {{ $kls->tingkat == 'XII' || $kls->tingkat == 12 ? 'selected' : '' }}>Tingkat XII</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Wali Kelas</label>
                            <select name="wali_id_kelas" class="form-select rounded-3" required>
                                <option value="">-- Pilih Wali Kelas --</option>
                                @foreach(\App\Models\Guru::orderBy('nama')->get() as $gWk)
                                    <option value="{{ $gWk->id_guru }}" {{ $kls->id_wali_kelas == $gWk->id_guru ? 'selected' : '' }}>
                                        {{ $gWk->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Konsentrasi Keahlian / Jurusan</label>
                            <select name="jurusan_id" class="form-select rounded-3" required>
                                @foreach(\App\Models\KonsentrasiKeahlian::all() as $jurSelect)
                                    <option value="{{ $jurSelect->id_konsentrasi_keahlian }}" {{ ($kls->id_konsentrasi_keahlian == $jurSelect->id_konsentrasi_keahlian) ? 'selected' : '' }}>
                                        {{ $jurSelect->nama_konsentrasi }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Tahun Ajaran</label>
                            <select name="id_tahun_ajaran" class="form-select rounded-3" required>
                                @foreach(\App\Models\TahunAjaran::all() as $taSelect)
                                    <option value="{{ $taSelect->id_tahun_ajaran }}" {{ $kls->id_tahun_ajaran == $taSelect->id_tahun_ajaran ? 'selected' : '' }}>
                                        {{ $taSelect->tahun_ajaran }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Kapasitas Maksimal Kelas</label>
                            <input type="number" name="kapasitas" class="form-control rounded-3" value="36" required>
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

<!-- Create Kelas Modal -->
<div class="modal fade" id="createKelasModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-dark"><i class="bi bi-house-plus me-1 text-info"></i> Tambah Kelas Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.kelas.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Kelas</label>
                        <input type="text" name="nama_kelas" class="form-control rounded-3" placeholder="Contoh: X (SEPULUH)-TSM" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tingkatan</label>
                        <select name="tingkat" class="form-select rounded-3" required>
                            <option value="10">Tingkat X</option>
                            <option value="11">Tingkat XI</option>
                            <option value="12">Tingkat XII</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Wali Kelas</label>
                        <select name="wali_id_kelas" class="form-select rounded-3" required>
                            <option value="">-- Pilih Wali Kelas --</option>
                            @foreach(\App\Models\Guru::orderBy('nama')->get() as $gWk)
                                <option value="{{ $gWk->id_guru }}">{{ $gWk->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Konsentrasi Keahlian / Jurusan</label>
                        <select name="jurusan_id" class="form-select rounded-3" required>
                            <option value="">-- Pilih Jurusan --</option>
                            @foreach(\App\Models\KonsentrasiKeahlian::all() as $jurSelect)
                                <option value="{{ $jurSelect->id_konsentrasi_keahlian }}">{{ $jurSelect->nama_konsentrasi }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tahun Ajaran</label>
                        <select name="id_tahun_ajaran" class="form-select rounded-3" required>
                            @foreach(\App\Models\TahunAjaran::all() as $taSelect)
                                <option value="{{ $taSelect->id_tahun_ajaran }}">{{ $taSelect->tahun_ajaran }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Kapasitas Maksimal Kelas</label>
                        <input type="number" name="kapasitas" class="form-control rounded-3" placeholder="Contoh: 36" min="1" max="50" required>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-info text-white rounded-pill px-4">Tambah Kelas</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
