@extends('layouts.app')

@section('title', 'Dashboard Mahasiswa - SISTER')
@section('header', 'Dashboard Mahasiswa')

@section('content')
<!-- Welcome Card -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card bg-gradient-primary text-white">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <div class="rounded-circle bg-white text-primary d-flex align-items-center justify-content-center" style="width: 80px; height: 80px; font-size: 2rem;">
                            <i class="bi bi-person-circle"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <h3 class="mb-1">Selamat Datang, {{ $mahasiswa->nama_mahasiswa }}!</h3>
                        <p class="mb-1">
                            <i class="bi bi-hash"></i> NIM: <strong>{{ $mahasiswa->nim }}</strong>
                        </p>
                        <p class="mb-0">
                            <i class="bi bi-building"></i> {{ $mahasiswa->programStudi->nama_program_studi ?? '-' }} | 
                            {{ $mahasiswa->programStudi->fakultas->nama_fakultas ?? '-' }}
                        </p>
                    </div>
                    <div class="flex-shrink-0 text-end">
                        <h5 class="mb-0">
                            <span class="badge bg-light text-primary">
                                @if($semester_aktif)
                                    {{ $semester_aktif->nama_semester }}
                                @else
                                    Tidak ada semester aktif
                                @endif
                            </span>
                        </h5>
                        <small>Semester Aktif</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="row mb-4">
    <!-- IPK Card -->
    <div class="col-md-3 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="text-muted mb-2">Indeks Prestasi Kumulatif</h6>
                        <h2 class="mb-0 fw-bold text-primary">{{ number_format($ipk, 2) }}</h2>
                        <small class="text-muted">dari 4.00</small>
                    </div>
                    <div class="bg-primary bg-opacity-10 rounded p-3">
                        <i class="bi bi-trophy-fill text-primary" style="font-size: 2rem;"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-primary" role="progressbar" style="width: {{ ($ipk/4)*100 }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total SKS Lulus -->
    <div class="col-md-3 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="text-muted mb-2">Total SKS Lulus</h6>
                        <h2 class="mb-0 fw-bold text-success">{{ $total_sks_lulus }}</h2>
                        <small class="text-muted">SKS</small>
                    </div>
                    <div class="bg-success bg-opacity-10 rounded p-3">
                        <i class="bi bi-check-circle-fill text-success" style="font-size: 2rem;"></i>
                    </div>
                </div>
                <div class="mt-3">
                    @php
                        $targetSks = 144; // Sesuaikan dengan target SKS program studi
                        $persenSks = ($total_sks_lulus / $targetSks) * 100;
                    @endphp
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $persenSks }}%"></div>
                    </div>
                    <small class="text-muted">Target: {{ $targetSks }} SKS</small>
                </div>
            </div>
        </div>
    </div>

    <!-- KRS Semester Ini -->
    <div class="col-md-3 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="text-muted mb-2">Mata Kuliah Semester Ini</h6>
                        <h2 class="mb-0 fw-bold text-info">{{ $total_mata_kuliah_semester_ini }}</h2>
                        <small class="text-muted">Mata Kuliah</small>
                    </div>
                    <div class="bg-info bg-opacity-10 rounded p-3">
                        <i class="bi bi-book-fill text-info" style="font-size: 2rem;"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <h4 class="mb-0 text-info">{{ $total_sks_semester_ini }} SKS</h4>
                    <small class="text-muted">Total beban SKS</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Mahasiswa -->
    <div class="col-md-3 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="text-muted mb-2">Status Mahasiswa</h6>
                        <h4 class="mb-0">
                            @if($mahasiswa->status == 'Aktif')
                                <span class="badge bg-success" style="font-size: 1rem;">
                                    <i class="bi bi-check-circle"></i> Aktif
                                </span>
                            @else
                                <span class="badge bg-danger" style="font-size: 1rem;">
                                    <i class="bi bi-x-circle"></i> {{ $mahasiswa->status }}
                                </span>
                            @endif
                        </h4>
                        <small class="text-muted">Tahun Masuk: {{ $mahasiswa->tahun_masuk }}</small>
                    </div>
                    <div class="bg-warning bg-opacity-10 rounded p-3">
                        <i class="bi bi-person-badge-fill text-warning" style="font-size: 2rem;"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <small class="text-muted">
                        <i class="bi bi-calendar3"></i> Semester: 
                        @php
                            $tahunSekarang = date('Y');
                            $bulanSekarang = date('m');
                            $semesterKeberapa = (($tahunSekarang - $mahasiswa->tahun_masuk) * 2) + ($bulanSekarang >= 8 ? 1 : 0);
                        @endphp
                        <strong>{{ $semesterKeberapa }}</strong>
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Jadwal Kuliah Semester Ini -->
@if($krs_aktif->count() > 0)
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-calendar-week text-primary"></i> 
                        Jadwal Kuliah Semester Ini
                    </h5>
                    <a href="{{ route('krs.index') }}" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-eye"></i> Lihat Semua KRS
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">No</th>
                                <th width="10%">Kode</th>
                                <th width="30%">Mata Kuliah</th>
                                <th width="8%">SKS</th>
                                <th width="20%">Dosen</th>
                                <th width="12%">Kelas</th>
                                <th width="15%">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($krs_aktif as $index => $krs)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td><code>{{ $krs->kelas->mataKuliah->kode_mata_kuliah ?? '-' }}</code></td>
                                <td>{{ $krs->kelas->mataKuliah->nama_mata_kuliah ?? '-' }}</td>
                                <td>
                                    <span class="badge bg-info">{{ $krs->kelas->mataKuliah->sks ?? 0 }} SKS</span>
                                </td>
                                <td>
                                    <small>{{ $krs->kelas->dosen->nama_dosen ?? '-' }}</small>
                                </td>
                                <td>{{ $krs->kelas->nama_kelas ?? '-' }}</td>
                                <td>
                                    @if($krs->status == 'Disetujui')
                                        <span class="badge bg-success">
                                            <i class="bi bi-check-circle"></i> Disetujui
                                        </span>
                                    @elseif($krs->status == 'Diajukan')
                                        <span class="badge bg-warning">
                                            <i class="bi bi-clock"></i> Diajukan
                                        </span>
                                    @elseif($krs->status == 'Draft')
                                        <span class="badge bg-secondary">
                                            <i class="bi bi-pencil"></i> Draft
                                        </span>
                                    @else
                                        <span class="badge bg-danger">
                                            <i class="bi bi-x-circle"></i> Ditolak
                                        </span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@else
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="bi bi-calendar-x text-muted" style="font-size: 4rem;"></i>
                <h5 class="mt-3 text-muted">Belum Ada KRS untuk Semester Ini</h5>
                <p class="text-muted">Silakan isi KRS untuk mengambil mata kuliah semester ini.</p>
                @if($semester_aktif)
                <a href="{{ route('krs.index') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Isi KRS Sekarang
                </a>
                @endif
            </div>
        </div>
    </div>
