@extends('layouts.app')

@section('title', 'Detail Nilai - ' . ($siswa->nama ?? ''))

@section('content')
<div class="container-fluid py-3">
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('walikelas.dashboard') }}" class="btn btn-outline-secondary btn-sm me-3">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h4 class="mb-0">Detail Nilai Siswa</h4>
            <small class="text-muted">
                <strong>{{ $siswa->nama ?? '-' }}</strong> | 
                Kelas: {{ $siswa->kelas->nama_kelas ?? '-' }} |
                NISN: {{ $siswa->nisn ?? '-' }}
            </small>
        </div>
    </div>

    @if($nilaiTps->count() > 0)
        @foreach($nilaiTps as $mapelId => $grades)
            @php
                $firstGrade = $grades->first();
                $mapelName = $firstGrade->guruMapel->mapel->nama_mapel ?? 'Mata Pelajaran';
                $guruName = $firstGrade->guruMapel->guru->nama ?? '-';
            @endphp
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <div>
                        <strong>{{ $mapelName }}</strong>
                        <small class="text-muted d-block">Guru: {{ $guruName }}</small>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Tujuan Pembelajaran</th>
                                    <th class="text-center">TH1</th>
                                    <th class="text-center">TH2</th>
                                    <th class="text-center">UH1</th>
                                    <th class="text-center">UH2</th>
                                    <th class="text-center">AS</th>
                                    <th class="text-center">Rata-rata</th>
                                    <th class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($grades as $grade)
                                    @php
                                        $avg = 0;
                                        $count = 0;
                                        $vals = [$grade->th1, $grade->th2, $grade->uh1, $grade->uh2, $grade->akhir_semester];
                                        foreach ($vals as $v) {
                                            if ($v !== null) { $avg += $v; $count++; }
                                        }
                                        $avg = $count > 0 ? $avg / $count : 0;
                                    @endphp
                                    <tr>
                                        <td>{{ $grade->tp->kode_tp ?? '-' }} - {{ $grade->tp->deskripsi ?? '' }}</td>
                                        <td class="text-center">{{ $grade->th1 ?? '-' }}</td>
                                        <td class="text-center">{{ $grade->th2 ?? '-' }}</td>
                                        <td class="text-center">{{ $grade->uh1 ?? '-' }}</td>
                                        <td class="text-center">{{ $grade->uh2 ?? '-' }}</td>
                                        <td class="text-center">{{ $grade->akhir_semester ?? '-' }}</td>
                                        <td class="text-center">
                                            <span class="badge bg-{{ $avg >= 75 ? 'success' : ($avg >= 60 ? 'warning' : 'danger') }}">
                                                {{ number_format($avg, 1) }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            @php
                                                $statusColor = match($grade->status_nilai ?? 'draft') {
                                                    'submitted' => 'warning',
                                                    'locked' => 'success',
                                                    default => 'secondary'
                                                };
                                            @endphp
                                            <span class="badge bg-{{ $statusColor }}">{{ ucfirst($grade->status_nilai ?? 'draft') }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endforeach
    @else
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="bi bi-clipboard-x" style="font-size: 3rem; color: #ccc;"></i>
                <h5 class="text-muted mt-3">Belum Ada Nilai</h5>
                <p class="text-muted">Belum ada nilai yang diinput untuk siswa ini.</p>
            </div>
        </div>
    @endif
</div>
@endsection
