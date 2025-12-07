@extends('layouts.pmb')

@section('title', 'Verifikasi Email')

@section('content')
<div class="container">
    <div class="row justify-content-center mt-5">
        <div class="col-md-8">
            <div class="card-pmb">
                <div class="card-body p-5 text-center">
                    @if($success)
                        <div class="mb-4">
                            <i class="bi bi-check-circle-fill text-success" style="font-size: 5rem;"></i>
                        </div>
                        <h2 class="mb-3 text-success">Email Berhasil Diverifikasi!</h2>
                    @else
                        <div class="mb-4">
                            <i class="bi bi-x-circle-fill text-danger" style="font-size: 5rem;"></i>
                        </div>
                        <h2 class="mb-3 text-danger">Verifikasi Gagal</h2>
                    @endif

                    <div class="alert alert-{{ $success ? 'success' : 'danger' }} mb-4">
                        <p class="mb-0">{{ $message }}</p>
                    </div>

                    @if($success && isset($pendaftaran))
                        <div class="bg-light p-4 rounded mb-4 text-start">
                            <h5 class="mb-3"><i class="bi bi-info-circle"></i> Detail Pendaftaran:</h5>
                            <table class="table table-borderless mb-0">
                                <tr>
                                    <td width="180"><strong>No. Pendaftaran</strong></td>
                                    <td>: <strong class="text-primary">{{ $pendaftaran->no_pendaftaran }}</strong></td>
                                </tr>
                                <tr>
                                    <td><strong>Nama</strong></td>
                                    <td>: {{ $pendaftaran->nama_lengkap }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Email</strong></td>
                                    <td>: {{ $pendaftaran->email }} <span class="badge bg-success"><i class="bi bi-check-circle"></i> Terverifikasi</span></td>
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
                                    <td>: <span class="badge bg-{{ $pendaftaran->status_badge }}">{{ $pendaftaran->status }}</span></td>
                                </tr>
                            </table>
                        </div>

                        <div class="alert alert-info mb-4">
                            <i class="bi bi-info-circle"></i> <strong>Langkah Selanjutnya:</strong><br>
                            Tim kami akan memverifikasi dokumen dan data Anda. Silakan tunggu informasi lebih lanjut melalui email.
                        </div>

                        <div class="d-grid gap-2">
                            <a href="{{ route('pmb.check-status') }}" class="btn btn-primary btn-lg">
                                <i class="bi bi-search"></i> Cek Status Pendaftaran
                            </a>
                            <a href="{{ route('pmb.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-house"></i> Kembali ke Beranda
                            </a>
                        </div>
                    @elseif(isset($expired) && $expired)
                        <div class="alert alert-warning mb-4">
                            <i class="bi bi-exclamation-triangle"></i> <strong>Token Kadaluarsa</strong><br>
                            Link verifikasi hanya berlaku 24 jam setelah pendaftaran.
                        </div>

                        <form action="{{ route('pmb.resend-verification') }}" method="POST">
                            @csrf
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <input type="text" name="no_pendaftaran" class="form-control" placeholder="Nomor Pendaftaran" required>
                                </div>
                                <div class="col-md-6">
                                    <input type="email" name="email" class="form-control" placeholder="Email" required>
                                </div>
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-warning btn-lg">
                                    <i class="bi bi-envelope"></i> Kirim Ulang Email Verifikasi
                                </button>
                                <a href="{{ route('pmb.index') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-house"></i> Kembali ke Beranda
                                </a>
                            </div>
                        </form>
                    @else
                        <div class="d-grid gap-2">
                            <a href="{{ route('pmb.check-status') }}" class="btn btn-primary btn-lg">
                                <i class="bi bi-search"></i> Cek Status Pendaftaran
                            </a>
                            <a href="{{ route('pmb.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-house"></i> Kembali ke Beranda
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
