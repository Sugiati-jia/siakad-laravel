@extends('layouts.app')
@section('title', 'Leger Nilai - ' . ($kelas->nama_kelas ?? ''))

@php
/**
 * Abbreviate a mapel name into a short acronym / known abbreviation.
 */
function mapelSingkat(string $nama): string {
    $known = [
        'Pendidikan Agama Islam dan Budi Pekerti' => 'PAI',
        'Pendidikan Agama Islam'  => 'PAI',
        'Pendidikan Pancasila'    => 'PPKn',
        'Pendidikan Kewarganegaraan' => 'PKn',
        'Bahasa Indonesia'        => 'B.Indo',
        'Bahasa Inggris'          => 'B.Ing',
        'Matematika'              => 'MTK',
        'Informatika'             => 'TIK',
        'Seni Budaya'             => 'SBd',
        'Seni dan Budaya'         => 'SBd',
        'Pendidikan Jasmani'      => 'PJOK',
        'Pendidikan Jasmani Olahraga dan Kesehatan' => 'PJOK',
        'Sejarah Indonesia'       => 'Sej.Indo',
        'Bahasa Daerah'           => 'B.Daerah',
        'Aswaja'                  => 'Aswaja',
        'Projek Ilmu Pengetahuan Alam dan Sosial' => 'IPAS',
        'Projek Kreatif dan Kewirausahaan' => 'PKK',
        'Kimia'                   => 'KIM',
        'Fisika'                  => 'FIS',
        'Biologi'                 => 'BIO',
        'Ekonomi'                 => 'EKO',
        'Geografi'                => 'GEO',
        'Sosiologi'               => 'SOS',
        'Dasar-dasar Otomotif'    => 'Oto.Dasar',
        'Dasar-dasar Teknik Mesin' => 'TM.Dasar',
        'Dasar-dasar Teknik Komputer' => 'TKJ.Dasar',
        'Dasar-dasar Akuntansi'   => 'Akun.Dasar',
        'Koding dan Kecerdasan Artifisial' => 'KKA',
    ];
    // Exact match
    foreach ($known as $key => $abbr) {
        if (stripos($nama, $key) !== false || $key === $nama) return $abbr;
    }
    // Auto: take initials of words, max 4 letters
    $words = preg_split('/[\s\-\/]+/', $nama);
    
    // If only 1 word, take first 3 letters
    if (count($words) === 1) {
        return strtoupper(substr($nama, 0, 3));
    }

    $initials = '';
    foreach ($words as $w) {
        if (strlen($w) >= 2) $initials .= strtoupper($w[0]);
        if (strlen($initials) >= 4) break;
    }
    return $initials ?: substr($nama, 0, 6);
}
@endphp

@section('content')
<style>
    /* Prevent dark-mode from overriding our custom table */
    .ltbl thead th,
    .ltbl thead td,
    .ltbl .r1 th,
    .ltbl .r2 th,
    .ltbl .r3 td,
    .ltbl tbody td { color: inherit !important; }

    .leger-page { padding: 0; }

    /* ── Top Bar ── */
    .leger-topbar {
        background: white; border-radius: 16px;
        box-shadow: 0 2px 8px rgba(30,41,59,.07);
        padding: 16px 22px; margin-bottom: 18px;
        display: flex; align-items: center; justify-content: space-between;
        flex-wrap: wrap; gap: 12px;
    }
    html.dark-mode .leger-topbar { background: #1e293b !important; }

    .back-btn {
        width: 36px; height: 36px; border-radius: 10px;
        background: #f1f5f9; border: none;
        display: flex; align-items: center; justify-content: center;
        color: #475569; text-decoration: none;
        transition: background .2s, color .2s; flex-shrink: 0;
    }
    .back-btn:hover { background: #1e293b; color: white; }
    .page-title { font-size: 1.05rem; font-weight: 700; color: #1e293b; margin: 0; }
    .page-sub   { font-size: .75rem; color: #94a3b8; margin: 2px 0 0; }
    html.dark-mode .page-title { color: #f8fafc !important; }
    .badge-ta {
        font-size: .68rem; padding: 3px 9px; border-radius: 7px;
        font-weight: 600; background: #eff6ff; color: #1d4ed8;
        border: 1px solid #bfdbfe;
    }
    .btn-dl {
        font-size: .78rem; font-weight: 600; border-radius: 10px;
        padding: 7px 15px; display: inline-flex; align-items: center;
        gap: 6px; text-decoration: none; border: none;
        transition: opacity .2s, transform .15s;
    }
    .btn-dl:hover { opacity: .85; transform: translateY(-1px); }
    .btn-excel { background: #16a34a; color: white; }
    .btn-pdf   { background: #dc2626; color: white; }

    /* ── Stats ── */
    .stat-row { display: grid; grid-template-columns: repeat(4,1fr); gap: 12px; margin-bottom: 18px; }
    @media(max-width:767px){ .stat-row{ grid-template-columns: repeat(2,1fr); } }
    .stat-mini {
        background: white; border-radius: 13px; padding: 13px 15px;
        box-shadow: 0 2px 8px rgba(30,41,59,.06);
        display: flex; align-items: center; gap: 11px;
    }
    html.dark-mode .stat-mini { background: #1e293b !important; }
    .s-icon { width: 38px; height: 38px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1rem; flex-shrink: 0; }
    .s-val  { font-size: 1.2rem; font-weight: 800; color: #1e293b; line-height: 1; }
    .s-lbl  { font-size: .68rem; color: #94a3b8; margin-top: 2px; }
    html.dark-mode .s-val { color: #f8fafc !important; }

    /* ── Table Card ── */
    .leger-card { background: white; border-radius: 16px; box-shadow: 0 2px 12px rgba(30,41,59,.08); overflow: hidden; }
    html.dark-mode .leger-card { background: #1e293b !important; }
    .leger-card-head {
        padding: 15px 20px; border-bottom: 1px solid #f1f5f9;
        display: flex; align-items: center; justify-content: space-between;
        flex-wrap: wrap; gap: 10px;
    }
    html.dark-mode .leger-card-head { border-bottom-color: rgba(255,255,255,.08) !important; }
    .leger-card-head h6 { font-size: .88rem; font-weight: 700; color: #1e293b; margin: 0; }
    html.dark-mode .leger-card-head h6 { color: #f8fafc !important; }
    .leger-card-head small { font-size: .7rem; color: #94a3b8; }

    /* ── Scroll Wrapper ── */
    .tbl-wrap { overflow-x: auto; width: 100%; position: relative; padding-bottom: 20px; }
    /* Custom Thin Scrollbar */
    .tbl-wrap::-webkit-scrollbar { width: 6px; height: 6px; }
    .tbl-wrap::-webkit-scrollbar-track { background: transparent; }
    .tbl-wrap::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
    .tbl-wrap::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    html.dark-mode .tbl-wrap::-webkit-scrollbar-thumb { background: #475569; }
    html.dark-mode .tbl-wrap::-webkit-scrollbar-thumb:hover { background: #64748b; }

    /* ── Table ── */
    .ltbl {
        border-collapse: separate; border-spacing: 0;
        font-size: 11.5px; white-space: nowrap; width: 100%;
    }

    /* ─ Row 1 group headers ─ */
    .ltbl thead tr.r1 th {
        position: sticky; top: 0; z-index: 3;
        background: #1e293b !important;
        color: white !important;
        font-size: 10px; font-weight: 700;
        text-transform: uppercase; letter-spacing: .5px;
        padding: 10px 8px; text-align: center; vertical-align: middle;
        border-right: 1px solid rgba(255,255,255,.12);
        border-bottom: none;
    }
    .ltbl thead tr.r1 th.g-mapel  { background: #1e3a5f !important; }
    .ltbl thead tr.r1 th.g-ekstra { background: #1e3a5f !important; border-left: 2px solid #3b82f6; }
    .ltbl thead tr.r1 th.g-absen  { background: #1e3a5f !important; border-left: 2px solid #3b82f6; }

    /* ─ Row 2 sub-labels ─ */
    .ltbl thead tr.r2 th {
        position: sticky; top: 38px; z-index: 3;
        background: #334155 !important;
        color: rgba(255,255,255,.88) !important;
        font-size: 9px; font-weight: 600;
        padding: 0; text-align: center; vertical-align: middle;
        border-right: 1px solid rgba(255,255,255,.08);
        border-bottom: none;
        overflow: hidden;
    }
    /* Inner div to constrain cell width */
    .ltbl thead tr.r2 th .th-inner {
        width: 48px;
        min-width: 48px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        padding: 7px 4px;
        margin: 0 auto;
        text-align: center;
    }
    .ltbl thead tr.r2 th.sub-ekstra .th-inner { width: 100px; max-width: 120px; white-space: normal; word-break: break-word; }
    .ltbl thead tr.r2 th.sub-absen  .th-inner { width: 44px;  max-width: 44px; }
    .ltbl thead tr.r2 th.sub-ekstra { background: #243a5e !important; }
    .ltbl thead tr.r2 th.sub-absen  { background: #243a5e !important; border-left: 2px solid #3b82f6; }

    /* ─ Row 3 column numbers ─ */
    .ltbl thead tr.r3 td {
        position: sticky; top: 80px; z-index: 3;
        background: #f1f5f9 !important;
        color: #64748b !important;
        font-size: 10px; font-weight: 700;
        text-align: center; padding: 5px 4px;
        border-bottom: 2px solid #cbd5e1 !important;
        border-right: 1px solid #e2e8f0;
    }

    /* ─ Sticky left cols ─ */
    .col-no   { position: sticky !important; left: 0;    z-index: 4; width: 38px; min-width: 38px; }
    .col-name { position: sticky !important; left: 38px; z-index: 4; min-width: 180px; max-width: 200px; }

    /* thead sticky-left = highest z */
    .ltbl thead tr.r1 .col-no,
    .ltbl thead tr.r1 .col-name { z-index: 10 !important; background: #1e293b !important; }
    .ltbl thead tr.r2 .col-no,
    .ltbl thead tr.r2 .col-name { z-index: 10 !important; background: #334155 !important; }
    .ltbl thead tr.r3 .col-no,
    .ltbl thead tr.r3 .col-name { z-index: 8 !important;  background: #f1f5f9 !important; color: #64748b !important; }

    /* Solid bg for body sticky cols */
    .ltbl tbody tr:nth-child(odd)  td.col-no,
    .ltbl tbody tr:nth-child(odd)  td.col-name { background: #ffffff !important; }
    .ltbl tbody tr:nth-child(even) td.col-no,
    .ltbl tbody tr:nth-child(even) td.col-name { background: #f8fafc !important; }
    .ltbl tbody tr:hover           td.col-no,
    .ltbl tbody tr:hover           td.col-name { background: #eff6ff !important; }

    /* Faint shadow on name col */
    .ltbl tbody td.col-name::after, .ltbl thead th.col-name::after {
        content: ''; position: absolute;
        top: 0; right: -5px; bottom: 0; width: 5px;
        background: linear-gradient(to right, rgba(0,0,0,.05), transparent);
        pointer-events: none;
    }

    /* ─ tbody rows ─ */
    .ltbl tbody tr:nth-child(even) { background: #f8fafc; }
    .ltbl tbody tr:hover           { background: #eff6ff !important; }
    .ltbl tbody td {
        padding: 7px 8px; text-align: center; vertical-align: middle;
        border-bottom: 1px solid #f1f5f9; border-right: 1px solid #f1f5f9;
        color: #374151 !important;
    }
    .ltbl tbody td.td-no   { font-weight: 700; color: #64748b !important; border-right: 1px solid #e2e8f0; text-align: center; }
    .ltbl tbody td.td-name { text-align: left; font-weight: 600; color: #1e293b !important; padding-left: 14px; border-right: 2px solid #e2e8f0; position: relative; }
    .ltbl tbody td.sep-l   { border-left: 2px solid #e2e8f0 !important; }

    /* Values */
    .v-high { color: #16a34a !important; font-weight: 700; }
    .v-low  { color: #dc2626 !important; font-weight: 700; }
    .v-zero { color: #d1d5db !important; }

    /* Chips */
    .chip-total { display: inline-block; background: #1e293b; color: white !important; font-weight: 800; font-size: 10.5px; padding: 3px 9px; border-radius: 6px; }
    .chip-rank  { display: inline-block; background: #eff6ff; color: #1d4ed8 !important; font-weight: 700; font-size: 10.5px; padding: 3px 8px; border-radius: 6px; }
    .chip-rank.r1 { background: #fef9c3; color: #854d0e !important; }
    .chip-rank.r2 { background: #f1f5f9; color: #475569 !important; }
    .chip-rank.r3 { background: #fff7ed; color: #9a3412 !important; }

    /* Ekstra nilai badges */
    .bek { font-size: 9.5px; font-weight: 700; padding: 2px 7px; border-radius: 5px; white-space: nowrap; }
    .bek-sb { background: #dcfce7; color: #14532d !important; }
    .bek-b  { background: #f0fdf4; color: #166534 !important; }
    .bek-c  { background: #fefce8; color: #92400e !important; }
    .bek-k  { background: #fef2f2; color: #991b1b !important; }
    .bek-d  { background: #eff6ff; color: #1d4ed8 !important; }

    .bs { background:#fefce8; color:#92400e !important; font-size:10px; font-weight:700; padding:2px 7px; border-radius:5px; }
    .bi { background:#eff6ff; color:#1e40af !important; font-size:10px; font-weight:700; padding:2px 7px; border-radius:5px; }
    .ba { background:#fef2f2; color:#991b1b !important; font-size:10px; font-weight:700; padding:2px 7px; border-radius:5px; }
    .dash { color: #d1d5db !important; }
</style>

<div class="leger-page container-fluid py-3">

    {{-- Top Bar --}}
    <div class="leger-topbar">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route(Auth::user()->role === 'wali_kelas' ? 'walikelas.leger.select-kelas' : 'admin.leger.select-kelas') }}" class="back-btn">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <p class="page-title mb-0">Leger Nilai &mdash; {{ $kelas->nama_kelas ?? '-' }}</p>
                <p class="page-sub mb-0">
                    <span class="badge-ta">TP {{ $activeTahunAjaran->tahun_ajaran }} &middot; Semester {{ $activeTahunAjaran->id_semester }}</span>
                    <span class="ms-2" style="font-size:.7rem;"><i class="bi bi-calendar3 me-1"></i>{{ date('d F Y') }}</span>
                </p>
            </div>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route(Auth::user()->role === 'wali_kelas' ? 'walikelas.leger.excel' : 'admin.leger.excel', $kelas->id_kelas) }}" class="btn-dl btn-excel">
                <i class="bi bi-file-earmark-excel-fill"></i>Excel
            </a>
        </div>
    </div>



    {{-- Table Card --}}
    <div class="leger-card">
        <div class="leger-card-head">
            <div>
                <h6><i class="bi bi-table me-2" style="color:#3b82f6;"></i>Tabel Leger Nilai Terkonsolidasi</h6>
                <small>Rekap seluruh mata pelajaran, ekstrakurikuler, dan ketidakhadiran siswa</small>
            </div>
        </div>

        @if(count($studentsList) > 0)
        <div class="tbl-wrap">
            <table class="ltbl">
                <thead>

                    {{-- Row 1: Group headers --}}
                    <tr class="r1">
                        <th class="col-no"   rowspan="2" style="text-align:center; border-right:1px solid rgba(255,255,255,.2);">No</th>
                        <th class="col-name" rowspan="2" style="text-align:center; border-right:2px solid rgba(255,255,255,.25); position:relative;">NAMA SISWA</th>
                        <th class="g-mapel"  colspan="{{ max(count($mapels), 1) }}">MATA PELAJARAN</th>
                        <th rowspan="2" style="background:#0f172a !important; min-width:60px; font-size:9px; padding:6px 4px; line-height:1.5; color:white !important; text-align:center;">JUMLAH<br>NILAI</th>
                        <th rowspan="2" style="background:#0f172a !important; min-width:55px; font-size:9px; padding:6px 4px; line-height:1.5; color:white !important; border-right:2px solid rgba(255,255,255,.2); text-align:center;">PERING-<br>KAT</th>
                        @if($ekstraList->count() > 0)
                            <th class="g-ekstra" colspan="{{ $ekstraList->count() }}">EKSTRAKURIKULER</th>
                        @endif
                        <th class="g-absen" colspan="3">KETIDAKHADIRAN</th>
                    </tr>

                    {{-- Row 2: Sub-labels --}}
                    <tr class="r2">
                        @if(count($mapels) > 0)
                            @foreach($mapels as $mapel)
                                <th title="{{ $mapel->nama_mapel }}" style="width: 1%;">
                                    <div class="th-inner">{{ mapelSingkat($mapel->nama_mapel) }}</div>
                                </th>
                            @endforeach
                        @else
                            <th><div class="th-inner" style="color:#94a3b8; font-style:italic;">-</div></th>
                        @endif

                        {{-- One column per unique ekstra name --}}
                        @foreach($ekstraList as $ekstra)
                            <th class="sub-ekstra">
                                <div class="th-inner">{{ $ekstra->nama ?? '-' }}</div>
                            </th>
                        @endforeach

                        <th class="sub-absen"><div class="th-inner">Sakit</div></th>
                        <th class="sub-absen" style="border-left:none;"><div class="th-inner">Ijin</div></th>
                        <th class="sub-absen" style="border-left:none;"><div class="th-inner">Alpa</div></th>
                    </tr>

                </thead>
                <tbody>
                    @php $no = ($studentsList->currentPage() - 1) * $studentsList->perPage() + 1; @endphp
                    @foreach($studentsList as $item)
                        <tr class="siswa-row">
                            <td class="col-no td-no">{{ $no++ }}</td>
                            <td class="col-name td-name">{{ $item['siswa']->nama }}</td>

                            {{-- Nilai mapel --}}
                            @foreach($mapels as $mapel)
                                @php $mark = $item['grades'][$mapel->id_mapel] ?? null; @endphp
                                <td class="{{ $mark === null ? 'v-zero' : ($mark >= 75 ? 'v-high' : 'v-low') }}">
                                    {{ $mark !== null ? number_format($mark, 0) : '0' }}
                                </td>
                            @endforeach

                            <td class="sep-l">
                                @if($item['total'] > 0)
                                    <span class="chip-total">{{ number_format($item['total'], 0) }}</span>
                                @else
                                    <span class="dash">-</span>
                                @endif
                            </td>
                            <td>
                                @php $r = $item['rank']; @endphp
                                @if($r !== null)
                                    <span class="chip-rank {{ $r==1?'r1':($r==2?'r2':($r==3?'r3':'')) }}">{{ $r }}</span>
                                @else
                                    <span class="dash">-</span>
                                @endif
                            </td>

                            {{-- Ekstra: one col per ekstra --}}
                            @foreach($ekstraList as $ekstra)
                                @php
                                    $ket = $item['ekstra'][$ekstra->id_ekstrakurikuler] ?? null;
                                    $ku  = $ket ? strtoupper(trim($ket)) : null;
                                    $cls = '';
                                    if ($ku) {
                                        if (str_contains($ku, 'SANGAT BAIK'))   $cls = 'bek-sb';
                                        elseif (str_contains($ku, 'BAIK'))      $cls = 'bek-b';
                                        elseif (str_contains($ku, 'CUKUP'))     $cls = 'bek-c';
                                        elseif (str_contains($ku, 'KURANG'))    $cls = 'bek-k';
                                        else                                     $cls = 'bek-d';
                                    }
                                @endphp
                                <td class="{{ $loop->first ? 'sep-l' : '' }}">
                                    @if($ku)
                                        <span class="bek {{ $cls }}">{{ $ku }}</span>
                                    @else
                                        <span class="dash">—</span>
                                    @endif
                                </td>
                            @endforeach

                            <td class="sep-l"><span class="bs">{{ $item['kehadiran']['sakit'] ?? 0 }}</span></td>
                            <td><span class="bi">{{ $item['kehadiran']['izin'] ?? 0 }}</span></td>
                            <td><span class="ba">{{ $item['kehadiran']['alpa'] ?? 0 }}</span></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4 px-3" style="font-size: 0.85rem;">
            {{ $studentsList->links('pagination::bootstrap-5') }}
        </div>
        @else
            <div class="text-center py-5">
                <div style="width:60px;height:60px;background:#f1f5f9;border-radius:14px;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;">
                    <i class="bi bi-inbox fs-3 text-muted"></i>
                </div>
                <h6 class="fw-bold text-dark mb-1">Belum Ada Data</h6>
                <p class="text-muted mb-0" style="font-size:.82rem;">Belum ada nilai terkonsolidasi untuk kelas ini.</p>
            </div>
        @endif
    </div>
</div>


@endsection
