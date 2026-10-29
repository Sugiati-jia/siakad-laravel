@extends('layouts.app')
@section('title', 'Kelola Ruangan')
@section('content')
<div class="container-fluid py-4">

    {{-- Header --}}
    <div class="card border-0 shadow-sm mb-4" style="border-radius:12px; background:#1e293b; color:white;">
        <div class="card-body p-4">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h2 class="fw-bold mb-1"><i class="bi bi-door-open text-info me-2"></i>Kelola Ruangan</h2>
                    <p class="mb-0 text-white-50">Daftar ruangan dan laboratorium yang tersedia di sekolah.</p>
                </div>
                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    <a href="{{ route('admin.ruangan.create') }}" class="btn btn-info text-white rounded-pill px-4 fw-bold">
                        <i class="bi bi-plus-circle me-1"></i> Tambah Ruangan
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3"><i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-3"><i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    <div class="card border-0 shadow-sm" style="border-radius:12px;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size:0.9rem;">
                    <thead>
                        <tr class="bg-light text-muted fw-bold">
                            <th class="py-3 px-4" style="width:60px;">#</th>
                            <th class="py-3">Nama Ruangan</th>
                            <th class="py-3">Kode</th>
                            <th class="py-3">Dipakai di Jadwal</th>
                            <th class="py-3 text-end px-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ruangans as $ruangan)
                        <tr>
                            <td class="py-3 px-4 text-muted fw-semibold">{{ ($ruangans->currentPage()-1)*$ruangans->perPage()+$loop->iteration }}</td>
                            <td class="py-3 fw-bold text-dark">
                                <i class="bi bi-door-open text-info me-2"></i>{{ $ruangan->nama_ruangan }}
                            </td>
                            <td class="py-3">
                                @if($ruangan->kode_ruangan)
                                    <span class="badge bg-secondary-subtle text-secondary rounded-pill px-3">{{ $ruangan->kode_ruangan }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="py-3">
                                <span class="badge bg-info-subtle text-info rounded-pill px-3">{{ $ruangan->jadwal()->count() }} jadwal</span>
                            </td>
                            <td class="py-3 px-4 text-end">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('admin.ruangan.edit', $ruangan->id_ruangan) }}" class="btn btn-sm btn-outline-warning rounded-pill px-3">
                                        <i class="bi bi-pencil-fill"></i>
                                    </a>
                                    <form action="{{ route('admin.ruangan.destroy', $ruangan->id_ruangan) }}" method="POST"
                                          onsubmit="return confirm('Hapus ruangan {{ $ruangan->nama_ruangan }}?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="bi bi-door-open display-4 mb-2 d-block"></i>
                                Belum ada data ruangan.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($ruangans->hasPages())
        <div class="card-footer bg-white border-0 py-3">{{ $ruangans->links() }}</div>
        @endif
    </div>

</div>
@endsection
