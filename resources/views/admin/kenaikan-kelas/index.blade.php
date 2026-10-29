@extends('layouts.app')

@section('title', 'Proses Kenaikan Kelas & Kelulusan')

@section('content')
<div class="container-fluid py-4">

    {{-- Header --}}
    <div class="card border-0 shadow-sm mb-4" style="border-radius:14px; background: linear-gradient(135deg,#1e293b 0%,#0f172a 100%); color:white;">
        <div class="card-body p-4">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h2 class="fw-bold mb-1">
                        <i class="bi bi-arrow-up-circle-fill text-warning me-2"></i>Proses Kenaikan Kelas & Kelulusan
                    </h2>
                    <p class="mb-0 text-white-50">
                        Tahun Ajaran: <strong class="text-white">{{ $activeTahun->tahun_ajaran }}</strong>
                        &nbsp;|&nbsp; Semester: <strong class="text-white">{{ $activeTahun->id_semester == 1 ? 'Ganjil' : 'Genap' }}</strong>
                    </p>
                </div>
                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    @if($isSemesterGenap)
                        <form action="{{ route('admin.kenaikan-kelas.proses') }}" method="POST"
                              onsubmit="return confirm('⚠️ PERINGATAN!\n\nProses ini akan memperbarui data kelas seluruh siswa secara PERMANEN.\n\nSiswa kelas XII yang lulus akan diubah statusnya.\nSiswa kelas X & XI yang naik akan dipindahkan ke kelas baru.\n\nYakin ingin melanjutkan?')">
                            @csrf
                            <button type="submit" class="btn btn-warning fw-bold rounded-pill px-4">
                                <i class="bi bi-play-circle-fill me-1"></i> Jalankan Proses Kenaikan
                            </button>
                        </form>
                    @else
                        <span class="badge bg-secondary fs-6 px-3 py-2 rounded-pill">
                            <i class="bi bi-lock-fill me-1"></i> Hanya Aktif di Semester Genap
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Alert Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-3" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(!$isSemesterGenap)
        <div class="alert alert-warning rounded-3 d-flex align-items-center gap-3">
            <i class="bi bi-info-circle-fill fs-4 text-warning"></i>
            <div>
                <strong>Mode Preview</strong> — Proses kenaikan kelas dan kelulusan hanya dapat <strong>dieksekusi</strong> ketika Semester Genap sedang aktif.
                Halaman ini menampilkan <strong>prediksi</strong> hasil jika proses dijalankan sekarang.
            </div>
        </div>
    @endif

    {{-- Summary Cards --}}
    <div class="row mb-4 g-3">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-3 text-center p-3" style="border-left: 4px solid #10b981 !important; background:#f0fdf4;">
                <div class="fs-1 fw-bold text-success">{{ $grouped->get('lulus', collect())->count() }}</div>
                <div class="text-muted small fw-semibold">🎓 Lulus (Kelas XII)</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-3 text-center p-3" style="border-left: 4px solid #3b82f6 !important; background:#eff6ff;">
                <div class="fs-1 fw-bold text-primary">{{ $grouped->get('naik_kelas', collect())->count() }}</div>
                <div class="text-muted small fw-semibold">⬆️ Naik Kelas</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-3 text-center p-3" style="border-left: 4px solid #ef4444 !important; background:#fef2f2;">
                <div class="fs-1 fw-bold text-danger">{{ $grouped->get('tinggal_kelas', collect())->count() }}</div>
                <div class="text-muted small fw-semibold">🔁 Tinggal Kelas</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-3 text-center p-3" style="border-left: 4px solid #8b5cf6 !important; background:#f5f3ff;">
                <div class="fs-1 fw-bold text-purple" style="color:#7c3aed">{{ count($siswaDenganNilai) }}</div>
                <div class="text-muted small fw-semibold">👥 Total Siswa</div>
            </div>
        </div>
    </div>

    {{-- Kriteria --}}
    <div class="alert alert-info rounded-3 mb-4 d-flex align-items-start gap-3">
        <i class="bi bi-lightbulb-fill fs-5 mt-1 text-info"></i>
        <div class="small">
            <strong>Kriteria yang digunakan:</strong>
            Rata-rata <code>nilai_akhir_tp</code> seluruh mata pelajaran &ge; <strong>65</strong> → Naik Kelas / Lulus.
            Siswa kelas XII dengan rata-rata &ge; 65 statusnya diubah menjadi <strong>Lulus</strong>.
            Siswa yang tidak memenuhi syarat dianggap <strong>Tinggal Kelas</strong> dan datanya tidak diubah.
        </div>
    </div>

    {{-- Tabel Daftar Siswa --}}
    <div class="card border-0 shadow-sm" style="border-radius:12px;">
        <div class="card-header bg-white border-0 pt-4 pb-0 px-4 d-flex align-items-center justify-content-between">
            <h5 class="fw-bold mb-0">Daftar Siswa & Hasil Prediksi</h5>
            <span class="text-muted small">Diurutkan berdasarkan kelas</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size:0.88rem;">
                    <thead>
                        <tr class="bg-light text-muted fw-bold">
                            <th class="py-3 px-4">#</th>
                            <th class="py-3">Nama Siswa</th>
                            <th class="py-3">NIS</th>
                            <th class="py-3">Kelas Sekarang</th>
                            <th class="py-3">Tingkat</th>
                            <th class="py-3 text-center">Rata-rata Nilai</th>
                            <th class="py-3 text-center">Status Prediksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($siswaDenganNilai as $i => $row)
                            <tr>
                                <td class="py-3 px-4 fw-semibold text-muted">{{ $i + 1 }}</td>
                                <td class="py-3 fw-bold text-dark">{{ $row['siswa']->nama }}</td>
                                <td class="py-3 text-muted">{{ $row['siswa']->nis ?: '-' }}</td>
                                <td class="py-3">
                                    <span class="badge bg-secondary-subtle text-secondary fw-bold rounded-pill px-3">
                                        {{ $row['kelas']->nama }}
                                    </span>
                                </td>
                                <td class="py-3">
                                    <span class="badge
                                        @if($row['kelas']->tingkat === 'XII') bg-danger-subtle text-danger
                                        @elseif($row['kelas']->tingkat === 'XI') bg-warning-subtle text-warning
                                        @else bg-info-subtle text-info @endif
                                        fw-bold rounded-pill px-2">
                                        {{ $row['kelas']->tingkat }}
                                    </span>
                                </td>
                                <td class="py-3 text-center fw-semibold
                                    @if($row['rata_rata'] >= 65) text-success @else text-danger @endif">
                                    {{ $row['rata_rata'] > 0 ? $row['rata_rata'] : '<span class="text-muted">Belum Ada Nilai</span>' }}
                                    @if($row['rata_rata'] == 0)
                                        <small class="text-muted d-block" style="font-size:0.7rem;">Belum Ada Nilai</small>
                                    @endif
                                </td>
                                <td class="py-3 text-center">
                                    @if($row['status_baru'] === 'lulus')
                                        <span class="badge bg-success px-3 py-2 rounded-pill">
                                            <i class="bi bi-mortarboard-fill me-1"></i> Lulus
                                        </span>
                                    @elseif($row['status_baru'] === 'naik_kelas')
                                        <span class="badge bg-primary px-3 py-2 rounded-pill">
                                            <i class="bi bi-arrow-up-circle-fill me-1"></i> Naik Kelas
                                        </span>
                                    @else
                                        <span class="badge bg-danger px-3 py-2 rounded-pill">
                                            <i class="bi bi-arrow-repeat me-1"></i> Tinggal Kelas
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="bi bi-people display-4 mb-2 d-block"></i>
                                    Tidak ada data siswa di tahun ajaran aktif.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection
