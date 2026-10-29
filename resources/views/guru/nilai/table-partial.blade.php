<div class="table-responsive">
    <table class="table table-bordered mb-0 excel-like-table table-hover align-middle" style="min-width: 800px;">
        <thead class="table-light text-center align-middle">
            <tr>
                <th rowspan="3" width="5%">No</th>
                <th rowspan="3" width="20%" style="min-width: 200px;">NAMA PESERTA DIDIK</th>
                <th colspan="7">JENIS PENILAIAN</th>
                <th rowspan="3" width="10%">AKHIR<br>SEMESTER</th>
                <th rowspan="3" width="5%">NA</th>
            </tr>
            <tr>
                <th rowspan="2" width="5%">KD</th>
                <th colspan="2">TUGAS HARIAN</th>
                <th rowspan="2" width="8%">Rerata</th>
                <th colspan="2">ULANGAN HARIAN</th>
                <th rowspan="2" width="8%">Rerata</th>
            </tr>
            <tr>
                <th width="8%" style="min-width: 65px;">1</th>
                <th width="8%" style="min-width: 65px;">2</th>
                <th width="8%" style="min-width: 65px;">1</th>
                <th width="8%" style="min-width: 65px;">2</th>
            </tr>
        </thead>
        <tbody>
            @php $gradeIndex = 0; @endphp
            @foreach($siswas as $index => $siswa)
                @php
                    $tpCount = $tujuanPembelajarans->count();
                    // Rowspan is the number of TPs + 1 (for the summary row). If no TPs, at least 1 + 1 = 2
                    $rowspan = $tpCount > 0 ? $tpCount + 1 : 2;
                    
                    // Get AS value by checking all TPs for this student
                    $siswaId = $siswa->id_siswa ?? $siswa->id;
                    $asValue = '';
                    foreach ($tujuanPembelajarans as $tp) {
                        $tpId = $tp->id_tujuan_pembelajaran ?? $tp->id;
                        $gradeKey = $siswaId . '_' . $tpId;
                        if (isset($existingGrades[$gradeKey]) && $existingGrades[$gradeKey]->nilai_as !== null) {
                            $asValue = $existingGrades[$gradeKey]->nilai_as;
                            break;
                        }
                    }
                    
                    // Variables for summary row calculation
                    $totalRerataTh = 0;
                    $totalRerataUh = 0;
                    $countRerataTh = 0;
                    $countRerataUh = 0;
                @endphp
                
                @if($tpCount > 0)
                    @foreach($tujuanPembelajarans as $tpIdx => $tp)
                        @php
                            $tpId = $tp->id_tujuan_pembelajaran ?? $tp->id;
                            $gradeKey = $siswaId . '_' . $tpId;
                            $existingGrade = $existingGrades[$gradeKey] ?? null;
                            
                            if ($existingGrade && $existingGrade->nilai_th !== null) {
                                $totalRerataTh += $existingGrade->nilai_th;
                                $countRerataTh++;
                            }
                            if ($existingGrade && $existingGrade->nilai_uh !== null) {
                                $totalRerataUh += $existingGrade->nilai_uh;
                                $countRerataUh++;
                            }
                        @endphp
                        <tr class="student-row-{{ $siswaId }}">
                            @if($tpIdx === 0)
                                <td rowspan="{{ $rowspan }}" class="text-center fw-bold">{{ $index + 1 }}</td>
                                <td rowspan="{{ $rowspan }}">{{ $siswa->nama }}</td>
                            @endif
                            
                            <td class="text-center bg-light fw-bold text-secondary">{{ $tp->kode_tp }}</td>
                            
                            <input type="hidden" name="grades[{{ $gradeIndex }}][id_siswa]" value="{{ $siswaId }}">
                            <input type="hidden" name="grades[{{ $gradeIndex }}][id_tujuan_pembelajaran]" value="{{ $tpId }}">
                            
                            <td>
                                <input type="number" 
                                       name="grades[{{ $gradeIndex }}][nilai_th1]" 
                                       class="form-control form-control-sm text-center grade-input th-input th1-input" 
                                       value="{{ $existingGrade->nilai_th1 ?? '' }}"
                                       min="70" max="100" step="0.01"
                                       data-siswa-id="{{ $siswaId }}" data-row-idx="{{ $tpIdx }}"
                                       @if(isset($isLocked) && $isLocked) disabled @endif>
                            </td>
                            <td>
                                <input type="number" 
                                       name="grades[{{ $gradeIndex }}][nilai_th2]" 
                                       class="form-control form-control-sm text-center grade-input th-input th2-input" 
                                       value="{{ $existingGrade->nilai_th2 ?? '' }}"
                                       min="70" max="100" step="0.01"
                                       data-siswa-id="{{ $siswaId }}" data-row-idx="{{ $tpIdx }}"
                                       @if(isset($isLocked) && $isLocked) disabled @endif>
                            </td>
                            <td class="text-center bg-light rerata-th-cell" data-siswa-id="{{ $siswaId }}" data-row-idx="{{ $tpIdx }}">
                                {{ $existingGrade && $existingGrade->nilai_th !== null ? number_format($existingGrade->nilai_th, 2) : '' }}
                            </td>
                            
                            <td>
                                <input type="number" 
                                       name="grades[{{ $gradeIndex }}][nilai_uh1]" 
                                       class="form-control form-control-sm text-center grade-input uh-input uh1-input" 
                                       value="{{ $existingGrade->nilai_uh1 ?? '' }}"
                                       min="70" max="100" step="0.01"
                                       data-siswa-id="{{ $siswaId }}" data-row-idx="{{ $tpIdx }}"
                                       @if(isset($isLocked) && $isLocked) disabled @endif>
                            </td>
                            <td>
                                <input type="number" 
                                       name="grades[{{ $gradeIndex }}][nilai_uh2]" 
                                       class="form-control form-control-sm text-center grade-input uh-input uh2-input" 
                                       value="{{ $existingGrade->nilai_uh2 ?? '' }}"
                                       min="70" max="100" step="0.01"
                                       data-siswa-id="{{ $siswaId }}" data-row-idx="{{ $tpIdx }}"
                                       @if(isset($isLocked) && $isLocked) disabled @endif>
                            </td>
                            <td class="text-center bg-light rerata-uh-cell" data-siswa-id="{{ $siswaId }}" data-row-idx="{{ $tpIdx }}">
                                {{ $existingGrade && $existingGrade->nilai_uh !== null ? number_format($existingGrade->nilai_uh, 2) : '' }}
                            </td>
                            
                            @if($tpIdx === 0)
                                <td rowspan="{{ $rowspan }}">
                                    <input type="number" 
                                           name="grades[{{ $gradeIndex }}][nilai_as]" 
                                           class="form-control form-control-sm text-center grade-input as-input" 
                                           value="{{ $asValue }}"
                                           min="70" max="100" step="0.01"
                                           id="as-input-{{ $siswaId }}"
                                           data-siswa-id="{{ $siswaId }}"
                                           @if(isset($isLocked) && $isLocked) disabled @endif>
                                </td>
                                <td rowspan="{{ $rowspan }}" class="text-center bg-light fw-bold na-cell" data-siswa-id="{{ $siswaId }}">
                                    <!-- NA handled by JS -->
                                </td>
                            @else
                                <input type="hidden" name="grades[{{ $gradeIndex }}][nilai_as]" class="as-hidden-{{ $siswaId }}" value="{{ $asValue }}">
                            @endif
                            
                        </tr>
                        @php $gradeIndex++; @endphp
                    @endforeach
                    
                    {{-- Summary Row for this student --}}
                    <tr class="table-secondary summary-row-{{ $siswaId }}">
                        <td colspan="3" class="text-end fw-bold align-middle">Nilai Penugasan Harian</td>
                        <td class="text-center fw-bold align-middle total-rerata-th-cell" data-siswa-id="{{ $siswaId }}">
                            {{ $countRerataTh > 0 ? number_format($totalRerataTh / $countRerataTh, 2) : '' }}
                        </td>
                        <td colspan="2" class="text-end fw-bold align-middle">Nilai PH</td>
                        <td class="text-center fw-bold align-middle total-rerata-uh-cell" data-siswa-id="{{ $siswaId }}">
                            {{ $countRerataUh > 0 ? number_format($totalRerataUh / $countRerataUh, 2) : '' }}
                        </td>
                    </tr>
                @else
                    <tr>
                        <td class="text-center fw-bold align-middle">{{ $index + 1 }}</td>
                        <td class="align-middle">{{ $siswa->nama }}</td>
                        <td colspan="9" class="text-center text-muted py-4">Belum ada Tujuan Pembelajaran di kelas ini. Silakan tambahkan Tujuan Pembelajaran (minimal 5, maksimal 15) di panel atas.</td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>
</div>
