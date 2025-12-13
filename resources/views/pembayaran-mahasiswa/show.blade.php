@extends('layouts.app')

@section('title', 'Detail Pembayaran Mahasiswa')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Detail Pembayaran</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('pembayaran-mahasiswa.index') }}">Pembayaran Mahasiswa</a></li>
                    <li class="breadcrumb-item active">Detail</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('pembayaran-mahasiswa.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <strong>Informasi Pembayaran</strong>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="text-muted">No. Pembayaran</div>
                            <div class="fw-semibold font-monospace">{{ $pembayaranMahasiswa->nomor_pembayaran }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="text-muted">Status Verifikasi</div>
                            <div>
                                @if($pembayaranMahasiswa->status_verifikasi == 'Pending')
                                    <span class="badge bg-warning">Pending</span>
                                @elseif($pembayaranMahasiswa->status_verifikasi == 'Diverifikasi')
                                    <span class="badge bg-success">Diverifikasi</span>
                                @else
                                    <span class="badge bg-danger">Ditolak</span>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="text-muted">Tanggal Bayar</div>
                            <div class="fw-semibold">{{ optional($pembayaranMahasiswa->tanggal_bayar)->format('d/m/Y') ?? '-' }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="text-muted">Waktu Bayar</div>
                            <div class="fw-semibold">{{ $pembayaranMahasiswa->waktu_bayar ?? '-' }}</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="text-muted">Jumlah Bayar</div>
                            <div class="fw-semibold text-success">Rp {{ number_format($pembayaranMahasiswa->jumlah_bayar ?? 0, 0, ',', '.') }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="text-muted">Metode Pembayaran</div>
                            <div class="fw-semibold">{{ $pembayaranMahasiswa->metode_pembayaran ?? '-' }}</div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <div class="text-muted">Nama Bank</div>
                            <div class="fw-semibold">{{ $pembayaranMahasiswa->nama_bank ?? '-' }}</div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="text-muted">Nomor Rekening</div>
                            <div class="fw-semibold">{{ $pembayaranMahasiswa->nomor_rekening ?? '-' }}</div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="text-muted">Nama Pemilik Rekening</div>
                            <div class="fw-semibold">{{ $pembayaranMahasiswa->nama_pemilik_rekening ?? '-' }}</div>
                        </div>

                        <div class="col-md-12 mb-3">
                            <div class="text-muted">Nomor Referensi</div>
                            <div class="fw-semibold">{{ $pembayaranMahasiswa->nomor_referensi ?? '-' }}</div>
                        </div>

                        <div class="col-md-12 mb-3">
                            <div class="text-muted">Keterangan</div>
                            <div class="fw-semibold">{{ $pembayaranMahasiswa->keterangan ?? '-' }}</div>
                        </div>

                        <div class="col-md-12">
                            <div class="text-muted">Bukti Pembayaran</div>
                            <div>
                                @if($pembayaranMahasiswa->bukti_pembayaran)
                                    <a href="{{ Storage::url($pembayaranMahasiswa->bukti_pembayaran) }}" target="_blank" class="btn btn-outline-primary btn-sm">
                                        <i class="bi bi-file-earmark"></i> Lihat Bukti
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header">
                    <strong>Informasi Tagihan</strong>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="text-muted">No. Tagihan</div>
                            <div class="fw-semibold font-monospace">{{ $pembayaranMahasiswa->tagihanMahasiswa->nomor_tagihan ?? '-' }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="text-muted">Jenis Pembayaran</div>
                            <div class="fw-semibold">{{ $pembayaranMahasiswa->tagihanMahasiswa->jenisPembayaran->nama ?? '-' }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="text-muted">Tahun Akademik</div>
                            <div class="fw-semibold">{{ $pembayaranMahasiswa->tagihanMahasiswa->tahunAkademik->nama ?? '-' }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="text-muted">Semester</div>
                            <div class="fw-semibold">{{ $pembayaranMahasiswa->tagihanMahasiswa->semester->nama ?? '-' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-4" id="fileUploadSection">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="bi bi-paperclip"></i> Lampiran</h5>
                </div>
                <div class="card-body">
                    @if(($pembayaranMahasiswa->files ?? collect())->count() > 0)
                        <div class="list-group">
                            @foreach($pembayaranMahasiswa->files as $file)
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <i class="{{ $file->icon_class }} fs-4 me-2"></i>
                                        <div>
                                            <div><strong>{{ $file->file_name }}</strong></div>
                                            <small class="text-muted">{{ $file->formatted_size }}</small>
                                        </div>
                                    </div>
                                    <a href="{{ route('api.file-upload.download', $file->id) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-download"></i> Download
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted mb-0">Tidak ada lampiran</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <strong>Mahasiswa</strong>
                </div>
                <div class="card-body">
                    <div class="mb-2"><span class="text-muted">Nama:</span> <strong>{{ $pembayaranMahasiswa->mahasiswa->nama_mahasiswa ?? '-' }}</strong></div>
                    <div class="mb-2"><span class="text-muted">NIM:</span> <strong>{{ $pembayaranMahasiswa->mahasiswa->nim ?? '-' }}</strong></div>
                    <div class="mb-2"><span class="text-muted">Program Studi:</span> <strong>{{ $pembayaranMahasiswa->mahasiswa->programStudi->nama_prodi ?? '-' }}</strong></div>
                    <div class="mb-2"><span class="text-muted">Fakultas:</span> <strong>{{ $pembayaranMahasiswa->mahasiswa->programStudi->fakultas->nama_fakultas ?? '-' }}</strong></div>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header">
                    <strong>Verifikasi</strong>
                </div>
                <div class="card-body">
                    <div class="mb-2"><span class="text-muted">Verified By:</span> <strong>{{ $pembayaranMahasiswa->verifiedBy->name ?? '-' }}</strong></div>
                    <div class="mb-2"><span class="text-muted">Verified At:</span> <strong>{{ optional($pembayaranMahasiswa->verified_at)->format('d/m/Y H:i') ?? '-' }}</strong></div>
                    <div class="mb-0"><span class="text-muted">Catatan:</span> <strong>{{ $pembayaranMahasiswa->catatan_verifikasi ?? '-' }}</strong></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
