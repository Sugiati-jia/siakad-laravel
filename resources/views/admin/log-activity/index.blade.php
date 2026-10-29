@extends('layouts.app')

@section('title', "Log Aktivitas Pengguna")

@section('content')
<div class="container-fluid py-4">
    <div class="card mb-4 border-0 shadow-sm" style="border-radius: 12px; background-color: #1e293b;">
        <div class="card-body p-4 text-white">
            <h3 class="fw-bold mb-1"><i class="bi bi-clock-history me-2 text-info"></i> Riwayat Aktivitas</h3>
            <p class="mb-0 text-white-50">Memantau aktivitas login dan perubahan data yang dilakukan oleh pengguna.</p>
        </div>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius: 12px;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light fs-7 text-uppercase">
                        <tr>
                            <th class="px-4 py-3">Waktu</th>
                            <th class="py-3">Pengguna</th>
                            <th class="py-3">Aksi</th>
                            <th class="py-3">Keterangan</th>
                            <th class="py-3">IP Address</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                            <tr>
                                <td class="px-4 fw-semibold text-muted">{{ $log->created_at->format('d M Y H:i:s') }}</td>
                                <td>
                                    <span class="fw-bold text-dark">{{ $log->user->name ?? 'Sistem/Unknown' }}</span><br>
                                    <small class="text-muted">{{ $log->user->role ?? '-' }}</small>
                                </td>
                                <td>
                                    <span class="badge {{ $log->action == 'Login' ? 'bg-success' : 'bg-primary' }} rounded-pill px-3">
                                        {{ $log->action }}
                                    </span>
                                </td>
                                <td class="text-dark">{{ $log->description }}</td>
                                <td class="text-muted small">
                                    <i class="bi bi-globe me-1"></i> {{ $log->ip_address ?: '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">Belum ada aktivitas yang tercatat.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-end p-4">
                {{ $logs->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
