@extends('layouts.app')

@section('title', "Data Siswa - Pilih Kelas")

@section('content')
<div class="container-fluid py-4">
    <div class="card mb-4 border-0 shadow-sm" style="border-radius: 12px; background-color: #1e293b;">
        <div class="card-body p-4 text-white">
            <h3 class="fw-bold mb-1"><i class="bi bi-people-fill me-2 text-info"></i> Data Siswa</h3>
            <p class="mb-0 text-white-50">Pilih kelas untuk melihat daftar siswa, menambah data, atau mengubah data siswa.</p>
        </div>
    </div>

    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        @forelse($kelas as $k)
            <div class="col">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; transition: transform 0.2s, box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 10px 20px rgba(0,0,0,0.1)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 .125rem .25rem rgba(0,0,0,0.075)';">
                    <div class="card-body p-4 text-center">
                        <div class="mb-3">
                            <div class="d-inline-flex align-items-center justify-content-center bg-info bg-opacity-10 text-info rounded-circle" style="width: 60px; height: 60px;">
                                <i class="bi bi-door-open fs-2"></i>
                            </div>
                        </div>
                        <h4 class="fw-bold text-dark mb-1">{{ $k->nama_kelas ?: $k->nama }}</h4>
                        <p class="text-muted small mb-3">Tingkat {{ $k->tingkat }}</p>
                        
                        <div class="d-flex justify-content-center align-items-center mb-3">
                            <span class="badge bg-light text-dark border px-3 py-2 rounded-pill">
                                <i class="bi bi-person me-1"></i> {{ $k->siswas_count }} Siswa
                            </span>
                        </div>
                        
                        <a href="{{ route('admin.siswa.kelas', $k->id_kelas) }}" class="btn btn-outline-info rounded-pill w-100 fw-semibold">
                            Lihat Data Siswa
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-warning text-center border-0 shadow-sm rounded-3 py-4">
                    <i class="bi bi-exclamation-circle display-4 d-block mb-3"></i>
                    Belum ada data kelas. Silakan tambahkan kelas terlebih dahulu melalui menu Kelas.
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection
