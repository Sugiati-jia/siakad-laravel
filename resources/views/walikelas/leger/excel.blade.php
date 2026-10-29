<meta charset="utf-8">
<table border="1">
    <thead>
        <tr>
            <th colspan="{{ count($mapels) + 13 }}" style="text-align: center; font-size: 14px; font-weight: bold;">
                LEGER NILAI KELAS {{ $kelas->nama_kelas }}
            </th>
        </tr>
        <tr>
            <th colspan="{{ count($mapels) + 13 }}" style="text-align: center; font-size: 11px;">
                SMK SUNAN GIRI | Tahun Pelajaran: {{ $activeTahunAjaran->tahun_ajaran }} - Semester {{ $activeTahunAjaran->id_semester }}
            </th>
        </tr>
        <tr>
            <th colspan="{{ count($mapels) + 13 }}"></th>
        </tr>
        <tr>
            <th rowspan="2" style="font-weight: bold; text-align: center; vertical-align: middle; background-color: #f2f2f2;">No</th>
            <th rowspan="2" style="font-weight: bold; text-align: center; vertical-align: middle; background-color: #f2f2f2;">NAMA SISWA</th>
            @foreach($mapels as $mapel)
                <th rowspan="2" style="font-weight: bold; text-align: center; vertical-align: bottom; background-color: #f2f2f2;">
                    {{ $mapel->nama_mapel }}
                </th>
            @endforeach
            <th rowspan="2" style="font-weight: bold; text-align: center; vertical-align: bottom; background-color: #f2f2f2;">
                JUMLAH NILAI RAPOT
            </th>
            <th rowspan="2" style="font-weight: bold; text-align: center; vertical-align: bottom; background-color: #f2f2f2;">
                PERINGKAT
            </th>
            <th colspan="6" style="font-weight: bold; text-align: center; vertical-align: middle; background-color: #f2f2f2;">EKSTRAKURIKULER</th>
            <th colspan="3" style="font-weight: bold; text-align: center; vertical-align: middle; background-color: #f2f2f2;">KETIDAKHADIRAN</th>
        </tr>
        <tr>
            <th style="font-weight: bold; text-align: center; background-color: #f2f2f2;">PILIHAN 1</th>
            <th style="font-weight: bold; text-align: center; background-color: #f2f2f2;">KET.</th>
            <th style="font-weight: bold; text-align: center; background-color: #f2f2f2;">PILIHAN 2</th>
            <th style="font-weight: bold; text-align: center; background-color: #f2f2f2;">KET.</th>
            <th style="font-weight: bold; text-align: center; background-color: #f2f2f2;">PILIHAN 3</th>
            <th style="font-weight: bold; text-align: center; background-color: #f2f2f2;">KET.</th>
            <th style="font-weight: bold; text-align: center; background-color: #f2f2f2;">Sakit</th>
            <th style="font-weight: bold; text-align: center; background-color: #f2f2f2;">Ijin</th>
            <th style="font-weight: bold; text-align: center; background-color: #f2f2f2;">Alpa</th>
        </tr>
        <tr style="background-color: #e0e0e0; font-weight: bold;">
            <td style="text-align: center;">1</td>
            <td style="text-align: center;">2</td>
            @php $colNum = 3; @endphp
            @foreach($mapels as $mapel)
                <td style="text-align: center;">{{ $colNum++ }}</td>
            @endforeach
            <td style="text-align: center;">{{ $colNum++ }}</td>
            <td style="text-align: center;">{{ $colNum++ }}</td>
            <td style="text-align: center;">{{ $colNum++ }}</td>
            <td style="text-align: center;">{{ $colNum++ }}</td>
            <td style="text-align: center;">{{ $colNum++ }}</td>
            <td style="text-align: center;">{{ $colNum++ }}</td>
            <td style="text-align: center;">{{ $colNum++ }}</td>
            <td style="text-align: center;">{{ $colNum++ }}</td>
            <td style="text-align: center;">{{ $colNum++ }}</td>
            <td style="text-align: center;">{{ $colNum++ }}</td>
            <td style="text-align: center;">{{ $colNum++ }}</td>
        </tr>
    </thead>
    <tbody>
        @php $no = 1; @endphp
        @foreach($studentsList as $item)
            <tr>
                <td style="text-align: center;">{{ $no++ }}</td>
                <td style="text-align: left;">{{ $item['siswa']->nama }}</td>
                
                @foreach($mapels as $mapel)
                    @php
                        $mark = $item['grades'][$mapel->id_mapel] ?? null;
                    @endphp
                    <td style="text-align: center;">
                        {{ $mark !== null ? number_format($mark, 0) : '0' }}
                    </td>
                @endforeach
                
                <td style="text-align: center;">{{ number_format($item['total'], 0) }}</td>
                <td style="text-align: center;">{{ $item['rank'] }}</td>

                {{-- Ekstrakurikuler --}}
                @for($i = 0; $i < 3; $i++)
                    <td style="text-align: center;">{{ strtoupper($item['ekstra'][$i]['nama'] ?? '0') ?: '0' }}</td>
                    <td style="text-align: center;">{{ strtoupper($item['ekstra'][$i]['ket'] ?? '0') ?: '0' }}</td>
                @endfor

                {{-- Kehadiran --}}
                <td style="text-align: center;">{{ $item['kehadiran']['sakit'] ?? '0' }}</td>
                <td style="text-align: center;">{{ $item['kehadiran']['izin'] ?? '0' }}</td>
                <td style="text-align: center;">{{ $item['kehadiran']['alpa'] ?? '0' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
