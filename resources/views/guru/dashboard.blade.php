@extends('layouts.app')

@section('title', 'Dashboard Guru')

@section('content')
<style>
    /* Dark Mode Overrides for Guru Dashboard */
    html.dark-mode .guru-bg-white { background-color: #0f172a !important; color: #f8fafc !important; border-color: rgba(255,255,255,0.05) !important; }
    html.dark-mode .guru-bg-card { background-color: #1e293b !important; color: #f8fafc !important; border-color: rgba(255,255,255,0.05) !important; }
    html.dark-mode .guru-bg-light { background-color: #1e293b !important; color: #f8fafc !important; }
    html.dark-mode .guru-icon-primary { background-color: rgba(59, 130, 246, 0.15) !important; color: #60a5fa !important; }
    html.dark-mode .guru-icon-success { background-color: rgba(22, 163, 74, 0.15) !important; color: #4ade80 !important; }
    html.dark-mode .guru-icon-warning { background-color: rgba(245, 158, 11, 0.15) !important; color: #fbbf24 !important; }
    html.dark-mode .guru-icon-danger { background-color: rgba(239, 68, 68, 0.15) !important; color: #f87171 !important; }
    html.dark-mode .guru-icon-gold { background-color: rgba(212, 160, 23, 0.15) !important; color: #fcd34d !important; }
    html.dark-mode .guru-icon-secondary { background-color: #334155 !important; color: #94a3b8 !important; }
    html.dark-mode .guru-icon-info { background-color: rgba(6, 182, 212, 0.15) !important; color: #67e8f9 !important; }
    html.dark-mode .guru-tab-active { background-color: #3b82f6 !important; color: #ffffff !important; }

    /* Mobile Responsive */
    @media (max-width: 576px) {
        .guru-hero-title { font-size: 1.2rem !important; }
        .guru-hero-card .card-body { padding: 1rem !important; }
        .guru-stats-icon { width: 46px !important; height: 46px !important; }
        .nav-pills .nav-link { font-size: 0.75rem !important; padding: 6px 4px !important; }
    }
</style>
<div class="container-fluid py-4">
    <!-- Header Greeting Card -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px; background-color: #1e293b; color: white;">
        <div class="card-body p-4">
            <div class="row align-items-center">
                <div class="col-12 col-md-8">
                    <span class="badge bg-info mb-2 px-3 py-1 rounded-pill fw-bold text-uppercase" style="font-size:0.75rem;">Selamat Datang</span>
                    <h2 class="fw-bold mb-1 guru-hero-title"><i class="bi bi-person-workspace text-info me-2"></i> {{ auth()->user()->guru->nama }}</h2>
                    <p class="mb-0 text-white-50" style="font-size:0.85rem;">NIP: <span class="text-white fw-semibold me-3">{{ auth()->user()->guru->nip }}</span> Role: <span class="badge bg-secondary text-dark fw-bold rounded-pill px-3">{{ auth()->user()->role == 'wali_kelas' ? 'Wali Kelas' : 'Guru Mapel' }}</span></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Solid Stats Cards (Matched with Reference Image) -->
    <div class="row g-3 mb-4">
        <!-- Total Kelas Mengajar -->
        <div class="col-12 col-sm-6 col-md-3">
            <div class="card border-0 shadow-sm h-100 text-center transition-hover position-relative guru-bg-white" style="border-radius: 16px;">
                <div class="card-body p-4">
                    <div class="d-inline-flex align-items-center justify-content-center bg-primary-subtle text-primary rounded-circle mb-3 guru-icon-primary" style="width: 60px; height: 60px;">
                        <i class="bi bi-building fs-3"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-1">
                        <a href="#kelas-mengajar" class="text-decoration-none text-dark stretched-link">Kelas Mengajar</a>
                    </h6>
                    <div class="text-muted small mb-2">Total</div>
                    <div class="fw-bold text-success">
                        +{{ $totalKelasMengajar }} <i class="bi bi-arrow-up-right"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Siswa Diajar -->
        <div class="col-12 col-sm-6 col-md-3">
            <div class="card border-0 shadow-sm h-100 text-center transition-hover position-relative guru-bg-white" style="border-radius: 16px;">
                <div class="card-body p-4">
                    <div class="d-inline-flex align-items-center justify-content-center bg-success-subtle text-success rounded-circle mb-3 guru-icon-success" style="width: 60px; height: 60px;">
                        <i class="bi bi-people fs-3"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-1">
                        <a href="#kelas-mengajar" class="text-decoration-none text-dark stretched-link">Siswa Diajar</a>
                    </h6>
                    <div class="text-muted small mb-2">Total</div>
                    <div class="fw-bold text-success">
                        +{{ $totalSiswaMengajar }} <i class="bi bi-arrow-up-right"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Nilai Draft -->
        <div class="col-12 col-sm-6 col-md-3">
            <div class="card border-0 shadow-sm h-100 text-center transition-hover position-relative guru-bg-white" style="border-radius: 16px;">
                <div class="card-body p-4">
                    <div class="d-inline-flex align-items-center justify-content-center bg-warning-subtle text-warning rounded-circle mb-3 guru-icon-warning" style="width: 60px; height: 60px;">
                        <i class="bi bi-pencil fs-3"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-1">
                        <a href="{{ route('guru.nilai.select-kelas') }}" class="text-decoration-none text-dark stretched-link">Nilai Draft</a>
                    </h6>
                    <div class="text-muted small mb-2">Status</div>
                    <div class="fw-bold text-success">
                        +{{ $draftGrades }} <i class="bi bi-arrow-up-right"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Nilai Terkunci -->
        <div class="col-12 col-sm-6 col-md-3">
            <div class="card border-0 shadow-sm h-100 text-center transition-hover position-relative guru-bg-white" style="border-radius: 16px;">
                <div class="card-body p-4">
                    <div class="d-inline-flex align-items-center justify-content-center bg-danger-subtle text-danger rounded-circle mb-3 guru-icon-danger" style="width: 60px; height: 60px;">
                        <i class="bi bi-lock-fill fs-3"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-1">
                        <a href="{{ route('guru.nilai.select-kelas') }}" class="text-decoration-none text-dark stretched-link">Nilai Terkunci</a>
                    </h6>
                    <div class="text-muted small mb-2">Status</div>
                    <div class="fw-bold text-success">
                        +{{ $lockedGrades }} <i class="bi bi-arrow-up-right"></i>
                    </div>
                </div>
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

    <div class="row g-4">
        <!-- Left Side: Weekly Schedule -->
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm h-100 guru-bg-white" style="border-radius: 20px; background-color: var(--custom-card-bg, #ffffff);">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="mb-0 fw-bold text-dark" style="font-size: 1.1rem;">Kalender</h5>
                    </div>
                    
                    @php
                        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
                        $todayCarbon = \Carbon\Carbon::now();
                        $startOfWeek = $todayCarbon->copy()->startOfWeek();
                    @endphp
                    
                    <div class="d-flex justify-content-between align-items-center mb-3 px-2">
                        <i class="bi bi-chevron-left text-muted" style="cursor:pointer;"></i>
                        <span class="fw-bold text-dark">{{ $todayCarbon->locale('id')->translatedFormat('F Y') }}</span>
                        <i class="bi bi-chevron-right text-muted" style="cursor:pointer;"></i>
                    </div>

                    <ul class="nav nav-pills nav-fill mb-4 border-bottom pb-3" id="weeklyScheduleTabs" role="tablist" style="gap: 5px;">
                        @foreach($days as $index => $day)
                            @php 
                                $date = $startOfWeek->copy()->addDays($index);
                                $isToday = $date->isToday();
                                $dbDayMap = ['Senin'=>'Senin', 'Selasa'=>'Selasa', 'Rabu'=>'Rabu', 'Kamis'=>'Kamis', 'Jumat'=>'Jumat', 'Sabtu'=>'Sabtu', 'Minggu'=>'Ahad'];
                                $dbDay = $dbDayMap[$day];
                                $hasSchedule = isset($weeklySchedules[$dbDay]) && $weeklySchedules[$dbDay]->count() > 0;
                            @endphp
                            <li class="nav-item" role="presentation">
                                <button class="nav-link rounded-circle p-0 d-flex flex-column align-items-center justify-content-center border-0 {{ $isToday ? 'active bg-primary shadow-sm text-white' : 'text-dark bg-transparent' }}" 
                                        style="width: 45px; height: 45px; transition: all 0.2s; margin: 0 auto;"
                                        id="tab-{{ $index }}" 
                                        data-bs-toggle="tab" 
                                        data-bs-target="#content-{{ $index }}" 
                                        type="button" 
                                        role="tab" 
                                        aria-controls="content-{{ $index }}" 
                                        aria-selected="{{ $isToday ? 'true' : 'false' }}">
                                    <span style="font-size: 0.65rem; font-weight: 600; opacity: {{ $isToday ? '1' : '0.5' }}; text-transform: uppercase;">{{ substr($day, 0, 3) }}</span>
                                    <span style="font-size: 0.95rem; font-weight: 700;">{{ $date->format('d') }}</span>
                                    @if($hasSchedule && !$isToday)
                                        <div style="width: 4px; height: 4px; background-color: #3b82f6; border-radius: 50%; position: absolute; bottom: 2px;"></div>
                                    @endif
                                </button>
                            </li>
                        @endforeach
                    </ul>
                    
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h6 class="mb-0 fw-bold text-dark" style="font-size: 1.05rem;">Jadwal Hari Ini</h6>
                        <a href="#" class="text-decoration-none text-primary" style="font-size: 0.85rem; font-weight: 600;">Lihat Jadwal</a>
                    </div>

                    <!-- Tabs Content -->
                    <div class="tab-content" id="weeklyScheduleTabsContent">
                        @foreach($days as $index => $day)
                            @php
                                $dbDayMap = ['Senin'=>'Senin', 'Selasa'=>'Selasa', 'Rabu'=>'Rabu', 'Kamis'=>'Kamis', 'Jumat'=>'Jumat', 'Sabtu'=>'Sabtu', 'Minggu'=>'Ahad'];
                                $dbDay = $dbDayMap[$day];
                                $date = $startOfWeek->copy()->addDays($index);
                                $isToday = $date->isToday();
                                $daySchedules = $weeklySchedules->get($dbDay, collect());
                                $colors = ['#3b82f6', '#f97316', '#eab308', '#ef4444', '#8b5cf6', '#10b981'];
                            @endphp
                            <div class="tab-pane fade {{ $isToday ? 'show active' : '' }}" 
                                 id="content-{{ $index }}" 
                                 role="tabpanel" 
                                 aria-labelledby="tab-{{ $index }}">
                                 
                                @forelse($daySchedules as $sIndex => $sched)
                                    @php 
                                        $color = $colors[$sIndex % count($colors)]; 
                                        $jamMulai = $sched->jam ? substr($sched->jam->jam_mulai, 0, 5) : '00:00';
                                        $jamSelesai = $sched->jam ? substr($sched->jam->jam_selesai, 0, 5) : '00:00';
                                        
                                        // AM/PM format matching design
                                        $jamMulaiFormatted = $sched->jam ? \Carbon\Carbon::parse($sched->jam->jam_mulai)->format('H:i') : '-';
                                        $jamSelesaiFormatted = $sched->jam ? \Carbon\Carbon::parse($sched->jam->jam_selesai)->format('H:i') : '-';
                                        
                                        $isNow = false;
                                        if ($isToday && $sched->jam) {
                                            $now = \Carbon\Carbon::now()->format('H:i');
                                            if ($now >= $jamMulai && $now <= $jamSelesai) {
                                                $isNow = true;
                                            }
                                        }
                                    @endphp
                                    <div class="d-flex align-items-stretch mb-3 bg-white" style="border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); border: 1px solid #f1f5f9; overflow: hidden; transition: all 0.2s;">
                                        <!-- Left Color Bar & Time -->
                                        <div class="d-flex flex-column justify-content-center p-3 text-center" style="border-left: 4px solid {{ $color }}; background-color: #f8fafc; min-width: 110px;">
                                            <span class="fw-bold" style="color: {{ $color }}; font-size: 0.85rem;">{{ $jamMulaiFormatted }}</span>
                                            <span class="fw-bold text-muted mt-1" style="font-size: 0.75rem;">{{ $jamSelesaiFormatted }}</span>
                                        </div>
                                        
                                        <!-- Content -->
                                        <div class="p-3 flex-grow-1 d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="fw-bold text-dark mb-1" style="font-size: 0.95rem;">Kelas {{ $sched->kelas->nama_kelas }} - {{ $sched->mapel->nama }}</h6>
                                                <span class="text-muted" style="font-size: 0.85rem;">Ruang {{ $sched->ruangan ? $sched->ruangan->nama_ruangan : '-' }}</span>
                                            </div>
                                            @if($isNow)
                                                <span class="badge bg-primary rounded-pill px-3 py-2" style="font-size: 0.75rem;">Sekarang</span>
                                            @endif
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-5 text-muted">
                                        <i class="bi bi-calendar-x display-6 mb-3 d-block" style="opacity:0.3"></i>
                                        <p class="mb-0" style="font-size:0.95rem;">Tidak ada jadwal mengajar pada hari {{ $day }}.</p>
                                    </div>
                                @endforelse
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side: Teaching Penugasan -->
        <div class="col-12 col-lg-6" id="kelas-mengajar">
            <div class="card border-0 shadow-sm h-100 guru-bg-white" style="border-radius: 12px; background-color: var(--custom-card-bg, #ffffff);">
                <div class="card-header bg-transparent py-3 border-0">
                    <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-journal-text text-success me-2"></i>Daftar Kelas & Input Nilai</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                            <thead>
                                <tr class="bg-light text-muted fw-bold guru-bg-card">
                                    <th class="py-3 px-4">Mata Pelajaran</th>
                                    <th class="py-3">Kelas</th>
                                    <th class="py-3 text-end px-4">Tindakan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($guruMapels as $gm)
                                    <tr>
                                        <td class="py-3 px-4 fw-bold text-dark">{{ $gm->mapel->nama }}</td>
                                        <td class="py-3">
                                            <span class="badge bg-primary-subtle text-primary fw-bold px-3 py-1 rounded-pill guru-icon-primary">
                                                {{ $gm->kelas->nama_kelas }}
                                            </span>
                                        </td>
                                        <td class="py-3 text-end px-4">
                                            <a href="{{ route('guru.nilai.select-mapel', $gm->id_kelas) }}" class="btn btn-sm btn-outline-info rounded-pill px-3 fw-bold">
                                                <i class="bi bi-pencil-square me-1"></i> Input Nilai
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-5 text-muted">
                                            <i class="bi bi-clipboard-x display-4 mb-2 d-block"></i>
                                            Belum ada penugasan mengajar aktif.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Student Rankings Section -->
    <div class="row g-4 mt-1">
        <div class="col-12">
            <div class="card border-0 shadow-sm guru-bg-white" style="border-radius: 12px; background-color: var(--custom-card-bg, #ffffff);">
                <div class="card-header border-0 py-3" style="background-color: #1e293b; border-radius: 12px 12px 0 0;">
                    <h5 class="mb-0 fw-bold text-white"><i class="bi bi-trophy-fill text-warning me-2"></i>Peringkat Siswa Terbaik</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                            <thead>
                                <tr class="bg-light text-muted fw-bold guru-bg-card">
                                    <th class="py-3 px-4 text-center" style="width: 100px;">Peringkat</th>
                                    <th class="py-3">Nama Siswa</th>
                                    <th class="py-3">Kelas</th>
                                    <th class="py-3 text-center px-4">Rata-Rata Nilai</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($rankings as $index => $siswa)
                                    <tr>
                                        <td class="py-3 px-4 text-center">
                                            @if($siswa->calculated_rank === 1)
                                                <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-warning-subtle guru-icon-gold" style="width: 40px; height: 40px;" title="Juara 1">
                                                    <i class="bi bi-trophy-fill fs-5" style="color: #d4a017;"></i>
                                                </span>
                                            @elseif($siswa->calculated_rank === 2)
                                                <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-secondary-subtle guru-icon-secondary" style="width: 40px; height: 40px;" title="Juara 2">
                                                    <i class="bi bi-trophy-fill fs-5" style="color: #a0a0a0;"></i>
                                                </span>
                                            @elseif($siswa->calculated_rank === 3)
                                                <span class="d-inline-flex align-items-center justify-content-center rounded-circle guru-icon-secondary" style="width: 40px; height: 40px; background-color: #ffe4d3;" title="Juara 3">
                                                    <i class="bi bi-trophy-fill fs-5" style="color: #cd7f32;"></i>
                                                </span>
                                            @else
                                                <span class="fw-bold text-muted fs-5">{{ $siswa->calculated_rank }}</span>
                                            @endif
                                        </td>
                                        <td class="py-3">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center me-3 guru-icon-primary" style="width: 36px; height: 36px; min-width: 36px;">
                                                    <i class="bi bi-person-fill"></i>
                                                </div>
                                                <span class="fw-bold text-dark">{{ $siswa->nama }}</span>
                                            </div>
                                        </td>
                                        <td class="py-3">
                                            <span class="badge bg-info-subtle text-info fw-bold px-3 py-1 rounded-pill guru-icon-info">
                                                {{ $siswa->kelas->nama_kelas ?? '-' }}
                                            </span>
                                        </td>
                                        <td class="py-3 text-center px-4">
                                            <span class="badge {{ $siswa->rata_rata >= 80 ? 'bg-success' : ($siswa->rata_rata >= 60 ? 'bg-warning text-dark' : 'bg-danger') }} fw-bold px-3 py-2 rounded-pill fs-6">
                                                {{ $siswa->rata_rata }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-5 text-muted">
                                            <i class="bi bi-bar-chart-line display-4 mb-2 d-block"></i>
                                            Belum ada data peringkat siswa.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
