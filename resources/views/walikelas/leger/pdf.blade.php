<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Leger Nilai - {{ $kelas->nama_kelas }}</title>
    <style>
        @page {
            size: a4 landscape;
            margin: 1.2cm;
        }
        
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 9px;
            line-height: 1.3;
            color: #333;
        }
        
        .header {
            text-align: center;
            margin-bottom: 15px;
        }
        
        .header h3 {
            margin: 0;
            font-size: 13px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .header p {
            margin: 2px 0;
            font-size: 9px;
            color: #666;
        }
        
        .meta-info {
            width: 100%;
            margin-bottom: 10px;
            font-size: 9.5px;
        }
        
        .leger-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            text-align: center;
        }
        
        .leger-table th,
        .leger-table td {
            padding: 4px 3px;
            border: 1px solid #333;
        }
        
        .leger-table th {
            background-color: #f0f0f0;
            font-weight: bold;
            font-size: 8.5px;
            text-transform: uppercase;
        }
        
        .leger-table td.text-left {
            text-align: left;
            font-weight: bold;
        }
        
        .leger-table td.text-bold {
            font-weight: bold;
        }
        
        .signature-table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }
        
        .signature-table td {
            width: 50%;
            text-align: center;
            font-size: 9.5px;
        }
        
        .signature-space {
            height: 45px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h3>LEGER NILAI SISWA KELAS {{ $kelas->nama_kelas }}</h3>
        <p>SMK SUNAN GIRI | Tahun Pelajaran: {{ $activeTahunAjaran->tahun_ajaran }} - Semester {{ $activeTahunAjaran->id_semester }}</p>
    </div>

    <table class="meta-info">
        <tr>
            <td style="width: 50%;">Kelas: <strong>{{ $kelas->nama_kelas }}</strong></td>
            <td style="width: 50%; text-align: right;">Tanggal Cetak: <strong>{{ date('d-m-Y H:i:s') }}</strong></td>
        </tr>
    </table>

    <table class="leger-table">
        <thead>
            <tr>
                <th rowspan="2" style="width: 25px;">No</th>
                <th rowspan="2" style="text-align: left; min-width: 150px;">Nama Siswa</th>
                @foreach($mapels as $mapel)
                    <th rowspan="2" style="font-size: 7.5px; width: 20px; font-weight: normal; vertical-align: bottom;">
                        <!-- In PDF, vertical text usually requires writing-mode or rotation CSS that dompdf supports. -->
                        <div style="writing-mode: vertical-rl; transform: rotate(180deg); margin: 0 auto; height: 100px;">
                            {{ $mapel->kode_mapel ?? substr($mapel->nama, 0, 7) }}
                        </div>
                    </th>
                @endforeach
                <th rowspan="2" style="width: 40px;">Jumlah Nilai Rapot</th>
                <th rowspan="2" style="width: 30px;">Peringkat</th>
                <th colspan="6">Ekstrakurikuler</th>
                <th colspan="3">Ketidakhadiran</th>
            </tr>
            <tr>
                <th style="font-size: 7.5px;">Pil 1</th>
                <th style="font-size: 7.5px;">Ket</th>
                <th style="font-size: 7.5px;">Pil 2</th>
                <th style="font-size: 7.5px;">Ket</th>
                <th style="font-size: 7.5px;">Pil 3</th>
                <th style="font-size: 7.5px;">Ket</th>
                <th style="font-size: 7.5px;">S</th>
                <th style="font-size: 7.5px;">I</th>
                <th style="font-size: 7.5px;">A</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach($studentsList as $item)
                <tr>
                    <td class="text-bold">{{ $no++ }}</td>
                    <td class="text-left">{{ $item['siswa']->nama }}</td>
                    
                    @foreach($mapels as $mapel)
                        @php
                            $mark = $item['grades'][$mapel->id_mapel] ?? null;
                        @endphp
                        <td class="text-bold">
                            {{ $mark !== null ? number_format($mark, 0) : '0' }}
                        </td>
                    @endforeach
                    
                    <td class="text-bold">{{ number_format($item['total'], 0) }}</td>
                    <td class="text-bold">{{ $item['rank'] }}</td>

                    {{-- Ekstrakurikuler --}}
                    @for($i = 0; $i < 3; $i++)
                        <td style="font-size: 8px;">{{ strtoupper(substr($item['ekstra'][$i]['nama'] ?? '0', 0, 6)) ?: '0' }}</td>
                        <td style="font-size: 8px;">{{ strtoupper(substr($item['ekstra'][$i]['ket'] ?? '0', 0, 2)) ?: '0' }}</td>
                    @endfor

                    {{-- Kehadiran --}}
                    <td>{{ $item['kehadiran']['sakit'] ?? '0' }}</td>
                    <td>{{ $item['kehadiran']['izin'] ?? '0' }}</td>
                    <td>{{ $item['kehadiran']['alpa'] ?? '0' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Signatures -->
    <table class="signature-table">
        <tr>
            <td>
                <p>Mengetahui,</p>
                <p>Kepala Sekolah SMK Sunan Giri</p>
                <div class="signature-space"></div>
                <p style="text-decoration: underline; font-weight: bold;">H. Mas'ud, S.T., M.M.</p>
            </td>
            <td>
                <p>Gresik, {{ date('d F Y') }}</p>
                <p>Wali Kelas</p>
                <div class="signature-space"></div>
                <p style="text-decoration: underline; font-weight: bold;">{{ $kelas->waliKelas->nama ?? 'Akhmad Baikuni, S.T' }}</p>
                <p>NIP. {{ $kelas->waliKelas->nip ?? '-' }}</p>
            </td>
        </tr>
    </table>
</body>
</html>
