@extends('layouts.app')

@section('title', 'Detail Tagihan Mahasiswa')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Detail Tagihan Mahasiswa</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('tagihan-mahasiswa.index') }}">Tagihan Mahasiswa</a></li>
                    <li class="breadcrumb-item active">Detail</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('tagihan-mahasiswa.edit', $tagihanMahasiswa->id) }}" class="btn btn-warning me-2">
                <i class="bi bi-pencil"></i> Edit
            </a>
            <a href="{{ route('tagihan-mahasiswa.index') }}" class="btn btn-secondary">
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
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-receipt me-2"></i>Informasi Tagihan</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm mb-0">
                        <tr>
                            <th style="width: 40%">No. Tagihan</th>
                            <td><span class="font-monospace">{{ $tagihanMahasiswa->nomor_tagihan }}</span></td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                @if($tagihanMahasiswa->status == 'Belum Dibayar')
                                    <span class="badge bg-danger">{{ $tagihanMahasiswa->status }}</span>
                                @elseif($tagihanMahasiswa->status == 'Dibayar Sebagian')
                                    <span class="badge bg-warning">{{ $tagihanMahasiswa->status }}</span>
                                @elseif($tagihanMahasiswa->status == 'Lunas')
                                    <span class="badge bg-success">{{ $tagihanMahasiswa->status }}</span>
                                @else
                                    <span class="badge bg-secondary">{{ $tagihanMahasiswa->status }}</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Jenis Pembayaran</th>
                            <td>{{ $tagihanMahasiswa->jenisPembayaran->nama ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Tahun Akademik</th>
                            <td>{{ $tagihanMahasiswa->tahunAkademik->nama ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Semester</th>
                            <td>{{ $tagihanMahasiswa->semester->nama ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Tanggal Tagihan</th>
                            <td>{{ optional($tagihanMahasiswa->tanggal_tagihan)->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <th>Jatuh Tempo</th>
                            <td>
                                {{ optional($tagihanMahasiswa->tanggal_jatuh_tempo)->format('d/m/Y') }}
                                @if($tagihanMahasiswa->tanggal_jatuh_tempo && $tagihanMahasiswa->tanggal_jatuh_tempo->isPast() && $tagihanMahasiswa->status != 'Lunas')
                                    <span class="badge bg-danger ms-2">Lewat</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Tanggal Lunas</th>
                            <td>{{ $tagihanMahasiswa->tanggal_lunas ? $tagihanMahasiswa->tanggal_lunas->format('d/m/Y') : '-' }}</td>
                        </tr>
                        <tr>
                            <th>Jumlah Tagihan</th>
                            <td>Rp {{ number_format($tagihanMahasiswa->jumlah_tagihan, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th>Denda</th>
                            <td>Rp {{ number_format($tagihanMahasiswa->denda ?? 0, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th>Diskon</th>
                            <td>Rp {{ number_format($tagihanMahasiswa->diskon ?? 0, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th>Jumlah Dibayar</th>
                            <td>Rp {{ number_format($tagihanMahasiswa->jumlah_dibayar, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th>Sisa Tagihan</th>
                            <td>
                                @if($tagihanMahasiswa->sisa_tagihan > 0)
                                    <span class="text-danger fw-bold">Rp {{ number_format($tagihanMahasiswa->sisa_tagihan, 0, ',', '.') }}</span>
                                @else
                                    <span class="text-success fw-bold">Rp 0</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Keterangan</th>
                            <td>{{ $tagihanMahasiswa->keterangan ?: '-' }}</td>
                        </tr>
                    </table>

                    @if($tagihanMahasiswa->jumlah_dibayar == 0)
                        <form action="{{ route('tagihan-mahasiswa.destroy', $tagihanMahasiswa->id) }}" method="POST" class="mt-3" onsubmit="return confirm('Hapus tagihan ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="bi bi-trash"></i> Hapus
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-person-badge me-2"></i>Mahasiswa</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm mb-0">
                        <tr>
                            <th style="width: 40%">Nama</th>
                            <td>{{ $tagihanMahasiswa->mahasiswa->nama_mahasiswa ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>NIM</th>
                            <td>{{ $tagihanMahasiswa->mahasiswa->nim ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Program Studi</th>
                            <td>{{ $tagihanMahasiswa->mahasiswa->programStudi->nama_prodi ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Fakultas</th>
                            <td>{{ $tagihanMahasiswa->mahasiswa->programStudi->fakultas->nama_fakultas ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-cash-coin me-2"></i>Riwayat Pembayaran</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>No. Pembayaran</th>
                                    <th>Tanggal</th>
                                    <th>Jumlah</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tagihanMahasiswa->pembayaran as $p)
                                    <tr>
                                        <td><span class="font-monospace">{{ $p->nomor_pembayaran }}</span></td>
                                        <td>{{ optional($p->tanggal_bayar)->format('d/m/Y') }}</td>
                                        <td>Rp {{ number_format($p->jumlah_bayar, 0, ',', '.') }}</td>
                                        <td>
                                            @if($p->status_verifikasi === 'Diverifikasi')
                                                <span class="badge bg-success">Diverifikasi</span>
                                            @elseif($p->status_verifikasi === 'Ditolak')
                                                <span class="badge bg-danger">Ditolak</span>
                                            @else
                                                <span class="badge bg-warning">Pending</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-3">Belum ada pembayaran</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card mt-4" id="fileUploadSection">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="bi bi-paperclip"></i> Lampiran</h5>
                </div>
                <div class="card-body">
                    @if(($tagihanMahasiswa->files ?? collect())->count() > 0)
                        <div class="list-group">
                            @foreach($tagihanMahasiswa->files as $file)
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
    </div>
</div>
@endsection