</div>
@endif

<!-- Quick Actions -->
<div class="row">
    <div class="col-md-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="mb-0">
                    <i class="bi bi-lightning-charge text-warning"></i> 
                    Menu Cepat
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <a href="{{ route('krs.index') }}" class="text-decoration-none">
                            <div class="card border-0 bg-primary bg-opacity-10 h-100">
                                <div class="card-body text-center">
                                    <i class="bi bi-card-checklist text-primary" style="font-size: 2.5rem;"></i>
                                    <h6 class="mt-3 mb-0 text-primary">KRS</h6>
                                    <small class="text-muted">Kartu Rencana Studi</small>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('nilai.index') }}" class="text-decoration-none">
                            <div class="card border-0 bg-success bg-opacity-10 h-100">
                                <div class="card-body text-center">
                                    <i class="bi bi-award text-success" style="font-size: 2.5rem;"></i>
                                    <h6 class="mt-3 mb-0 text-success">Nilai</h6>
                                    <small class="text-muted">Lihat Nilai & Transkrip</small>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('jadwal-kuliah.index') }}" class="text-decoration-none">
                            <div class="card border-0 bg-info bg-opacity-10 h-100">
                                <div class="card-body text-center">
                                    <i class="bi bi-calendar3 text-info" style="font-size: 2.5rem;"></i>
                                    <h6 class="mt-3 mb-0 text-info">Jadwal Kuliah</h6>
                                    <small class="text-muted">Lihat Jadwal</small>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('profil-mahasiswa.index') }}" class="text-decoration-none">
                            <div class="card border-0 bg-warning bg-opacity-10 h-100">
                                <div class="card-body text-center">
                                    <i class="bi bi-person-circle text-warning" style="font-size: 2.5rem;"></i>
                                    <h6 class="mt-3 mb-0 text-warning">Profil</h6>
                                    <small class="text-muted">Edit Profil Mahasiswa</small>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .bg-gradient-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    .card:hover {
        transform: translateY(-2px);
        transition: all 0.3s ease;
    }
</style>
@endpush
