@extends('layouts.pmb')

@section('title', 'Pendaftaran Berhasil')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card-pmb mt-5">
                <div class="card-body p-5 text-center">
                    <div class="mb-4">
                        <i class="bi bi-check-circle-fill text-success" style="font-size: 5rem;"></i>
                    </div>
                    <h2 class="mb-3">Pendaftaran Berhasil!</h2>
                    <p class="lead text-muted mb-4">Terima kasih telah mendaftar di Universitas XYZ</p>
                    
                    <div class="alert alert-info mb-4">
                        <h5 class="alert-heading"><i class="bi bi-info-circle"></i> Nomor Pendaftaran Anda:</h5>
                        <h2 class="mb-0 text-primary">{{ $pendaftaran->no_pendaftaran }}</h2>
                        <small class="text-muted">Simpan nomor ini untuk cek status pendaftaran</small>
                    </div>

                    <div class="text-start bg-light p-4 rounded mb-4">
                        <h5 class="mb-3"><i class="bi bi-card-checklist"></i> Detail Pendaftaran:</h5>
                        <table class="table table-borderless mb-0">
                            <tr>
                                <td width="180"><strong>Nama</strong></td>
                                <td>: {{ $pendaftaran->nama_lengkap }}</td>
                            </tr>
                            <tr>
                                <td><strong>Email</strong></td>
                                <td>: {{ $pendaftaran->email }}</td>
                            </tr>
                            <tr>
                                <td><strong>Jalur Pendaftaran</strong></td>
                                <td>: <span class="badge bg-{{ $pendaftaran->jalur_badge }}">{{ $pendaftaran->jalur_masuk }}</span></td>
                            </tr>
                            <tr>
                                <td><strong>Program Studi</strong></td>
                                <td>: {{ $pendaftaran->programStudi->nama_prodi }}</td>
                            </tr>
                            <tr>
                                <td><strong>Fakultas</strong></td>
                                <td>: {{ $pendaftaran->programStudi->fakultas->nama_fakultas }}</td>
                            </tr>
                            <tr>
                                <td><strong>Status</strong></td>
                                <td>: <span class="badge bg-warning">Pending - Menunggu Verifikasi</span></td>
                            </tr>
                        </table>
                    </div>

                    <div class="alert alert-warning mb-4">
                        <h6 class="alert-heading"><i class="bi bi-envelope-check"></i> Verifikasi Email Diperlukan!</h6>
                        <ul class="text-start mb-0">
                            <li><strong>Cek email Anda</strong> untuk link verifikasi yang kami kirim ke <strong>{{ $pendaftaran->email }}</strong></li>
                            <li><strong>Klik link verifikasi</strong> dalam email tersebut untuk mengaktifkan pendaftaran Anda</li>
                            <li>Link verifikasi berlaku <strong>24 jam</strong> setelah pendaftaran</li>
                            <li>Jika tidak menemukan email, cek folder spam/junk</li>
                            <li>Setelah email terverifikasi, data Anda akan diproses dalam 1-3 hari kerja</li>
                        </ul>
                    </div>

                    <div class="alert alert-info mb-4">
                        <i class="bi bi-info-circle"></i> Belum menerima email? 
                        <form action="{{ route('pmb.resend-verification') }}" method="POST" class="d-inline">
                            @csrf
                            <input type="hidden" name="no_pendaftaran" value="{{ $pendaftaran->no_pendaftaran }}">
                            <input type="hidden" name="email" value="{{ $pendaftaran->email }}">
                            <button type="submit" class="btn btn-link p-0 align-baseline">Kirim Ulang Email Verifikasi</button>
                        </form>
                    </div>

                    <div class="d-grid gap-2">
                        <a href="{{ route('pmb.check-status') }}" class="btn btn-primary btn-lg btn-pmb">
                            <i class="bi bi-search"></i> Cek Status Pendaftaran
                        </a>
                        <a href="{{ route('pmb.index') }}" class="btn btn-outline-secondary btn-lg">
                            <i class="bi bi-house"></i> Kembali ke Beranda
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
