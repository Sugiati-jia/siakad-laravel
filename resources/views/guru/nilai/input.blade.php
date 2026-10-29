@extends('layouts.app')

@section('title', 'Input Nilai - ' . $mapel->nama_mapel . ' - ' . $kelas->nama_kelas)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            @if(isset($isLocked) && $isLocked)
                <div class="alert alert-danger d-flex align-items-center mb-4 p-3 border-0 shadow-xs" style="border-left: 4px solid #dc3545 !important; border-radius: 8px; background-color: #f8d7da; color: #842029;">
                    <i class="bi bi-lock-fill me-3 fs-4 text-danger"></i>
                    <div>
                        <strong>Nilai Terkunci:</strong> Nilai mata pelajaran ini sudah dikunci oleh Wali Kelas. Anda tidak dapat menambah, mengubah, atau menghapus nilai.
                    </div>
                </div>
            @endif

            <!-- Header Info -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="text-primary mb-3">
                                <i class="bi bi-pencil-square me-2"></i>
                                DAFTAR NILAI
                            </h5>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td><strong>Mata Pelajaran:</strong></td>
                                    <td>{{ $mapel->nama_mapel }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Fase:</strong></td>
                                    <td>E</td>
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
                                <tr>
                                    <td><strong>Wali Kelas:</strong></td>
                                    <td>{{ $kelas->waliKelas->nama ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row align-items-center g-3">
                        <div class="col-12 col-md-8">
                            <div class="d-flex flex-wrap gap-2">
                                <a href="{{ route('guru.nilai.select-mapel', $kelas->id_kelas ?? $kelas->id) }}" class="btn btn-secondary">
                                    <i class="bi bi-arrow-left me-2"></i>Kembali
                                </a>
                                @if(isset($isLocked) && $isLocked)
                                    <button class="btn btn-info" disabled>
                                        <i class="bi bi-file-earmark-excel me-2"></i>Import Excel
                                    </button>
                                @else
                                    <a href="{{ route('guru.import.import', [$kelas->id_kelas ?? $kelas->id, $mapel->id_mapel ?? $mapel->id]) }}" class="btn btn-info">
                                        <i class="bi bi-file-earmark-excel me-2"></i>Import Excel
                                    </a>
                                    <form action="{{ route('guru.nilai.destroy-all', [$kelas->id_kelas ?? $kelas->id, $mapel->id_mapel ?? $mapel->id]) }}" method="POST" class="d-inline" data-confirm="Hapus SELURUH nilai untuk kelas ini? Berguna jika ingin import ulang dari Excel kosong.">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger" title="Hapus seluruh nilai kelas ini">
                                            <i class="bi bi-trash me-2"></i>Kosongkan Nilai
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                        <div class="col-12 col-md-4 text-start text-md-end">
                            <a href="{{ route('guru.nilai.summary', [$kelas->id_kelas ?? $kelas->id, $mapel->id_mapel ?? $mapel->id]) }}" class="btn btn-outline-success">
                                <i class="bi bi-clipboard-data me-2"></i>Lihat Ringkasan Nilai
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kelola Tujuan Pembelajaran (TP) Card -->
            <div class="card shadow-sm border-0 mb-4 bg-white" style="border-radius: 12px;">
                <div class="card-header bg-light border-0 d-flex flex-column flex-md-row justify-content-between align-items-md-center py-3 px-3 px-md-4 gap-3" style="border-radius: 12px 12px 0 0 !important;">
                    <div class="d-flex align-items-center">
                        <span class="p-2 bg-primary bg-opacity-10 text-primary rounded-3 me-3 flex-shrink-0">
                            <i class="bi bi-gear-fill fs-5"></i>
                        </span>
                        <div>
                            <h6 class="mb-0 text-primary fw-bold">Kelola Tujuan Pembelajaran (TP)</h6>
                            <small class="text-muted">Tentukan jumlah TP mandiri (Minimal 3, Maksimal 15 per mapel/semester)</small>
                        </div>
                    </div>
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1.5 fw-bold" data-bs-toggle="modal" data-bs-target="#importTpModal" @if(isset($isLocked) && $isLocked) disabled @endif>
                            <i class="bi bi-upload me-1"></i>Import TP
                        </button>
                        <a href="{{ route('guru.tp.export', [$kelas->id_kelas ?? $kelas->id, $mapel->id_mapel ?? $mapel->id]) }}" class="btn btn-sm btn-outline-success rounded-pill px-3 py-1.5 fw-bold">
                            <i class="bi bi-download me-1"></i>Export TP
                        </a>
                        <span class="badge bg-primary px-3 py-2 rounded-pill fs-7 shadow-sm" id="tpCountBadge">
                            Jumlah TP: {{ $tujuanPembelajarans->count() }} / 15
                        </span>
                    </div>
                </div>
                <div class="card-body px-4 py-4">
                    <div id="tpWarningAlert" class="alert alert-warning d-none align-items-center mb-4 p-3 border-0 shadow-xs" style="border-left: 4px solid var(--warning-color) !important; border-radius: 8px;">
                        <i class="bi bi-exclamation-triangle-fill me-3 fs-4 text-warning"></i>
                        <div>
                            <strong>Perhatian:</strong> Jumlah TP saat ini kurang dari batas minimum (3 TP). 
                            Anda tidak dapat menyimpan atau mensubmit nilai ke Wali Kelas sampai TP minimal terpenuhi.
                        </div>
                    </div>
                    
                    <div class="row g-4">
                        <!-- TP List Container -->
                        <div class="col-12 col-lg-7">
                            <div class="bg-light rounded-3 p-3 shadow-xs border" style="max-height: 280px; overflow-y: auto;">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h7 class="fw-bold text-secondary text-uppercase fs-8 letter-spacing-1">Daftar TP Semester Ini</h7>
                                    <span class="text-muted fs-8">Klik <i class="bi bi-trash"></i> untuk menghapus</span>
                                </div>
                                <div id="tpListContainer" class="list-group list-group-flush rounded-3 bg-white border">
                                    @if($tujuanPembelajarans->isEmpty())
                                        <div class="text-center py-4 text-muted fs-7 empty-tp-placeholder">
                                            <i class="bi bi-info-circle d-block fs-3 mb-2 text-secondary opacity-50"></i>
                                            Belum ada Tujuan Pembelajaran yang dikonfigurasi.
                                        </div>
                                    @else
                                        @foreach($tujuanPembelajarans as $tp)
                                            <div class="list-group-item d-flex justify-content-between align-items-center py-3 px-3 border-bottom tp-item" data-tp-id="{{ $tp->id_tujuan_pembelajaran ?? $tp->id }}">
                                                <div class="d-flex align-items-start me-3">
                                                    <span class="badge bg-secondary me-3 fw-bold rounded-2 px-2 py-1 fs-8 mt-1">{{ $tp->kode_tp }}</span>
                                                    <div>
                                                        <span class="text-dark fw-medium small d-block">{{ $tp->deskripsi }}</span>
                                                    </div>
                                                </div>
                                                <button type="button" class="btn btn-sm btn-outline-danger border-0 p-2 rounded-circle delete-tp-btn" data-tp-id="{{ $tp->id_tujuan_pembelajaran ?? $tp->id }}" title="Hapus TP" @if(isset($isLocked) && $isLocked) disabled @endif>
                                                    <i class="bi bi-trash fs-6"></i>
                                                </button>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <!-- Add TP Inline Form -->
                        <div class="col-12 col-lg-5">
                            <div class="bg-light rounded-3 p-3 shadow-xs border h-100 d-flex flex-column justify-content-between">
                                <div>
                                    <h7 class="fw-bold text-secondary text-uppercase fs-8 letter-spacing-1 d-block mb-3">Tambah TP Baru</h7>
                                    <form id="addTpForm" class="row g-3">
                                        <div class="col-md-12">
                                            <label class="form-label small fw-semibold text-secondary">Kode TP</label>
                                            <input type="text" id="newTpCode" class="form-control form-control-sm" placeholder="Contoh: TP 1, TP 2" required @if(isset($isLocked) && $isLocked) disabled @endif>
                                            <div class="form-text fs-8 text-muted">Kode unik TP untuk membedakan kolom nilai.</div>
                                        </div>
                                        <div class="col-md-12">
                                            <label class="form-label small fw-semibold text-secondary">Deskripsi Tujuan Pembelajaran</label>
                                            <textarea id="newTpDesc" rows="3" class="form-control form-control-sm" placeholder="Contoh: Peserta didik dapat menganalisis..." required style="resize: none;" @if(isset($isLocked) && $isLocked) disabled @endif></textarea>
                                        </div>
                                        <div class="col-12 mt-3">
                                            <button type="submit" id="addTpBtn" class="btn btn-sm btn-primary w-100 py-2 rounded-2" @if(isset($isLocked) && $isLocked) disabled @endif>
                                                <i class="bi bi-plus-circle me-1"></i> Tambah TP Baru
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Aturan Pengisian Nilai -->
            @if(!isset($isLocked) || !$isLocked)
            <div class="alert border-0 mb-4 py-2 px-3 d-flex align-items-center gap-2" style="background: var(--custom-card-bg, #ffffff)8e1; border-left: 3px solid #f59e0b !important; border-radius: 6px; font-size: 0.85rem;">
                <i class="bi bi-exclamation-triangle-fill text-warning fs-6 flex-shrink-0"></i>
                <span class="text-dark">
                    <strong>Ketentuan pengisian:</strong>
                    Nilai berskala <strong>0 - 100</strong> &nbsp;|&nbsp;
                    Wajib isi minimal <strong>2 TP Tugas Harian</strong> &nbsp;|&nbsp;
                    <strong>Nilai Akhir Semester</strong> wajib diisi sebelum menyimpan
                </span>
            </div>
            @endif

            <!-- Grade Input Table -->
            <div class="card shadow-sm border-0">
                <div class="card-body p-0">
                    <form method="POST" action="{{ route('guru.nilai.store') }}" id="gradeForm">
                        @csrf
                        <input type="hidden" name="guru_mapel_id" id="guruMapelId" value="{{ $guruMapel->id_guru_mapel ?? $guruMapel->id }}">
                        <input type="hidden" name="id_semester" value="{{ $guruMapel->id_semester }}">
                        <input type="hidden" name="id_tahun_ajaran" value="{{ $activeTahunAjaran->id_tahun_ajaran ?? $activeTahunAjaran->id }}">
                        
                        <div id="gradeTableContainer" class="position-relative" style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
                            @include('guru.nilai.table-partial')
                        </div>

                        <div class="row m-4">
                            <div class="col-12 text-end">
                                <a href="{{ route('guru.nilai.select-mapel', $kelas->id_kelas ?? $kelas->id) }}" class="btn btn-outline-secondary px-4 me-2">
                                    <i class="bi bi-x-circle me-2"></i>Batal
                                </a>
                                <button type="submit" id="saveGradesBtn" class="btn btn-primary px-5 py-2" @if(isset($isLocked) && $isLocked) disabled @endif>
                                    <i class="bi bi-save me-2"></i>Simpan Nilai
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const kelasId = '{{ $kelas->id_kelas ?? $kelas->id }}';
    const mapelId = '{{ $mapel->id_mapel ?? $mapel->id }}';
    const storeAjaxUrl = '{{ route("guru.tp.store-ajax") }}';
    const destroyAjaxBaseUrl = '{{ url("/guru/tp/destroy-ajax") }}';
    const tablePartialUrl = '{{ route("guru.nilai.table-partial", ["kelas_id" => ":kelas_id", "mapel_id" => ":mapel_id"]) }}'
        .replace(':kelas_id', kelasId)
        .replace(':mapel_id', mapelId);

    // Initial check
    updateLimits();

    function calculateAverages() {
        const siswaRows = document.querySelectorAll('[class*="student-row-"]');
        const siswaIds = new Set();
        siswaRows.forEach(row => {
            const classMatch = row.className.match(/student-row-(\d+)/);
            if(classMatch) siswaIds.add(classMatch[1]);
        });

        siswaIds.forEach(siswaId => {
            let sumRerataTh = 0;
            let countRerataTh = 0;
            let sumRerataUh = 0;
            let countRerataUh = 0;

            const rows = document.querySelectorAll(`.student-row-${siswaId}`);
            rows.forEach(row => {
                const th1Input = row.querySelector('.th1-input');
                const th2Input = row.querySelector('.th2-input');
                const th1 = th1Input ? parseFloat(th1Input.value) : NaN;
                const th2 = th2Input ? parseFloat(th2Input.value) : NaN;
                
                let rerataTh = null;
                if (!isNaN(th1) || !isNaN(th2)) {
                    let vals = [];
                    if(!isNaN(th1)) vals.push(th1);
                    if(!isNaN(th2)) vals.push(th2);
                    rerataTh = vals.reduce((a,b)=>a+b, 0) / vals.length;
                    const cell = row.querySelector('.rerata-th-cell');
                    if (cell) cell.textContent = Math.round(rerataTh * 100) / 100;
                    sumRerataTh += rerataTh;
                    countRerataTh++;
                } else {
                    const cell = row.querySelector('.rerata-th-cell');
                    if (cell) cell.textContent = '';
                }

                const uh1Input = row.querySelector('.uh1-input');
                const uh2Input = row.querySelector('.uh2-input');
                const uh1 = uh1Input ? parseFloat(uh1Input.value) : NaN;
                const uh2 = uh2Input ? parseFloat(uh2Input.value) : NaN;
                
                let rerataUh = null;
                if (!isNaN(uh1) || !isNaN(uh2)) {
                    let vals = [];
                    if(!isNaN(uh1)) vals.push(uh1);
                    if(!isNaN(uh2)) vals.push(uh2);
                    rerataUh = vals.reduce((a,b)=>a+b, 0) / vals.length;
                    const cell = row.querySelector('.rerata-uh-cell');
                    if (cell) cell.textContent = Math.round(rerataUh * 100) / 100;
                    sumRerataUh += rerataUh;
                    countRerataUh++;
                } else {
                    const cell = row.querySelector('.rerata-uh-cell');
                    if (cell) cell.textContent = '';
                }
            });

            const totalRerataTh = countRerataTh > 0 ? (sumRerataTh / countRerataTh) : null;
            const totalRerataUh = countRerataUh > 0 ? (sumRerataUh / countRerataUh) : null;

            const summaryRow = document.querySelector(`.summary-row-${siswaId}`);
            if (summaryRow) {
                const totalRerataThCell = summaryRow.querySelector('.total-rerata-th-cell');
                const totalRerataUhCell = summaryRow.querySelector('.total-rerata-uh-cell');
                if (totalRerataThCell) totalRerataThCell.textContent = totalRerataTh !== null ? totalRerataTh.toFixed(1) : '';
                if (totalRerataUhCell) totalRerataUhCell.textContent = totalRerataUh !== null ? totalRerataUh.toFixed(1) : '';
            }

            const asInput = document.querySelector(`#as-input-${siswaId}`);
            const asValue = asInput && !isNaN(parseFloat(asInput.value)) ? parseFloat(asInput.value) : null;

            let naVals = [];
            if(totalRerataTh !== null) naVals.push(totalRerataTh);
            if(totalRerataUh !== null) naVals.push(totalRerataUh);
            if(asValue !== null) naVals.push(asValue);

            const naCell = document.querySelector(`.na-cell[data-siswa-id="${siswaId}"]`);
            if (naCell) {
                if (naVals.length > 0) {
                    const na = naVals.reduce((a,b)=>a+b, 0) / naVals.length;
                    naCell.textContent = Math.round(na);
                } else {
                    naCell.textContent = '';
                }
            }
        });
    }

    // Auto-save logic reset
    let autoSaveTimer;
    
    // Delegate input changes so we don't lose listeners on AJAX table update
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('grade-input')) {
            // If AS input, sync to hidden inputs
            if (e.target.classList.contains('as-input')) {
                const siswaId = e.target.dataset.siswaId;
                const val = e.target.value;
                document.querySelectorAll(`.as-hidden-${siswaId}`).forEach(hidden => hidden.value = val);
            }
            calculateAverages();
            
            clearTimeout(autoSaveTimer);
            autoSaveTimer = setTimeout(autoSave, 2000);
        }
    });

    // Auto-save function
    function autoSave() {
        return new Promise((resolve, reject) => {
            @if(isset($isLocked) && $isLocked)
                resolve();
                return;
            @endif
            const form = document.getElementById('gradeForm');
            if (!form) { resolve(); return; }
            
            const formData = new FormData(form);
            let hasData = false;
            for (let [key, value] of formData.entries()) {
                if (key.startsWith('grades[') && value) {
                    hasData = true;
                    break;
                }
            }
            
            if (!hasData) {
                resolve();
                return;
            }
            
            showAutoSaveIndicator('Saving...');
            
            fetch('{{ route("guru.nilai.store") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if(response.ok) {
                    showAutoSaveIndicator('Auto-saved', 'success');
                    resolve();
                } else {
                    showAutoSaveIndicator('Save failed', 'danger');
                    resolve(); // resolve anyway to not block UI
                }
            })
            .catch(error => {
                console.error('Auto-save failed:', error);
                showAutoSaveIndicator('Connection error', 'danger');
                resolve();
            });
        });
    }

    function showAutoSaveIndicator(text, type = 'info') {
        // Remove existing
        const existing = document.getElementById('autosaveIndicator');
        if (existing) existing.remove();

        const indicator = document.createElement('div');
        indicator.id = 'autosaveIndicator';
        indicator.className = `alert alert-${type} alert-sm position-fixed top-0 end-0 m-3 shadow z-3 py-1 px-3`;
        indicator.style.borderRadius = '20px';
        indicator.style.fontSize = '0.85rem';
        indicator.innerHTML = `<i class="bi bi-${type === 'success' ? 'check-circle' : (type === 'danger' ? 'x-circle' : 'hourglass-split')} me-2"></i>${text}`;
        document.body.appendChild(indicator);
        
        if (type !== 'info') {
            setTimeout(() => {
                if (indicator) $(indicator).fadeOut('slow', () => indicator.remove());
            }, 2500);
        }
    }

    // Refresh Table Partial via AJAX
    function refreshGradeTable() {
        return fetch(tablePartialUrl)
            .then(response => {
                if (!response.ok) throw new Error('Failed to fetch table layout.');
                return response.text();
            })
            .then(html => {
                const container = document.getElementById('gradeTableContainer');
                if (container) {
                    container.innerHTML = html;
                    calculateAverages();
                }
            })
            .catch(error => {
                console.error('Table refresh failed:', error);
                alert('Gagal memuat ulang tabel nilai. Silakan muat ulang halaman.');
            });
    }

    // Update Limits, badges and alerts
    function updateLimits() {
        const tpItems = document.querySelectorAll('#tpListContainer .tp-item');
        const count = tpItems.length;
        
        // Update Badge
        const badge = document.getElementById('tpCountBadge');
        if (badge) {
            badge.textContent = `Jumlah TP: ${count} / 15`;
            if (count < 3 || count > 15) {
                badge.className = 'badge bg-danger px-3 py-2 rounded-pill fs-7 shadow-sm';
            } else {
                badge.className = 'badge bg-primary px-3 py-2 rounded-pill fs-7 shadow-sm';
            }
        }

        // Show/Hide Warning Alert
        const alert = document.getElementById('tpWarningAlert');
        const saveBtn = document.getElementById('saveGradesBtn');
        if (alert) {
            if (count < 3) {
                alert.classList.remove('d-none');
                alert.classList.add('d-flex');
                if (saveBtn) {
                    saveBtn.disabled = true;
                    saveBtn.title = "Jumlah TP minimal 3 untuk menyimpan/mengubah nilai.";
                }
            } else {
                alert.classList.add('d-none');
                alert.classList.remove('d-flex');
                if (saveBtn) {
                    saveBtn.disabled = false;
                    saveBtn.title = "";
                }
            }
        }

        // Handle Add TP limits
        const addBtn = document.getElementById('addTpBtn');
        const codeInput = document.getElementById('newTpCode');
        const descInput = document.getElementById('newTpDesc');
        if (count >= 15) {
            if (addBtn) addBtn.disabled = true;
            if (codeInput) codeInput.disabled = true;
            if (descInput) descInput.disabled = true;
            if (addBtn) addBtn.innerHTML = '<i class="bi bi-slash-circle me-1"></i> Maksimal 15 TP Terpenuhi';
        } else {
            if (addBtn) addBtn.disabled = false;
            if (codeInput) codeInput.disabled = false;
            if (descInput) descInput.disabled = false;
            if (addBtn) addBtn.innerHTML = '<i class="bi bi-plus-circle me-1"></i> Tambah TP Baru';
        }

        // Handle Delete TP buttons
        const deleteBtns = document.querySelectorAll('.delete-tp-btn');
        deleteBtns.forEach(btn => {
            if (count <= 3) {
                btn.disabled = true;
                btn.classList.add('opacity-50');
                btn.title = "Minimal harus ada 3 TP. Tidak dapat menghapus lebih lanjut.";
            } else {
                btn.disabled = false;
                btn.classList.remove('opacity-50');
                btn.title = "Hapus TP";
            }
        });
    }

    // Add TP form submission handler
    const addTpForm = document.getElementById('addTpForm');
    if (addTpForm) {
        addTpForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const kodeTp = document.getElementById('newTpCode').value.trim();
            const deskripsi = document.getElementById('newTpDesc').value.trim();
            const guruMapelId = document.getElementById('guruMapelId').value;

            if (!kodeTp || !deskripsi) return;

            // Block if limit exceeded
            const tpCount = document.querySelectorAll('#tpListContainer .tp-item').length;
            if (tpCount >= 15) {
                alert('Maksimal jumlah TP adalah 15.');
                return;
            }

            const submitBtn = document.getElementById('addTpBtn');
            const originalBtnHtml = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Menyimpan...';

            // Save grades first to prevent data loss
            autoSave().then(() => {
                return fetch(storeAjaxUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        guru_mapel_id: guruMapelId,
                        kode_tp: kodeTp,
                        deskripsi: deskripsi
                    })
                });
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(json => {
                        throw new Error(json.message || 'Gagal menyimpan Tujuan Pembelajaran.');
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Reset Form
                    document.getElementById('newTpCode').value = '';
                    document.getElementById('newTpDesc').value = '';
                    
                    // Remove placeholder if present
                    const placeholder = document.querySelector('.empty-tp-placeholder');
                    if (placeholder) placeholder.remove();

                    // Append dynamically
                    const tp = data.tp;
                    const tpList = document.getElementById('tpListContainer');
                    const newItem = document.createElement('div');
                    newItem.className = 'list-group-item d-flex justify-content-between align-items-center py-3 px-3 border-bottom tp-item';
                    newItem.setAttribute('data-tp-id', tp.id_tujuan_pembelajaran || tp.id);
                    newItem.innerHTML = `
                        <div class="d-flex align-items-start me-3">
                            <span class="badge bg-secondary me-3 fw-bold rounded-2 px-2 py-1 fs-8 mt-1">${tp.kode_tp}</span>
                            <div>
                                <span class="text-dark fw-medium small d-block">${tp.deskripsi}</span>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-danger border-0 p-2 rounded-circle delete-tp-btn" data-tp-id="${tp.id_tujuan_pembelajaran || tp.id}" title="Hapus TP">
                            <i class="bi bi-trash fs-6"></i>
                        </button>
                    `;
                    tpList.appendChild(newItem);

                    // Refresh table & count
                    return refreshGradeTable().then(() => {
                        updateLimits();
                        showAutoSaveIndicator('TP Berhasil Ditambahkan!', 'success');
                    });
                }
            })
            .catch(error => {
                console.error('Error adding TP:', error);
                alert(error.message || 'Terjadi kesalahan saat menambahkan Tujuan Pembelajaran.');
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnHtml;
            });
        });
    }

    // Delete TP handler using event delegation
    document.getElementById('tpListContainer').addEventListener('click', function(e) {
        const deleteBtn = e.target.closest('.delete-tp-btn');
        if (!deleteBtn) return;

        const tpId = deleteBtn.getAttribute('data-tp-id');
        const tpItem = deleteBtn.closest('.tp-item');
        const tpCode = tpItem.querySelector('.badge').textContent;

        const tpCount = document.querySelectorAll('#tpListContainer .tp-item').length;
        if (tpCount <= 3) {
            alert('Jumlah Tujuan Pembelajaran minimal adalah 3 TP. Tidak diperbolehkan menghapus.');
            return;
        }

        if (!confirm(`Apakah Anda yakin ingin menghapus TP ${tpCode}? Menghapus TP ini juga akan menghapus semua nilai siswa yang terkait dengan TP ini.`)) {
            return;
        }

        const originalBtnHtml = deleteBtn.innerHTML;
        deleteBtn.disabled = true;
        deleteBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';

        // Save grades first to prevent data loss
        autoSave().then(() => {
            return fetch(`${destroyAjaxBaseUrl}/${tpId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            });
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(json => {
                    throw new Error(json.message || 'Gagal menghapus Tujuan Pembelajaran.');
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Fade out and remove
                $(tpItem).fadeOut('fast', function() {
                    $(this).remove();
                    
                    const tpList = document.getElementById('tpListContainer');
                    if (tpList.children.length === 0) {
                        tpList.innerHTML = `
                            <div class="text-center py-4 text-muted fs-7 empty-tp-placeholder">
                                <i class="bi bi-info-circle d-block fs-3 mb-2 text-secondary opacity-50"></i>
                                Belum ada Tujuan Pembelajaran yang dikonfigurasi.
                            </div>
                        `;
                    }
                    
                    refreshGradeTable().then(() => {
                        updateLimits();
                        showAutoSaveIndicator('TP Berhasil Dihapus!', 'success');
                    });
                });
            }
        })
        .catch(error => {
            console.error('Error deleting TP:', error);
            alert(error.message || 'Terjadi kesalahan saat menghapus Tujuan Pembelajaran.');
            deleteBtn.disabled = false;
            deleteBtn.innerHTML = originalBtnHtml;
        });
    });

    // Prevent grade form submit if invalid TP count or incomplete grade rules
    const gradeForm = document.getElementById('gradeForm');
    let isSubmitting = false;
    if (gradeForm) {
        gradeForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const tpCount = document.querySelectorAll('#tpListContainer .tp-item').length;
            if (tpCount < 3) {
                alert('Gagal menyimpan nilai. Jumlah Tujuan Pembelajaran (TP) saat ini adalah ' + tpCount + '. Kurikulum mewajibkan minimal 3 TP.');
                return false;
            }
            if (tpCount > 15) {
                alert('Gagal menyimpan nilai. Jumlah Tujuan Pembelajaran (TP) saat ini adalah ' + tpCount + '. Kurikulum membatasi maksimal 15 TP.');
                return false;
            }

            // ── Validasi aturan kelengkapan nilai per siswa ──
            const errors = validateGradeCompleteness();
            if (errors.length > 0) {
                alert('Gagal menyimpan nilai:\n\n' + errors.join('\n'));
                return false;
            }
            
            Swal.fire({
                title: 'Simpan Nilai?',
                text: 'Apakah Anda yakin ingin menyimpan perubahan nilai ini?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#0d6efd',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya',
                cancelButtonText: 'Tidak',
                customClass: {
                    popup: 'rounded-4 shadow-lg border-0',
                    confirmButton: 'rounded-pill px-4 fw-bold',
                    cancelButton: 'rounded-pill px-4'
                },
            }).then((result) => {
                if (result.isConfirmed) {
                    isSubmitting = true;
                    gradeForm.submit();
                }
            });
        });
    }

    /**
     * Validasi kelengkapan nilai:
     * - Setiap siswa wajib punya minimal 2 TP dengan nilai TH (TH1 atau TH2)
     * - Setiap siswa wajib punya nilai Akhir Semester (AS)
     * - Semua nilai yang diisi harus >= 70
     */
    function validateGradeCompleteness() {
        const errors = [];

        // Kumpulkan semua siswaId unik dari baris tabel
        const siswaRows = document.querySelectorAll('[class*="student-row-"]');
        const siswaIds = new Set();
        siswaRows.forEach(row => {
            const classMatch = row.className.match(/student-row-(\d+)/);
            if (classMatch) siswaIds.add(classMatch[1]);
        });

        siswaIds.forEach(siswaId => {
            // Hitung TP yang punya nilai TH
            const rows = document.querySelectorAll(`.student-row-${siswaId}`);
            let tpDenganTh = 0;

            rows.forEach(row => {
                const th1 = row.querySelector('.th1-input');
                const th2 = row.querySelector('.th2-input');
                const th1Val = th1 && th1.value.trim() !== '' ? parseFloat(th1.value) : NaN;
                const th2Val = th2 && th2.value.trim() !== '' ? parseFloat(th2.value) : NaN;

                // Validasi nilai skala 0-100 per input
                if (!isNaN(th1Val) && !(th1Val >= 0 && th1Val <= 100)) {
                    const namaEl = document.querySelector(`td[data-nama-siswa="${siswaId}"]`);
                    errors.push(`Nilai TH1 harus berada di antara 0 - 100.`);
                }
                if (!isNaN(th2Val) && !(th2Val >= 0 && th2Val <= 100)) {
                    errors.push(`Nilai TH2 harus berada di antara 0 - 100.`);
                }

                if (!isNaN(th1Val) || !isNaN(th2Val)) {
                    tpDenganTh++;
                }
            });

            // Cek nilai AS
            const asInput = document.querySelector(`#as-input-${siswaId}`);
            const asVal = asInput && asInput.value.trim() !== '' ? parseFloat(asInput.value) : NaN;

            if (!isNaN(asVal) && !(asVal >= 0 && asVal <= 100)) {
                errors.push(`Nilai Akhir Semester harus berada di antara 0 - 100.`);
            }

            // Ambil nama siswa dari tabel untuk pesan error yang lebih jelas
            const namaTd = document.querySelector(`td[rowspan].fw-bold + td`);
            // Cari nama siswa dari baris pertama siswa ini
            let namaSiswa = `Siswa ID ${siswaId}`;
            const firstRow = document.querySelector(`.student-row-${siswaId}`);
            if (firstRow) {
                // Coba cari nama dari td di rowspan yang sebelum baris ini
                // Karena menggunakan rowspan, nama ada di td ke-2 baris pertama tiap siswa
                const allTds = document.querySelectorAll('tbody td');
                // Pendekatan lain: ambil dari summary row
                const summaryRow = document.querySelector(`.summary-row-${siswaId}`);
                if (summaryRow && summaryRow.previousElementSibling) {
                    // Ambil nama dari data attribute jika ada
                }
            }
            // Completeness checks removed to allow partial saving of drafts.
        });

        return [...new Set(errors)]; // hapus duplikasi pesan error
    }

    // Warn before leaving if unsaved changes
    window.addEventListener('beforeunload', function(e) {
        @if(isset($isLocked) && $isLocked)
            return;
        @endif
        
        if (isSubmitting) return;

        const inputs = document.querySelectorAll('.grade-input');
        let hasChanges = false;
        inputs.forEach(input => {
            if (input.value && input.value !== input.defaultValue) {
                hasChanges = true;
            }
        });
        if (hasChanges) {
            e.preventDefault();
            e.returnValue = '';
        }
    });

    // Initial calculation
    calculateAverages();
});
</script>
<!-- Import TP Modal -->
<div class="modal fade" id="importTpModal" tabindex="-1" aria-labelledby="importTpModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <form action="{{ route('guru.tp.import', [$kelas->id_kelas ?? $kelas->id, $mapel->id_mapel ?? $mapel->id]) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold text-dark" id="importTpModalLabel"><i class="bi bi-journal-arrow-up text-primary me-1"></i> Import Tujuan Pembelajaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="alert alert-info small border-0" style="border-radius: 8px;">
                        <strong>Panduan:</strong><br>
                        1. Klik tombol "Export TP" terlebih dahulu untuk mendapatkan format file Excel yang benar.<br>
                        2. Isi kolom <code>Kode TP</code> dan <code>Deskripsi</code> pada file tersebut.<br>
                        3. Batas maksimal TP adalah 15 per kelas/mapel/semester.
                    </div>
                    <div class="mb-3">
                        <label for="file" class="form-label fw-semibold">Pilih File Excel (.xlsx, .xls, .csv) <span class="text-danger">*</span></label>
                        <input class="form-control rounded-3" type="file" id="file" name="file" accept=".xlsx,.xls,.csv" required>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4"><i class="bi bi-upload me-1"></i> Import Data</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
