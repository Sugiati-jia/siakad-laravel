@extends('layouts.app')

@section('title', 'Dashboard Wali Kelas')

@section('content')
<style>
    /* Dark Mode Overrides for Walikelas Dashboard */
    html.dark-mode .wk-bg-light { background-color: #1e293b !important; color: #f8fafc !important; border-color: rgba(255,255,255,0.05) !important; }
    html.dark-mode .wk-bg-white { background-color: #0f172a !important; color: #f8fafc !important; border-color: rgba(255,255,255,0.05) !important; }
    html.dark-mode .wk-tab-active { background-color: #3b82f6 !important; color: #ffffff !important; }
    html.dark-mode .wk-badge-gray { background-color: #334155 !important; color: #cbd5e1 !important; }
    html.dark-mode .wk-badge-blue { background-color: rgba(59, 130, 246, 0.15) !important; color: #60a5fa !important; }
    html.dark-mode .wk-btn-green { background-color: rgba(22, 163, 74, 0.15) !important; color: #4ade80 !important; border-color: rgba(74, 222, 128, 0.2) !important; }
    
    html.dark-mode .wk-icon-blue { background: rgba(59, 130, 246, 0.15) !important; }
    html.dark-mode .wk-icon-green { background: rgba(22, 163, 74, 0.15) !important; }
    html.dark-mode .wk-icon-yellow { background: rgba(202, 138, 4, 0.15) !important; }
    html.dark-mode .wk-icon-red { background: rgba(225, 29, 72, 0.15) !important; }
    html.dark-mode .wk-icon-purple { background: rgba(168, 85, 247, 0.15) !important; }
    html.dark-mode .wk-icon-cyan { background: rgba(6, 182, 212, 0.15) !important; }

    /* Mobile Responsive */
    @media (max-width: 576px) {
        .wk-hero-title { font-size: 1.3rem !important; }
        .wk-hero-card .card-body { padding: 1rem !important; }
        .wk-menu-card .card-body { padding: 0.75rem !important; }
        .wk-menu-card .fw-bold { font-size: 0.75rem !important; }
        .stat-icon { width: 44px !important; height: 44px !important; }
        .fw-bold.fs-4 { font-size: 1.2rem !important; }
    }
</style>
<div class="container-fluid py-4">

    @if($kelas)

    {{-- ══════════════════ HERO HEADER ══════════════════ --}}
    <div class="card border-0 shadow mb-4" style="border-radius:20px; background: linear-gradient(135deg, #1e293b 0%, #0f172a 60%, #1e3a5f 100%); overflow:hidden; position:relative;">
        <div style="position:absolute; top:-40px; right:-40px; width:220px; height:220px; background:rgba(255,255,255,0.04); border-radius:50%;"></div>
        <div style="position:absolute; bottom:-60px; right:80px; width:160px; height:160px; background:rgba(59,130,246,0.08); border-radius:50%;"></div>
        <div class="card-body p-4 text-white position-relative">
            <div class="row align-items-center">
                <div class="col-12 col-md-7">
                    <span class="badge px-3 py-1 rounded-pill fw-bold mb-2 d-inline-block" style="background:rgba(59,130,246,0.25); color:#93c5fd; font-size:0.75rem; letter-spacing:1px;">WALI KELAS</span>
                    <h2 class="fw-bold mb-1 wk-hero-title">
                        <i class="bi bi-star-fill me-2" style="color:#fbbf24;"></i>
                        Kelas {{ $kelas->nama_kelas }}
                    </h2>
                    <p class="mb-0" style="color:rgba(255,255,255,0.6); font-size:0.85rem;">
                        <i class="bi bi-person-badge me-1"></i>
                        {{ auth()->user()->guru->nama ?? auth()->user()->name }}
                        &nbsp;·&nbsp;
                        <i class="bi bi-calendar3 me-1"></i>
                        {{ date('l, d F Y') }}
                    </p>
                </div>
                <div class="col-12 col-md-5 mt-3 mt-md-0">
                    <div class="row g-2 text-center">
                        <div class="col-4">
                            <div style="background:rgba(255,255,255,0.08); border-radius:14px; padding:12px 8px;">
                                <div class="fw-bold fs-4" style="color:#60a5fa;">{{ $totalSiswa }}</div>
                                <small style="color:rgba(255,255,255,0.55); font-size:0.7rem;">Siswa</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div style="background:rgba(255,255,255,0.08); border-radius:14px; padding:12px 8px;">
                                <div class="fw-bold fs-4" style="color:#34d399;">{{ $raporStats['dikirim'] + $raporStats['diverifikasi'] }}</div>
                                <small style="color:rgba(255,255,255,0.55); font-size:0.7rem;">Dikirim</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div style="background:rgba(255,255,255,0.08); border-radius:14px; padding:12px 8px;">
                                <div class="fw-bold fs-4" style="color:#fbbf24;">{{ $raporStats['draft'] }}</div>
                                <small style="color:rgba(255,255,255,0.55); font-size:0.7rem;">Draft</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════ STATS CARDS ══════════════════ --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-md-3">
            <div class="card border-0 shadow-sm h-100 wk-stat-card wk-bg-white" style="border-radius:16px;">
                <div class="card-body p-4 text-center">
                    <div class="stat-icon mb-3 mx-auto d-flex align-items-center justify-content-center rounded-circle wk-icon-blue" style="width:58px; height:58px; background:linear-gradient(135deg,#dbeafe,#bfdbfe);">
                        <i class="bi bi-people-fill fs-4 text-primary"></i>
                    </div>
                    <div class="fw-bold fs-4 text-dark dark-text"> {{ $totalSiswa }}</div>
                    <div class="text-muted small">Total Siswa</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="card border-0 shadow-sm h-100 wk-stat-card wk-bg-white" style="border-radius:16px;">
                <div class="card-body p-4 text-center">
                    <div class="stat-icon mb-3 mx-auto d-flex align-items-center justify-content-center rounded-circle wk-icon-green" style="width:58px; height:58px; background:linear-gradient(135deg,#dcfce7,#bbf7d0);">
                        <i class="bi bi-graph-up-arrow fs-4 text-success"></i>
                    </div>
                    <div class="fw-bold fs-4 text-dark dark-text">{{ isset($gradeStats['average']) ? number_format($gradeStats['average'], 1) : '-' }}</div>
                    <div class="text-muted small">Rata-rata Kelas</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="card border-0 shadow-sm h-100 wk-stat-card wk-bg-white" style="border-radius:16px;">
                <div class="card-body p-4 text-center">
                    <div class="stat-icon mb-3 mx-auto d-flex align-items-center justify-content-center rounded-circle wk-icon-yellow" style="width:58px; height:58px; background:linear-gradient(135deg,#fef9c3,#fef08a);">
                        <i class="bi bi-arrow-up-circle-fill fs-4 text-warning"></i>
                    </div>
                    <div class="fw-bold fs-4 text-dark dark-text">{{ isset($gradeStats['highest']) ? number_format($gradeStats['highest'], 1) : '-' }}</div>
                    <div class="text-muted small">Nilai Tertinggi</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="card border-0 shadow-sm h-100 wk-stat-card wk-bg-white" style="border-radius:16px;">
                <div class="card-body p-4 text-center">
                    <div class="stat-icon mb-3 mx-auto d-flex align-items-center justify-content-center rounded-circle wk-icon-red" style="width:58px; height:58px; background:linear-gradient(135deg,#fee2e2,#fecaca);">
                        <i class="bi bi-arrow-down-circle-fill fs-4 text-danger"></i>
                    </div>
                    <div class="fw-bold fs-4 text-dark dark-text">{{ isset($gradeStats['lowest']) ? number_format($gradeStats['lowest'], 1) : '-' }}</div>
                    <div class="text-muted small">Nilai Terendah</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════ MENU WALI KELAS ══════════════════ --}}
    <h6 class="fw-bold text-dark mb-3">
        <i class="bi bi-lightning-charge-fill text-warning me-2"></i>Menu Wali Kelas
    </h6>
    <div class="row row-cols-2 row-cols-md-5 g-3 mb-4">
        @php
            $menus = [
                ['route' => route('walikelas.nilai.index', $kelas->id_kelas), 'icon' => 'clipboard-check-fill', 'color' => '#3b82f6', 'bg' => '#dbeafe', 'bg_class' => 'wk-icon-blue', 'label' => 'Rekap Nilai', 'sub' => 'Siswa'],
                ['route' => route('walikelas.ekstrakurikuler.index', $kelas->id_kelas), 'icon' => 'trophy-fill', 'color' => '#22c55e', 'bg' => '#dcfce7', 'bg_class' => 'wk-icon-green', 'label' => 'Ekstrakurikuler', 'sub' => 'Nilai Ekskul'],
                ['route' => route('walikelas.kokurikuler.index', $kelas->id_kelas), 'icon' => 'award-fill', 'color' => '#a855f7', 'bg' => '#f3e8ff', 'bg_class' => 'wk-icon-purple', 'label' => 'Kokurikuler', 'sub' => 'P5'],
                ['route' => route('walikelas.catatan.index', $kelas->id_kelas), 'icon' => 'journal-text', 'color' => '#f59e0b', 'bg' => '#fef3c7', 'bg_class' => 'wk-icon-yellow', 'label' => 'Catatan', 'sub' => 'Per Siswa'],
                ['route' => route('walikelas.rapor.index', $kelas->id_kelas), 'icon' => 'file-earmark-text-fill', 'color' => '#06b6d4', 'bg' => '#cffafe', 'bg_class' => 'wk-icon-cyan', 'label' => 'E-Rapor', 'sub' => 'Lihat & Kirim'],
                ['route' => route('walikelas.leger.select-kelas'), 'icon' => 'grid-3x3-gap-fill', 'color' => '#64748b', 'bg' => '#f1f5f9', 'bg_class' => 'wk-badge-gray', 'label' => 'Leger Nilai', 'sub' => 'Rekap Lengkap'],
                ['route' => route('walikelas.ranking', $kelas->id_kelas), 'icon' => 'bar-chart-line-fill', 'color' => '#ef4444', 'bg' => '#fee2e2', 'bg_class' => 'wk-icon-red', 'label' => 'Ranking', 'sub' => 'Peringkat'],
            ];
        @endphp
        @foreach($menus as $menu)
        <div class="col">
            <a href="{{ $menu['route'] }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm h-100 text-center wk-menu-card wk-bg-white" style="border-radius:16px;">
                    <div class="card-body p-3">
                        <div class="mx-auto mb-2 d-flex align-items-center justify-content-center rounded-circle {{ $menu['bg_class'] }}" style="width:46px; height:46px; background:{{ $menu['bg'] }};">
                            <i class="bi bi-{{ $menu['icon'] }}" style="color:{{ $menu['color'] }}; font-size:1.25rem;"></i>
                        </div>
                        <div class="fw-bold text-dark mb-0" style="font-size:0.85rem;">{{ $menu['label'] }}</div>
                        <div class="text-muted" style="font-size:0.78rem;">{{ $menu['sub'] }}</div>
                    </div>
                </div>
            </a>
        </div>
        @endforeach
    </div>

    {{-- ══════════════════ JADWAL & KELAS MENGAJAR ══════════════════ --}}
    <div class="row g-4 mb-4">

        <!-- Left Side: Weekly Schedule -->
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm h-100 wk-bg-white" style="border-radius: 20px; background-color: var(--custom-card-bg, #ffffff);">
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
                                        id="wktab-{{ $index }}" 
                                        data-bs-toggle="tab" 
                                        data-bs-target="#wkcontent-{{ $index }}" 
                                        type="button" 
                                        role="tab" 
                                        aria-controls="wkcontent-{{ $index }}" 
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
                    <div class="tab-content" id="wkScheduleTabsContent">
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
                                 id="wkcontent-{{ $index }}" 
                                 role="tabpanel" 
                                 aria-labelledby="wktab-{{ $index }}">
                                 
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

        {{-- Daftar Kelas Mengajar --}}
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm h-100" style="border-radius:16px;">
                <div class="card-header border-0 bg-white wk-bg-white py-3 px-4">
                    <h6 class="mb-0 fw-bold text-dark">
                        <i class="bi bi-journal-text text-success me-2"></i>Daftar Kelas Mengajar
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="font-size:0.88rem;">
                            <thead>
                                <tr class="wk-bg-light" style="background:var(--custom-header-bg, #f8fafc);">
                                    <th class="py-3 px-4 text-muted fw-semibold border-0">Mata Pelajaran</th>
                                    <th class="py-3 text-muted fw-semibold border-0">Kelas</th>
                                    <th class="py-3 text-end px-4 text-muted fw-semibold border-0">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($guruMapels as $gm)
                                    <tr>
                                        <td class="py-3 px-4 fw-bold text-dark">{{ $gm->mapel->nama }}</td>
                                        <td class="py-3">
                                            <span class="badge rounded-pill fw-bold px-3 py-1 wk-badge-blue" style="background:#dbeafe; color:#1d4ed8; font-size:0.78rem;">
                                                {{ $gm->kelas->nama_kelas }}
                                            </span>
                                        </td>
                                        <td class="py-3 text-end px-4">
                                            <a href="{{ route('guru.nilai.select-mapel', $gm->id_kelas) }}"
                                               class="btn btn-sm rounded-pill fw-bold px-3 wk-btn-green"
                                               style="background:#f0fdf4; color:#16a34a; border:1px solid #bbf7d0; font-size:0.78rem;">
                                                <i class="bi bi-pencil-square me-1"></i>Input Nilai
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-5 text-muted">
                                            <i class="bi bi-clipboard-x display-5 mb-2 d-block opacity-30"></i>
                                            <small>Belum ada penugasan mengajar aktif.</small>
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

    {{-- ══════════════════ STATUS RAPOR PROGRESS ══════════════════ --}}
    @if($totalSiswa > 0)
    <div class="card border-0 shadow-sm mb-4" style="border-radius:16px;">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="mb-0 fw-bold text-dark">
                    <i class="bi bi-send-check-fill text-success me-2"></i>Progress Pengiriman E-Rapor
                </h6>
                <a href="{{ route('walikelas.rapor.index', $kelas->id_kelas) }}" class="btn btn-sm rounded-pill px-3 fw-bold" style="background:#f0fdf4; color:#16a34a; border:1px solid #bbf7d0; font-size:0.78rem;">
                    Kelola Rapor <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
            @php
                $sentCount = $raporStats['dikirim'] + $raporStats['diverifikasi'];
                $pct = $totalSiswa > 0 ? round($sentCount / $totalSiswa * 100) : 0;
            @endphp
            <div class="d-flex gap-4 mb-3 flex-wrap">
                <div class="d-flex align-items-center gap-2">
                    <span style="width:10px; height:10px; border-radius:50%; background:#22c55e; display:inline-block;"></span>
                    <small class="text-muted">Dikirim: <strong class="text-dark">{{ $raporStats['dikirim'] }}</strong></small>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span style="width:10px; height:10px; border-radius:50%; background:#3b82f6; display:inline-block;"></span>
                    <small class="text-muted">Diverifikasi: <strong class="text-dark">{{ $raporStats['diverifikasi'] }}</strong></small>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span style="width:10px; height:10px; border-radius:50%; background:var(--custom-header-bg, #e2e8f0); display:inline-block;"></span>
                    <small class="text-muted">Draft: <strong class="text-dark">{{ $raporStats['draft'] }}</strong></small>
                </div>
            </div>
            <div class="progress rounded-pill" style="height:10px; background:var(--custom-header-bg, #f1f5f9);">
                <div class="progress-bar rounded-pill"
                     style="width:{{ $pct }}%; background:linear-gradient(90deg,#22c55e,#16a34a); transition: width 1s ease;"
                     role="progressbar"
                     aria-valuenow="{{ $pct }}" aria-valuemin="0" aria-valuemax="100">
                </div>
            </div>
            <small class="text-muted mt-1 d-block">{{ $sentCount }} dari {{ $totalSiswa }} siswa — {{ $pct }}% terkirim</small>
        </div>
    </div>
    @endif

    @else
    {{-- Belum ditugaskan sebagai wali kelas --}}
    <div class="card border-0 shadow-sm" style="border-radius:16px;">
        <div class="card-body text-center py-5">
            <i class="bi bi-exclamation-triangle" style="font-size: 3.5rem; color: #f59e0b;"></i>
            <h4 class="text-muted mt-3">Belum Ada Kelas</h4>
            <p class="text-muted">Anda belum ditugaskan sebagai wali kelas. Hubungi admin untuk pengaturan.</p>
        </div>
    </div>
    @endif

</div>

<style>
    .wk-stat-card,
    .wk-menu-card {
        transition: all 0.25s ease;
        cursor: default;
    }
    .wk-stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(0,0,0,0.1) !important;
    }
    .wk-menu-card {
        cursor: pointer;
    }
    .wk-menu-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 28px rgba(0,0,0,0.12) !important;
    }
</style>
@endsection
