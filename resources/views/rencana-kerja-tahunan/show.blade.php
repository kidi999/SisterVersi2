@extends('layouts.app')

@section('content')
<div class="container-fluid">
    {{-- Header Section --}}
    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h4 class="mb-1"><i class="bi bi-clipboard-data me-2"></i>{{ $rkt->judul_rkt }}</h4>
                            <p class="text-muted mb-2">
                                <code>{{ $rkt->kode_rkt }}</code> 
                                <span class="mx-2">|</span>
                                <span class="badge bg-info">{{ $rkt->level }}</span>
                                <span class="mx-2">|</span>
                                <span class="badge bg-{{ $rkt->status_badge }}">{{ $rkt->status }}</span>
                            </p>
                            <p class="mb-0">
                                <strong>Periode:</strong> {{ $rkt->tanggal_mulai->format('d M Y') }} - {{ $rkt->tanggal_selesai->format('d M Y') }}
                            </p>
                        </div>
                        <div class="text-end">
                            <a href="{{ route('rencana-kerja-tahunan.index') }}" class="btn btn-secondary btn-sm mb-2">
                                <i class="bi bi-arrow-left me-1"></i>Kembali
                            </a><br>
                            @if($rkt->canEdit())
                                <a href="{{ route('rencana-kerja-tahunan.edit', $rkt->id) }}" class="btn btn-warning btn-sm mb-2">
                                    <i class="bi bi-pencil me-1"></i>Edit
                                </a><br>
                            @endif
                            @if($rkt->canSubmit())
                                <form action="{{ route('rencana-kerja-tahunan.submit', $rkt->id) }}" method="POST" class="d-inline" 
                                      onsubmit="return confirm('Ajukan RKT ini untuk persetujuan?')">
                                    @csrf
                                    <button type="submit" class="btn btn-primary btn-sm mb-2">
                                        <i class="bi bi-send me-1"></i>Ajukan
                                    </button>
                                </form><br>
                            @endif
                            @if($rkt->canApprove())
                                <form action="{{ route('rencana-kerja-tahunan.approve', $rkt->id) }}" method="POST" class="d-inline" 
                                      onsubmit="return confirm('Setujui RKT ini?')">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm mb-2">
                                        <i class="bi bi-check-circle me-1"></i>Setujui
                                    </button>
                                </form><br>
                                <button type="button" class="btn btn-danger btn-sm mb-2" data-bs-toggle="modal" data-bs-target="#rejectModal">
                                    <i class="bi bi-x-circle me-1"></i>Tolak
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Alerts --}}
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

    {{-- Info Cards --}}
    <div class="row mb-3">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h6 class="card-title">Total Anggaran</h6>
                    <h4 class="mb-0">Rp {{ number_format($rkt->anggaran, 0, ',', '.') }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h6 class="card-title">Anggaran Program</h6>
                    <h4 class="mb-0">Rp {{ number_format($rkt->totalAnggaranProgram, 0, ',', '.') }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h6 class="card-title">Realisasi Anggaran</h6>
                    <h4 class="mb-0">Rp {{ number_format($rkt->totalRealisasiAnggaran, 0, ',', '.') }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h6 class="card-title">Pencapaian</h6>
                    <h4 class="mb-0">{{ number_format($rkt->persentasePencapaian, 2) }}%</h4>
                </div>
            </div>
        </div>
    </div>

    {{-- Deskripsi --}}
    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Deskripsi</h5>
                </div>
                <div class="card-body">
                    <p>{{ $rkt->deskripsi ?? '-' }}</p>
                    @if($rkt->level == 'Universitas')
                        <p class="mb-0"><strong>Unit:</strong> {{ $rkt->university->name ?? '-' }}</p>
                    @elseif($rkt->level == 'Fakultas')
                        <p class="mb-0"><strong>Fakultas:</strong> {{ $rkt->fakultas->nama_fakultas ?? '-' }}</p>
                    @else
                        <p class="mb-0"><strong>Program Studi:</strong> {{ $rkt->programStudi->nama_program_studi ?? '-' }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Program RKT --}}
    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Program Kerja</h5>
                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addProgramModal">
                        <i class="bi bi-plus-circle me-1"></i>Tambah Program
                    </button>
                </div>
                <div class="card-body">
                    @forelse($rkt->programRkt as $program)
                        <div class="card mb-3 border-start border-primary border-4">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">{{ $program->nama_program }}</h6>
                                        <p class="text-muted small mb-2">
                                            <code>{{ $program->kode_program }}</code> | 
                                            <span class="badge bg-secondary">{{ $program->kategori }}</span>
                                        </p>
                                        <p class="mb-2">{{ $program->deskripsi }}</p>
                                        <div class="row text-sm">
                                            <div class="col-md-3">
                                                <strong>Anggaran:</strong> Rp {{ number_format($program->anggaran, 0, ',', '.') }}
                                            </div>
                                            <div class="col-md-3">
                                                <strong>Realisasi:</strong> Rp {{ number_format($program->totalRealisasiAnggaran, 0, ',', '.') }}
                                            </div>
                                            <div class="col-md-3">
                                                <strong>Pencapaian:</strong> {{ number_format($program->persentaseSelesai, 2) }}%
                                            </div>
                                            <div class="col-md-3">
                                                <strong>Penanggung Jawab:</strong> {{ $program->penanggung_jawab }}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Kegiatan dalam Program --}}
                                @if($program->kegiatanRkt->count() > 0)
                                    <hr>
                                    <h6 class="mb-2"><i class="bi bi-list-check me-1"></i>Kegiatan ({{ $program->kegiatanRkt->count() }})</h6>
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Kode</th>
                                                    <th>Nama Kegiatan</th>
                                                    <th>Periode</th>
                                                    <th>Anggaran</th>
                                                    <th>Status</th>
                                                    <th>Pencapaian</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($program->kegiatanRkt as $kegiatan)
                                                    <tr>
                                                        <td><code>{{ $kegiatan->kode_kegiatan }}</code></td>
                                                        <td>{{ $kegiatan->nama_kegiatan }}</td>
                                                        <td>{{ $kegiatan->tanggal_mulai?->format('d/m/Y') }} - {{ $kegiatan->tanggal_selesai?->format('d/m/Y') }}</td>
                                                        <td>Rp {{ number_format($kegiatan->anggaran, 0, ',', '.') }}</td>
                                                        <td><span class="badge bg-secondary">{{ $kegiatan->status }}</span></td>
                                                        <td>{{ number_format($kegiatan->persentaseCapaian, 2) }}%</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="alert alert-info mt-2 mb-0">
                                        <i class="bi bi-info-circle me-1"></i>Belum ada kegiatan untuk program ini
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle me-2"></i>Belum ada program kerja. Tambahkan program untuk melanjutkan.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- Catatan Penolakan --}}
    @if($rkt->status == 'Ditolak' && $rkt->catatan_penolakan)
        <div class="row">
            <div class="col-12">
                <div class="card border-danger">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0"><i class="bi bi-x-circle me-2"></i>Catatan Penolakan</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-0">{{ $rkt->catatan_penolakan }}</p>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

{{-- Reject Modal --}}
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('rencana-kerja-tahunan.reject', $rkt->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tolak RKT</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="catatan_penolakan" class="form-label">Catatan Penolakan <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="catatan_penolakan" name="catatan_penolakan" rows="4" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Tolak RKT</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
