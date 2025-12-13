@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="mb-0"><i class="bi bi-speedometer2"></i> Dashboard Admin Universitas</h2>
            <p class="text-muted">Selamat datang, {{ Auth::user()->name }}</p>
        </div>
    </div>

    <div class="d-flex justify-content-end mb-3">
        <a href="{{ route('dashboard.exportExcel') }}" class="btn btn-success btn-sm me-2">
            <i class="bi bi-file-earmark-excel"></i> Export Excel
        </a>
        <a href="{{ route('dashboard.exportPdf') }}" class="btn btn-danger btn-sm">
            <i class="bi bi-file-earmark-pdf"></i> Export PDF
        </a>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Fakultas</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $total_fakultas }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-building fs-2 text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Program Studi</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $total_prodi }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-mortarboard fs-2 text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total Mahasiswa</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $total_mahasiswa }}</div>
                            <small class="text-muted">Aktif: {{ $mahasiswa_aktif }}</small>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-people fs-2 text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Total Dosen</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $total_dosen }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-person-workspace fs-2 text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Statistics -->
    <div class="row mb-4">
        <div class="col-xl-4 col-md-6 mb-3">
            <div class="card shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                                Total Mata Kuliah</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $total_mata_kuliah }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-book fs-2 text-secondary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-3">
            <div class="card shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Mahasiswa Baru ({{ date('Y') }})</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $mahasiswa_baru }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-person-plus fs-2 text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-3">
            <div class="card shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Pendaftar Baru (Pending)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $pendaftar_baru }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-clipboard-check fs-2 text-danger"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Links -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-link-45deg"></i> Quick Links</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('fakultas.index') }}" class="btn btn-outline-primary btn-block">
                                <i class="bi bi-building"></i> Kelola Fakultas
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('program-studi.index') }}" class="btn btn-outline-success btn-block">
                                <i class="bi bi-mortarboard"></i> Kelola Program Studi
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('mahasiswa.index') }}" class="btn btn-outline-info btn-block">
                                <i class="bi bi-people"></i> Kelola Mahasiswa
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('dosen.index') }}" class="btn btn-outline-warning btn-block">
                                <i class="bi bi-person-workspace"></i> Kelola Dosen
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('pendaftaran-mahasiswa.index') }}" class="btn btn-outline-danger btn-block">
                                <i class="bi bi-clipboard-check"></i> Verifikasi Pendaftaran
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('mata-kuliah.index') }}" class="btn btn-outline-secondary btn-block">
                                <i class="bi bi-book"></i> Kelola Mata Kuliah
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('jadwal-kuliah.index') }}" class="btn btn-outline-primary btn-block">
                                <i class="bi bi-calendar3"></i> Jadwal Kuliah
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('tagihan-mahasiswa.index') }}" class="btn btn-outline-success btn-block">
                                <i class="bi bi-receipt"></i> Kelola Tagihan
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.border-left-primary { border-left: 0.25rem solid #4e73df !important; }
.border-left-success { border-left: 0.25rem solid #1cc88a !important; }
.border-left-info { border-left: 0.25rem solid #36b9cc !important; }
.border-left-warning { border-left: 0.25rem solid #f6c23e !important; }
</style>
@endsection
