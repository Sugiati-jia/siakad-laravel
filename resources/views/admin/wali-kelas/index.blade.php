@extends('layouts.app')

@section('title', "Manajemen Wali Kelas")

@section('content')
<div class="container-fluid py-4">
    <!-- Header Card (Flat Modern Slate Gray - No Gradients) -->
    <div class="card mb-4 border-0 shadow-sm" style="border-radius: 12px; background-color: #1e293b;">
        <div class="card-body p-4 text-white">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <h3 class="fw-bold mb-1"><i class="bi bi-person-badge me-2 text-info"></i> Wali Kelas</h3>
                    <p class="mb-0 text-white-50">Kelola guru yang ditugaskan sebagai wali kelas beserta pembagian kelasnya.</p>
                </div>
                <div class="mt-3 mt-md-0">
                    <a href="{{ route('admin.wali-kelas.create') }}" class="btn btn-info fw-bold text-white px-4 py-2 rounded-pill shadow-sm">
                        <i class="bi bi-person-plus-fill me-1"></i> Tambah Wali Kelas
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Table (Minimalist & Flat Grid) -->
    <div class="card border-0 shadow-sm" style="border-radius: 12px;">
        <div class="card-header bg-white py-3 border-0">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-list-stars me-1 text-info"></i> Daftar Wali Kelas</h5>
                <div class="mt-2 mt-md-0">
                    <form action="" method="GET" class="d-flex">
                        <input type="text" name="search" class="form-control form-control-sm me-2 rounded-pill px-3" placeholder="Cari wali kelas..." value="{{ request('search') }}">
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
                            <th class="py-3">NIP</th>
                            <th class="py-3">Nama Guru</th>
                            <th class="py-3">Jenis Kelamin</th>
                            <th class="py-3">Kelas yang Diampu</th>
                            <th class="py-3 text-center" style="width: 200px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($waliKelas as $index => $wk)
                            <tr>
                                <td class="px-4 text-center text-muted fw-bold">{{ $waliKelas->firstItem() + $index }}</td>
                                <td>
                                    <span class="text-muted fw-semibold">{{ $wk->nip ?: '-' }}</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-primary-subtle text-primary me-2" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                            <i class="bi bi-person-fill"></i>
                                        </span>
                                        <h6 class="mb-0 fw-bold text-dark">{{ $wk->nama }}</h6>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border px-2 py-1 rounded-pill">
                                        {{ $wk->jenis_kelamin == 'L' ? 'Laki-laki' : ($wk->jenis_kelamin == 'P' ? 'Perempuan' : 'Belum diatur') }}
                                    </span>
                                </td>
                                <td>
                                    @if($wk->kelasWali)
                                        <span class="badge bg-success-subtle text-success px-3 py-1 rounded-pill fw-bold fs-7">
                                            Kelas {{ $wk->kelasWali->nama_kelas }}
                                        </span>
                                    @else
                                        <span class="badge bg-warning-subtle text-warning px-3 py-1 rounded-pill fw-bold fs-7">
                                            Belum Mengampu Kelas
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4">
                                    <div class="action-group">
                                        <a href="{{ route('admin.wali-kelas.edit', $wk->id_guru) }}" class="btn-action btn-action-edit" title="Edit Penugasan">
                                            <i class="bi bi-pencil-fill"></i>
                                        </a>
                                        <form action="{{ route('admin.wali-kelas.destroy', $wk->id_guru) }}" method="POST" data-confirm="Cabut status Wali Kelas dari {{ $wk->nama }}? Guru ini akan dikembalikan menjadi guru biasa." class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-action btn-action-delete" title="Cabut Status Wali Kelas">
                                                <i class="bi bi-person-dash-fill"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="bi bi-person-x display-4 mb-3 d-block text-muted"></i>
                                    Belum ada data wali kelas terdaftar.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-end p-4">
                {{ $waliKelas->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
