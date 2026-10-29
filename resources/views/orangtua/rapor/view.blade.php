@extends('layouts.app')

@section('title', 'E-Rapor - ' . $siswa->nama)

@section('content')
<div class="container-fluid py-3">
    {{-- Header Bar --}}
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; background-color: #1e293b; color: white;">
        <div class="card-body py-3 px-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div class="d-flex align-items-center">
                <a href="{{ route('orangtua.rapor.index') }}" class="btn btn-sm me-3 border-0 text-white-50">
                    <i class="bi bi-arrow-left fs-5"></i>
                </a>
                <div>
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-file-earmark-text-fill me-2"></i>E-Rapor: {{ $siswa->nama }}
                    </h5>
                    <small class="text-white-50">
                        {{ $activeTahunAjaran->tahun_ajaran }} — Semester {{ $activeTahunAjaran->id_semester == 1 ? 'Ganjil' : 'Genap' }}
                    </small>
                </div>
            </div>
            <div>
                @php
                    $raporStatusObj = \App\Models\RaporStatus::where('id_siswa', $siswa->id_siswa)
                        ->where('id_tahun_ajaran', $activeTahunAjaran->id_tahun_ajaran)
                        ->where('id_semester', $activeTahunAjaran->id_semester)
                        ->first();
                    $status = $raporStatusObj->status ?? 'draft';

                    $badgeClass = match($status) {
                        'dikirim' => 'bg-primary',
                        'diverifikasi' => 'bg-success',
                        default => 'bg-secondary'
                    };
                    $badgeIcon = match($status) {
                        'dikirim' => 'bi-send-fill',
                        'diverifikasi' => 'bi-patch-check-fill',
                        default => 'bi-hourglass-split'
                    };
                @endphp
                <span class="badge {{ $badgeClass }} rounded-pill px-3 py-2 fw-semibold">
                    <i class="bi {{ $badgeIcon }} me-1"></i>{{ ucfirst($status) }}
                </span>
            </div>
        </div>
    </div>

    {{-- Student Bio --}}
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 col-12 mb-3 mb-md-0">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td class="fw-bold text-muted" style="width: 180px;">Nama Peserta Didik</td>
                            <td class="text-dark">: {{ $siswa->nama }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">NISN / NIS</td>
                            <td class="text-dark">: {{ $siswa->nisn }} / {{ $siswa->nis ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6 col-12">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td class="fw-bold text-muted" style="width: 180px;">Kelas</td>
                            <td class="text-dark">: {{ $siswa->kelas->nama_kelas ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Konsentrasi Keahlian</td>
                            <td class="text-dark">: {{ $siswa->kelas->jurusan->nama_konsentrasi ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>    {{-- Section A: Nilai Akademik --}}
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
        <div class="card-header bg-white py-3 border-bottom border-light" style="border-radius: 12px 12px 0 0 !important;">
            <h6 class="mb-0 fw-bold text-secondary"><i class="bi bi-bookmark-star-fill me-2"></i>A. Nilai Akademik</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle mb-0">
                    <thead class="table-light text-center">
                        <tr>
                            <th style="width: 60px;">No</th>
                            <th style="width: 30%;">Mata Pelajaran</th>
                            <th style="width: 100px;">Nilai Akhir</th>
                            <th style="width: 100px;">Predikat</th>
                            <th>Deskripsi Capaian Kompetensi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            // Dynamically map groups
                            $groupedMapels = $nilaiTps->groupBy(function($grades) {
                                $kel = $grades->first()->mapel->kelompok_mapel ?? 'A';
                                if (in_array($kel, ['A', 'Kelompok A', 'Muatan Nasional'])) return 'Kelompok Mata Pelajaran Umum';
                                if (in_array($kel, ['C', 'Kelompok C', 'Mata Pelajaran Pilihan'])) return 'Kelompok Mata Pelajaran Pilihan';
                                return 'Kelompok Mata Pelajaran Kejuruan';
                            });
                        @endphp
                        @if($nilaiTps->isEmpty())
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">Belum ada data nilai akademik.</td>
                            </tr>
                        @else
                            @foreach($groupedMapels as $title => $filtered)
                                @php
                                    $no = 1;
                                @endphp
                                @if($filtered->isNotEmpty())
                                    <tr class="table-light">
                                        <td colspan="5" class="fw-bold ps-3 text-dark text-uppercase small" style="letter-spacing: 0.5px;">{{ $title }}</td>
                                    </tr>
                                    @foreach($filtered as $mapelId => $grades)
                                        @php
                                            $firstGrade = $grades->first();
                                            $actualMapelId = $firstGrade->id_mapel;
                                            // Fix: use nilai_akhir_tp instead of nilai_tp
                                            $tpValues = $grades->pluck('nilai_akhir_tp')->filter(fn($v) => $v !== null);
                                            $nilaiAkhir = $tpValues->isNotEmpty() ? round($tpValues->avg(), 0) : 0;
                                            
                                            $predikat = match(true) {
                                                $nilaiAkhir >= 90 => 'A',
                                                $nilaiAkhir >= 80 => 'B',
                                                $nilaiAkhir >= 70 => 'C',
                                                $nilaiAkhir >= 60 => 'D',
                                                default => 'E'
                                            };
                                        @endphp
                                        <tr>
                                            <td class="text-center fw-bold text-muted">{{ $no++ }}</td>
                                            <td class="fw-bold text-dark">{{ $firstGrade->mapel->nama ?? $firstGrade->mapel->nama_mapel ?? 'Mapel' }}</td>
                                            <td class="text-center fw-bold fs-5 text-primary">{{ number_format($nilaiAkhir, 0) }}</td>
                                            <td class="text-center"><span class="badge bg-light text-dark border">{{ $predikat }}</span></td>
                                            <td class="small text-muted">{{ $descriptions[$actualMapelId] ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                @endif
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Section B: Kegiatan Kokurikuler (P5) --}}
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
        <div class="card-header bg-white py-3 border-bottom border-light" style="border-radius: 12px 12px 0 0 !important;">
            <h6 class="mb-0 fw-bold text-secondary"><i class="bi bi-award-fill me-2 text-success"></i>B. Kegiatan Kokurikuler (P5)</h6>
        </div>
        <div class="card-body">
            @if($kokurikuler && $kokurikuler->deskripsi)
                <div class="p-3 bg-light rounded text-dark fs-6" style="min-height: 80px; border-left: 4px solid #10b981;">
                    {{ $kokurikuler->deskripsi }}
                </div>
            @else
                <div class="p-3 bg-light rounded text-muted fst-italic text-center py-4">
                    <i class="bi bi-info-circle me-1"></i> Belum ada data projek kokurikuler P5 untuk semester ini.
                </div>
            @endif
        </div>
    </div>

    {{-- Section C: Kegiatan Ekstrakurikuler --}}
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
        <div class="card-header bg-white py-3 border-bottom border-light" style="border-radius: 12px 12px 0 0 !important;">
            <h6 class="mb-0 fw-bold text-secondary"><i class="bi bi-trophy-fill me-2"></i>C. Kegiatan Ekstrakurikuler</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle mb-0">
                    <thead class="table-light text-center">
                        <tr>
                            <th style="width: 60px;">No</th>
                            <th style="width: 50%;">Kegiatan Ekstrakurikuler</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($nilaiEkstrakurikulers as $idx => $ekstra)
                            <tr>
                                <td class="text-center fw-bold text-muted">{{ $idx + 1 }}</td>
                                <td class="fw-bold text-dark">{{ $ekstra->ekstrakurikuler->nama ?? '-' }}</td>
                                <td class="fw-semibold text-dark">{{ $ekstra->keterangan ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted py-4">
                                    <i class="bi bi-inbox fs-4 d-block mb-2"></i>
                                    Belum ada data kegiatan ekstrakurikuler.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Section D & E: Catatan Wali Kelas & Ketidakhadiran --}}
    <div class="row">
        {{-- Section D: Catatan Wali Kelas --}}
        <div class="col-md-8 col-12 mb-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
                <div class="card-header bg-white py-3 border-bottom border-light" style="border-radius: 12px 12px 0 0 !important;">
                    <h6 class="mb-0 fw-bold text-secondary"><i class="bi bi-journal-text me-2"></i>D. Catatan Wali Kelas</h6>
                </div>
                <div class="card-body">
                    @if($catatanWaliKelas && $catatanWaliKelas->catatan)
                        <div class="p-3 rounded text-dark fs-6" style="min-height: 100px; background-color: var(--custom-header-bg, #f8fafc); border: 1px solid #e2e8f0;">
                            {{ $catatanWaliKelas->catatan }}
                        </div>
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-journal fs-4 d-block mb-2"></i>
                            Belum ada catatan wali kelas untuk semester ini.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Section E: Ketidakhadiran --}}
        <div class="col-md-4 col-12 mb-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
                <div class="card-header bg-white py-3 border-bottom border-light" style="border-radius: 12px 12px 0 0 !important;">
                    <h6 class="mb-0 fw-bold text-secondary"><i class="bi bi-clock-fill me-2"></i>E. Ketidakhadiran (Absensi)</h6>
                </div>
                <div class="card-body p-0">
                    <table class="table align-middle table-bordered mb-0">
                        <tr>
                            <td class="fw-bold text-muted ps-3">
                                <i class="bi bi-thermometer-half me-2 text-info"></i>Sakit
                            </td>
                            <td class="text-center fw-bold text-dark" style="width: 100px;">{{ $catatanWaliKelas->sakit ?? 0 }} Hari</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted ps-3">
                                <i class="bi bi-envelope-check me-2 text-warning"></i>Izin
                            </td>
                            <td class="text-center fw-bold text-dark">{{ $catatanWaliKelas->izin ?? 0 }} Hari</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted ps-3">
                                <i class="bi bi-exclamation-triangle me-2 text-danger"></i>Tanpa Keterangan (Alpa)
                            </td>
                            <td class="text-center fw-bold text-danger">{{ $catatanWaliKelas->alpa ?? 0 }} Hari</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @if(isset($activeTahunAjaran) && $activeTahunAjaran->id_semester == 2)
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm" style="border-radius: 12px; border-left: 5px solid #ffc107 !important;">
                    <div class="card-body">
                        <h5 class="fw-bold text-dark mb-3"><i class="bi bi-star-fill text-warning me-2"></i>Keterangan Kenaikan Kelas</h5>
                        @if(isset($catatanWaliKelas) && $catatanWaliKelas->status_kenaikan === 'naik')
                            @php
                                $tingkat = $siswa->kelas->tingkat ?? 'X';
                                $nextTingkat = match($tingkat) {
                                    'X' => 'XI (Sebelas)',
                                    'XI' => 'XII (Dua Belas)',
                                    default => ''
                                };
                                $namaKelas = $siswa->kelas->nama ?? '';
                                $kelasClean = preg_replace('/^(XII|XI|X)\s*/i', '', $namaKelas);
                                $kelasNaik = $nextTingkat . ' ' . trim($kelasClean);
                            @endphp
                            <div class="alert alert-success border-0 mb-0 d-flex align-items-center" style="background-color: #d1e7dd; border-radius: 10px;">
                                <i class="bi bi-check-circle-fill fs-3 me-3 text-success"></i>
                                <div>
                                    <h6 class="fw-bold mb-1 text-success">Naik Kelas</h6>
                                    <p class="mb-0 text-dark">Berdasarkan pencapaian kompetensi pada semester 1 dan 2, peserta didik ditetapkan <strong>Naik ke kelas {{ $kelasNaik }}</strong>.</p>
                                </div>
                            </div>
                        @elseif(isset($catatanWaliKelas) && $catatanWaliKelas->status_kenaikan === 'tinggal')
                            <div class="alert alert-danger border-0 mb-0 d-flex align-items-center" style="background-color: #f8d7da; border-radius: 10px;">
                                <i class="bi bi-x-circle-fill fs-3 me-3 text-danger"></i>
                                <div>
                                    <h6 class="fw-bold mb-1 text-danger">Tinggal Kelas</h6>
                                    <p class="mb-0 text-dark">Berdasarkan pencapaian kompetensi pada semester 1 dan 2, peserta didik ditetapkan <strong>Tinggal di kelas {{ $siswa->kelas->nama ?? '' }}</strong>.</p>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-secondary border-0 mb-0" style="border-radius: 10px;">
                                <i class="bi bi-info-circle-fill me-2"></i> Keterangan kenaikan kelas belum ditetapkan oleh Wali Kelas.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<style>
    @media (max-width: 767.98px) {
        .container-fluid {
            padding-left: 10px;
            padding-right: 10px;
        }
        .card-body {
            padding: 1rem !important;
        }
        table {
            font-size: 0.85rem;
        }
        th, td {
            padding: 0.5rem !important;
        }
        /* Make table headers break cleanly on small screens */
        th {
            white-space: nowrap;
        }
    }
</style>
@endsection
