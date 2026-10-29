@extends('layouts.app')

@section('title', 'Rekap Nilai - ' . ($kelas->nama_kelas ?? ''))

@section('content')
<div class="container-fluid py-3">
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('walikelas.dashboard') }}" class="btn btn-outline-secondary btn-sm me-3">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h4 class="mb-0">Rekap Nilai</h4>
            <small class="text-muted">Kelas: <strong>{{ $kelas->nama_kelas ?? '-' }}</strong> | Tahun Ajaran: {{ $activeTahunAjaran->tahun_ajaran ?? '-' }}</small>
        </div>
    </div>

    @if($guruMapels->count() > 0)
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap gap-3 border-bottom-0 pt-3 pb-3">
                <span class="fs-5 fw-semibold text-dark">
                    <i class="bi bi-clipboard-data text-primary me-2"></i>Status Nilai Per Mata Pelajaran
                </span>
                <div class="d-flex flex-wrap gap-2">
                    {{-- Tombol Buka Kunci Semua --}}
                    <form action="{{ route('walikelas.nilai.unlock-all') }}" method="POST" class="m-0"
                          data-confirm="Buka kunci SEMUA nilai di kelas ini? Nilai akan dikembalikan ke status draft sehingga guru dapat mengubahnya kembali.">
                        @csrf
                        <input type="hidden" name="id_kelas" value="{{ $kelas->id_kelas }}">
                        <button type="submit" class="btn btn-sm btn-outline-warning d-flex align-items-center shadow-sm" style="border-radius: 8px; font-weight: 500; transition: all 0.2s;">
                            <i class="bi bi-unlock-fill me-1"></i> Buka Semua Kunci
                        </button>
                    </form>

                    {{-- Tombol Kunci Semua --}}
                    <form action="{{ route('walikelas.nilai.lock-all') }}" method="POST" class="m-0"
                          data-confirm="Kunci SEMUA nilai di kelas ini? Setelah dikunci, guru tidak dapat mengubah nilai lagi kecuali Anda membuka kunci per mata pelajaran.">
                        @csrf
                        <input type="hidden" name="id_kelas" value="{{ $kelas->id_kelas }}">
                        <button type="submit" class="btn btn-sm btn-danger d-flex align-items-center shadow-sm" style="border-radius: 8px; font-weight: 500; transition: all 0.2s;">
                            <i class="bi bi-lock-fill me-1"></i> Kunci Semua Nilai
                        </button>
                    </form>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 40px;">No</th>
                                <th>Mata Pelajaran</th>
                                <th>Guru</th>
                                <th class="text-center">Submited</th>
                                <th class="text-center">Terkunci</th>
                                <th class="text-center">Progress Sistem</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($guruMapels as $idx => $gm)
                                @php
                                    $summary = $gradeSummaries[$gm->id] ?? [
                                        'total' => 0, 'submitted' => 0, 'locked' => 0,
                                        'has_grade' => 0, 'completion_percentage' => 0,
                                        'lock_percentage' => 0, 'is_fully_locked' => false,
                                        'is_all_submitted' => false,
                                    ];
                                @endphp
                                <tr class="{{ $summary['is_fully_locked'] ? 'table-success' : '' }}">
                                    <td>{{ $idx + 1 }}</td>
                                    <td class="fw-semibold">{{ $gm->mapel->nama_mapel ?? '-' }}</td>
                                    <td>{{ $gm->guru->nama ?? '-' }}</td>
                                    {{-- Submitted = siswa yang sudah submit --}}
                                    <td class="text-center">
                                        <span class="badge {{ $summary['submitted'] > 0 ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $summary['submitted'] }}
                                        </span>
                                    </td>
                                    {{-- Terkunci --}}
                                    <td class="text-center">
                                        <span class="badge {{ $summary['locked'] > 0 ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $summary['locked'] }}
                                        </span>
                                    </td>
                                    {{-- Progress bar sistem --}}
                                    <td class="text-center">
                                        @php
                                            $pct = $summary['completion_percentage'];
                                            $barColor = 'bg-success';
                                        @endphp
                                        <div class="progress" style="height: 8px; min-width: 90px;">
                                            <div class="progress-bar {{ $barColor }}" style="width: {{ $pct }}%"></div>
                                        </div>
                                        <small class="text-muted d-block">
                                            {{ $summary['has_grade'] }}/{{ $summary['total'] }} siswa ({{ $pct }}%)
                                        </small>
                                    </td>
                                    {{-- Status badge --}}
                                    <td class="text-center">
                                        @if($summary['is_fully_locked'])
                                            <span class="badge bg-success"><i class="bi bi-lock-fill me-1"></i>Terkunci</span>
                                        @elseif($summary['submitted'] > 0)
                                            <span class="badge bg-warning text-dark"><i class="bi bi-clock me-1"></i>Menunggu Kunci</span>
                                        @elseif($summary['has_grade'] > 0)
                                            <span class="badge bg-secondary"><i class="bi bi-pencil me-1"></i>Draft</span>
                                        @else
                                            <span class="badge bg-light text-muted border">Belum Ada Nilai</span>
                                        @endif
                                    </td>
                                    {{-- Aksi --}}
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center align-items-center gap-3">
                                            {{-- Lihat Detail --}}
                                            <button type="button" class="btn btn-sm btn-outline-primary border-0 btn-detail-mapel"
                                                    data-mapel-id="{{ $gm->id_guru_mapel }}"
                                                    data-mapel-nama="{{ $gm->mapel->nama_mapel ?? 'Mapel' }}"
                                                    title="Lihat Detail Nilai">
                                                <i class="bi bi-eye fs-5"></i>
                                            </button>
 
                                            @if($summary['is_fully_locked'])
                                                {{-- Sudah locked: tampilkan tombol Buka Kunci + Kembalikan --}}
                                                <form action="{{ route('walikelas.nilai.unlock') }}" method="POST" class="m-0"
                                                      onsubmit="return confirm('Buka kunci nilai {{ addslashes($gm->mapel->nama_mapel ?? '') }}?\n\nNilai akan dikembalikan ke guru untuk diperbaiki.')">
                                                    @csrf
                                                    <input type="hidden" name="guru_mapel_id" value="{{ $gm->id_guru_mapel }}">
                                                    <button type="submit" class="btn btn-sm btn-outline-warning border-0" title="Buka Kunci & Kembalikan ke Guru">
                                                        <i class="bi bi-unlock-fill fs-5"></i>
                                                    </button>
                                                </form>
                                            @else
                                                {{-- Belum locked: tampilkan tombol Kunci --}}
                                                <form action="{{ route('walikelas.nilai.lock') }}" method="POST" class="m-0"
                                                      onsubmit="return confirm('Kunci nilai {{ addslashes($gm->mapel->nama_mapel ?? '') }}?\n\nSetelah dikunci, guru tidak dapat mengubah nilai lagi.')">
                                                    @csrf
                                                    <input type="hidden" name="guru_mapel_id" value="{{ $gm->id_guru_mapel }}">
                                                    <button type="submit"
                                                            class="btn btn-sm {{ $summary['submitted'] > 0 ? 'btn-outline-danger' : 'btn-outline-secondary' }} border-0"
                                                            title="Kunci Nilai"
                                                            {{ $summary['submitted'] == 0 && $summary['has_grade'] == 0 ? 'disabled' : '' }}>
                                                        <i class="bi bi-lock-fill fs-5"></i>
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
            </div>
        </div>

        {{-- Info kunci untuk rapor --}}
        @if(!$allNilaiLocked)
            <div class="alert alert-warning mt-3 mb-0 d-flex align-items-center gap-2">
                <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                <div>
                    <strong>Nilai belum semuanya dikunci!</strong>
                    Untuk dapat mengunduh atau mengirim E-Rapor, semua nilai mata pelajaran harus dikunci terlebih dahulu.
                    Gunakan tombol <strong>"Kunci Semua Nilai"</strong> di atas jika semua guru sudah mengumpulkan nilai.
                </div>
            </div>
        @else
            <div class="alert alert-success mt-3 mb-0 d-flex align-items-center gap-2">
                <i class="bi bi-check-circle-fill fs-5"></i>
                <div>
                    <strong>Semua nilai sudah dikunci!</strong>
                    Anda sekarang dapat mengunduh dan mengirim E-Rapor ke orang tua dan kurikulum.
                    <a href="{{ route('walikelas.rapor.index', $kelas->id_kelas) }}" class="btn btn-sm btn-success ms-2">
                        <i class="bi bi-file-earmark-text me-1"></i> Buka E-Rapor
                    </a>
                </div>
            </div>
        @endif

        {{-- Daftar Siswa & Detail Nilai --}}
        <div class="card border-0 shadow-sm mt-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span><i class="bi bi-people me-2"></i>Detail Nilai Siswa</span>
                <small class="text-muted">Klik <i class="bi bi-eye"></i> untuk melihat detail</small>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 40px;">No</th>
                                <th>NISN</th>
                                <th>Nama Siswa</th>
                                <th class="text-center">Jumlah Mapel Dinilai</th>
                                <th class="text-center" style="width: 60px;">Detail</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($siswaList ?? [] as $idx => $siswa)
                                <tr>
                                    <td>{{ $idx + 1 }}</td>
                                    <td>{{ $siswa->nisn ?? '-' }}</td>
                                    <td class="fw-semibold">{{ $siswa->nama ?? '-' }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-info">{{ $siswa->mapel_count ?? 0 }}</span>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-outline-primary btn-detail-siswa"
                                                data-siswa-id="{{ $siswa->id_siswa }}"
                                                data-siswa-nama="{{ $siswa->nama }}"
                                                title="Lihat Detail Nilai">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                                        <p class="mt-2 mb-0">Belum ada data siswa</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @else
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                <h5 class="text-muted mt-3">Belum Ada Data Nilai</h5>
                <p class="text-muted">Belum ada mata pelajaran yang terdaftar di kelas ini.</p>
            </div>
        </div>
    @endif
</div>

{{-- Detail Modal --}}
<div class="modal fade" id="detailNilaiModal" tabindex="-1" aria-labelledby="detailNilaiModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="detailNilaiModalLabel">
                    <i class="bi bi-clipboard-data me-2"></i>Detail Nilai Siswa
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body detail-modal-body" id="detailNilaiContent">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Memuat...</span>
                    </div>
                    <p class="mt-2 text-muted">Memuat data nilai...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Detail Siswa button click → load via AJAX
    $('.btn-detail-siswa').on('click', function() {
        const siswaId   = $(this).data('siswa-id');
        const siswaNama = $(this).data('siswa-nama');

        $('#detailNilaiModalLabel').html(
            '<i class="bi bi-clipboard-data me-2"></i>Detail Nilai — ' + siswaNama
        );

        $('#detailNilaiContent').html(`
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Memuat...</span>
                </div>
                <p class="mt-2 text-muted">Memuat data nilai...</p>
            </div>
        `);

        var modal = new bootstrap.Modal(document.getElementById('detailNilaiModal'));
        modal.show();

        $.ajax({
            url: '{{ url("walikelas/nilai/detail") }}/' + siswaId,
            method: 'GET',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            success: function(html) {
                $('#detailNilaiContent').html(html);
            },
            error: function(xhr) {
                $('#detailNilaiContent').html(`
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Gagal memuat data nilai. Silakan coba lagi.
                    </div>
                `);
            }
        });
    });

    // Detail Mapel button click → load via AJAX
    $('.btn-detail-mapel').on('click', function() {
        const mapelId   = $(this).data('mapel-id');
        const mapelNama = $(this).data('mapel-nama');

        $('#detailNilaiModalLabel').html(
            '<i class="bi bi-book me-2"></i>Detail Nilai Mata Pelajaran — ' + mapelNama
        );

        $('#detailNilaiContent').html(`
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Memuat...</span>
                </div>
                <p class="mt-2 text-muted">Memuat data nilai mata pelajaran...</p>
            </div>
        `);

        var modal = new bootstrap.Modal(document.getElementById('detailNilaiModal'));
        modal.show();

        $.ajax({
            url: '{{ url("walikelas/nilai/detail-mapel") }}/' + mapelId,
            method: 'GET',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            success: function(html) {
                $('#detailNilaiContent').html(html);
            },
            error: function(xhr) {
                $('#detailNilaiContent').html(`
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Gagal memuat data nilai mata pelajaran. Silakan coba lagi.
                    </div>
                `);
            }
        });
    });
});
</script>
@endsection
