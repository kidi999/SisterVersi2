@extends('layouts.app')

@section('title', 'Pembayaran Mahasiswa')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Pembayaran Mahasiswa</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Pembayaran Mahasiswa</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('pembayaran-mahasiswa.exportExcel', request()->query()) }}" class="btn btn-success me-2">
                <i class="bi bi-file-earmark-excel"></i> Export Excel
            </a>
            <a href="{{ route('pembayaran-mahasiswa.exportPdf', request()->query()) }}" class="btn btn-danger me-2">
                <i class="bi bi-file-earmark-pdf"></i> Export PDF
            </a>
            <a href="{{ route('pembayaran-mahasiswa.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Input Pembayaran
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

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Pending Verifikasi</h6>
                            <h4 class="mb-0 text-warning">{{ $pembayaran->where('status_verifikasi', 'Pending')->count() }}</h4>
                        </div>
                        <i class="bi bi-clock-history text-warning" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Diverifikasi</h6>
                            <h4 class="mb-0 text-success">{{ $pembayaran->where('status_verifikasi', 'Diverifikasi')->count() }}</h4>
                        </div>
                        <i class="bi bi-check-circle text-success" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-danger">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Ditolak</h6>
                            <h4 class="mb-0 text-danger">{{ $pembayaran->where('status_verifikasi', 'Ditolak')->count() }}</h4>
                        </div>
                        <i class="bi bi-x-circle text-danger" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('pembayaran-mahasiswa.index') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <input type="text" class="form-control" name="search" placeholder="Cari NIM/Nama/No. Pembayaran" value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <select class="form-select" name="status_verifikasi">
                            <option value="">Semua Status</option>
                            <option value="Pending" {{ request('status_verifikasi') == 'Pending' ? 'selected' : '' }}>Pending</option>
                            <option value="Diverifikasi" {{ request('status_verifikasi') == 'Diverifikasi' ? 'selected' : '' }}>Diverifikasi</option>
                            <option value="Ditolak" {{ request('status_verifikasi') == 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="date" class="form-control" name="tanggal_dari" value="{{ request('tanggal_dari') }}" placeholder="Dari Tanggal">
                    </div>
                    <div class="col-md-2">
                        <input type="date" class="form-control" name="tanggal_sampai" value="{{ request('tanggal_sampai') }}" placeholder="Sampai Tanggal">
                    </div>
                    <div class="col-md-1">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('pembayaran-mahasiswa.index') }}" class="btn btn-secondary w-100">
                            <i class="bi bi-x-circle"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>No. Pembayaran</th>
                            <th>Mahasiswa</th>
                            <th>Jenis Pembayaran</th>
                            <th>Jumlah</th>
                            <th>Tanggal Bayar</th>
                            <th>Metode</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pembayaran as $p)
                        <tr>
                            <td><small class="font-monospace">{{ $p->nomor_pembayaran }}</small></td>
                            <td>
                                <strong>{{ $p->mahasiswa->nama_mahasiswa }}</strong><br>
                                <small class="text-muted">{{ $p->mahasiswa->nim }}</small>
                            </td>
                            <td>{{ $p->tagihanMahasiswa->jenisPembayaran->nama }}</td>
                            <td><strong>Rp {{ number_format($p->jumlah_bayar, 0, ',', '.') }}</strong></td>
                            <td>{{ $p->tanggal_bayar->format('d/m/Y') }}</td>
                            <td>
                                {{ $p->metode_pembayaran }}
                                @if($p->nama_bank)
                                    <br><small class="text-muted">{{ $p->nama_bank }}</small>
                                @endif
                            </td>
                            <td>
                                @if($p->status_verifikasi == 'Pending')
                                    <span class="badge bg-warning">Pending</span>
                                @elseif($p->status_verifikasi == 'Diverifikasi')
                                    <span class="badge bg-success">Diverifikasi</span>
                                @else
                                    <span class="badge bg-danger">Ditolak</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('pembayaran-mahasiswa.show', $p->id) }}" class="btn btn-info" title="Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if($p->status_verifikasi == 'Pending')
                                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#verifyModal{{ $p->id }}" title="Verifikasi">
                                        <i class="bi bi-check-circle"></i>
                                    </button>
                                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $p->id }}" title="Tolak">
                                        <i class="bi bi-x-circle"></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>

                        <!-- Modal Verify -->
                        <div class="modal fade" id="verifyModal{{ $p->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="{{ route('pembayaran-mahasiswa.verify', $p->id) }}" method="POST">
                                        @csrf
                                        <div class="modal-header">
                                            <h5 class="modal-title">Verifikasi Pembayaran</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p>Verifikasi pembayaran dari <strong>{{ $p->mahasiswa->nama_mahasiswa }}</strong>?</p>
                                            <p>Jumlah: <strong class="text-success">Rp {{ number_format($p->jumlah_bayar, 0, ',', '.') }}</strong></p>
                                            <div class="mb-3">
                                                <label class="form-label">Catatan (Opsional)</label>
                                                <textarea class="form-control" name="catatan_verifikasi" rows="2"></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-success">Verifikasi</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Modal Reject -->
                        <div class="modal fade" id="rejectModal{{ $p->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="{{ route('pembayaran-mahasiswa.reject', $p->id) }}" method="POST">
                                        @csrf
                                        <div class="modal-header">
                                            <h5 class="modal-title">Tolak Pembayaran</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p>Tolak pembayaran dari <strong>{{ $p->mahasiswa->nama_mahasiswa }}</strong>?</p>
                                            <div class="mb-3">
                                                <label class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                                                <textarea class="form-control" name="catatan_verifikasi" rows="3" required></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-danger">Tolak</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                                <p class="text-muted mt-2">Tidak ada data pembayaran</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $pembayaran->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
