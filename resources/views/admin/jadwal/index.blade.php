@extends('layouts.app')

@section('title', "Jadwal Pelajaran")

@section('content')
<div class="container-fluid py-4">
    <!-- Header Card -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; background-color: #1e293b; color: white;">
        <div class="card-body p-4">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h2 class="fw-bold mb-1"><i class="bi bi-calendar-range text-info me-2"></i> Jadwal Pelajaran</h2>
                    <p class="mb-0 text-white-50">Kelola jadwal KBM (Kegiatan Belajar Mengajar) SMK Sunan Giri dengan mudah.</p>
                </div>
                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    <a href="{{ route('admin.jadwal.create') }}" class="btn btn-info text-white rounded-pill px-4 fw-bold">
                        <i class="bi bi-plus-circle me-1"></i> Tambah Jadwal
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
                            <th class="py-3">Kelas</th>
                            <th class="py-3">Mata Pelajaran</th>
                            <th class="py-3">Guru Pengampu</th>
                            <th class="py-3">Hari</th>
                            <th class="py-3">Waktu</th>
                            <th class="py-3">Ruangan</th>
                            <th class="py-3">Tahun Ajaran</th>
                            <th class="py-3 px-4 text-end" style="width: 150px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($jadwals as $jadwal)
                            <tr>
                                <td class="py-3 px-4 fw-semibold text-muted">{{ ($jadwals->currentPage() - 1) * $jadwals->perPage() + $loop->iteration }}</td>
                                <td class="py-3">
                                    <span class="badge bg-secondary-subtle text-secondary fw-bold px-3 py-1 rounded-pill">
                                        {{ $jadwal->kelas->nama_kelas }}
                                    </span>
                                </td>
                                <td class="py-3 fw-bold text-dark">{{ $jadwal->mapel->nama }}</td>
                                <td class="py-3 text-muted">
                                    <i class="bi bi-person me-1 text-info"></i> {{ $jadwal->guru->nama }}
                                </td>
                                <td class="py-3">
                                    @php
                                        $dayColors = [
                                            'Sabtu' => 'dark',
                                            'Ahad' => 'danger',
                                            'Senin' => 'primary',
                                            'Selasa' => 'success',
                                            'Rabu' => 'info',
                                            'Kamis' => 'warning',
                                        ];
                                        $color = $dayColors[$jadwal->hari] ?? 'secondary';
                                    @endphp
                                    <span class="badge bg-{{ $color }} px-3 py-1 rounded-pill">{{ $jadwal->hari }}</span>
                                </td>
                                <td class="py-3 fw-semibold">
                                    @if($jadwal->jam)
                                        <span class="badge bg-info-subtle text-info fw-bold rounded-pill px-3 py-1">
                                            Jam ke-{{ $jadwal->jam->jam_ke }}
                                        </span><br>
                                        <small class="text-muted" style="font-size:0.75rem;">
                                            {{ substr($jadwal->jam->waktu_mulai,0,5) }} - {{ substr($jadwal->jam->waktu_selesai,0,5) }}
                                        </small>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                    <br>
                                    @if($jadwal->status === 'aktif')
                                        <span class="badge bg-success-subtle text-success fw-bold rounded-pill px-2 py-0 mt-1" style="font-size:0.7rem;">Aktif</span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger fw-bold rounded-pill px-2 py-0 mt-1" style="font-size:0.7rem;">Non-Aktif</span>
                                    @endif
                                </td>
                                <td class="py-3 text-muted">
                                    {{ $jadwal->ruangan ? $jadwal->ruangan->nama_ruangan : '-' }}
                                </td>
                                <td class="py-3 text-muted">
                                    <small>{{ $jadwal->tahunAjaran->tahun_ajaran }}</small>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="action-group justify-content-end">
                                        <a href="{{ route('admin.jadwal.edit', $jadwal->id_jadwal) }}" class="btn-action btn-action-edit" title="Edit">
                                            <i class="bi bi-pencil-fill"></i>
                                        </a>
                                        <form action="{{ route('admin.jadwal.destroy', $jadwal->id_jadwal) }}" method="POST" data-confirm="Hapus jadwal ini?" class="d-inline">
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
                                <td colspan="9" class="text-center py-5 text-muted">
                                    <i class="bi bi-calendar-x display-4 mb-2 d-block"></i>
                                    Belum ada data jadwal pelajaran.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($jadwals->hasPages())
            <div class="card-footer bg-white border-0 py-3">
                {{ $jadwals->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
