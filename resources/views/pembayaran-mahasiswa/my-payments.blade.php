@extends('layouts.app')

@section('title', 'Tagihan & Pembayaran Saya')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Tagihan & Pembayaran Saya</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Tagihan & Pembayaran</li>
                </ol>
            </nav>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-danger">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Belum Dibayar</h6>
                            <h4 class="mb-0 text-danger">{{ $tagihan->where('status', 'Belum Dibayar')->count() }}</h4>
                        </div>
                        <div class="text-danger">
                            <i class="bi bi-exclamation-circle" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Dibayar Sebagian</h6>
                            <h4 class="mb-0 text-warning">{{ $tagihan->where('status', 'Dibayar Sebagian')->count() }}</h4>
                        </div>
                        <div class="text-warning">
                            <i class="bi bi-clock-history" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Lunas</h6>
                            <h4 class="mb-0 text-success">{{ $tagihan->where('status', 'Lunas')->count() }}</h4>
                        </div>
                        <div class="text-success">
                            <i class="bi bi-check-circle" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-info">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Total Sisa Tagihan</h6>
                            <h4 class="mb-0 text-info">Rp {{ number_format($tagihan->where('status', '!=', 'Lunas')->sum('sisa_tagihan'), 0, ',', '.') }}</h4>
                        </div>
                        <div class="text-info">
                            <i class="bi bi-wallet2" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <ul class="nav nav-tabs mb-3" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="tagihan-tab" data-bs-toggle="tab" data-bs-target="#tagihan" type="button" role="tab">
                <i class="bi bi-receipt"></i> Tagihan Saya
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="pembayaran-tab" data-bs-toggle="tab" data-bs-target="#pembayaran" type="button" role="tab">
                <i class="bi bi-credit-card"></i> Riwayat Pembayaran
            </button>
        </li>
    </ul>

    <div class="tab-content">
        <!-- Tab Tagihan -->
        <div class="tab-pane fade show active" id="tagihan" role="tabpanel">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Daftar Tagihan</h5>
                </div>
                <div class="card-body">
                    @if($tagihan->isEmpty())
                        <div class="text-center py-5">
                            <i class="bi bi-inbox" style="font-size: 4rem; color: #ccc;"></i>
                            <p class="text-muted mt-3">Belum ada tagihan</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>No. Tagihan</th>
                                        <th>Jenis Pembayaran</th>
                                        <th>Tahun/Semester</th>
                                        <th>Jumlah Tagihan</th>
                                        <th>Dibayar</th>
                                        <th>Sisa</th>
                                        <th>Jatuh Tempo</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($tagihan as $t)
                                    <tr>
                                        <td>
                                            <small class="font-monospace">{{ $t->nomor_tagihan }}</small>
                                        </td>
                                        <td>
                                            <strong>{{ $t->jenisPembayaran->nama }}</strong><br>
                                            <small class="text-muted">{{ $t->jenisPembayaran->kode }}</small>
                                        </td>
                                        <td>
                                            <small>{{ $t->tahunAkademik->nama }}<br>{{ $t->semester->nama }}</small>
                                        </td>
                                        <td><strong>Rp {{ number_format($t->jumlah_tagihan, 0, ',', '.') }}</strong></td>
                                        <td>Rp {{ number_format($t->jumlah_dibayar, 0, ',', '.') }}</td>
                                        <td>
                                            @if($t->sisa_tagihan > 0)
                                                <span class="text-danger fw-bold">Rp {{ number_format($t->sisa_tagihan, 0, ',', '.') }}</span>
                                            @else
                                                <span class="text-success">Rp 0</span>
                                            @endif
                                        </td>
                                        <td>
                                            <small>{{ $t->tanggal_jatuh_tempo->format('d/m/Y') }}</small>
                                            @if($t->tanggal_jatuh_tempo->isPast() && $t->status != 'Lunas')
                                                <br><span class="badge bg-danger">Lewat Tempo</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($t->status == 'Belum Dibayar')
                                                <span class="badge bg-danger">{{ $t->status }}</span>
                                            @elseif($t->status == 'Dibayar Sebagian')
                                                <span class="badge bg-warning">{{ $t->status }}</span>
                                            @elseif($t->status == 'Lunas')
                                                <span class="badge bg-success">{{ $t->status }}</span>
                                            @else
                                                <span class="badge bg-secondary">{{ $t->status }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($t->status != 'Lunas')
                                                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#uploadModal{{ $t->id }}">
                                                    <i class="bi bi-upload"></i> Bayar
                                                </button>
                                            @else
                                                <button class="btn btn-sm btn-success" disabled>
                                                    <i class="bi bi-check-circle"></i> Lunas
                                                </button>
                                            @endif
                                        </td>
                                    </tr>

                                    <!-- Modal Upload Bukti -->
                                    <div class="modal fade" id="uploadModal{{ $t->id }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form action="{{ route('my-payments.upload-bukti') }}" method="POST" enctype="multipart/form-data">
                                                    @csrf
                                                    <input type="hidden" name="tagihan_mahasiswa_id" value="{{ $t->id }}">
                                                    
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Upload Bukti Pembayaran</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="alert alert-info">
                                                            <strong>{{ $t->jenisPembayaran->nama }}</strong><br>
                                                            Sisa Tagihan: <strong class="text-danger">Rp {{ number_format($t->sisa_tagihan, 0, ',', '.') }}</strong>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label">Jumlah Bayar <span class="text-danger">*</span></label>
                                                            <input type="number" class="form-control" name="jumlah_bayar" max="{{ $t->sisa_tagihan }}" required>
                                                            <small class="text-muted">Maksimal: Rp {{ number_format($t->sisa_tagihan, 0, ',', '.') }}</small>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label">Tanggal Bayar <span class="text-danger">*</span></label>
                                                            <input type="date" class="form-control" name="tanggal_bayar" value="{{ date('Y-m-d') }}" required>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label">Waktu Bayar</label>
                                                            <input type="time" class="form-control" name="waktu_bayar">
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label">Metode Pembayaran <span class="text-danger">*</span></label>
                                                            <select class="form-select" name="metode_pembayaran" required>
                                                                <option value="">Pilih Metode</option>
                                                                <option value="Transfer Bank">Transfer Bank</option>
                                                                <option value="Virtual Account">Virtual Account</option>
                                                                <option value="E-Wallet">E-Wallet (OVO, GoPay, Dana, dll)</option>
                                                                <option value="Kartu Kredit/Debit">Kartu Kredit/Debit</option>
                                                            </select>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label">Nama Bank/E-Wallet</label>
                                                            <input type="text" class="form-control" name="nama_bank" placeholder="Contoh: BCA, Mandiri, OVO">
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label">Nomor Rekening/Virtual Account</label>
                                                            <input type="text" class="form-control" name="nomor_rekening">
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label">Nama Pemilik Rekening</label>
                                                            <input type="text" class="form-control" name="nama_pemilik_rekening">
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label">Nomor Referensi/Transaksi</label>
                                                            <input type="text" class="form-control" name="nomor_referensi">
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label">Bukti Pembayaran <span class="text-danger">*</span></label>
                                                            <input type="file" class="form-control" name="bukti_pembayaran" accept="image/*,.pdf" required>
                                                            <small class="text-muted">Format: JPG, PNG, PDF (Max 2MB)</small>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label">Keterangan</label>
                                                            <textarea class="form-control" name="keterangan" rows="2"></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-primary">
                                                            <i class="bi bi-upload"></i> Upload Bukti
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Tab Pembayaran -->
        <div class="tab-pane fade" id="pembayaran" role="tabpanel">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Riwayat Pembayaran</h5>
                </div>
                <div class="card-body">
                    @if($pembayaran->isEmpty())
                        <div class="text-center py-5">
                            <i class="bi bi-inbox" style="font-size: 4rem; color: #ccc;"></i>
                            <p class="text-muted mt-3">Belum ada riwayat pembayaran</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>No. Pembayaran</th>
                                        <th>Jenis Pembayaran</th>
                                        <th>Jumlah</th>
                                        <th>Tanggal Bayar</th>
                                        <th>Metode</th>
                                        <th>Status Verifikasi</th>
                                        <th>Bukti</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pembayaran as $p)
                                    <tr>
                                        <td>
                                            <small class="font-monospace">{{ $p->nomor_pembayaran }}</small>
                                        </td>
                                        <td>
                                            <strong>{{ $p->tagihanMahasiswa->jenisPembayaran->nama }}</strong>
                                        </td>
                                        <td><strong>Rp {{ number_format($p->jumlah_bayar, 0, ',', '.') }}</strong></td>
                                        <td>{{ $p->tanggal_bayar->format('d/m/Y') }}
                                            @if($p->waktu_bayar)
                                                <br><small class="text-muted">{{ $p->waktu_bayar }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $p->metode_pembayaran }}
                                            @if($p->nama_bank)
                                                <br><small class="text-muted">{{ $p->nama_bank }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            @if($p->status_verifikasi == 'Pending')
                                                <span class="badge bg-warning">Menunggu Verifikasi</span>
                                            @elseif($p->status_verifikasi == 'Diverifikasi')
                                                <span class="badge bg-success">Diverifikasi</span>
                                                @if($p->verified_at)
                                                    <br><small class="text-muted">{{ $p->verified_at->format('d/m/Y H:i') }}</small>
                                                @endif
                                            @else
                                                <span class="badge bg-danger">Ditolak</span>
                                            @endif
                                            
                                            @if($p->catatan_verifikasi)
                                                <br><small class="text-muted">{{ $p->catatan_verifikasi }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            @if($p->bukti_pembayaran)
                                                <a href="{{ Storage::url($p->bukti_pembayaran) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-file-earmark-image"></i> Lihat
                                                </a>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Auto dismiss alerts after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
</script>
@endsection
