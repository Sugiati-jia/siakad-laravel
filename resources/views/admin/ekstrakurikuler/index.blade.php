@extends('layouts.app')

@section('title', 'Manajemen Ekstrakurikuler')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Card (sama dengan halaman lain) -->
    <div class="card mb-4 border-0 shadow-sm" style="border-radius: 12px; background-color: #1e293b;">
        <div class="card-body p-4 text-white">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <h3 class="fw-bold mb-1"><i class="bi bi-trophy me-2 text-warning"></i> Ekstrakurikuler</h3>
                    <p class="mb-0 text-white-50">Kelola data kegiatan ekstrakurikuler dan statusnya.</p>
                </div>
                <div class="mt-3 mt-md-0">
                    <button class="btn btn-warning fw-bold text-dark px-4 py-2 rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#createModal">
                        <i class="bi bi-plus-lg me-1"></i> Tambah Ekstrakurikuler
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Table -->
    <div class="card border-0 shadow-sm" style="border-radius: 12px;">
        <div class="card-header bg-white py-3 border-0">
            <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-list-task me-1 text-warning"></i> Daftar Ekstrakurikuler</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-uppercase fs-7">
                        <tr>
                            <th class="px-4 py-3 text-center" style="width: 70px;">No</th>
                            <th class="py-3">Nama Ekstrakurikuler</th>
                            <th class="py-3">Deskripsi</th>
                            <th class="py-3 text-center" style="width: 120px;">Status</th>
                            <th class="py-3 text-center" style="width: 160px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ekstrakurikulers as $ekskul)
                            <tr>
                                <td class="px-4 text-center text-muted fw-bold">{{ $loop->iteration + $ekstrakurikulers->firstItem() - 1 }}</td>
                                <td>
                                    <h6 class="mb-0 fw-bold text-dark">{{ $ekskul->nama }}</h6>
                                </td>
                                <td>
                                    <span class="text-muted">{{ $ekskul->deskripsi ?? '-' }}</span>
                                </td>
                                <td class="text-center">
                                    @if($ekskul->status == 'aktif')
                                        <span class="badge bg-success-subtle text-success px-3 py-1 rounded-pill fw-bold">
                                            <i class="bi bi-check-circle me-1"></i>Aktif
                                        </span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary px-3 py-1 rounded-pill fw-bold">
                                            <i class="bi bi-x-circle me-1"></i>Nonaktif
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4">
                                    <div class="action-group">
                                        <button class="btn-action btn-action-edit"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editModal{{ $ekskul->id_ekstrakurikuler }}"
                                                title="Edit Data">
                                            <i class="bi bi-pencil-fill"></i>
                                        </button>
                                        <form action="{{ route('admin.ekstrakurikuler.destroy', $ekskul->id_ekstrakurikuler) }}"
                                              method="POST"
                                              data-confirm="Hapus ekstrakurikuler '{{ $ekskul->nama }}'?"
                                              class="d-inline">
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
                                    <i class="bi bi-trophy display-4 mb-3 d-block text-muted"></i>
                                    Belum ada data ekstrakurikuler terdaftar.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($ekstrakurikulers->hasPages())
                <div class="d-flex justify-content-end p-4">
                    {{ $ekstrakurikulers->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Edit Modals (diletakkan di luar card container) --}}
@foreach($ekstrakurikulers as $ekskul)
    <div class="modal fade" id="editModal{{ $ekskul->id_ekstrakurikuler }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold text-dark">
                        <i class="bi bi-pencil-square me-1 text-primary"></i> Edit Ekstrakurikuler
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.ekstrakurikuler.update', $ekskul->id_ekstrakurikuler) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama Ekstrakurikuler <span class="text-danger">*</span></label>
                            <input type="text" name="nama" class="form-control rounded-3" value="{{ $ekskul->nama }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Deskripsi</label>
                            <textarea name="deskripsi" class="form-control rounded-3" rows="3">{{ $ekskul->deskripsi }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Status</label>
                            <select name="status" class="form-select rounded-3">
                                <option value="aktif" {{ $ekskul->status == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="nonaktif" {{ $ekskul->status == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
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

<!-- Create Modal -->
<div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-dark">
                    <i class="bi bi-trophy-fill me-1 text-warning"></i> Tambah Ekstrakurikuler Baru
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.ekstrakurikuler.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Ekstrakurikuler <span class="text-danger">*</span></label>
                        <input type="text" name="nama" class="form-control rounded-3" required placeholder="Contoh: Pramuka, OSIS, Taekwondo...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Deskripsi</label>
                        <textarea name="deskripsi" class="form-control rounded-3" rows="3" placeholder="Deskripsi singkat kegiatan..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning text-dark fw-bold rounded-pill px-4">
                        <i class="bi bi-plus-lg me-1"></i> Tambah
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
