@extends('layouts.app')

@section('title', 'Detail Rapor - ' . $siswa->nama)

@section('content')
<div class="container-fluid py-3">
    <!-- Top Action Menu Bar -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body py-3 px-4 d-flex justify-content-between align-items-center flex-wrap gap-3 bg-white">
            @php
                $role = auth()->user()->role;
                $routePrefix = in_array($role, ['admin', 'kurikulum']) ? 'admin' : 'walikelas';
            @endphp
            <div class="d-flex align-items-center">
                <a href="{{ $routePrefix === 'admin' ? route('admin.rapor.kelas', $siswa->id_kelas) : route('walikelas.rapor.index', $siswa->id_kelas) }}" class="btn btn-outline-secondary btn-sm me-3 border-0">
                    <i class="bi bi-arrow-left fs-5"></i>
                </a>
                <div>
                    <h5 class="mb-0 fw-bold text-dark">Rapor: {{ $siswa->nama }}</h5>
                    <small class="text-muted">Status: 
                        @php
                            $status = $siswa->catatanWaliKelas->first()->status ?? 'draft';
                            // Let's query status record
                            $raporStatusObj = \App\Models\RaporStatus::where('id_siswa', $siswa->id_siswa)
                                ->where('id_tahun_ajaran', $activeTahunAjaran->id_tahun_ajaran)
                                ->where('id_semester', $activeTahunAjaran->id_semester)
                                ->first();
                            $status = $raporStatusObj->status ?? 'draft';

                            $badgeClass = match($status) {
                                'dikirim' => 'bg-primary text-white',
                                'diverifikasi' => 'bg-success text-white',
                                default => 'bg-secondary text-white'
                            };
                        @endphp
                        <span class="badge {{ $badgeClass }} rounded-pill ms-1 px-2.5 py-1 small fw-semibold">{{ ucfirst($status) }}</span>
                    </small>
                </div>
            </div>
            
            <div class="d-flex align-items-center">
                <!-- Top action buttons (clean, borderless with tab-width spacing) -->
                @if(auth()->user()->role === 'wali_kelas')
                    @if($status === 'draft')
                        <form action="{{ route('walikelas.rapor.kirim') }}" method="POST" class="d-inline">
                            @csrf
                            <input type="hidden" name="id_siswa" value="{{ $siswa->id_siswa }}">
                            <input type="hidden" name="status" value="dikirim">
                            <button type="submit" class="btn btn-success btn-sm border-0 shadow-sm me-3 px-3">
                                <i class="bi bi-send-fill me-1"></i> Kirim Rapor
                            </button>
                        </form>
                    @elseif($status === 'dikirim')
                        <form action="{{ route('walikelas.rapor.kirim') }}" method="POST" class="d-inline">
                            @csrf
                            <input type="hidden" name="id_siswa" value="{{ $siswa->id_siswa }}">
                            <input type="hidden" name="status" value="diverifikasi">
                            <button type="submit" class="btn btn-warning btn-sm border-0 text-white shadow-sm me-3 px-3">
                                <i class="bi bi-patch-check-fill me-1"></i> Verifikasi Rapor
                            </button>
                        </form>
                        <form action="{{ route('walikelas.rapor.kirim') }}" method="POST" class="d-inline">
                            @csrf
                            <input type="hidden" name="id_siswa" value="{{ $siswa->id_siswa }}">
                            <input type="hidden" name="status" value="draft">
                            <button type="submit" class="btn btn-outline-secondary btn-sm border-0 me-3 px-3">
                                <i class="bi bi-arrow-counterclockwise me-1"></i> Tarik ke Draft
                            </button>
                        </form>
                    @elseif($status === 'diverifikasi')
                        <span class="text-success small fw-semibold me-3"><i class="bi bi-check-circle-fill me-1"></i> Terverifikasi</span>
                        <form action="{{ route('walikelas.rapor.kirim') }}" method="POST" class="d-inline">
                            @csrf
                            <input type="hidden" name="id_siswa" value="{{ $siswa->id_siswa }}">
                            <input type="hidden" name="status" value="draft">
                            <button type="submit" class="btn btn-outline-danger btn-sm border-0 me-3 px-3">
                                <i class="bi bi-x-circle-fill me-1"></i> Batal Verifikasi
                            </button>
                        </form>
                    @endif
                @else
                    @if($status === 'diverifikasi')
                        <span class="text-success small fw-semibold me-3"><i class="bi bi-check-circle-fill me-1"></i> Terverifikasi</span>
                    @endif
                @endif

                <a href="{{ route($routePrefix . '.rapor.pdf', $siswa->id_siswa) }}" class="btn btn-danger btn-sm border-0 shadow-sm px-3" target="_blank">
                    <i class="bi bi-file-earmark-pdf-fill me-1"></i> Unduh PDF
                </a>
            </div>
        </div>
    </div>

    <!-- Student Bio -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 col-12 mb-3 mb-md-0">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td class="fw-bold text-muted" style="width: 150px;">Nama Peserta Didik</td>
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
                            <td class="fw-bold text-muted" style="width: 150px;">Kelas</td>
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
    </div>

    <!-- Grades -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3 border-bottom border-light">
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
                            // Group by kelompok_mapel and sort by urutan (same as Excel)
                            $sortedNilai = $nilaiTps->sortBy(function($grades) {
                                $m = $grades->first()->mapel;
                                $kelGrp = $m->kelompok_mapel ?? 'A';
                                $urutan = $m->urutan ?? 999;
                                return $kelGrp . sprintf('%04d', $urutan);
                            });

                            $groupedMapels = $sortedNilai->groupBy(function($grades) {
                                $kel = $grades->first()->mapel->kelompok_mapel ?? 'A';
                                if ($kel === 'A') return 'A. Kelompok Mata Pelajaran Umum';
                                if ($kel === 'B') return 'B. Kelompok Pelajaran Kejuruan';
                                return 'C. Kelompok Pelajaran Pilihan';
                            });

                            // Ensure correct group order
                            $groupOrder = [
                                'A. Kelompok Mata Pelajaran Umum',
                                'B. Kelompok Pelajaran Kejuruan',
                                'C. Kelompok Pelajaran Pilihan',
                            ];
                            $groupedMapels = collect($groupOrder)
                                ->mapWithKeys(fn($g) => [$g => $groupedMapels->get($g, collect())])
                                ->filter(fn($g) => $g->isNotEmpty());
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
                                            <td class="fw-bold text-dark">{{ $firstGrade->mapel->nama ?? 'Mapel' }}</td>
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

    <!-- B. Kegiatan Kokurikuler (P5) -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3 border-bottom border-light">
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

    <!-- Extracurriculars -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3 border-bottom border-light">
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
                                <td colspan="3" class="text-center text-muted py-4">Belum ada data kegiatan ekstrakurikuler.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Wali Kelas Note & Attendance -->
    <div class="row">
        <div class="col-md-8 col-12 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3 border-bottom border-light">
                    <h6 class="mb-0 fw-bold text-secondary"><i class="bi bi-journal-text me-2"></i>D. Catatan Wali Kelas</h6>
                </div>
                <div class="card-body">
                    @if($catatanWaliKelas)
                        <div class="p-3 bg-light rounded text-dark fs-6" style="min-height: 100px;">
                            {{ $catatanWaliKelas->catatan }}
                        </div>
                    @else
                        <div class="text-center text-muted py-4">
                            Belum ada catatan wali kelas untuk semester ini.
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-md-4 col-12 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3 border-bottom border-light d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold text-secondary"><i class="bi bi-clock-fill me-2"></i>E. Ketidakhadiran (Absensi)</h6>
                    @if(auth()->user()->role === 'wali_kelas')
                        <a href="{{ route('walikelas.catatan.index', $siswa->id_kelas) }}" class="btn btn-sm btn-link text-decoration-none p-0 fw-bold" style="font-size: 0.8rem;">
                            <i class="bi bi-pencil-square me-1"></i> Edit Absen
                        </a>
                    @endif
                </div>
                <div class="card-body p-0">
                    <table class="table align-middle table-bordered mb-0">
                        <tr>
                            <td class="fw-bold text-muted ps-3">Sakit</td>
                            <td class="text-center fw-bold text-dark" style="width: 100px;">{{ $catatanWaliKelas->sakit ?? 0 }} Hari</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted ps-3">Izin</td>
                            <td class="text-center fw-bold text-dark">{{ $catatanWaliKelas->izin ?? 0 }} Hari</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted ps-3">Tanpa Keterangan (Alpa)</td>
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
@endsection
