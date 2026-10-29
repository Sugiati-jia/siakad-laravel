@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
<style>
    /* Dark Mode Overrides for Admin Dashboard */
    html.dark-mode .adm-bg-white { background-color: #0f172a !important; color: #f8fafc !important; border-color: rgba(255,255,255,0.05) !important; }
    html.dark-mode .adm-bg-card { background-color: #1e293b !important; color: #f8fafc !important; border-color: rgba(255,255,255,0.05) !important; }
    html.dark-mode .adm-icon-primary { background-color: rgba(59, 130, 246, 0.15) !important; color: #60a5fa !important; }
    html.dark-mode .adm-icon-success { background-color: rgba(22, 163, 74, 0.15) !important; color: #4ade80 !important; }
    html.dark-mode .adm-icon-warning { background-color: rgba(245, 158, 11, 0.15) !important; color: #fbbf24 !important; }
    html.dark-mode .adm-icon-danger { background-color: rgba(239, 68, 68, 0.15) !important; color: #f87171 !important; }
    html.dark-mode .adm-icon-gold { background-color: rgba(212, 160, 23, 0.15) !important; color: #fcd34d !important; }
    html.dark-mode .adm-icon-secondary { background-color: #334155 !important; color: #94a3b8 !important; }
    html.dark-mode .adm-badge-light { background-color: #334155 !important; color: #cbd5e1 !important; }
    html.dark-mode .adm-alert-info { background-color: rgba(6, 182, 212, 0.1) !important; border: 1px solid rgba(6, 182, 212, 0.2) !important; color: #a5f3fc !important; }
    html.dark-mode .adm-alert-success { background-color: rgba(22, 163, 74, 0.1) !important; border: 1px solid rgba(22, 163, 74, 0.2) !important; color: #86efac !important; }
    html.dark-mode .adm-alert-info .text-info { color: #67e8f9 !important; }
    html.dark-mode .adm-alert-success .text-success { color: #4ade80 !important; }

    /* Mobile Responsive */
    @media (max-width: 576px) {
        .adm-hero-title { font-size: 1.2rem !important; }
        .adm-stats-icon { width: 46px !important; height: 46px !important; }
        .adm-quick-icon { width: 40px !important; height: 40px !important; }
    }
</style>
<div class="container-fluid py-4">
    <!-- Greeting Card -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px; background-color: #1e293b; color: white;">
        <div class="card-body p-4">
            <div class="row align-items-center">
                <div class="col-12 col-md-8">
                    <span class="badge bg-info mb-2 px-3 py-1 rounded-pill fw-bold text-uppercase fs-8">Sistem Informasi Akademik</span>
                    <h2 class="fw-bold mb-1"><i class="bi bi-shield-lock text-info me-2"></i> Portal Administrasi Utama</h2>
                    <p class="mb-0 text-white-50">Selamat datang, <span class="text-white fw-bold">{{ auth()->user()->name }}</span>. Kelola seluruh database, tahun ajaran, kelas, dan jadwal pelajaran.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Solid Stats Cards (Matched with Reference Image) -->
    <div class="row g-3 mb-4">
        <!-- Total Guru -->
        <div class="col-12 col-sm-6 col-md-3">
            <div class="card border-0 shadow-sm h-100 text-center adm-bg-white" style="border-radius: 16px;">
                <div class="card-body p-4">
                    <div class="d-inline-flex align-items-center justify-content-center bg-primary-subtle text-primary rounded-circle mb-3 adm-icon-primary" style="width: 60px; height: 60px;">
                        <i class="bi bi-person-workspace fs-3"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-1">Total Guru</h6>
                    <div class="text-muted small mb-2">Pegawai</div>
                    <div class="fw-bold text-success">
                        +{{ $totalGuru }} <i class="bi bi-arrow-up-right"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Siswa -->
        <div class="col-12 col-sm-6 col-md-3">
            <div class="card border-0 shadow-sm h-100 text-center adm-bg-white" style="border-radius: 16px;">
                <div class="card-body p-4">
                    <div class="d-inline-flex align-items-center justify-content-center bg-success-subtle text-success rounded-circle mb-3 adm-icon-success" style="width: 60px; height: 60px;">
                        <i class="bi bi-people fs-3"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-1">Total Siswa</h6>
                    <div class="text-muted small mb-2">Terdaftar</div>
                    <div class="fw-bold text-success">
                        +{{ $totalSiswa }} <i class="bi bi-arrow-up-right"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Kelas -->
        <div class="col-12 col-sm-6 col-md-3">
            <div class="card border-0 shadow-sm h-100 text-center adm-bg-white" style="border-radius: 16px;">
                <div class="card-body p-4">
                    <div class="d-inline-flex align-items-center justify-content-center bg-warning-subtle text-warning rounded-circle mb-3 adm-icon-warning" style="width: 60px; height: 60px;">
                        <i class="bi bi-house-door fs-3"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-1">Total Kelas</h6>
                    <div class="text-muted small mb-2">Ruangan</div>
                    <div class="fw-bold text-success">
                        +{{ $totalKelas }} <i class="bi bi-arrow-up-right"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Mapel -->
        <div class="col-12 col-sm-6 col-md-3">
            <div class="card border-0 shadow-sm h-100 text-center adm-bg-white" style="border-radius: 16px;">
                <div class="card-body p-4">
                    <div class="d-inline-flex align-items-center justify-content-center bg-danger-subtle text-danger rounded-circle mb-3 adm-icon-danger" style="width: 60px; height: 60px;">
                        <i class="bi bi-book fs-3"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-1">Total Mapel</h6>
                    <div class="text-muted small mb-2">Pelajaran</div>
                    <div class="fw-bold text-success">
                        +{{ $totalMapel }} <i class="bi bi-arrow-up-right"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions (Grid Cards Matched with Reference Image) -->
    <div class="mb-4">
        <h5 class="mb-3 fw-bold text-dark">
            <i class="bi bi-lightning-charge text-warning me-2"></i> Aksi Cepat
        </h5>
        <div class="row row-cols-2 row-cols-md-4 g-3">
            <!-- Tambah Guru -->
            <div class="col">
                <a href="{{ route('admin.guru.create') }}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm h-100 text-center transition-hover adm-bg-white" style="border-radius: 16px;">
                        <div class="card-body p-4">
                            <div class="d-inline-flex align-items-center justify-content-center bg-primary-subtle text-primary rounded-circle mb-3 adm-icon-primary" style="width: 50px; height: 50px;">
                                <i class="bi bi-person-plus fs-4"></i>
                            </div>
                            <h6 class="fw-bold text-dark mb-1">Tambah Guru</h6>
                            <div class="text-muted small mb-2">Baru</div>
                            <div class="fw-bold text-success small">
                                Buka <i class="bi bi-arrow-up-right"></i>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            
            <!-- Tambah Siswa -->
            <div class="col">
                <a href="{{ route('admin.siswa.create') }}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm h-100 text-center transition-hover adm-bg-white" style="border-radius: 16px;">
                        <div class="card-body p-4">
                            <div class="d-inline-flex align-items-center justify-content-center bg-success-subtle text-success rounded-circle mb-3 adm-icon-success" style="width: 50px; height: 50px;">
                                <i class="bi bi-person-plus-fill fs-4"></i>
                            </div>
                            <h6 class="fw-bold text-dark mb-1">Tambah Siswa</h6>
                            <div class="text-muted small mb-2">Baru</div>
                            <div class="fw-bold text-success small">
                                Buka <i class="bi bi-arrow-up-right"></i>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Tambah Kelas -->
            <div class="col">
                <a href="{{ route('admin.kelas.create') }}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm h-100 text-center transition-hover adm-bg-white" style="border-radius: 16px;">
                        <div class="card-body p-4">
                            <div class="d-inline-flex align-items-center justify-content-center bg-warning-subtle text-warning rounded-circle mb-3 adm-icon-warning" style="width: 50px; height: 50px;">
                                <i class="bi bi-house-add fs-4"></i>
                            </div>
                            <h6 class="fw-bold text-dark mb-1">Tambah Kelas</h6>
                            <div class="text-muted small mb-2">Baru</div>
                            <div class="fw-bold text-success small">
                                Buka <i class="bi bi-arrow-up-right"></i>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Tambah Mapel -->
            <div class="col">
                <a href="{{ route('admin.mapel.create') }}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm h-100 text-center transition-hover adm-bg-white" style="border-radius: 16px;">
                        <div class="card-body p-4">
                            <div class="d-inline-flex align-items-center justify-content-center bg-danger-subtle text-danger rounded-circle mb-3 adm-icon-danger" style="width: 50px; height: 50px;">
                                <i class="bi bi-journal-plus fs-4"></i>
                            </div>
                            <h6 class="fw-bold text-dark mb-1">Tambah Mapel</h6>
                            <div class="text-muted small mb-2">Baru</div>
                            <div class="fw-bold text-success small">
                                Buka <i class="bi bi-arrow-up-right"></i>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <style>
        .transition-hover {
            transition: all 0.3s ease;
        }
        .transition-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
        }
    </style>

    <!-- Academic Year Settings -->
    <div class="row g-4">
        <!-- Tahun Ajaran -->
        <div class="col-12 col-md-6">
            <div class="card border-0 shadow-sm h-100 adm-bg-white" style="border-radius: 12px; background-color: var(--custom-card-bg, #ffffff);">
                <div class="card-header bg-transparent py-3 border-0">
                    <h5 class="mb-0 fw-bold text-dark">
                        <i class="bi bi-calendar3 text-info me-2"></i> Pengaturan Tahun Ajaran Aktif
                    </h5>
                </div>
                <div class="card-body p-4 d-flex flex-column justify-content-between">
                    <div>
                        @if($activeTahunAjaran)
                            <div class="alert alert-info adm-alert-info border-0 p-3 mb-4 d-flex align-items-center" style="border-radius: 8px;">
                                <i class="bi bi-info-circle-fill fs-4 me-3 text-info"></i>
                                <div>
                                    <strong>Tahun Ajaran Aktif:</strong><br>
                                    {{ $activeTahunAjaran->tahun_ajaran }} - Semester {{ $activeTahunAjaran->id_semester }}
                                </div>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('admin.settings.set-active-tahun-ajaran') }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-bold text-muted small">Pilih Tahun Ajaran</label>
                                <select name="id_tahun_ajaran" class="form-select" required>
                                    <option value="">-- Pilih Tahun Ajaran --</option>
                                    @foreach(\App\Models\TahunAjaran::all() as $tahun)
                                        <option value="{{ $tahun->id }}" {{ $activeTahunAjaran?->id == $tahun->id ? 'selected' : '' }}>
                                            {{ $tahun->tahun_ajaran }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="btn btn-info text-white rounded-pill px-4 fw-bold mt-2">
                                <i class="bi bi-check-circle me-1"></i> Atur Tahun Ajaran Aktif
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Semester -->
        <div class="col-12 col-md-6">
            <div class="card border-0 shadow-sm h-100 adm-bg-white" style="border-radius: 12px; background-color: var(--custom-card-bg, #ffffff);">
                <div class="card-header bg-transparent py-3 border-0">
                    <h5 class="mb-0 fw-bold text-dark">
                        <i class="bi bi-calendar-week text-success me-2"></i> Pengaturan Semester Aktif
                    </h5>
                </div>
                <div class="card-body p-4 d-flex flex-column justify-content-between">
                    <div>
                        @if($activeTahunAjaran)
                            <div class="alert alert-success adm-alert-success border-0 p-3 mb-4 d-flex align-items-center" style="border-radius: 8px;">
                                <i class="bi bi-check-circle-fill fs-4 me-3 text-success"></i>
                                <div>
                                    <strong>Semester Aktif Sekarang:</strong><br>
                                    Semester {{ $activeTahunAjaran->id_semester == 1 ? '1 (Ganjil)' : '2 (Genap)' }}
                                </div>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('admin.settings.set-active-semester') }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-bold text-muted small">Pilih Semester</label>
                                <select name="id_semester" class="form-select" required>
                                    <option value="">-- Pilih Semester --</option>
                                    <option value="1" {{ $activeTahunAjaran?->id_semester == 1 ? 'selected' : '' }}>Semester 1 (Ganjil)</option>
                                    <option value="2" {{ $activeTahunAjaran?->id_semester == 2 ? 'selected' : '' }}>Semester 2 (Genap)</option>
                                </select>
                            </div>
                            <input type="hidden" name="id_tahun_ajaran" value="{{ $activeTahunAjaran?->id ?? '' }}">
                            <button type="submit" class="btn btn-success text-white rounded-pill px-4 fw-bold mt-2">
                                <i class="bi bi-check-circle me-1"></i> Atur Semester Aktif
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Student Leaderboard -->
    <div class="card border-0 shadow-sm mt-4 adm-bg-white" style="border-radius: 12px; background-color: var(--custom-card-bg, #ffffff);">
        <div class="card-header py-3 border-0" style="border-radius: 12px 12px 0 0; background-color: #1e293b; color: white;">
            <h5 class="mb-0 fw-bold">
                <i class="bi bi-trophy-fill text-warning me-2"></i> Peringkat Siswa Terbaik
            </h5>
        </div>
        <div class="card-body p-0">
            @if(isset($topStudents) && $topStudents->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                        <thead>
                            <tr class="bg-light text-muted fw-bold adm-bg-card">
                                <th class="py-3 px-4 text-center" style="width: 80px;">Peringkat</th>
                                <th class="py-3">Nama Siswa</th>
                                <th class="py-3">Kelas</th>
                                <th class="py-3 text-center" style="width: 140px;">Rata-Rata Nilai</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($topStudents as $index => $student)
                                <tr>
                                    <td class="py-3 px-4 text-center">
                                        @if($index == 0)
                                            <span class="d-inline-flex align-items-center justify-content-center rounded-circle adm-icon-gold" style="width: 36px; height: 36px; background-color: var(--custom-card-bg, #ffffff)3cd;">
                                                <i class="bi bi-trophy-fill" style="color: #d4a017; font-size: 1.1rem;"></i>
                                            </span>
                                        @elseif($index == 1)
                                            <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-secondary-subtle adm-icon-secondary" style="width: 36px; height: 36px;">
                                                <i class="bi bi-trophy-fill" style="color: #a0a0a0; font-size: 1.1rem;"></i>
                                            </span>
                                        @elseif($index == 2)
                                            <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-secondary-subtle adm-icon-secondary" style="width: 36px; height: 36px;">
                                                <i class="bi bi-trophy-fill" style="color: #cd7f32; font-size: 1.1rem;"></i>
                                            </span>
                                        @else
                                            <span class="fw-bold text-muted">{{ $index + 1 }}</span>
                                        @endif
                                    </td>
                                    <td class="py-3">
                                        <div class="d-flex align-items-center">
                                            <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-primary-subtle text-primary me-2 adm-icon-primary" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                                <i class="bi bi-person-fill"></i>
                                            </span>
                                            <span class="fw-bold text-dark">{{ $student->nama }}</span>
                                        </div>
                                    </td>
                                    <td class="py-3">
                                        <span class="badge bg-light text-secondary border px-3 py-2 rounded-pill fw-bold adm-badge-light">
                                            {{ $student->kelas->nama_kelas ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="py-3 text-center">
                                        @php
                                            $badgeColor = $student->rata_rata >= 80 ? 'bg-success' : ($student->rata_rata >= 60 ? 'bg-warning text-dark' : 'bg-danger');
                                        @endphp
                                        <span class="badge {{ $badgeColor }} px-3 py-1 rounded-pill fs-6">
                                            {{ number_format($student->rata_rata, 1) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-bar-chart-line display-4 mb-2 d-block"></i>
                    Belum ada data peringkat siswa.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
