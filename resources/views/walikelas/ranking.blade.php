@extends('layouts.app')

@section('title', 'Ranking - ' . ($kelas->nama_kelas ?? ''))

@section('content')
<div class="container-fluid py-3">
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('walikelas.dashboard') }}" class="btn btn-outline-secondary btn-sm me-3">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h4 class="mb-0"><i class="bi bi-bar-chart-line me-2"></i>Ranking Kelas</h4>
            <small class="text-muted">Kelas: <strong>{{ $kelas->nama_kelas ?? '-' }}</strong></small>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white">
            <i class="bi bi-trophy me-2"></i>Peringkat Siswa Berdasarkan Rata-rata Nilai
        </div>
        <div class="card-body p-0">
            @if(!empty($rankings) && count($rankings) > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" style="width: 60px;">Peringkat</th>
                                <th>Nama Siswa</th>
                                <th>NISN</th>
                                <th class="text-center">Rata-rata</th>
                                <th class="text-center">Predikat</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rankings as $idx => $ranking)
                                @php
                                    $rankVal = (int) ($ranking->ranking ?? ($idx + 1));
                                @endphp
                                <tr class="{{ $rankVal <= 3 ? 'table-warning' : '' }}">
                                    <td class="text-center">
                                        @if($rankVal === 1)
                                            <span class="badge bg-warning text-dark fs-6"><i class="bi bi-trophy-fill"></i> 1</span>
                                        @elseif($rankVal === 2)
                                            <span class="badge bg-secondary fs-6">2</span>
                                        @elseif($rankVal === 3)
                                            <span class="badge bg-danger fs-6">3</span>
                                        @else
                                            <span class="fw-semibold">{{ $rankVal }}</span>
                                        @endif
                                    </td>
                                    <td class="fw-semibold">{{ $ranking->nama ?? '-' }}</td>
                                    <td>{{ $ranking->nisn ?? '-' }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-{{ ($ranking->average ?? 0) >= 75 ? 'success' : (($ranking->average ?? 0) >= 60 ? 'warning' : 'danger') }} fs-6">
                                            {{ number_format($ranking->average ?? 0, 1) }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        @php
                                            $avg = $ranking->average ?? 0;
                                            $predikat = match(true) {
                                                $avg >= 90 => 'A (Sangat Baik)',
                                                $avg >= 80 => 'B (Baik)',
                                                $avg >= 70 => 'C (Cukup)',
                                                $avg >= 60 => 'D (Kurang)',
                                                default => 'E (Sangat Kurang)'
                                            };
                                        @endphp
                                        {{ $predikat }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-bar-chart" style="font-size: 3rem; color: #ccc;"></i>
                    <h5 class="text-muted mt-3">Belum Ada Data Ranking</h5>
                    <p class="text-muted">Belum ada data nilai yang cukup untuk menghitung ranking.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
