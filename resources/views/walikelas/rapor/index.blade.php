@extends('layouts.app')

@section('title', 'E-Rapor Kelas - ' . ($kelas->nama_kelas ?? ''))

@section('content')
<style>
    /* Dark Mode Overrides for E-Rapor */
    :root {
        --alert-danger-bg: #fef2f2; --alert-danger-border: #fecaca; --alert-danger-icon-bg: #fee2e2; --alert-danger-text: #991b1b; --alert-danger-icon: #dc2626;
        --alert-warning-bg: #fffbeb; --alert-warning-border: #fde68a; --alert-warning-icon-bg: #fef3c7; --alert-warning-text: #92400e; --alert-warning-icon: #d97706;
        --alert-success-bg: #f0fdf4; --alert-success-border: #bbf7d0; --alert-success-icon-bg: #dcfce7; --alert-success-text: #14532d; --alert-success-icon: #16a34a;
        --table-header-bg: #f8fafc; --table-header-border: #e2e8f0;
    }
    html.dark-mode:root {
        --alert-danger-bg: rgba(220, 38, 38, 0.1); --alert-danger-border: rgba(220, 38, 38, 0.2); --alert-danger-icon-bg: rgba(220, 38, 38, 0.2); --alert-danger-text: #fca5a5; --alert-danger-icon: #ef4444;
        --alert-warning-bg: rgba(217, 119, 6, 0.1); --alert-warning-border: rgba(217, 119, 6, 0.2); --alert-warning-icon-bg: rgba(217, 119, 6, 0.2); --alert-warning-text: #fcd34d; --alert-warning-icon: #f59e0b;
        --alert-success-bg: rgba(22, 163, 74, 0.1); --alert-success-border: rgba(22, 163, 74, 0.2); --alert-success-icon-bg: rgba(22, 163, 74, 0.2); --alert-success-text: #86efac; --alert-success-icon: #22c55e;
        --table-header-bg: #0f172a; --table-header-border: rgba(255,255,255,0.1);
    }
    html.dark-mode .rapor-table th { background: var(--table-header-bg) !important; border-bottom: 2px solid var(--table-header-border) !important; }
    
    /* Dynamic Badges & Buttons for Light & Dark Mode */
    .badge-ok { background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; }
    .badge-err { background: var(--custom-card-bg, #ffffff)1f2; color: #e11d48; border: 1px solid #fecdd3; }
    .st-dikirim { background: #dbeafe; color: #1d4ed8; }
    .st-diverifikasi { background: #dcfce7; color: #15803d; }
    .st-draft { background: var(--custom-header-bg, #f1f5f9); color: #64748b; }
    .btn-lihat { background: #eff6ff; color: #3b82f6; border: 1px solid #bfdbfe; }
    .btn-pdf { background: var(--custom-card-bg, #ffffff)1f2; color: #e11d48; border: 1px solid #fecdd3; }
    .btn-kirim { background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; }
    .btn-verifikasi { background: #fefce8; color: #ca8a04; border: 1px solid #fde68a; }
    .btn-revert { background: var(--custom-header-bg, #f8fafc); color: #94a3b8; border: 1px solid #e2e8f0; }

    /* Dark Mode Overrides */
    html.dark-mode .badge-ok { background: rgba(22,163,74,0.15); color: #4ade80; border-color: rgba(74,222,128,0.2); }
    html.dark-mode .badge-err { background: rgba(225,29,72,0.15); color: #fb7185; border-color: rgba(251,113,133,0.2); }
    html.dark-mode .st-dikirim { background: rgba(59,130,246,0.15); color: #60a5fa; }
    html.dark-mode .st-diverifikasi { background: rgba(21,128,61,0.15); color: #4ade80; }
    html.dark-mode .st-draft { background: rgba(100,116,139,0.15); color: #94a3b8; }
    html.dark-mode .btn-lihat { background: rgba(59,130,246,0.15); color: #60a5fa; border-color: rgba(96,165,250,0.2); }
    html.dark-mode .btn-pdf { background: rgba(225,29,72,0.15); color: #fb7185; border-color: rgba(251,113,133,0.2); }
    html.dark-mode .btn-kirim { background: rgba(22,163,74,0.15); color: #4ade80; border-color: rgba(74,222,128,0.2); }
    html.dark-mode .btn-verifikasi { background: rgba(202,138,4,0.15); color: #facc15; border-color: rgba(250,204,21,0.2); }
    html.dark-mode .btn-revert { background: rgba(148,163,184,0.15); color: #cbd5e1; border-color: rgba(203,213,225,0.2); }
    
    html.dark-mode .rapor-row { border-bottom-color: rgba(255,255,255,0.05) !important; }
    html.dark-mode .text-dikirim { color: #cbd5e1 !important; }
</style>
<div class="container-fluid py-3">

    {{-- ══════ HERO HEADER ══════ --}}
    <div class="card border-0 shadow mb-4" style="border-radius:18px; background:linear-gradient(135deg,#0f172a 0%,#1e3a5f 60%,#0e4f6e 100%); overflow:hidden; position:relative;">
        <div style="position:absolute;top:-50px;right:-50px;width:200px;height:200px;background:rgba(255,255,255,0.04);border-radius:50%;pointer-events:none;"></div>
        <div class="card-body p-4 text-white position-relative">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div class="d-flex align-items-center gap-3">
                    <a href="{{ route('walikelas.dashboard') }}"
                       class="btn btn-sm d-flex align-items-center justify-content-center"
                       style="background:rgba(255,255,255,0.12); border:0; color:white; width:36px; height:36px; border-radius:10px;">
                        <i class="bi bi-arrow-left"></i>
                    </a>
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="badge px-2 py-1 rounded-pill" style="background:rgba(59,130,246,0.3); color:#93c5fd; font-size:0.7rem; letter-spacing:1px;">E-RAPOR</span>
                            <span class="badge px-2 py-1 rounded-pill" style="background:rgba(255,255,255,0.1); color:rgba(255,255,255,0.7); font-size:0.7rem;">Semester {{ $activeTahunAjaran->id_semester }}</span>
                        </div>
                        <h5 class="fw-bold mb-0">Kelas {{ $kelas->nama_kelas ?? '-' }}</h5>
                        <small style="color:rgba(255,255,255,0.55);">Tahun Pelajaran {{ $activeTahunAjaran->tahun_ajaran }}</small>
                    </div>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ route('walikelas.rapor.download-all', $kelas->id_kelas) }}"
                       class="btn btn-sm fw-semibold px-3 rounded-pill"
                       style="background:rgba(255,255,255,0.12); border:1px solid rgba(255,255,255,0.2); color:white; font-size:0.82rem;">
                        <i class="bi bi-file-earmark-zip me-1"></i> Unduh Semua
                    </a>
                    <form action="{{ route('walikelas.rapor.kirim-semua', $kelas->id_kelas) }}" method="POST" class="d-inline"
                          data-confirm="Kirim semua E-Rapor kelas ini ke Orang Tua dan Kurikulum?">
                        @csrf
                        <button type="submit" class="btn btn-sm fw-semibold px-3 rounded-pill"
                                style="background:linear-gradient(135deg,#22c55e,#16a34a); border:0; color:white; font-size:0.82rem;">
                            <i class="bi bi-send-check me-1"></i> Kirim Semua
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════ ALERT STATUS NILAI ══════ --}}
    @php
        $hasIncomplete = false;
        foreach($siswas as $s) {
            if(!($hasGrades->get($s->id_siswa, false))) {
                $hasIncomplete = true;
                break;
            }
        }
    @endphp

    @if($hasIncomplete)
        <div class="d-flex align-items-center gap-3 p-3 mb-3 rounded-3"
             style="background:var(--alert-danger-bg); border:1px solid var(--alert-danger-border);">
            <div class="d-flex align-items-center justify-content-center rounded-circle flex-shrink-0"
                 style="width:36px;height:36px;background:var(--alert-danger-icon-bg);">
                <i class="bi bi-x-circle-fill" style="color:var(--alert-danger-icon);"></i>
            </div>
            <div class="flex-grow-1">
                <span class="fw-semibold" style="color:var(--alert-danger-text); font-size:0.88rem;">Data nilai akademik belum lengkap.</span>
                <span class="text-muted ms-1" style="font-size:0.82rem;">Pastikan guru mapel telah menginput nilai untuk semua mata pelajaran.</span>
            </div>
            <a href="{{ route('walikelas.nilai.index', $kelas->id_kelas) }}"
               class="btn btn-sm fw-bold rounded-pill px-3 flex-shrink-0"
               style="background:#dc2626; border:0; color:white; font-size:0.78rem;">
                <i class="bi bi-eye me-1"></i> Cek Rekap
            </a>
        </div>
    @elseif(isset($hasUnlockedGrades) && $hasUnlockedGrades)
        <div class="d-flex align-items-center gap-3 p-3 mb-3 rounded-3"
             style="background:var(--alert-warning-bg); border:1px solid var(--alert-warning-border);">
            <div class="d-flex align-items-center justify-content-center rounded-circle flex-shrink-0"
                 style="width:36px;height:36px;background:var(--alert-warning-icon-bg);">
                <i class="bi bi-exclamation-triangle-fill" style="color:var(--alert-warning-icon);"></i>
            </div>
            <div class="flex-grow-1">
                <span class="fw-semibold" style="color:var(--alert-warning-text); font-size:0.88rem;">Ada nilai yang belum dikunci.</span>
                <span class="text-muted ms-1" style="font-size:0.82rem;">Kunci semua nilai sebelum mengirim E-Rapor.</span>
            </div>
            <a href="{{ route('walikelas.nilai.index', $kelas->id_kelas) }}"
               class="btn btn-sm fw-bold rounded-pill px-3 flex-shrink-0"
               style="background:#f59e0b; border:0; color:white; font-size:0.78rem;">
                <i class="bi bi-lock me-1"></i> Kunci Nilai
            </a>
        </div>
    @else
        <div class="d-flex align-items-center gap-3 p-3 mb-3 rounded-3"
             style="background:var(--alert-success-bg); border:1px solid var(--alert-success-border);">
            <div class="d-flex align-items-center justify-content-center rounded-circle flex-shrink-0"
                 style="width:36px;height:36px;background:var(--alert-success-icon-bg);">
                <i class="bi bi-check-circle-fill" style="color:var(--alert-success-icon);"></i>
            </div>
            <div style="font-size:0.88rem;">
                <span class="fw-semibold" style="color:var(--alert-success-text);">Semua nilai sudah lengkap dan terkunci.</span>
                <span class="text-muted ms-1">E-Rapor siap dikirim ke orang tua dan kurikulum.</span>
            </div>
        </div>
    @endif

    {{-- ══════ TABEL SISWA ══════ --}}
    <div class="card border-0 shadow-sm" style="border-radius:16px; overflow:hidden;">
        <div class="card-header border-0 py-3 px-4 d-flex align-items-center justify-content-between rapor-table-header"
             style="background:var(--table-header-bg);">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-people-fill text-primary"></i>
                <span class="fw-bold text-dark" style="font-size:0.9rem;">Daftar Siswa & Status Pengiriman Rapor</span>
            </div>
            <span class="badge rounded-pill px-3" style="background:var(--custom-header-bg, #e2e8f0); color:#64748b; font-size:0.75rem;">
                {{ count($siswas) }} siswa
            </span>
        </div>

        <div class="card-body p-0">
            @if(count($siswas) > 0)
                <div class="table-responsive">
                    <table class="table mb-0 rapor-table" style="font-size:0.84rem;">
                        <thead>
                            <tr style="background:var(--table-header-bg); border-bottom:2px solid var(--table-header-border);">
                                <th class="py-3 px-3 text-center text-muted fw-semibold" style="width:44px;">#</th>
                                <th class="py-3 px-3 text-muted fw-semibold" style="min-width:180px;">Nama Siswa</th>
                                <th class="py-3 px-3 text-muted fw-semibold">NISN</th>
                                <th class="py-3 px-3 text-center text-muted fw-semibold">Kelengkapan</th>
                                <th class="py-3 px-3 text-center text-muted fw-semibold">Status</th>
                                <th class="py-3 px-3 text-muted fw-semibold">Dikirim Oleh</th>
                                <th class="py-3 px-3 text-center text-muted fw-semibold" style="width:120px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($siswas as $idx => $siswa)
                                @php
                                    $rapor       = $raporStatuses[$siswa->id_siswa] ?? null;
                                    $status      = $rapor->status ?? 'draft';

                                    $statusStyle = match($status) {
                                        'dikirim'     => 'st-dikirim',
                                        'diverifikasi'=> 'st-diverifikasi',
                                        default       => 'st-draft',
                                    };
                                    $statusIcon = match($status) {
                                        'dikirim'     => 'send-check-fill',
                                        'diverifikasi'=> 'patch-check-fill',
                                        default       => 'file-earmark-text',
                                    };
                                    $statusText = match($status) {
                                        'dikirim'     => 'Dikirim',
                                        'diverifikasi'=> 'Terverifikasi',
                                        default       => 'Draft',
                                    };

                                    $isAkademikOk   = $hasGrades->get($siswa->id_siswa, false);
                                    $isEkskulOk     = $hasEkskul->get($siswa->id_siswa, false);
                                    $isCatatanOk    = isset($hasCatatan[$siswa->id_siswa]);
                                    $isKokurikulerOk= $hasKokurikuler->get($siswa->id_siswa, false);
                                    $isAllComplete  = $isAkademikOk && $isEkskulOk && $isCatatanOk && $isKokurikulerOk;

                                    $checks = [
                                        ['ok' => $isAkademikOk,    'label' => 'Nilai', 'icon' => 'journal-bookmark'],
                                        ['ok' => $isEkskulOk,      'label' => 'Ekskul','icon' => 'trophy'],
                                        ['ok' => $isCatatanOk,     'label' => 'Absen', 'icon' => 'calendar-check'],
                                        ['ok' => $isKokurikulerOk, 'label' => 'P5',    'icon' => 'award'],
                                    ];
                                @endphp
                                <tr class="rapor-row" style="border-bottom:1px solid #f1f5f9;">
                                    {{-- No --}}
                                    <td class="py-3 px-3 text-center" style="color:#94a3b8; font-size:0.8rem; font-weight:600;">{{ $idx + 1 }}</td>

                                    {{-- Nama --}}
                                    <td class="py-3 px-3">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="d-flex align-items-center justify-content-center rounded-circle flex-shrink-0"
                                                 style="width:32px;height:32px;background:linear-gradient(135deg,#dbeafe,#bfdbfe); font-size:0.75rem; font-weight:700; color:#1d4ed8;">
                                                {{ strtoupper(substr($siswa->nama, 0, 1)) }}
                                            </div>
                                            <span class="fw-semibold text-dark" style="font-size:0.85rem;">{{ $siswa->nama }}</span>
                                        </div>
                                    </td>

                                    {{-- NISN --}}
                                    <td class="py-3 px-3" style="color:#64748b; font-size:0.82rem; letter-spacing:0.3px; font-family:monospace;">
                                        {{ $siswa->nisn ?? '-' }}
                                    </td>

                                    {{-- Kelengkapan --}}
                                    <td class="py-3 px-3 text-center">
                                        <div class="d-inline-flex gap-1">
                                            @foreach($checks as $c)
                                                <span title="{{ $c['label'] }}"
                                                      class="{{ $c['ok'] ? 'badge-ok' : 'badge-err' }}"
                                                      style="display:inline-flex;align-items:center;gap:3px;padding:3px 8px;border-radius:20px;font-size:0.72rem;font-weight:600;">
                                                    <i class="bi bi-{{ $c['ok'] ? 'check2' : 'x' }}"></i>
                                                    {{ $c['label'] }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </td>

                                    {{-- Status --}}
                                    <td class="py-3 px-3 text-center">
                                        <span class="{{ $statusStyle }}" style="display:inline-flex;align-items:center;gap:5px;padding:4px 12px;border-radius:20px;font-size:0.76rem;font-weight:700;">
                                            <i class="bi bi-{{ $statusIcon }}" style="font-size:0.78rem;"></i>
                                            {{ $statusText }}
                                        </span>
                                    </td>

                                    {{-- Dikirim Oleh --}}
                                    <td class="py-3 px-3">
                                        @if($rapor && $rapor->dikirim_oleh)
                                            <span class="text-dikirim" style="font-size:0.78rem; color: #475569;">{{ $rapor->dikirim_oleh }}</span>
                                        @else
                                            <span class="text-dikirim" style="font-size:0.78rem; color:#cbd5e1;">—</span>
                                        @endif
                                    </td>

                                    {{-- Aksi --}}
                                    <td class="py-3 px-3">
                                        <div class="d-flex align-items-center justify-content-center gap-1">
                                            {{-- Lihat --}}
                                            <a href="{{ route('walikelas.rapor.view', $siswa->id_siswa) }}"
                                               title="Lihat Rapor"
                                               class="action-btn btn-lihat">
                                                <i class="bi bi-eye-fill"></i>
                                            </a>

                                            {{-- PDF --}}
                                            <a href="{{ route('walikelas.rapor.pdf', $siswa->id_siswa) }}"
                                               title="Download PDF" target="_blank"
                                               class="action-btn btn-pdf">
                                                <i class="bi bi-filetype-pdf"></i>
                                            </a>

                                            {{-- Send / Verify / Revert --}}
                                            @if($status === 'draft')
                                                <form action="{{ route('walikelas.rapor.kirim') }}" method="POST" class="m-0"
                                                      onsubmit="return confirm('Kirim rapor {{ addslashes($siswa->nama) }}?')">
                                                    @csrf
                                                    <input type="hidden" name="id_siswa" value="{{ $siswa->id_siswa }}">
                                                    <input type="hidden" name="status" value="dikirim">
                                                    <button type="submit" title="Kirim ke Orang Tua"
                                                            class="action-btn btn-kirim">
                                                        <i class="bi bi-send-fill"></i>
                                                    </button>
                                                </form>

                                            @elseif($status === 'dikirim')
                                                <form action="{{ route('walikelas.rapor.kirim') }}" method="POST" class="m-0">
                                                    @csrf
                                                    <input type="hidden" name="id_siswa" value="{{ $siswa->id_siswa }}">
                                                    <input type="hidden" name="status" value="diverifikasi">
                                                    <button type="submit" title="Verifikasi"
                                                            class="action-btn btn-verifikasi">
                                                        <i class="bi bi-patch-check-fill"></i>
                                                    </button>
                                                </form>
                                                <form action="{{ route('walikelas.rapor.kirim') }}" method="POST" class="m-0">
                                                    @csrf
                                                    <input type="hidden" name="id_siswa" value="{{ $siswa->id_siswa }}">
                                                    <input type="hidden" name="status" value="draft">
                                                    <button type="submit" title="Tarik ke Draft"
                                                            class="action-btn btn-revert">
                                                        <i class="bi bi-arrow-counterclockwise"></i>
                                                    </button>
                                                </form>

                                            @elseif($status === 'diverifikasi')
                                                <form action="{{ route('walikelas.rapor.kirim') }}" method="POST" class="m-0">
                                                    @csrf
                                                    <input type="hidden" name="id_siswa" value="{{ $siswa->id_siswa }}">
                                                    <input type="hidden" name="status" value="draft">
                                                    <button type="submit" title="Batalkan Verifikasi"
                                                            class="action-btn btn-revert">
                                                        <i class="bi bi-x-circle-fill"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <div style="font-size:3.5rem; opacity:0.2;">👥</div>
                    <h6 class="text-muted mt-3">Belum Ada Data Siswa</h6>
                    <p class="text-muted small">Siswa tidak ditemukan untuk kelas ini.</p>
                </div>
            @endif
        </div>
    </div>

</div>

<style>
    .rapor-table thead th { border:0; }
    .rapor-row { transition: background 0.15s; }
    .rapor-row:hover { background: var(--custom-header-bg, #f8fafc) !important; }
    .rapor-row:last-child { border-bottom: 0 !important; }

    .action-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 28px;
        height: 28px;
        border-radius: 8px;
        font-size: 0.78rem;
        text-decoration: none;
        cursor: pointer;
        transition: all 0.15s ease;
        padding: 0;
    }
    .action-btn:hover {
        filter: brightness(0.9);
        transform: scale(1.1);
    }
    button.action-btn { background: none; }
</style>
@endsection
