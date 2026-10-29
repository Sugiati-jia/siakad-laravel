@extends('layouts.app')
@section('title', 'Kelola Tingkat Kelas')
@section('content')
<div class="container-fluid py-4">

    <div class="card border-0 shadow-sm mb-4" style="border-radius:12px; background:#1e293b; color:white;">
        <div class="card-body p-4">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h2 class="fw-bold mb-1"><i class="bi bi-layers text-warning me-2"></i>Kelola Tingkat Kelas</h2>
                    <p class="mb-0 text-white-50">Daftar tingkatan kelas (X, XI, XII) yang tersedia di sekolah.</p>
                </div>
                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    <a href="{{ route('admin.tingkat.create') }}" class="btn btn-warning rounded-pill px-4 fw-bold">
                        <i class="bi bi-plus-circle me-1"></i> Tambah Tingkat
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
                            <th class="py-3">Tingkat</th>
                            <th class="py-3">Jumlah Kelas</th>
                            <th class="py-3 text-end px-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tingkats as $tingkat)
                        <tr>
                            <td class="py-3 px-4 text-muted fw-semibold">{{ $loop->iteration }}</td>
                            <td class="py-3">
                                <span class="badge fs-5 px-4 py-2 rounded-pill
                                    @if($tingkat->nama_tingkat === 'X') bg-info
                                    @elseif($tingkat->nama_tingkat === 'XI') bg-warning text-dark
                                    @else bg-danger @endif fw-bold">
                                    {{ $tingkat->nama_tingkat }}
                                </span>
                            </td>
                            <td class="py-3">
                                <span class="badge bg-secondary-subtle text-secondary rounded-pill px-3">{{ $tingkat->kelas_count }} kelas</span>
                            </td>
                            <td class="py-3 px-4 text-end">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('admin.tingkat.edit', $tingkat->id_tingkat) }}" class="btn btn-sm btn-outline-warning rounded-pill px-3">
                                        <i class="bi bi-pencil-fill"></i>
                                    </a>
                                    <form action="{{ route('admin.tingkat.destroy', $tingkat->id_tingkat) }}" method="POST"
                                          onsubmit="return confirm('Hapus tingkat {{ $tingkat->nama_tingkat }}?')">
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
                            <td colspan="4" class="text-center py-5 text-muted">
                                <i class="bi bi-layers display-4 mb-2 d-block"></i>
                                Belum ada data tingkat kelas.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($tingkats->hasPages())
        <div class="card-footer bg-white border-0 py-3">{{ $tingkats->links() }}</div>
        @endif
    </div>

</div>
@endsection
