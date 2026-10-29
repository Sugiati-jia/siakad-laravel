@extends('layouts.app')

@section('title', 'Kokurikuler (P5) - ' . ($kelas->nama_kelas ?? ''))

@section('content')
<div class="container-fluid py-3">
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('walikelas.dashboard') }}" class="btn btn-outline-secondary btn-sm me-3 border-0">
            <i class="bi bi-arrow-left fs-5"></i>
        </a>
        <div>
            <h4 class="mb-0 fw-bold text-dark">Projek Penguatan Profil Pelajar Pancasila (P5)</h4>
            <small class="text-muted">Kelas: <strong>{{ $kelas->nama_kelas ?? '-' }}</strong> | Tahun Pelajaran: {{ $activeTahunAjaran->tahun_ajaran }}</small>
        </div>
    </div>

    {{-- Form Input Kokurikuler --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3 border-bottom border-light">
            <h6 class="mb-0 fw-bold text-secondary"><i class="bi bi-plus-circle-fill me-2 text-success"></i>Tambah / Simpan Nilai Projek P5</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('walikelas.kokurikuler.store') }}" method="POST">
                @csrf
                <input type="hidden" name="id_kelas" value="{{ $kelas->id_kelas }}">
                <input type="hidden" name="id_semester" value="{{ $activeTahunAjaran->id_semester ?? 1 }}">
                <input type="hidden" name="id_tahun_ajaran" value="{{ $activeTahunAjaran->id_tahun_ajaran ?? '' }}">

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-muted small">PILIH SISWA</label>
                        <select name="id_siswa" class="form-select border-light-subtle shadow-none" required>
                            <option value="">-- Pilih Siswa --</option>
                            @foreach($siswas as $siswa)
                                <option value="{{ $siswa->id_siswa }}">{{ $siswa->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label fw-semibold text-muted small">DESKRIPSI CAPAIAN PROJEK P5</label>
                        <div class="input-group">
                            <textarea name="deskripsi" class="form-control border-light-subtle shadow-none" rows="1" placeholder="Contoh: Aktif dalam projek P5 dan mampu bekerja sama dengan tim." required></textarea>
                            <button type="submit" class="btn btn-primary border-0 px-4">
                                <i class="bi bi-save-fill me-1"></i> Simpan
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Daftar Kokurikuler Per Siswa --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 border-bottom border-light">
            <h6 class="mb-0 fw-bold text-secondary"><i class="bi bi-journal-text me-2"></i>Daftar Capaian Projek P5 Siswa</h6>
        </div>
        <div class="card-body p-0">
            @if($siswas->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" style="width: 60px;">No</th>
                                <th style="width: 25%;">Nama Siswa</th>
                                <th>Deskripsi Projek P5</th>
                                <th class="text-center" style="width: 120px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($siswas as $idx => $siswa)
                                @php
                                    $note = $existingNotes[$siswa->id_siswa] ?? null;
                                @endphp
                                <tr>
                                    <td class="text-center fw-bold text-muted">{{ $idx + 1 }}</td>
                                    <td>
                                        <div class="fw-semibold text-dark">{{ $siswa->nama }}</div>
                                        <small class="text-muted">NISN: {{ $siswa->nisn }}</small>
                                    </td>
                                    <td>
                                        @if($note)
                                            <div class="text-dark">{{ $note->deskripsi }}</div>
                                        @else
                                            <span class="text-muted fst-italic">Belum diinput</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($note)
                                            <!-- Borderless actions separated by me-3 -->
                                            <button class="btn btn-link text-primary text-decoration-none border-0 p-0 me-3" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#editKokurikulerModal{{ $note->id_kokurikuler }}"
                                                    title="Edit Nilai">
                                                <i class="bi bi-pencil-square fs-5"></i>
                                            </button>

                                            <form action="{{ route('walikelas.kokurikuler.destroy', $note->id_kokurikuler) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="btn btn-link text-danger text-decoration-none border-0 p-0 me-3" 
                                                        title="Hapus Nilai"
                                                        onclick="return confirm('Apakah Anda yakin ingin menghapus capaian P5 siswa ini?')">
                                                    <i class="bi bi-trash-fill fs-5"></i>
                                                </button>
                                            </form>

                                        @else
                                            <span class="text-muted">-</span>
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
        <div class="modal fade" id="editKokurikulerModal{{ $note->id_kokurikuler }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content border-0 shadow-lg">
                    <form action="{{ route('walikelas.kokurikuler.update', $note->id_kokurikuler) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-header border-bottom border-light">
                            <h5 class="modal-title fw-bold text-dark">Edit Capaian P5 - {{ $siswa->nama }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-start">
                            <div class="mb-3">
                                <label class="form-label fw-semibold text-muted small">DESKRIPSI CAPAIAN P5</label>
                                <textarea name="deskripsi" class="form-control border-light-subtle shadow-none" rows="5" required>{{ $note->deskripsi }}</textarea>
                            </div>
                        </div>
                        <div class="modal-footer border-top border-light">
                            <button type="button" class="btn btn-secondary border-0 px-3" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary border-0 px-4">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endforeach

@endsection
