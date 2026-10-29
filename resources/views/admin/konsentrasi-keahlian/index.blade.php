@extends('layouts.app')

@section('title', "Jurusan / Konsentrasi Keahlian")

@section('content')
<div class="container-fluid py-4">
    <!-- Header Card -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; background-color: #1e293b; color: white;">
        <div class="card-body p-4">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h2 class="fw-bold mb-1"><i class="bi bi-diagram-3 text-info me-2"></i> Jurusan / Konsentrasi Keahlian</h2>
                    <p class="mb-0 text-white-50">Kelola konsentrasi keahlian (jurusan) yang diselenggarakan di SMK Sunan Giri.</p>
                </div>
                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    <a href="{{ route('admin.konsentrasi-keahlian.create') }}" class="btn btn-info text-white rounded-pill px-4 fw-bold">
                        <i class="bi bi-plus-circle me-1"></i> Tambah Jurusan
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Table Card -->
    <div class="card border-0 shadow-sm" style="border-radius: 12px; background-color: var(--custom-card-bg, #ffffff);">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                    <thead>
                        <tr class="bg-light text-muted fw-bold">
                            <th class="py-3 px-4" style="width: 60px;">#</th>
                            <th class="py-3">Kode Jurusan</th>
                            <th class="py-3">Nama Konsentrasi Keahlian</th>
                            <th class="py-3">Program Keahlian</th>
                            <th class="py-3">Bidang Keahlian</th>
                            <th class="py-3 px-4 text-end" style="width: 150px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($konsentrasiKeahlians as $kk)
                            <tr>
                                <td class="py-3 px-4 fw-semibold text-muted">{{ ($konsentrasiKeahlians->currentPage() - 1) * $konsentrasiKeahlians->perPage() + $loop->iteration }}</td>
                                <td class="py-3">
                                    <span class="badge bg-secondary-subtle text-secondary fw-bold px-3 py-1 rounded-pill">
                                        {{ $kk->kode_konsentrasi }}
                                    </span>
                                </td>
                                <td class="py-3 fw-bold text-dark">{{ $kk->nama_konsentrasi }}</td>
                                <td class="py-3 text-muted">{{ $kk->programKeahlian?->nama_program ?: '-' }}</td>
                                <td class="py-3 text-muted">
                                    <small>{{ $kk->programKeahlian?->bidangKeahlian?->nama_bidang ?: '-' }}</small>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="action-group">
                                        <a href="{{ route('admin.konsentrasi-keahlian.edit', $kk->id_konsentrasi_keahlian) }}" class="btn-action btn-action-edit" title="Edit">
                                            <i class="bi bi-pencil-fill"></i>
                                        </a>
                                        <form action="{{ route('admin.konsentrasi-keahlian.destroy', $kk->id_konsentrasi_keahlian) }}" method="POST" data-confirm="Hapus jurusan '{{ $kk->nama_konsentrasi ?? $kk->nama }}'?" class="d-inline">
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
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="bi bi-diagram-3-fill display-4 mb-2 d-block"></i>
                                    Belum ada data jurusan / konsentrasi keahlian.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($konsentrasiKeahlians->hasPages())
            <div class="card-footer bg-white border-0 py-3">
                {{ $konsentrasiKeahlians->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
