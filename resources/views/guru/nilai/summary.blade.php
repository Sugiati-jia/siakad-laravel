@extends('layouts.app')

@section('title', 'Ringkasan Nilai - ' . $mapel->nama_mapel . ' - ' . $kelas->nama_kelas)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            {{-- Header Info --}}
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="text-primary mb-3">
                                <i class="bi bi-clipboard-data me-2"></i>
                                RINGKASAN NILAI
                            </h5>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td><strong>Mata Pelajaran:</strong></td>
                                    <td>{{ $mapel->nama_mapel }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Kelas:</strong></td>
                                    <td>{{ $kelas->nama_kelas }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td><strong>Semester:</strong></td>
                                    <td>{{ $guruMapel->id_semester == 1 ? '1 (Satu)' : '2 (Dua)' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Guru Mata Pelajaran:</strong></td>
                                    <td>{{ Auth::user()->guru->nama }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Alert messages --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- Action Buttons --}}
            @if(isset($isLocked) && $isLocked)
                <div class="alert alert-danger d-flex align-items-center mb-4 p-3 border-0 shadow-xs" style="border-left: 4px solid #dc3545 !important; border-radius: 8px; background-color: #f8d7da; color: #842029;">
                    <i class="bi bi-lock-fill me-3 fs-4 text-danger"></i>
                    <div>
                        <strong>Nilai Terkunci:</strong> Nilai mata pelajaran ini sudah dikunci oleh Wali Kelas. Anda tidak dapat mengubah atau mensubmit nilai.
                    </div>
                </div>
            @endif

            <div class="card mb-4">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            @if(isset($isLocked) && $isLocked)
                                <a href="{{ route('guru.nilai.input', [$kelas->id_kelas ?? $kelas->id, $mapel->id_mapel ?? $mapel->id]) }}" class="btn btn-outline-secondary me-2">
                                    <i class="bi bi-eye-fill me-2"></i>Lihat Detail Nilai
                                </a>
                            @else
                                <a href="{{ route('guru.nilai.input', [$kelas->id_kelas ?? $kelas->id, $mapel->id_mapel ?? $mapel->id]) }}" class="btn btn-outline-primary me-2">
                                    <i class="bi bi-pencil-square me-2"></i>Edit Semua Nilai
                                </a>
                            @endif
                            <a href="{{ route('guru.nilai.select-mapel', $kelas->id_kelas ?? $kelas->id) }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left me-2"></i>Kembali
                            </a>
                        </div>
                        <div class="col-md-6 text-end">
                            @php
                                $allSubmitted = collect($summaryData)->every(fn($d) => $d['status'] === 'submitted');
                                $anyStudentMissingGrades = collect($summaryData)->contains(fn($d) => $d['nilai_akhir'] === null || $d['jumlah_tp'] === 0);
                            @endphp
                            @if(isset($isLocked) && $isLocked)
                                <span class="badge bg-danger fs-6 p-2">
                                    <i class="bi bi-lock-fill me-2"></i>Nilai Terkunci oleh Wali Kelas
                                </span>
                            @elseif($anyStudentMissingGrades)
                                <div class="alert alert-warning border-0 shadow-sm d-inline-flex align-items-center mb-0 p-3" style="border-radius: 8px;">
                                    <i class="bi bi-exclamation-triangle-fill me-2 fs-5 text-warning"></i>
                                    <div class="text-start">
                                        <strong>Submit Terkunci:</strong> Harap lengkapi semua nilai siswa terlebih dahulu.
                                    </div>
                                </div>
                            @elseif(!$allSubmitted && count($summaryData) > 0)
                                <form method="POST" action="{{ route('guru.nilai.update-status') }}" class="d-inline" id="submitForm">
                                    @csrf
                                    <input type="hidden" name="guru_mapel_id" value="{{ $guruMapel->id_guru_mapel ?? $guruMapel->id }}">
                                    <input type="hidden" name="id_semester" value="{{ $guruMapel->id_semester }}">
                                    <input type="hidden" name="status" value="submitted">
                                    <button type="button" class="btn btn-success btn-lg" onclick="confirmSubmit()">
                                        <i class="bi bi-send me-2"></i>Submit Nilai ke Wali Kelas
                                    </button>
                                </form>
                            @else
                                @if(count($summaryData) > 0)
                                    <span class="badge bg-success fs-6 p-2">
                                        <i class="bi bi-check-circle me-2"></i>Submited
                                    </span>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Summary Table --}}
            <div class="card shadow-sm border-0">
                <div class="card-body p-0">
                    @if(count($summaryData) > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover mb-0 align-middle">
                                <thead class="table-light text-center">
                                    <tr>
                                        <th width="5%">No</th>
                                        <th width="25%">Nama Siswa</th>
                                        <th width="8%">Jumlah TP</th>
                                        <th width="10%">Nilai Akhir</th>
                                        <th width="8%">Predikat</th>
                                        <th>Deskripsi Capaian Kompetensi</th>
                                        <th width="12%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($summaryData as $index => $data)
                                        @php
                                            $nilaiAkhir = $data['nilai_akhir'];
                                            if ($nilaiAkhir !== null) {
                                                if ($nilaiAkhir >= 90) $predikat = 'A';
                                                elseif ($nilaiAkhir >= 80) $predikat = 'B';
                                                elseif ($nilaiAkhir >= 70) $predikat = 'C';
                                                elseif ($nilaiAkhir >= 60) $predikat = 'D';
                                                else $predikat = 'E';
                                            } else {
                                                $predikat = '-';
                                            }

                                            // Badge color based on predikat
                                            $badgeColor = match($predikat) {
                                                'A' => 'success',
                                                'B' => 'primary',
                                                'C' => 'warning',
                                                'D' => 'danger',
                                                'E' => 'dark',
                                                default => 'secondary',
                                            };
                                        @endphp
                                        <tr>
                                            <td class="text-center fw-bold">{{ $index + 1 }}</td>
                                            <td>{{ $data['siswa']->nama }}</td>
                                            <td class="text-center">{{ $data['jumlah_tp'] }}</td>
                                            <td class="text-center fw-bold fs-5">
                                                {{ $nilaiAkhir !== null ? number_format($nilaiAkhir, 0) : '-' }}
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-{{ $badgeColor }} fs-6">{{ $predikat }}</span>
                                            </td>
                                            <td class="small">{{ $data['deskripsi'] }}</td>
                                            <td class="text-center">
                                                <div class="action-group justify-content-center">
                                                    @if(isset($isLocked) && $isLocked)
                                                        <a href="{{ route('guru.nilai.input', [$kelas->id_kelas ?? $kelas->id, $mapel->id_mapel ?? $mapel->id]) }}"
                                                           class="btn-action btn-action-view" title="Lihat Nilai">
                                                            <i class="bi bi-eye-fill"></i>
                                                        </a>
                                                    @else
                                                        <a href="{{ route('guru.nilai.input', [$kelas->id_kelas ?? $kelas->id, $mapel->id_mapel ?? $mapel->id]) }}"
                                                           class="btn-action btn-action-edit" title="Edit Nilai">
                                                            <i class="bi bi-pencil-fill"></i>
                                                        </a>
                                                        <form method="POST"
                                                              action="{{ route('guru.nilai.destroy-student', [$data['siswa']->id_siswa, $mapel->id_mapel ?? $mapel->id]) }}"
                                                              data-confirm="Hapus semua nilai {{ $data['siswa']->nama }}?"
                                                              class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn-action btn-action-delete" title="Hapus Nilai">
                                                                <i class="bi bi-trash-fill"></i>
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

                        {{-- Statistics --}}
                        <div class="card-footer bg-light">
                            <div class="row text-center">
                                @php
                                    $nilaiValues = collect($summaryData)->pluck('nilai_akhir')->filter(fn($v) => $v !== null);
                                    $avgNilai = $nilaiValues->isNotEmpty() ? $nilaiValues->avg() : 0;
                                    $maxNilai = $nilaiValues->isNotEmpty() ? $nilaiValues->max() : 0;
                                    $minNilai = $nilaiValues->isNotEmpty() ? $nilaiValues->min() : 0;
                                @endphp
                                <div class="col-6 col-md-3">
                                    <div class="fw-bold text-muted small">TOTAL SISWA</div>
                                    <div class="fs-4 fw-bold text-primary">{{ count($summaryData) }}</div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="fw-bold text-muted small">RATA-RATA</div>
                                    <div class="fs-4 fw-bold text-info">{{ number_format($avgNilai, 1) }}</div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="fw-bold text-muted small">NILAI TERTINGGI</div>
                                    <div class="fs-4 fw-bold text-success">{{ number_format($maxNilai, 1) }}</div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="fw-bold text-muted small">NILAI TERENDAH</div>
                                    <div class="fs-4 fw-bold text-danger">{{ number_format($minNilai, 1) }}</div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-inbox display-1 text-muted"></i>
                            <h5 class="text-muted mt-3">Belum ada nilai yang diinput</h5>
                            <p class="text-muted">Silakan input nilai terlebih dahulu.</p>
                            <a href="{{ route('guru.nilai.input', [$kelas->id_kelas ?? $kelas->id, $mapel->id_mapel ?? $mapel->id]) }}" class="btn btn-primary">
                                <i class="bi bi-pencil-square me-2"></i>Input Nilai
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function confirmSubmit() {
    if (confirm('Yakin ingin mengirim semua nilai ke Wali Kelas? Setelah disubmit, nilai tidak bisa diubah kecuali dibuka kembali oleh Wali Kelas.')) {
        document.getElementById('submitForm').submit();
    }
}
</script>
@endsection
