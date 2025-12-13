@extends('layouts.pmb')

@section('title', 'Penerimaan Mahasiswa Baru')

@section('content')
<div class="hero-section">
    <div class="container">
        <h1><i class="bi bi-mortarboard-fill"></i> Selamat Datang</h1>
        <h2>Penerimaan Mahasiswa Baru</h2>
        <p class="lead">Universitas XYZ - Tahun Akademik {{ date('Y') }}/{{ date('Y') + 1 }}</p>
        <div class="mt-3">
            <a href="{{ route('pmb.exportExcel') }}" class="btn btn-success btn-sm me-2">
                <i class="bi bi-file-earmark-excel"></i> Export Excel
            </a>
            <a href="{{ route('pmb.exportPdf') }}" class="btn btn-danger btn-sm">
                <i class="bi bi-file-earmark-pdf"></i> Export PDF
            </a>
        </div>
        <div class="mt-4">
            <a href="{{ route('pmb.create') }}" class="btn btn-light btn-lg btn-pmb me-3">
                <i class="bi bi-pencil-square"></i> Daftar Sekarang
            </a>
            <a href="{{ route('pmb.check-status') }}" class="btn btn-outline-light btn-lg">
                <i class="bi bi-search"></i> Cek Status Pendaftaran
            </a>
        </div>
    </div>
</div>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-4">
            <div class="feature-box">
                <i class="bi bi-calendar-check"></i>
                <h4>Mudah & Cepat</h4>
                <p>Proses pendaftaran online yang mudah dan cepat, kapan saja dan dimana saja</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="feature-box">
                <i class="bi bi-shield-check"></i>
                <h4>Aman & Terpercaya</h4>
                <p>Data Anda dijamin aman dan terenkripsi dengan sistem keamanan terbaik</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="feature-box">
                <i class="bi bi-chat-dots"></i>
                <h4>Support 24/7</h4>
                <p>Tim kami siap membantu Anda setiap saat jika ada pertanyaan</p>
            </div>
        </div>
    </div>

    <!-- Info Jalur Pendaftaran -->
    <div class="card-pmb mt-5">
        <div class="card-header-pmb">
            <h3 class="mb-0"><i class="bi bi-info-circle"></i> Jalur Pendaftaran</h3>
        </div>
        <div class="card-body p-4">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <div class="d-flex align-items-start">
                        <div class="badge bg-primary me-3 mt-1" style="font-size: 1.2rem;">SNBP</div>
                        <div>
                            <h5>Seleksi Nasional Berdasarkan Prestasi</h5>
                            <p class="text-muted">Jalur undangan berdasarkan prestasi akademik di sekolah</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="d-flex align-items-start">
                        <div class="badge bg-info me-3 mt-1" style="font-size: 1.2rem;">SNBT</div>
                        <div>
                            <h5>Seleksi Nasional Berdasarkan Tes</h5>
                            <p class="text-muted">Jalur tes tertulis berbasis komputer (UTBK)</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="d-flex align-items-start">
                        <div class="badge bg-success me-3 mt-1" style="font-size: 1.2rem;">Mandiri</div>
                        <div>
                            <h5>Jalur Mandiri Universitas</h5>
                            <p class="text-muted">Seleksi mandiri yang diselenggarakan oleh universitas</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="d-flex align-items-start">
                        <div class="badge bg-warning me-3 mt-1" style="font-size: 1.2rem;">Transfer</div>
                        <div>
                            <h5>Jalur Transfer/Pindahan</h5>
                            <p class="text-muted">Untuk mahasiswa pindahan dari perguruan tinggi lain</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alur Pendaftaran -->
    <div class="card-pmb mt-4">
        <div class="card-header-pmb">
            <h3 class="mb-0"><i class="bi bi-list-ol"></i> Alur Pendaftaran</h3>
        </div>
        <div class="card-body p-4">
            <div class="row">
                <div class="col-md-3 text-center mb-4">
                    <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px; font-size: 2rem;">
                        <i class="bi bi-1-circle"></i>
                    </div>
                    <h5 class="mt-3">Isi Formulir</h5>
                    <p class="text-muted">Lengkapi data diri dan upload dokumen persyaratan</p>
                </div>
                <div class="col-md-3 text-center mb-4">
                    <div class="rounded-circle bg-info text-white d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px; font-size: 2rem;">
                        <i class="bi bi-2-circle"></i>
                    </div>
                    <h5 class="mt-3">Verifikasi</h5>
                    <p class="text-muted">Tim kami akan memverifikasi data dan dokumen Anda</p>
                </div>
                <div class="col-md-3 text-center mb-4">
                    <div class="rounded-circle bg-success text-white d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px; font-size: 2rem;">
                        <i class="bi bi-3-circle"></i>
                    </div>
                    <h5 class="mt-3">Pengumuman</h5>
                    <p class="text-muted">Cek status pendaftaran untuk melihat hasil seleksi</p>
                </div>
                <div class="col-md-3 text-center mb-4">
                    <div class="rounded-circle bg-warning text-white d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px; font-size: 2rem;">
                        <i class="bi bi-4-circle"></i>
                    </div>
                    <h5 class="mt-3">Daftar Ulang</h5>
                    <p class="text-muted">Lakukan daftar ulang jika dinyatakan diterima</p>
                </div>
            </div>
        </div>
    </div>

    <!-- CTA -->
    <div class="text-center mt-5 mb-5">
        <h3 class="text-white mb-4">Siap Memulai Masa Depan Cerah Anda?</h3>
        <a href="{{ route('pmb.create') }}" class="btn btn-light btn-lg btn-pmb">
            <i class="bi bi-pencil-square"></i> Daftar Sekarang
        </a>
    </div>
</div>
@endsection
