<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rapor Siswa - {{ $siswa->nama }}</title>
    <style>
        @page {
            margin: 1.5cm 1.5cm 2.5cm 1.5cm;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #000;
        }

        /* ===== WATERMARK (Fixed on all pages) ===== */
        .watermark {
            position: fixed;
            top: 30%;
            left: 0;
            right: 0;
            text-align: center;
            z-index: -100;
            opacity: 0.3;
        }
        .watermark img {
            width: 350px;
            height: auto;
        }

        /* ===== FIXED FOOTER (repeats on every page) ===== */
        .page-footer {
            position: fixed;
            bottom: -2cm;
            left: 0;
            right: 0;
            height: 1.5cm;
            font-size: 8px;
            color: #555;
            border-top: 0.5px solid #999;
            padding-top: 4px;
        }
        .page-footer table {
            width: 100%;
            border-collapse: collapse;
        }
        .page-footer td {
            padding: 0;
            border: none;
            vertical-align: middle;
        }

        /* ===== STUDENT INFO ===== */
        .student-info {
            margin-bottom: 12px;
        }
        .student-info table {
            width: 100%;
            border-collapse: collapse;
        }
        .student-info td {
            padding: 2px 0;
            font-size: 11px;
            vertical-align: top;
        }
        .student-info .label {
            width: 140px;
        }
        .student-info .separator {
            width: 8px;
            text-align: center;
        }
        .student-info .value {
            font-weight: bold;
        }

        /* ===== SECTION TITLE ===== */
        h4.section-title {
            margin: 15px 0 6px 0;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }

        /* ===== GRADES TABLE ===== */
        .grades-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .grades-table th,
        .grades-table td {
            padding: 5px 6px;
            border: 1px solid #333;
            font-size: 10px;
        }
        .grades-table th {
            background-color: #f5f5f5;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
        }

        /* ===== DESCRIPTION BOX ===== */
        .description-box {
            border: 1px solid #333;
            padding: 8px;
            margin-bottom: 10px;
            min-height: 40px;
            font-size: 10.5px;
            background-color: #fafafa;
        }

        /* ===== SIGNATURE SECTION ===== */
        .signature-table {
            width: 100%;
            border-collapse: collapse;
        }
        .signature-table td {
            padding: 5px;
            vertical-align: top;
            font-size: 11px;
            border: none;
        }
        .signature-space {
            height: 60px;
        }

        /* ===== UTILITIES ===== */
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .text-bold { font-weight: bold; }
        .page-break { page-break-before: always; }
    </style>
