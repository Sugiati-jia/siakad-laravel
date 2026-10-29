@extends('layouts.app')

@section('title', "Log Error Sistem")

@section('content')
<div class="container-fluid py-4">
    <div class="card mb-4 border-0 shadow-sm" style="border-radius: 12px; background-color: #7f1d1d;">
        <div class="card-body p-4 text-white">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <h3 class="fw-bold mb-1"><i class="bi bi-bug me-2 text-warning"></i> Log Error Sistem</h3>
                    <p class="mb-0 text-white-50">Menampilkan rekaman log dari server untuk keperluan debugging.</p>
                </div>
                <div class="mt-3 mt-md-0">
                    <form action="{{ route('admin.log-error.clear') }}" method="POST" data-confirm="Bersihkan semua log error sistem? Tindakan ini tidak dapat dibatalkan.">
                        @csrf
                        <button type="submit" class="btn btn-warning fw-bold text-dark px-4 py-2 rounded-pill shadow-sm">
                            <i class="bi bi-trash-fill me-1"></i> Bersihkan Log
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm" style="border-radius: 12px;">
        <div class="card-header bg-white py-3 border-bottom border-light">
            <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-list-task me-2 text-info"></i> Daftar Error Terakhir</h6>
        </div>
        <div class="card-body p-0">
            @if(empty($parsedLogs))
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-shield-check display-4 mb-3 d-block text-success"></i>
                    <h5 class="fw-bold text-dark">Sistem Aman!</h5>
                    <p>File log tidak ditemukan atau belum ada error yang tercatat.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" style="width: 50px;">No</th>
                                <th style="width: 160px;">Tanggal</th>
                                <th style="width: 100px;">Level</th>
                                <th>Pesan Error & Lokasi File</th>
                                <th class="text-center" style="width: 120px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($parsedLogs as $index => $log)
                                @php
                                    $badgeClass = match($log['level']) {
                                        'ERROR', 'CRITICAL', 'EMERGENCY', 'ALERT' => 'bg-danger',
                                        'WARNING' => 'bg-warning text-dark',
                                        'INFO', 'NOTICE' => 'bg-info',
                                        default => 'bg-secondary'
                                    };
                                @endphp
                                <tr>
                                    <td class="text-center fw-bold text-muted">{{ $index + 1 }}</td>
                                    <td>
                                        <small class="fw-bold text-dark d-block">{{ \Carbon\Carbon::parse($log['date'])->format('d M Y') }}</small>
                                        <small class="text-muted">{{ \Carbon\Carbon::parse($log['date'])->format('H:i:s') }}</small>
                                    </td>
                                    <td>
                                        <span class="badge {{ $badgeClass }} px-2 py-1 rounded-pill" style="font-size: 0.7rem;">{{ $log['level'] }}</span>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark text-truncate" style="max-width: 500px;" title="{{ $log['short_message'] }}">
                                            {{ $log['short_message'] }}
                                        </div>
                                        <small class="text-muted"><i class="bi bi-file-code text-danger me-1"></i> {{ $log['file'] }}</small>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#logDetails{{ $index }}" aria-expanded="false" aria-controls="logDetails{{ $index }}">
                                            Rincian
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="5" class="p-0 border-0">
                                        <div class="collapse" id="logDetails{{ $index }}">
                                            <div class="p-4 bg-light border-bottom">
                                                <div class="row">
                                                    <div class="col-md-6 mb-3 mb-md-0">
                                                        <h6 class="fw-bold text-dark"><i class="bi bi-question-circle text-danger me-1"></i> Apa yang terjadi?</h6>
                                                        <div class="p-3 bg-white border rounded text-danger small" style="word-break: break-word;">
                                                            {{ $log['short_message'] }}
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <h6 class="fw-bold text-dark"><i class="bi bi-tools text-success me-1"></i> Rencana Perbaikan</h6>
                                                        <div class="p-3 bg-white border rounded text-success fw-semibold small">
                                                            {{ $log['solution'] }}
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="mt-3">
                                                    <h6 class="fw-bold text-dark"><i class="bi bi-terminal-fill text-secondary me-1"></i> Stack Trace (Detail Log)</h6>
                                                    <pre class="bg-dark text-success p-3 rounded small" style="max-height: 250px; overflow-y: auto; white-space: pre-wrap; font-family: 'Consolas', monospace;">{{ $log['full_message'] }}</pre>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
