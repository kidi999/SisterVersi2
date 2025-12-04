@extends('layouts.pmb')

@section('title', 'Cek Status Pendaftaran')

@section('content')
<div class="container">
    <div class="hero-section">
        <h1><i class="bi bi-search"></i> Cek Status Pendaftaran</h1>
        <p>Masukkan nomor pendaftaran dan email Anda untuk melihat status</p>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-6">
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="bi bi-exclamation-triangle"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card-pmb">
                <div class="card-header-pmb">
                    <h5 class="mb-0"><i class="bi bi-card-text"></i> Form Pencarian</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('pmb.check-status') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Nomor Pendaftaran <span class="text-danger">*</span></label>
                            <input type="text" name="no_pendaftaran" class="form-control @error('no_pendaftaran') is-invalid @enderror" 
                                   value="{{ old('no_pendaftaran') }}" placeholder="PMB20250010001" required>
                            @error('no_pendaftaran')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Contoh: PMB20250010001</small>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                                   value="{{ old('email') }}" placeholder="email@example.com" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Email yang digunakan saat mendaftar</small>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg btn-pmb">
                                <i class="bi bi-search"></i> Cek Status
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card-pmb mt-4">
                <div class="card-body p-4">
                    <h6 class="mb-3"><i class="bi bi-info-circle"></i> Informasi Status:</h6>
                    <div class="small">
                        <div class="d-flex align-items-center mb-2">
                            <span class="badge bg-warning me-2">Pending</span>
                            <span>: Menunggu verifikasi</span>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <span class="badge bg-info me-2">Diverifikasi</span>
                            <span>: Data telah diverifikasi</span>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <span class="badge bg-success me-2">Diterima</span>
                            <span>: Selamat! Anda diterima</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="badge bg-danger me-2">Ditolak</span>
                            <span>: Pendaftaran ditolak</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