</head>
<body>
    {{-- ===== WATERMARK ===== --}}
    <div class="watermark">
        @if(file_exists(public_path('images/watermark_rapor.jpeg')))
            <img src="{{ public_path('images/watermark_rapor.jpeg') }}" alt="Logo SMK">
        @endif
    </div>

    {{-- ===== COMPUTED VARIABLES ===== --}}
    @php
        $tingkat = optional($siswa->kelas)->tingkat ?? 'X';
        $fase = ($tingkat == 'X') ? 'E' : 'F';
        $semesterText = ($activeTahunAjaran->id_semester == 1) ? 'Ganjil' : 'Genap';
        $namaKelas = optional($siswa->kelas)->nama_kelas ?? '-';
        $bulanIndo = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $tanggalCetak = date('d') . ' ' . $bulanIndo[(int)date('n')] . ' ' . date('Y');
    @endphp

    {{-- ===== FOOTER (fixed, repeats on every page) ===== --}}
    <div class="page-footer">
        <table>
            <tr>
                <td style="text-align: left; width: 40%;">{{ strtoupper($siswa->nama) }} - {{ $namaKelas }}</td>
                <td style="text-align: center; width: 20%;"></td>
                <td style="text-align: right; width: 40%;">Dicetak dari e-Rapor SMK v.7.0.8</td>
            </tr>
        </table>
    </div>

    {{-- ================================================================= --}}
    {{-- HALAMAN 1 : Info Siswa + A. Nilai Akademik                        --}}
    {{-- ================================================================= --}}

    {{-- Student Info (2 kolom: kiri & kanan) — HANYA di halaman pertama --}}
    <div class="student-info">
        <table>
            <tr>
                <td style="width: 50%; vertical-align: top; border: none;">
                    <table style="border: none;">
                        <tr>
                            <td class="label" style="border: none;">Nama Peserta Didik</td>
                            <td class="separator" style="border: none;">:</td>
                            <td class="value" style="border: none;">{{ strtoupper($siswa->nama) }}</td>
                        </tr>
                        <tr>
                            <td class="label" style="border: none;">NISN</td>
                            <td class="separator" style="border: none;">:</td>
                            <td class="value" style="border: none;">{{ $siswa->nisn }}</td>
                        </tr>
                        <tr>
                            <td class="label" style="border: none;">Sekolah</td>
                            <td class="separator" style="border: none;">:</td>
                            <td class="value" style="border: none;">{{ \App\Models\Setting::get('nama_sekolah', 'SMK SUNAN GIRI') }}</td>
                        </tr>
                        <tr>
                            <td class="label" style="border: none;">Alamat</td>
                            <td class="separator" style="border: none;">:</td>
                            <td class="value" style="border: none;">{{ \App\Models\Setting::get('alamat_sekolah', 'Jl. Prof. Dr. Hamka 54 A Kota Probolinggo') }}</td>
                        </tr>
                    </table>
                </td>
                <td style="width: 50%; vertical-align: top; border: none;">
                    <table style="border: none;">
                        <tr>
                            <td class="label" style="border: none;">Kelas</td>
                            <td class="separator" style="border: none;">:</td>
                            <td class="value" style="border: none;">{{ $namaKelas }}</td>
                        </tr>
                        <tr>
                            <td class="label" style="border: none;">Fase</td>
                            <td class="separator" style="border: none;">:</td>
                            <td class="value" style="border: none;">{{ $fase }}</td>
                        </tr>
                        <tr>
                            <td class="label" style="border: none;">Semester</td>
                            <td class="separator" style="border: none;">:</td>
                            <td class="value" style="border: none;">{{ $semesterText }}</td>
                        </tr>
                        <tr>
                            <td class="label" style="border: none;">Tahun Pelajaran</td>
                            <td class="separator" style="border: none;">:</td>
                            <td class="value" style="border: none;">{{ $activeTahunAjaran->tahun_ajaran }}</td>
                        </tr>
                        <tr>
                            <td class="label" style="border: none;">Konsentrasi Keahlian</td>
                            <td class="separator" style="border: none;">:</td>
                            <td class="value" style="border: none;">{{ optional(optional($siswa->kelas)->konsentrasiKeahlian)->nama_konsentrasi ?? '-' }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>

    {{-- ===== A. NILAI AKADEMIK ===== --}}
    <h4 class="section-title">A. Nilai Akademik</h4>
    <table class="grades-table">
        <thead>
            <tr>
                <th style="width: 35px;">No</th>
                <th style="width: 180px;">Mata Pelajaran</th>
                <th style="width: 55px;">Nilai Akhir</th>
                <th>Capaian Kompetensi</th>
            </tr>
        </thead>
        <tbody>
            @php
                $umum = [];
                $kejuruan = [];
                $pilihan = [];

                foreach($nilaiTps as $mapelId => $grades) {
                    $firstGrade = $grades->first();
                    $kel = $firstGrade->mapel->kelompok_mapel ?? 'A';
                    
                    $item = ['grades' => $grades, 'mapelId' => $mapelId, 'mapel' => $firstGrade->mapel];
                    
                    if ($kel === 'A') {
                        $umum[] = $item;
                    } elseif ($kel === 'B') {
                        $kejuruan[] = $item;
                    } else {
                        $pilihan[] = $item;
                    }
                }
                
                $sortByUrutan = function($a, $b) {
                    $urutanA = $a['mapel']->urutan ?? 999;
                    $urutanB = $b['mapel']->urutan ?? 999;
                    if ($urutanA == $urutanB) return strcmp($a['mapel']->nama, $b['mapel']->nama);
                    return $urutanA <=> $urutanB;
                };

                usort($umum, $sortByUrutan);
                usort($kejuruan, $sortByUrutan);
                usort($pilihan, $sortByUrutan);

                // Helper: hitung NA sesuai rumus Excel AVERAGE(rerata_TH, rerata_UH, AS)
                $hitungNA = function($grades) {
                    $thVals = $grades->map(function($n) {
                        $th1 = isset($n->nilai_th1) ? (float)$n->nilai_th1 : null;
                        $th2 = isset($n->nilai_th2) ? (float)$n->nilai_th2 : null;
                        if ($th1 === null && $th2 === null) return null;
                        $v = array_filter([$th1, $th2], fn($x) => $x !== null);
                        return array_sum($v) / count($v);
                    })->filter(fn($v) => $v !== null);

                    $uhVals = $grades->map(function($n) {
                        $uh1 = isset($n->nilai_uh1) ? (float)$n->nilai_uh1 : null;
                        $uh2 = isset($n->nilai_uh2) ? (float)$n->nilai_uh2 : null;
                        if ($uh1 === null && $uh2 === null) return null;
                        $v = array_filter([$uh1, $uh2], fn($x) => $x !== null);
                        return array_sum($v) / count($v);
                    })->filter(fn($v) => $v !== null);

                    $asValue = null;
                    foreach ($grades as $n) {
                        $as = $n->nilai_as ?? null;
                        if ($as !== null && $as !== '') { $asValue = (float)$as; break; }
                    }

                    $components = array_filter([
                        $thVals->isNotEmpty() ? $thVals->avg() : null,
                        $uhVals->isNotEmpty() ? $uhVals->avg() : null,
                        $asValue,
                    ], fn($v) => $v !== null);

                    return count($components) > 0 ? round(array_sum($components) / count($components), 0) : 0;
                };
            @endphp

            {{-- A. KELOMPOK MATA PELAJARAN UMUM --}}
            <tr style="background-color: #e8e8e8;">
                <td colspan="4" style="font-weight: bold; font-size: 10px;">A. Kelompok Mata Pelajaran Umum</td>
            </tr>
            @php $no = 1; @endphp
            @forelse($umum as $item)
                @php
                    $grades = $item['grades'];
                    $mapelId = $item['mapelId'];
                    $firstGrade = $grades->first();
                    $nilaiAkhir = $hitungNA($grades);

                    $predikat = match(true) {
                        $nilaiAkhir >= 90 => 'A',
                        $nilaiAkhir >= 80 => 'B',
                        $nilaiAkhir >= 70 => 'C',
                        $nilaiAkhir >= 60 => 'D',
                        default => 'E'
                    };
                @endphp
                <tr>
                    <td class="text-center">{{ $no++ }}</td>
                    <td class="text-left">{{ optional($firstGrade->mapel)->nama_mapel ?? 'Mapel' }}</td>
                    <td class="text-center text-bold">{{ number_format($nilaiAkhir, 0) }}</td>
                    <td class="text-left" style="font-size: 9px; line-height: 1.3;">{{ $descriptions[$mapelId] ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center" style="color: #999;">Belum ada data mata pelajaran umum.</td>
                </tr>
            @endforelse

            {{-- B. KELOMPOK PELAJARAN KEJURUAN --}}
            @if(count($kejuruan) > 0)
                <tr style="background-color: #e8e8e8;">
                    <td colspan="4" style="font-weight: bold; font-size: 10px;">B. Kelompok Pelajaran Kejuruan</td>
                </tr>
                @php $no = 1; @endphp
                @foreach($kejuruan as $item)
                    @php
                        $grades = $item['grades'];
                        $mapelId = $item['mapelId'];
                        $firstGrade = $grades->first();
                        $nilaiAkhir = $hitungNA($grades);

                        $predikat = match(true) {
                            $nilaiAkhir >= 90 => 'A',
                            $nilaiAkhir >= 80 => 'B',
                            $nilaiAkhir >= 70 => 'C',
                            $nilaiAkhir >= 60 => 'D',
                            default => 'E'
                        };
                    @endphp
                    <tr>
                        <td class="text-center">{{ $no++ }}</td>
                        <td class="text-left">{{ optional($firstGrade->mapel)->nama ?? 'Mapel' }}</td>
                        <td class="text-center text-bold">{{ number_format($nilaiAkhir, 0) }}</td>
                        <td class="text-left" style="font-size: 9px; line-height: 1.3;">{{ $descriptions[$mapelId] ?? '-' }}</td>
                    </tr>
                @endforeach
            @endif

            {{-- C. KELOMPOK PELAJARAN PILIHAN --}}
            @if(count($pilihan) > 0)
                <tr style="background-color: #e8e8e8;">
                    <td colspan="4" style="font-weight: bold; font-size: 10px;">C. Kelompok Pelajaran Pilihan</td>
                </tr>
                @php $no = 1; @endphp
                @foreach($pilihan as $item)
                    @php
                        $grades = $item['grades'];
                        $mapelId = $item['mapelId'];
                        $firstGrade = $grades->first();
                        $nilaiAkhir = $hitungNA($grades);

                        $predikat = match(true) {
                            $nilaiAkhir >= 90 => 'A',
                            $nilaiAkhir >= 80 => 'B',
                            $nilaiAkhir >= 70 => 'C',
                            $nilaiAkhir >= 60 => 'D',
                            default => 'E'
                        };
                    @endphp
                    <tr>
                        <td class="text-center">{{ $no++ }}</td>
                        <td class="text-left">{{ optional($firstGrade->mapel)->nama ?? 'Mapel' }}</td>
                        <td class="text-center text-bold">{{ number_format($nilaiAkhir, 0) }}</td>
                        <td class="text-left" style="font-size: 9px; line-height: 1.3;">{{ $descriptions[$mapelId] ?? '-' }}</td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>

    {{-- ===== B. KOKURIKULER ===== --}}
    <h4 class="section-title">B. Kokurikuler</h4>
    <div class="description-box">
        @if($kokurikuler && $kokurikuler->deskripsi)
            {{ $kokurikuler->deskripsi }}
        @else
            Peserta didik telah berpartisipasi aktif dalam kegiatan Kokurikuler Projek Penguatan Profil Pelajar Pancasila (P5) dengan baik.
        @endif
    </div>

    {{-- ===== C. KEGIATAN EKSTRAKURIKULER ===== --}}
    <div style="page-break-inside: avoid;">
        <h4 class="section-title">C. Kegiatan Ekstrakurikuler</h4>
        <table class="grades-table">
        <thead>
            <tr>
                <th style="width: 35px;">No</th>
                <th>Kegiatan Ekstrakurikuler</th>
                <th style="width: 150px;">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($nilaiEkstrakurikulers as $index => $ne)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-left">{{ optional($ne->ekstrakurikuler)->nama ?? '-' }}</td>
                    <td class="text-left">{{ $ne->keterangan ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td class="text-center">-</td>
                    <td class="text-left">-</td>
                    <td class="text-left">-</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    </div>

    {{-- ===== D. KETIDAKHADIRAN & CATATAN WALI KELAS (Side by Side) ===== --}}
    <table style="width: 100%; border-collapse: collapse; border: none; margin-bottom: 15px; margin-top: 20px; page-break-inside: avoid;">
        <tr>
            {{-- Bagian Kiri: Ketidakhadiran --}}
            <td style="width: 45%; vertical-align: top; padding-right: 10px; border: none;">
                <table class="grades-table" style="margin-bottom: 0;">
                    <tr>
                        <th colspan="2" class="text-left" style="padding-left: 10px;">D. Ketidakhadiran</th>
                    </tr>
                    <tr>
                        <td style="width: 60%; padding-left: 10px;">Sakit</td>
                        <td class="text-center">{{ $catatanWaliKelas->sakit ?? 0 }} hari</td>
                    </tr>
                    <tr>
                        <td style="padding-left: 10px;">Izin</td>
                        <td class="text-center">{{ $catatanWaliKelas->izin ?? 0 }} hari</td>
                    </tr>
                    <tr>
                        <td style="padding-left: 10px;">Tanpa Keterangan</td>
                        <td class="text-center">{{ $catatanWaliKelas->alpa ?? 0 }} hari</td>
                    </tr>
                </table>
            </td>
            {{-- Bagian Kanan: Catatan Wali Kelas --}}
            <td style="width: 55%; vertical-align: top; border: none;">
                <table class="grades-table" style="margin-bottom: 0;">
                    <tr>
                        <th class="text-left" style="padding-left: 10px;">E. Catatan Wali Kelas</th>
                    </tr>
                    <tr>
                        <td style="vertical-align: top; height: 60px; padding: 10px; background-color: #fafafa;">
                            {{ $catatanWaliKelas->catatan ?? 'Tingkatkan motivasi belajarmu dan maksimalkan tugas-tugas yang diberikan oleh guru untuk mengasah kemampuanmu.' }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- ===== F. TANGGAPAN ORANG TUA / WALI MURID ===== --}}
    <table class="grades-table" style="margin-bottom: 15px; page-break-inside: avoid;">
        <tr>
            <th class="text-left" style="padding-left: 10px;">F. Tanggapan Orang Tua / Wali Murid</th>
        </tr>
        <tr>
            <td style="height: 45px; background-color: var(--custom-card-bg, #ffffff);">&nbsp;</td>
        </tr>
    </table>

    {{-- ===== G. KENAIKAN KELAS (HANYA semester Genap) ===== --}}
    @if($activeTahunAjaran->id_semester == 2)
        @php
            $nextTingkat = match($tingkat) {
                'X' => 'XI (Sebelas)',
                'XI' => 'XII (Dua Belas)',
                default => ''
            };
            $kelasClean = preg_replace('/^(XII|XI|X)\s*/i', '', $namaKelas);
            $kelasNaik = $nextTingkat . ' ' . trim($kelasClean);
        @endphp
        <h4 class="section-title">G. Keterangan Kenaikan Kelas</h4>
        <table class="grades-table" style="margin-bottom: 20px; page-break-inside: avoid;">
            <tr>
                <td style="padding: 10px; font-size: 11px;">
                    Berdasarkan pencapaian kompetensi pada semester 1 dan 2, peserta didik ditetapkan:<br>
                    @if(isset($catatanWaliKelas) && $catatanWaliKelas->status_kenaikan === 'naik')
                        <div style="margin-top: 10px; margin-bottom: 10px; font-size: 13px;">
                            <strong>Keputusan: Naik ke kelas {{ $kelasNaik }}</strong>
                        </div>
                    @elseif(isset($catatanWaliKelas) && $catatanWaliKelas->status_kenaikan === 'tinggal')
                        <div style="margin-top: 10px; margin-bottom: 10px; font-size: 13px;">
                            <strong>Keputusan: Tinggal di kelas {{ $namaKelas }}</strong>
                        </div>
                    @else
                        <table style="border: none; margin-top: 5px; margin-bottom: 5px;">
                            <tr>
                                <td style="border: none; padding: 2px 0; width: 120px;">Naik ke kelas</td>
                                <td style="border: none; padding: 2px 5px; width: 10px;">:</td>
                                <td style="border: none; padding: 2px 0;"><strong>{{ $kelasNaik }}</strong></td>
                            </tr>
                            <tr>
                                <td style="border: none; padding: 2px 0;">Tinggal di kelas</td>
                                <td style="border: none; padding: 2px 5px;">:</td>
                                <td style="border: none; padding: 2px 0;">......................................................................</td>
                            </tr>
                        </table>
                        <div style="font-size: 9px;"><i>*) Coret yang tidak perlu</i></div>
                    @endif
                </td>
            </tr>
        </table>
    @endif

    {{-- ================================================================= --}}
    {{-- TANDA TANGAN                                                      --}}
    {{-- ================================================================= --}}
    <div style="margin-top: 30px;">
        {{-- Baris 1: Orang Tua/Wali (kiri) & Wali Kelas (kanan) --}}
        <table class="signature-table" style="page-break-inside: avoid;">
            <tr>
                <td style="width: 50%; text-align: left;">
                    <p>&nbsp;</p>
                    <p>Orang Tua/Wali</p>
                    <div class="signature-space"></div>
                    <p>_________________________</p>
                </td>
                <td style="width: 50%; text-align: right;">
                    <p>Probolinggo, {{ $tanggalCetak }}</p>
                    <p>Wali Kelas</p>
                    <div class="signature-space"></div>
                    <p style="text-decoration: underline; font-weight: bold;">{{ optional(optional($siswa->kelas)->waliKelas)->nama ?? '-' }}</p>
                    <p>NIP. {{ optional(optional($siswa->kelas)->waliKelas)->nip ?? '-' }}</p>
                </td>
            </tr>
        </table>

        {{-- Baris 2: Kepala Sekolah (tengah, agak ke bawah) --}}
        @php
            $kepalaSekolahId = \App\Models\Setting::get('kepala_sekolah_id');
            $kepalaSekolah = $kepalaSekolahId ? \App\Models\Guru::find($kepalaSekolahId) : null;
            $ksNama = $kepalaSekolah ? $kepalaSekolah->nama : \App\Models\Setting::get('kepala_sekolah_nama', 'Sillahturohman, S.Pd., M.Pd');
            $ksNip = $kepalaSekolah ? ($kepalaSekolah->nip ?: '-') : \App\Models\Setting::get('kepala_sekolah_nip', '-');
        @endphp
        <div style="text-align: center; margin-top: 25px;">
            <p>Mengetahui,</p>
            <p>Kepala Sekolah {{ \App\Models\Setting::get('nama_sekolah', 'SMK Sunan Giri') }}</p>
            <div class="signature-space"></div>
            <p style="text-decoration: underline; font-weight: bold;">{{ $ksNama }}</p>
        </div>
    </div>

    {{-- ===== PAGE NUMBER (DomPDF inline PHP) ===== --}}
    <script type="text/php">
        if (isset($pdf)) {
            $pdf->page_script('
                $font = $fontMetrics->getFont("Helvetica", "normal");
                $size = 8;
                $pageText = "$PAGE_NUM";
                $w = $pdf->get_width();
                $h = $pdf->get_height();
                $pdf->text($w / 2 - 5, $h - 48, $pageText, $font, $size);
            ');
        }
    </script>
</body>
</html>
