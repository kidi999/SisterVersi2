@extends('layouts.app')

@section('title', 'Dashboard - SISTER')
@section('header', 'Dashboard')

@section('content')
<div class="d-flex justify-content-end mb-3">
    <a href="{{ route('dashboard.exportExcel') }}" class="btn btn-success btn-sm me-2">
        <i class="bi bi-file-earmark-excel"></i> Export Excel
    </a>
    <a href="{{ route('dashboard.exportPdf') }}" class="btn btn-danger btn-sm">
        <i class="bi bi-file-earmark-pdf"></i> Export PDF
    </a>
</div>
<div class="row">
    <!-- Card Total Mahasiswa -->
    <div class="col-md-3 mb-4">
        <div class="card text-white bg-primary">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title">Total Mahasiswa</h6>
                        <h2 class="mb-0">{{ $total_mahasiswa }}</h2>
                    </div>
                    <div>
                        <i class="bi bi-person-badge" style="font-size: 3rem;"></i>
                    </div>
                </div>
                <small>{{ $mahasiswa_aktif }} Aktif</small>
            </div>
        </div>
    </div>

    <!-- Card Total Dosen -->
    <div class="col-md-3 mb-4">
        <div class="card text-white bg-success">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title">Total Dosen</h6>
                        <h2 class="mb-0">{{ $total_dosen }}</h2>
                    </div>
                    <div>
                        <i class="bi bi-person-workspace" style="font-size: 3rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Card Total Fakultas -->
    <div class="col-md-3 mb-4">
        <div class="card text-white bg-warning">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title">Total Fakultas</h6>
                        <h2 class="mb-0">{{ $total_fakultas }}</h2>
                    </div>
                    <div>
                        <i class="bi bi-building" style="font-size: 3rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Card Total Program Studi -->
    <div class="col-md-3 mb-4">
        <div class="card text-white bg-info">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title">Total Prodi</h6>
                        <h2 class="mb-0">{{ $total_prodi }}</h2>
                    </div>
                    <div>
                        <i class="bi bi-journal-bookmark" style="font-size: 3rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Selamat Datang di SISTER</h5>
            </div>
            <div class="card-body">
                <p class="lead">Sistem Informasi Akademik Terintegrasi</p>
                <p>Sistem ini dirancang untuk mengelola data akademik secara terintegrasi, meliputi:</p>
                <ul>
                    <li>Manajemen Data Fakultas dan Program Studi</li>
                    <li>Manajemen Data Mahasiswa</li>
                    <li>Manajemen Data Dosen</li>
                    <li>Manajemen Mata Kuliah dan Kelas</li>
                    <li>Kartu Rencana Studi (KRS)</li>
                    <li>Penilaian dan Transkrip</li>
                </ul>
                
                <div class="mt-4">
                    <h6>Quick Actions:</h6>
                    <a href="{{ route('mahasiswa.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Tambah Mahasiswa
                    </a>
                    <a href="{{ route('dosen.create') }}" class="btn btn-success">
                        <i class="bi bi-plus-circle"></i> Tambah Dosen
                    </a>
                    <a href="{{ route('fakultas.create') }}" class="btn btn-warning">
                        <i class="bi bi-plus-circle"></i> Tambah Fakultas
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
