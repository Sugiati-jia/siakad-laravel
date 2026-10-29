@extends('layouts.app')

@section('title', "Tahun Ajaran")

@section('content')
<div class="container-fluid py-4">
    <!-- Header Card -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; background-color: #1e293b; color: white;">
        <div class="card-body p-4">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h2 class="fw-bold mb-1"><i class="bi bi-calendar-check text-info me-2"></i> Tahun Ajaran</h2>
                    <p class="mb-0 text-white-50">Kelola tahun ajaran dan semester aktif untuk seluruh data akademik sekolah.</p>
                </div>
                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    <a href="{{ route('admin.tahun-ajaran.create') }}" class="btn btn-info text-white rounded-pill px-4 fw-bold">
                        <i class="bi bi-plus-circle me-1"></i> Tambah Tahun Ajaran
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
                            <th class="py-3">Tahun Ajaran</th>
                            <th class="py-3">Semester Aktif</th>
                            <th class="py-3">Tanggal Mulai</th>
                            <th class="py-3">Tanggal Selesai</th>
                            <th class="py-3">Status</th>
                            <th class="py-3 px-4 text-center" style="width: 150px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tahunAjarans as $ta)
                            <tr>
                                <td class="py-3 px-4 fw-semibold text-muted">{{ ($tahunAjarans->currentPage() - 1) * $tahunAjarans->perPage() + $loop->iteration }}</td>
                                <td class="py-3 fw-bold text-dark">{{ $ta->tahun_ajaran }}</td>
                                <td class="py-3">
                                    <span class="badge bg-secondary text-dark fw-bold px-3 py-1 rounded-pill">
                                        Semester {{ $ta->id_semester }} ({{ $ta->id_semester == 1 ? 'Ganjil' : 'Genap' }})
                                    </span>
                                </td>
                                <td class="py-3 text-muted">
                                    <i class="bi bi-calendar2-event me-1"></i> {{ $ta->tanggal_mulai?->format('d M Y') ?: '-' }}
                                </td>
                                <td class="py-3 text-muted">
                                    <i class="bi bi-calendar2-check me-1"></i> {{ $ta->tanggal_selesai?->format('d M Y') ?: '-' }}
                                </td>
                                <td class="py-3">
                                    @if($ta->status === 'aktif')
                                        <span class="badge bg-success px-3 py-1 rounded-pill">Aktif</span>
                                    @else
                                        <span class="badge bg-light text-dark px-3 py-1 rounded-pill">Non-Aktif</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <div class="action-group justify-content-center">
                                        @if($ta->status !== 'aktif')
                                            <form action="{{ route('admin.tahun-ajaran.set-active', $ta->id_tahun_ajaran) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn-action btn-action-custom" title="Aktifkan Tahun Ajaran Ini">
                                                    <i class="bi bi-check-circle-fill"></i>
                                                </button>
                                            </form>
                                        @else
                                            <button type="button" class="btn-action btn-action-custom opacity-50" style="cursor: not-allowed;" title="Tahun Ajaran Sudah Aktif">
                                                <i class="bi bi-check-circle-fill"></i>
                                            </button>
                                        @endif

                                        <a href="{{ route('admin.tahun-ajaran.edit', $ta->id_tahun_ajaran) }}" class="btn-action btn-action-edit" title="Edit">
                                            <i class="bi bi-pencil-fill"></i>
                                        </a>

                                        @if($ta->status !== 'aktif')
                                            <form action="{{ route('admin.tahun-ajaran.destroy', $ta->id_tahun_ajaran) }}" method="POST" data-confirm="Hapus tahun ajaran ini?" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn-action btn-action-delete" title="Hapus">
                                                    <i class="bi bi-trash-fill"></i>
                                                </button>
                                            </form>
                                        @else
                                            <button type="button" class="btn-action btn-action-delete opacity-50" style="cursor: not-allowed;" title="Tahun ajaran aktif tidak dapat dihapus">
                                                <i class="bi bi-trash-fill"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="bi bi-calendar-x display-4 mb-2 d-block"></i>
                                    Belum ada data tahun ajaran.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($tahunAjarans->hasPages())
            <div class="card-footer bg-white border-0 py-3">
                {{ $tahunAjarans->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
