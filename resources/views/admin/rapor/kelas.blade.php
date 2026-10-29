@extends('layouts.app')

@section('title', 'E-Rapor Kelas - ' . ($kelas->nama_kelas ?? ''))

@section('content')
<div class="container-fluid py-3">
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
        <div class="d-flex align-items-center">
            <a href="{{ route('admin.rapor.index') }}" class="btn btn-outline-secondary btn-sm me-3 border-0">
                <i class="bi bi-arrow-left fs-5"></i>
            </a>
            <div>
                <h4 class="mb-0 fw-bold text-dark">E-Rapor Kelas</h4>
                <small class="text-muted">Kelas: <strong>{{ $kelas->nama_kelas ?? '-' }}</strong> | Wali Kelas: <strong>{{ $kelas->waliKelas ? $kelas->waliKelas->nama : 'Belum ditentukan' }}</strong></small>
            </div>
        </div>
        <div>
            <!-- Bulk ZIP download -->
            <a href="{{ route('admin.rapor.download-all', $kelas->id_kelas) }}" class="btn btn-secondary btn-sm border-0 shadow-sm px-4 py-2 fw-bold rounded-pill" style="background-color: #475569; color: white;">
                <i class="bi bi-file-earmark-zip-fill me-1"></i> Unduh Semua Rapor (ZIP)
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 border-bottom border-light">
            <h6 class="mb-0 fw-bold text-secondary"><i class="bi bi-people me-2"></i>Daftar Siswa & Status Pengiriman Rapor</h6>
        </div>
        <div class="card-body p-0">
            @if(count($siswas) > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" style="width: 60px;">No</th>
                                <th>Nama Siswa</th>
                                <th>NISN</th>
                                <th class="text-center" style="width: 280px;">Kelengkapan Data</th>
                                <th class="text-center">Status Rapor</th>
                                <th class="text-center">Dikirim Oleh</th>
                                <th class="text-center" style="min-width: 180px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($siswas as $idx => $siswa)
                                @php
                                    $rapor = $raporStatuses[$siswa->id_siswa] ?? null;
                                    $status = $rapor->status ?? 'draft';
                                    
                                    $badgeClass = match($status) {
                                        'dikirim' => 'bg-primary text-white',
                                        'diverifikasi' => 'bg-success text-white',
                                        default => 'bg-secondary text-white'
                                    };

                                    $statusText = match($status) {
                                        'dikirim' => 'Dikirim',
                                        'diverifikasi' => 'Diverifikasi',
                                        default => 'Draft'
                                    };

                                    // Completeness checks
                                    $isAkademikOk = $hasGrades->get($siswa->id_siswa, false);
                                    $isEkskulOk = $hasEkskul->get($siswa->id_siswa, false);
                                    $isCatatanOk = isset($hasCatatan[$siswa->id_siswa]);
                                    $isKokurikulerOk = $hasKokurikuler->get($siswa->id_siswa, false);
                                @endphp
                                <tr>
                                    <td class="text-center fw-bold text-muted">{{ $idx + 1 }}</td>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $siswa->nama }}</div>
                                        <small class="text-muted">NIS: {{ $siswa->nis ?? '-' }}</small>
                                    </td>
                                    <td>{{ $siswa->nisn }}</td>
                                    <td class="text-center">
                                        <div class="d-inline-flex gap-2">
                                            <span class="badge bg-{{ $isAkademikOk ? 'success-subtle text-success' : 'danger-subtle text-danger' }} border px-2 py-1.5" title="Nilai Akademik Siswa">
                                                <i class="bi bi-{{ $isAkademikOk ? 'check-circle-fill' : 'dash-circle' }} me-1"></i> Nilai
                                            </span>
                                            <span class="badge bg-{{ $isEkskulOk ? 'success-subtle text-success' : 'danger-subtle text-danger' }} border px-2 py-1.5" title="Ekstrakurikuler">
                                                <i class="bi bi-{{ $isEkskulOk ? 'check-circle-fill' : 'dash-circle' }} me-1"></i> Ekskul
                                            </span>
                                            <span class="badge bg-{{ $isCatatanOk ? 'success-subtle text-success' : 'danger-subtle text-danger' }} border px-2 py-1.5" title="Catatan Wali Kelas & Absen">
                                                <i class="bi bi-{{ $isCatatanOk ? 'check-circle-fill' : 'dash-circle' }} me-1"></i> Absen
                                            </span>
                                            <span class="badge bg-{{ $isKokurikulerOk ? 'success-subtle text-success' : 'danger-subtle text-danger' }} border px-2 py-1.5" title="Kokurikuler (P5)">
                                                <i class="bi bi-{{ $isKokurikulerOk ? 'check-circle-fill' : 'dash-circle' }} me-1"></i> P5
                                            </span>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge {{ $badgeClass }} px-2 py-1.5 fw-semibold rounded-pill">
                                            {{ $statusText }}
                                        </span>
                                    </td>
                                    <td class="text-center small text-muted">
                                        {{ $rapor ? ($rapor->dikirim_oleh ?? '-') : '-' }}
                                    </td>
                                    <td class="text-center">
                                        <!-- Actions borderless separated by 1 tab margin (me-3) -->
                                        <a href="{{ route('admin.rapor.view', $siswa->id_siswa) }}" 
                                           class="text-primary text-decoration-none border-0 p-0 me-3" 
                                           title="Lihat Detail Rapor">
                                            <i class="bi bi-eye-fill fs-5"></i>
                                        </a>

                                        <a href="{{ route('admin.rapor.pdf', $siswa->id_siswa) }}" 
                                           class="text-danger text-decoration-none border-0 p-0 me-3" 
                                           title="Unduh PDF Rapor"
                                           target="_blank">
                                            <i class="bi bi-file-earmark-pdf-fill fs-5"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-people" style="font-size: 3rem; color: #ccc;"></i>
                    <h5 class="mt-3">Belum Ada Data Siswa</h5>
                    <p>Siswa tidak ditemukan untuk kelas ini.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
