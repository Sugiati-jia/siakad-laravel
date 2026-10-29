@extends('layouts.app')

@section('title', 'Catatan Wali Kelas - ' . ($kelas->nama_kelas ?? ''))

@section('content')
<div class="container-fluid py-3">
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('walikelas.dashboard') }}" class="btn btn-outline-secondary btn-sm me-3">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h4 class="mb-0">Catatan Wali Kelas</h4>
            <small class="text-muted">Kelas: <strong>{{ $kelas->nama_kelas ?? '-' }}</strong></small>
        </div>
    </div>

    {{-- Form Input Catatan --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <h6 class="mb-0 fw-bold"><i class="bi bi-plus-circle text-primary me-2"></i>Tambah Catatan & Rekap Absensi</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('walikelas.catatan.store') }}" method="POST">
                @csrf
                <input type="hidden" name="id_kelas" value="{{ $kelas->id_kelas }}">
                <input type="hidden" name="id_semester" value="{{ $activeTahunAjaran->id_semester ?? 1 }}">
                <input type="hidden" name="id_tahun_ajaran" value="{{ $activeTahunAjaran->id_tahun_ajaran ?? '' }}">

                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Siswa</label>
                        <select name="id_siswa" class="form-select" required>
                            <option value="">-- Pilih Siswa --</option>
                            @foreach($siswas as $siswa)
                                <option value="{{ $siswa->id_siswa }}">{{ $siswa->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Jenis Catatan</label>
                        <select name="jenis_catatan" class="form-select" required>
                            <option value="umum">Umum</option>
                            <option value="prestasi">Prestasi</option>
                            <option value="pelanggaran">Pelanggaran</option>
                        </select>
                    </div>
                    <div class="col-md-1 col-4">
                        <label class="form-label fw-semibold text-success">Sakit</label>
                        <input type="number" name="sakit" class="form-control" min="0" value="0" required>
                    </div>
                    <div class="col-md-1 col-4">
                        <label class="form-label fw-semibold text-primary">Izin</label>
                        <input type="number" name="izin" class="form-control" min="0" value="0" required>
                    </div>
                    <div class="col-md-1 col-4">
                        <label class="form-label fw-semibold text-danger">Alpa</label>
                        <input type="number" name="alpa" class="form-control" min="0" value="0" required>
                    </div>
                    @if(isset($activeTahunAjaran) && $activeTahunAjaran->id_semester == 2)
                    <div class="col-md-3">
                        <label class="form-label fw-semibold text-warning"><i class="bi bi-star-fill me-1"></i>Kenaikan Kelas</label>
                        <select name="status_kenaikan" class="form-select border-warning shadow-sm">
                            <option value="">-- Pilih --</option>
                            <option value="naik">Naik Kelas</option>
                            <option value="tinggal">Tinggal Kelas</option>
                        </select>
                    </div>
                    @endif
                    <div class="col-md-12">
                        <label class="form-label fw-semibold">Catatan</label>
                        <div class="input-group">
                            <textarea name="catatan" class="form-control" rows="1" placeholder="Tulis catatan..." required></textarea>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-1"></i> Simpan
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Daftar Catatan Per Siswa --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h6 class="mb-0 fw-bold"><i class="bi bi-journal-text text-primary me-2"></i>Daftar Catatan & Absensi Siswa</h6>
        </div>
        <div class="card-body p-0">
            @if($siswas->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" style="width: 50px;">No</th>
                                <th>Siswa</th>
                                <th>Catatan</th>
                                <th class="text-center">Jenis</th>
                                <th class="text-center text-success">Sakit</th>
                                <th class="text-center text-primary">Izin</th>
                                <th class="text-center text-danger">Alpa</th>
                                @if(isset($activeTahunAjaran) && $activeTahunAjaran->id_semester == 2)
                                <th class="text-center">Kenaikan</th>
                                @endif
                                <th class="text-center" style="width: 100px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($siswas as $idx => $siswa)
                                @php
                                    $note = $existingNotes[$siswa->id_siswa] ?? null;
                                @endphp
                                <tr>
                                    <td class="text-center">{{ $idx + 1 }}</td>
                                    <td class="fw-semibold">{{ $siswa->nama }}</td>
                                    <td class="note-content-cell">
                                        @if($note)
                                            {{ $note->catatan }}
                                        @else
                                            <span class="text-muted fst-italic note-empty">Belum ada catatan</span>
                                        @endif
                                    </td>
                                    <td class="text-center badge-cell">
                                        @if($note)
                                            @php
                                                $badgeColor = match($note->jenis_catatan) {
                                                    'prestasi' => 'success',
                                                    'pelanggaran' => 'danger',
                                                    default => 'secondary'
                                                };
                                            @endphp
                                            <span class="badge bg-{{ $badgeColor }}">{{ ucfirst($note->jenis_catatan) }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center fw-semibold text-success att-sakit">{{ $note ? $note->sakit . ' Hari' : '-' }}</td>
                                    <td class="text-center fw-semibold text-primary att-izin">{{ $note ? $note->izin . ' Hari' : '-' }}</td>
                                    <td class="text-center fw-semibold text-danger att-alpa">{{ $note ? $note->alpa . ' Hari' : '-' }}</td>
                                    @if(isset($activeTahunAjaran) && $activeTahunAjaran->id_semester == 2)
                                    <td class="text-center">
                                        @if($note && $note->status_kenaikan)
                                            <span class="badge bg-{{ $note->status_kenaikan === 'naik' ? 'success' : 'danger' }}">
                                                {{ ucfirst($note->status_kenaikan) }}
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    @endif
                                    <td class="text-center">
                                        @if($note)
                                            <div class="d-flex justify-content-center align-items-center">
                                                <button class="btn btn-sm btn-outline-primary me-2 border-0" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#editNoteModal{{ $note->id_catatan }}"
                                                        title="Edit Catatan">
                                                    <i class="bi bi-pencil-square fs-6"></i>
                                                </button>

                                                <form action="{{ route('walikelas.catatan.destroy', $note->id_catatan) }}" method="POST" class="d-inline m-0 form-delete-catatan">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-outline-danger border-0 btn-delete-catatan" title="Hapus Catatan">
                                                        <i class="bi bi-trash fs-6"></i>
                                                    </button>
                                                </form>
                                            </div>

                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-people" style="font-size: 3rem; color: #ccc;"></i>
                    <h5 class="text-muted mt-3">Belum Ada Siswa</h5>
                    <p class="text-muted">Belum ada siswa di kelas ini.</p>
                </div>
            @endif
        </div>
    </div>
</div>

@foreach($siswas as $siswa)
    @php
        $note = $existingNotes[$siswa->id_siswa] ?? null;
    @endphp
    @if($note)
        {{-- Edit Modal --}}
        <div class="modal fade" id="editNoteModal{{ $note->id_catatan }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content border-0 shadow-lg">
                    <form action="{{ route('walikelas.catatan.update', $note->id_catatan) }}" method="POST" class="form-edit-catatan">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title fw-bold">Edit Catatan & Absensi</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Siswa</label>
                                <input type="text" class="form-control bg-light" value="{{ $siswa->nama }}" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Jenis Catatan</label>
                                <select name="jenis_catatan" class="form-select" required>
                                    <option value="umum" {{ $note->jenis_catatan === 'umum' ? 'selected' : '' }}>Umum</option>
                                    <option value="prestasi" {{ $note->jenis_catatan === 'prestasi' ? 'selected' : '' }}>Prestasi</option>
                                    <option value="pelanggaran" {{ $note->jenis_catatan === 'pelanggaran' ? 'selected' : '' }}>Pelanggaran</option>
                                </select>
                            </div>
                            <div class="row mb-3">
                                <div class="col-4">
                                    <label class="form-label fw-semibold text-success">Sakit</label>
                                    <input type="number" name="sakit" class="form-control" min="0" value="{{ $note->sakit ?? 0 }}" required>
                                </div>
                                <div class="col-4">
                                    <label class="form-label fw-semibold text-primary">Izin</label>
                                    <input type="number" name="izin" class="form-control" min="0" value="{{ $note->izin ?? 0 }}" required>
                                </div>
                                <div class="col-4">
                                    <label class="form-label fw-semibold text-danger">Alpa</label>
                                    <input type="number" name="alpa" class="form-control" min="0" value="{{ $note->alpa ?? 0 }}" required>
                                </div>
                            </div>
                            @if(isset($activeTahunAjaran) && $activeTahunAjaran->id_semester == 2)
                            <div class="mb-3">
                                <label class="form-label fw-semibold text-warning"><i class="bi bi-star-fill me-1"></i>Status Kenaikan Kelas</label>
                                <select name="status_kenaikan" class="form-select border-warning shadow-sm">
                                    <option value="">-- Kosongkan (Belum Ditentukan) --</option>
                                    <option value="naik" {{ $note->status_kenaikan === 'naik' ? 'selected' : '' }}>Naik Kelas</option>
                                    <option value="tinggal" {{ $note->status_kenaikan === 'tinggal' ? 'selected' : '' }}>Tinggal Kelas</option>
                                </select>
                            </div>
                            @endif
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Catatan</label>
                                <textarea name="catatan" class="form-control" rows="4" required>{{ $note->catatan }}</textarea>
                            </div>
                        </div>
                        <div class="modal-footer bg-light">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary btn-save-catatan">
                                <span class="spinner-border spinner-border-sm d-none me-1" role="status" aria-hidden="true"></span>
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endforeach

@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Tangani form edit dengan AJAX
    $('.form-edit-catatan').on('submit', function(e) {
        e.preventDefault();
        let form = $(this);
        let btn = form.find('.btn-save-catatan');
        let spinner = btn.find('.spinner-border');
        
        btn.prop('disabled', true);
        spinner.removeClass('d-none');

        $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: form.serialize(),
            success: function(response) {
                // Sembunyikan modal
                let modalId = form.closest('.modal').attr('id');
                let modalInstance = bootstrap.Modal.getInstance(document.getElementById(modalId));
                modalInstance.hide();
                
                // Update DOM baris tabel secara dinamis tanpa reload
                let row = form.closest('tr');
                
                let jenis = form.find('select[name="jenis_catatan"]').val();
                let sakit = form.find('input[name="sakit"]').val();
                let izin = form.find('input[name="izin"]').val();
                let alpa = form.find('input[name="alpa"]').val();
                let catatan = form.find('textarea[name="catatan"]').val();
                
                // Update text
                row.find('.note-content-cell').text(catatan);
                
                let badgeColor = jenis === 'prestasi' ? 'success' : (jenis === 'pelanggaran' ? 'danger' : 'secondary');
                let badgeText = jenis.charAt(0).toUpperCase() + jenis.slice(1);
                row.find('.badge-cell').html('<span class="badge bg-' + badgeColor + '">' + badgeText + '</span>');
                
                row.find('.att-sakit').text(sakit + ' Hari');
                row.find('.att-izin').text(izin + ' Hari');
                row.find('.att-alpa').text(alpa + ' Hari');

                btn.prop('disabled', false);
                spinner.addClass('d-none');
            },
            error: function() {
                alert('Terjadi kesalahan saat menyimpan data.');
                btn.prop('disabled', false);
                spinner.addClass('d-none');
            }
        });
    });

    // Tangani tombol hapus dengan konfirmasi dan AJAX
    $('.btn-delete-catatan').on('click', function(e) {
        e.preventDefault();
        let btn = $(this);
        let form = btn.closest('form');
        
        if (confirm('Apakah Anda yakin ingin menghapus catatan ini?')) {
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
            
            $.ajax({
                url: form.attr('action'),
                method: 'POST',
                data: form.serialize(),
                success: function(response) {
                    // Animasi menghapus baris tabel
                    form.closest('tr').fadeOut(400, function() {
                        $(this).remove();
                    });
                },
                error: function() {
                    alert('Gagal menghapus catatan.');
                    btn.prop('disabled', false).html('<i class="bi bi-trash fs-6"></i>');
                }
            });
        }
    });
});
</script>
@endsection
