{{-- walikelas/nilai/detail-partial.blade.php --}}
{{-- This is returned as HTML fragment via AJAX for the detail modal --}}

<div class="mb-3">
    <div class="row">
        <div class="col-md-6">
            <table class="table table-sm table-borderless mb-0">
                <tr>
                    <td class="fw-bold text-muted" style="width: 120px;">Nama</td>
                    <td>: {{ $siswa->nama }}</td>
                </tr>
                <tr>
                    <td class="fw-bold text-muted">NISN</td>
                    <td>: {{ $siswa->nisn ?? '-' }}</td>
                </tr>
            </table>
        </div>
        <div class="col-md-6">
            <table class="table table-sm table-borderless mb-0">
                <tr>
                    <td class="fw-bold text-muted" style="width: 120px;">Kelas</td>
                    <td>: {{ $siswa->kelas->nama_kelas ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="fw-bold text-muted">Tahun Ajaran</td>
                    <td>: {{ $activeTahunAjaran->tahun_ajaran ?? '-' }}</td>
                </tr>
            </table>
        </div>
    </div>
</div>

<hr>

@if($nilaiTps->count() > 0)
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="text-center" style="width: 50px;">No</th>
                    <th>Mata Pelajaran</th>
                    <th>Guru Mata Pelajaran</th>
                    <th class="text-center" style="width: 100px;">Nilai Akhir</th>
                    <th class="text-center" style="width: 80px;">Predikat</th>
                    <th>Capaian Kompetensi / Deskripsi</th>
                </tr>
            </thead>
            <tbody>
                @php $no = 1; @endphp
                @foreach($nilaiTps as $mapelId => $nilaiGroup)
                    @php
                        $firstNilai = $nilaiGroup->first();
                        $mapelName = $firstNilai->mapel->nama ?? $firstNilai->mapel->nama_mapel ?? 'Mapel';
                        $guruName  = $firstNilai->guruMapel?->guru?->nama ?? '-';

                        // Calculate final grade
                        $tpValues = $nilaiGroup->pluck('nilai_tp')->filter(fn($v) => $v !== null);
                        $nilaiAkhir = $tpValues->count() > 0 ? round($tpValues->avg(), 0) : 0;

                        // Determine Predikat
                        $predikat = match(true) {
                            $nilaiAkhir >= 90 => 'A',
                            $nilaiAkhir >= 80 => 'B',
                            $nilaiAkhir >= 70 => 'C',
                            $nilaiAkhir >= 60 => 'D',
                            default => 'E'
                        };

                        // Badge color
                        $badgeColor = match($predikat) {
                            'A' => 'success',
                            'B' => 'primary',
                            'C' => 'warning',
                            'D' => 'danger',
                            default => 'secondary'
                        };
                    @endphp
                    <tr>
                        <td class="text-center fw-bold">{{ $no++ }}</td>
                        <td class="fw-semibold text-dark">{{ $mapelName }}</td>
                        <td>{{ $guruName }}</td>
                        <td class="text-center fw-bold fs-5 text-primary">
                            {{ $nilaiAkhir > 0 ? $nilaiAkhir : '-' }}
                        </td>
                        <td class="text-center">
                            <span class="badge bg-{{ $badgeColor }} fs-6">{{ $predikat }}</span>
                        </td>
                        <td class="small" style="line-height: 1.4; font-size: 0.75rem;">
                            {{ $descriptions[$mapelId] ?? 'Belum ada deskripsi capaian.' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@else
    <div class="text-center py-5 text-muted">
        <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
        <h5 class="text-muted mt-3">Belum Ada Data Nilai</h5>
        <p class="text-muted">Siswa ini belum memiliki data nilai akademik untuk semester aktif.</p>
    </div>
@endif
