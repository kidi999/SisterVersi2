@extends('layouts.pmb')

@section('title', 'Status Pendaftaran')

@section('content')
<div class="container">
    <div class="hero-section">
        <h1><i class="bi bi-clipboard-check"></i> Status Pendaftaran</h1>
        <p>Detail dan status pendaftaran Anda</p>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <!-- Status Card -->
            <div class="card-pmb mb-4">
                <div class="card-body p-4 text-center">
                    <h3 class="mb-3">Status Pendaftaran</h3>
                    <span class="badge status-badge-large bg-{{ $pendaftaran->status_badge }}">
                        {{ $pendaftaran->status }}
                    </span>
                    
                    @if($pendaftaran->status === 'Diterima')
                        <div class="alert alert-success mt-4">
                            <h5><i class="bi bi-check-circle"></i> Selamat!</h5>
                            <p class="mb-0">Anda diterima sebagai mahasiswa baru. Silakan tunggu informasi lebih lanjut untuk proses daftar ulang.</p>
                        </div>
                    @elseif($pendaftaran->status === 'Ditolak')
                        <div class="alert alert-danger mt-4">
                            <h5><i class="bi bi-x-circle"></i> Mohon Maaf</h5>
                            <p class="mb-0">Pendaftaran Anda tidak dapat kami proses lebih lanjut.</p>
                        </div>
                    @elseif($pendaftaran->status === 'Diverifikasi')
                        <div class="alert alert-info mt-4">
                            <h5><i class="bi bi-clock"></i> Sedang Diproses</h5>
                            <p class="mb-0">Data Anda telah diverifikasi. Tunggu pengumuman hasil seleksi.</p>
                        </div>
                    @else
                        <div class="alert alert-warning mt-4">
                            <h5><i class="bi bi-hourglass"></i> Menunggu Verifikasi</h5>
                            <p class="mb-0">Data Anda sedang dalam proses verifikasi oleh tim kami.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Detail Pendaftaran -->
            <div class="card-pmb mb-4">
                <div class="card-header-pmb">
                    <h5 class="mb-0"><i class="bi bi-info-circle"></i> Detail Pendaftaran</h5>
                </div>
                <div class="card-body p-4">
                    <table class="table table-borderless">
                        <tr>
                            <th width="200">No. Pendaftaran</th>
                            <td><strong class="text-primary">{{ $pendaftaran->no_pendaftaran }}</strong></td>
                        </tr>
                        <tr>
                            <th>Tanggal Daftar</th>
                            <td>{{ $pendaftaran->tanggal_daftar->format('d/m/Y H:i') }} WIB</td>
                        </tr>
                        <tr>
                            <th>Nama Lengkap</th>
                            <td><strong>{{ $pendaftaran->nama_lengkap }}</strong></td>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <td>{{ $pendaftaran->email }}</td>
                        </tr>
                        <tr>
                            <th>Jalur Pendaftaran</th>
                            <td><span class="badge bg-{{ $pendaftaran->jalur_badge }}">{{ $pendaftaran->jalur_masuk }}</span></td>
                        </tr>
                        <tr>
                            <th>Program Studi</th>
                            <td>{{ $pendaftaran->programStudi->nama_prodi }}</td>
                        </tr>
                        <tr>
                            <th>Fakultas</th>
                            <td>{{ $pendaftaran->programStudi->fakultas->nama_fakultas }}</td>
                        </tr>
                        <tr>
                            <th>Tahun Akademik</th>
                            <td>{{ $pendaftaran->tahun_akademik }}</td>
                        </tr>
                        @if($pendaftaran->tanggal_verifikasi)
                        <tr>
                            <th>Tanggal Verifikasi</th>
                            <td>
                                {{ $pendaftaran->tanggal_verifikasi->format('d/m/Y H:i') }} WIB
                                @if($pendaftaran->verifikator)
                                    <br><small class="text-muted">Oleh: {{ $pendaftaran->verifikator->name }}</small>
                                @endif
                            </td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>

            <!-- Catatan -->
            @if($pendaftaran->catatan)
            <div class="card-pmb mb-4">
                <div class="card-header-pmb">
                    <h5 class="mb-0"><i class="bi bi-chat-left-text"></i> Catatan</h5>
                </div>
                <div class="card-body p-4">
                    <p class="mb-0" style="white-space: pre-line;">{{ $pendaftaran->catatan }}</p>
                </div>
            </div>
            @endif

            <!-- Timeline -->
            <div class="card-pmb mb-4">
                <div class="card-header-pmb">
                    <h5 class="mb-0"><i class="bi bi-clock-history"></i> Timeline</h5>
                </div>
                <div class="card-body p-4">
                    <div class="timeline">
                        <div class="d-flex mb-3">
                            <div class="me-3">
                                <i class="bi bi-check-circle-fill text-success fs-4"></i>
                            </div>
                            <div>
                                <strong>Pendaftaran Dibuat</strong>
                                <br><small class="text-muted">{{ $pendaftaran->tanggal_daftar->format('d F Y, H:i') }} WIB</small>
                            </div>
                        </div>
                        
                        @if($pendaftaran->tanggal_verifikasi)
                        <div class="d-flex mb-3">
                            <div class="me-3">
                                <i class="bi bi-check-circle-fill text-success fs-4"></i>
                            </div>
                            <div>
                                <strong>Data Diverifikasi</strong>
                                <br><small class="text-muted">{{ $pendaftaran->tanggal_verifikasi->format('d F Y, H:i') }} WIB</small>
                            </div>
                        </div>
                        @else
                        <div class="d-flex mb-3">
                            <div class="me-3">
                                <i class="bi bi-circle text-muted fs-4"></i>
                            </div>
                            <div>
                                <strong class="text-muted">Menunggu Verifikasi</strong>
                                <br><small class="text-muted">Proses verifikasi 1-3 hari kerja</small>
                            </div>
                        </div>
                        @endif

                        @if($pendaftaran->status === 'Diterima')
                        <div class="d-flex mb-3">
                            <div class="me-3">
                                <i class="bi bi-check-circle-fill text-success fs-4"></i>
                            </div>
                            <div>
                                <strong>Diterima</strong>
                                <br><small class="text-muted">Selamat! Anda diterima sebagai mahasiswa baru</small>
                            </div>
                        </div>
                        @elseif($pendaftaran->status === 'Ditolak')
                        <div class="d-flex mb-3">
                            <div class="me-3">
                                <i class="bi bi-x-circle-fill text-danger fs-4"></i>
                            </div>
                            <div>
                                <strong>Ditolak</strong>
                                <br><small class="text-muted">Pendaftaran tidak dapat diproses</small>
                            </div>
                        </div>
                        @else
                        <div class="d-flex mb-3">
                            <div class="me-3">
                                <i class="bi bi-circle text-muted fs-4"></i>
                            </div>
                            <div>
                                <strong class="text-muted">Menunggu Hasil Seleksi</strong>
                                <br><small class="text-muted">Pengumuman akan diinformasikan via email</small>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="text-center mb-5">
                <a href="{{ route('pmb.check-status') }}" class="btn btn-primary btn-lg btn-pmb me-2">
                    <i class="bi bi-arrow-clockwise"></i> Cek Lagi
                </a>
                <a href="{{ route('pmb.index') }}" class="btn btn-outline-secondary btn-lg">
                    <i class="bi bi-house"></i> Kembali ke Beranda
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
