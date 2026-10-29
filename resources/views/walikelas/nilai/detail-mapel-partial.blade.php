{{-- walikelas/nilai/detail-mapel-partial.blade.php --}}
{{-- This is returned as HTML fragment via AJAX for the detail modal --}}

<div class="mb-4">
    <div class="row">
        <div class="col-md-6">
            <h5 class="text-primary fw-bold mb-3">
                <i class="bi bi-book me-2"></i>{{ $guruMapel->mapel->nama_mapel ?? '-' }}
            </h5>
            <table class="table table-sm table-borderless mb-0">
                <tr>
                    <td class="fw-bold text-muted" style="width: 130px;">Guru Pengampu</td>
                    <td>: {{ $guruMapel->guru->nama ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="fw-bold text-muted">Kelas</td>
                    <td>: {{ $guruMapel->kelas->nama_kelas ?? '-' }}</td>
                </tr>
            </table>
        </div>
        <div class="col-md-6">
            <h5 class="text-secondary fw-bold mb-3">&nbsp;</h5>
            <table class="table table-sm table-borderless mb-0">
                <tr>
                    <td class="fw-bold text-muted" style="width: 130px;">Semester</td>
                    <td>: {{ $guruMapel->id_semester == 1 ? '1 (Satu)' : '2 (Dua)' }}</td>
                </tr>
                <tr>
                    <td class="fw-bold text-muted">Tahun Ajaran</td>
                    <td>: {{ $activeTahunAjaran->tahun_ajaran ?? '-' }}</td>
                </tr>
            </table>
        </div>
    </div>
</div>

{{-- Statistik Ringkas --}}
@php
    $nilaiValues = collect($summaryData)->pluck('nilai_akhir')->filter(fn($v) => $v !== null);
    $avgNilai = $nilaiValues->isNotEmpty() ? $nilaiValues->avg() : 0;
    $maxNilai = $nilaiValues->isNotEmpty() ? $nilaiValues->max() : 0;
    $minNilai = $nilaiValues->isNotEmpty() ? $nilaiValues->min() : 0;
    $totalSiswa = count($summaryData);
@endphp

<div class="row mb-4 text-center">
    <div class="col-sm-3 col-6 mb-2">
        <div class="card bg-light border-0 py-2">
            <small class="text-muted fw-bold">TOTAL SISWA</small>
            <div class="fs-4 fw-bold text-primary">{{ $totalSiswa }}</div>
        </div>
    </div>
    <div class="col-sm-3 col-6 mb-2">
        <div class="card bg-light border-0 py-2">
            <small class="text-muted fw-bold">RATA-RATA KELAS</small>
            <div class="fs-4 fw-bold text-info">{{ number_format($avgNilai, 1) }}</div>
        </div>
    </div>
    <div class="col-sm-3 col-6 mb-2">
        <div class="card bg-light border-0 py-2">
            <small class="text-muted fw-bold">NILAI TERTINGGI</small>
            <div class="fs-4 fw-bold text-success">{{ number_format($maxNilai, 1) }}</div>
        </div>
    </div>
    <div class="col-sm-3 col-6 mb-2">
        <div class="card bg-light border-0 py-2">
            <small class="text-muted fw-bold">NILAI TERENDAH</small>
            <div class="fs-4 fw-bold text-danger">{{ number_format($minNilai, 1) }}</div>
        </div>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-bordered table-hover align-middle mb-0">
        <thead class="table-light text-center">
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 25%;">Nama Siswa</th>
                <th style="width: 10%;">Status</th>
                <th style="width: 10%;">Jumlah TP</th>
                <th style="width: 12%;">Nilai Akhir</th>
                <th style="width: 8%;">Predikat</th>
                <th>Deskripsi Capaian Kompetensi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($summaryData as $idx => $data)
                @php
                    $nilai = $data['nilai_akhir'];
                    if ($nilai !== null) {
                        if ($nilai >= 90) $predikat = 'A';
                        elseif ($nilai >= 80) $predikat = 'B';
                        elseif ($nilai >= 70) $predikat = 'C';
                        elseif ($nilai >= 60) $predikat = 'D';
                        else $predikat = 'E';
                    } else {
                        $predikat = '-';
                    }

                    $predikatColor = match($predikat) {
                        'A' => 'success',
                        'B' => 'primary',
                        'C' => 'warning',
                        'D' => 'danger',
                        'E' => 'dark',
                        default => 'secondary',
                    };

                    $statusBadge = match($data['status']) {
                        'draft' => '<span class="badge bg-secondary">Draft</span>',
                        'submitted' => '<span class="badge bg-info text-dark">Submitted</span>',
                        'locked' => '<span class="badge bg-success">Locked</span>',
                        default => '<span class="badge bg-light text-muted">Belum Ada</span>',
                    };
                @endphp
                <tr>
                    <td class="text-center fw-bold">{{ $idx + 1 }}</td>
                    <td>
                        <span class="fw-semibold">{{ $data['siswa']->nama }}</span>
                        <div class="text-muted small">NISN: {{ $data['siswa']->nisn ?? '-' }}</div>
                    </td>
                    <td class="text-center">{!! $statusBadge !!}</td>
                    <td class="text-center">
                        <span class="badge bg-light text-dark border">{{ $data['jumlah_tp'] }} TP</span>
                    </td>
                    <td class="text-center fw-bold text-primary fs-5">
                        {{ $nilai !== null ? number_format($nilai, 0) : '-' }}
                    </td>
                    <td class="text-center">
                        <span class="badge bg-{{ $predikatColor }} fs-6">{{ $predikat }}</span>
                    </td>
                    <td class="small text-muted">{{ $data['deskripsi'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">
                        <i class="bi bi-inbox fs-2"></i>
                        <p class="mt-2 mb-0">Belum ada data nilai</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
