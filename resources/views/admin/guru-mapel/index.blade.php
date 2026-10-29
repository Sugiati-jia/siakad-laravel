@extends('layouts.app')

@section('title', "Jadwal Pelajaran & Mengajar")

@section('content')
<div class="container-fluid py-4">
    <!-- Header Card (Flat Modern Dark Slate Background) -->
    <div class="card mb-4 border-0 shadow-sm" style="border-radius: 12px; background-color: #1e293b;">
        <div class="card-body p-4 text-white">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <h3 class="fw-bold mb-1"><i class="bi bi-calendar-event me-2 text-info"></i> Jadwal Pelajaran (Penugasan Mengajar)</h3>
                    <p class="mb-0 text-white-50">Atur pembagian jam mengajar, mata pelajaran, serta kelas bagi tenaga pendidik SMK Sunan Giri.</p>
                </div>
                <div class="mt-3 mt-md-0">
                    <button class="btn btn-info fw-bold text-white px-4 py-2 rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#createJadwalModal">
                        <i class="bi bi-plus-lg me-1"></i> Tambah Jadwal Baru
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Table -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
        <div class="card-header bg-white py-3 border-0">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-collection-play-fill me-1 text-info"></i> Ringkasan Jadwal Aktif</h5>
                <div class="mt-2 mt-md-0">
                    <form action="" method="GET" class="d-flex">
                        <input type="text" name="search" class="form-control form-control-sm me-2 rounded-pill px-3" placeholder="Cari guru atau mapel..." value="{{ request('search') }}">
                        <button type="submit" class="btn btn-sm btn-secondary rounded-pill px-3">Filter</button>
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
                            <th class="py-3">Mata Pelajaran</th>
                            <th class="py-3">Kelas</th>
                            <th class="py-3">Guru Pengampu</th>
                            <th class="py-3 text-center" style="width: 150px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($guruMapels as $index => $gm)
                            <tr>
                                <td class="px-4 text-center text-muted fw-bold">{{ $guruMapels->firstItem() + $index }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm me-3 bg-info-subtle text-info d-flex align-items-center justify-content-center fw-bold" style="width: 38px; height: 38px; border-radius: 8px;">
                                            <i class="bi bi-journal-text fs-5"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 fw-bold text-dark">{{ $gm->mapel->nama_mapel }}</h6>
                                            <small class="text-muted">Kode: {{ $gm->mapel->kode_mapel }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-secondary text-dark fw-bold px-3 py-1 rounded-pill">{{ $gm->kelas->nama_kelas }}</span>
                                </td>
                                <td>
                                    <h6 class="mb-0 fw-semibold text-dark">{{ $gm->guru->nama }}</h6>
                                    <small class="text-muted">NIP: {{ $gm->guru->nip ?: '-' }}</small>
                                </td>
                                <td class="px-4">
                                    <div class="action-group">
                                        <button class="btn-action btn-action-view" data-bs-toggle="modal" data-bs-target="#detailJadwalModal{{ $gm->id_guru_mapel }}" title="Lihat Detail">
                                            <i class="bi bi-eye-fill"></i>
                                        </button>
                                        <form action="{{ route('admin.guru-mapel.destroy', $gm->id_guru_mapel) }}" method="POST" data-confirm="Hapus penugasan mengajar ini?" class="d-inline">
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
                                    <i class="bi bi-calendar-x display-4 mb-3 d-block text-muted"></i>
                                    Belum ada data jadwal mengajar terdaftar.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="d-flex justify-content-end p-4">
                {{ $guruMapels->links() }}
            </div>
        </div>
    </div>
</div>

@foreach($guruMapels as $gm)
<!-- Detail Jadwal Modal -->
<div class="modal fade" id="detailJadwalModal{{ $gm->id_guru_mapel }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-dark"><i class="bi bi-info-circle-fill me-1 text-info"></i> Rincian Jadwal Mengajar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between py-3">
                        <span class="text-muted fw-medium">Nama Guru:</span>
                        <span class="text-dark fw-bold">{{ $gm->guru->nama }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between py-3">
                        <span class="text-muted fw-medium">NIP Guru:</span>
                        <span class="text-dark fw-semibold">{{ $gm->guru->nip ?: '-' }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between py-3">
                        <span class="text-muted fw-medium">Mata Pelajaran:</span>
                        <span class="text-dark fw-bold">{{ $gm->mapel->nama_mapel }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between py-3">
                        <span class="text-muted fw-medium">Kelas Ajar:</span>
                        <span class="badge bg-dark text-white fw-bold px-3 py-1 rounded-pill">{{ $gm->kelas->nama_kelas }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between py-3">
                        <span class="text-muted fw-medium">Tahun Ajaran:</span>
                        <span class="text-dark fw-semibold">{{ $gm->tahunAjaran ? $gm->tahunAjaran->tahun_ajaran : '2025-2026' }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between py-3">
                        <span class="text-muted fw-medium">Status Penugasan:</span>
                        <span class="badge bg-success-subtle text-success px-3 py-1 rounded-pill fw-bold">Aktif</span>
                    </li>
                </ul>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Tutup Detail</button>
            </div>
        </div>
    </div>
</div>
@endforeach

<!-- Create Jadwal Modal -->
<div class="modal fade" id="createJadwalModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-dark"><i class="bi bi-calendar-plus me-1 text-info"></i> Tambah Jadwal Mengajar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.guru-mapel.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Pilih Guru Pengampu</label>
                        <select name="guru_id" class="form-select rounded-3" required>
                            <option value="">-- Pilih Guru --</option>
                            @foreach(\App\Models\Guru::orderBy('nama')->get() as $guruSelect)
                                <option value="{{ $guruSelect->id_guru }}">{{ $guruSelect->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Mata Pelajaran</label>
                        <select name="mapel_id" class="form-select rounded-3" required>
                            <option value="">-- Pilih Mapel --</option>
                            @foreach(\App\Models\Mapel::orderBy('nama')->get() as $mapelSelect)
                                <option value="{{ $mapelSelect->id_mapel }}">{{ $mapelSelect->nama }} ({{ $mapelSelect->kode_mapel }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Kelas</label>
                        <select name="id_kelas" class="form-select rounded-3" required>
                            <option value="">-- Pilih Kelas --</option>
                            @foreach(\App\Models\Kelas::orderBy('nama')->get() as $kelasSelect)
                                <option value="{{ $kelasSelect->id_kelas }}">{{ $kelasSelect->nama_kelas }}</option>
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
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-info text-white rounded-pill px-4">Tambah Jadwal</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
