@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><i class="bi bi-clipboard-data me-2"></i>Rencana Kerja Tahunan</h4>
                    <a href="{{ route('rencana-kerja-tahunan.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i>Tambah RKT
                    </a>
                </div>
                <div class="card-body">
                    {{-- Filter Form --}}
                    <form method="GET" class="row g-3 mb-4">
                        <div class="col-md-2">
                            <input type="text" class="form-control" name="search" placeholder="Cari kode/judul" value="{{ request('search') }}">
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" name="tahun">
                                <option value="">Semua Tahun</option>
                                @for($y = date('Y') + 2; $y >= 2024; $y--)
                                    <option value="{{ $y }}" {{ request('tahun') == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" name="level">
                                <option value="">Semua Level</option>
                                <option value="Universitas" {{ request('level') == 'Universitas' ? 'selected' : '' }}>Universitas</option>
                                <option value="Fakultas" {{ request('level') == 'Fakultas' ? 'selected' : '' }}>Fakultas</option>
                                <option value="Prodi" {{ request('level') == 'Prodi' ? 'selected' : '' }}>Prodi</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" name="status">
                                <option value="">Semua Status</option>
                                <option value="Draft" {{ request('status') == 'Draft' ? 'selected' : '' }}>Draft</option>
                                <option value="Diajukan" {{ request('status') == 'Diajukan' ? 'selected' : '' }}>Diajukan</option>
                                <option value="Disetujui" {{ request('status') == 'Disetujui' ? 'selected' : '' }}>Disetujui</option>
                                <option value="Ditolak" {{ request('status') == 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
                                <option value="Dalam Proses" {{ request('status') == 'Dalam Proses' ? 'selected' : '' }}>Dalam Proses</option>
                                <option value="Selesai" {{ request('status') == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-secondary me-2">
                                <i class="bi bi-search me-1"></i>Filter
                            </button>
                            <a href="{{ route('rencana-kerja-tahunan.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-1"></i>Reset
                            </a>
                        </div>
                    </form>

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

                    {{-- Table --}}
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Kode RKT</th>
                                    <th>Judul</th>
                                    <th>Tahun</th>
                                    <th>Level</th>
                                    <th>Unit</th>
                                    <th>Anggaran</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($rkt as $item)
                                    <tr>
                                        <td><code>{{ $item->kode_rkt }}</code></td>
                                        <td>{{ $item->judul_rkt }}</td>
                                        <td>{{ $item->tahun }}</td>
                                        <td><span class="badge bg-info">{{ $item->level }}</span></td>
                                        <td>
                                            @if($item->level == 'Universitas')
                                                {{ $item->university->nama ?? '-' }}
                                            @elseif($item->level == 'Fakultas')
                                                {{ $item->fakultas->nama_fakultas ?? '-' }}
                                            @else
                                                {{ $item->programStudi->nama_prodi ?? '-' }}
                                            @endif
                                        </td>
                                        <td>Rp {{ number_format($item->anggaran, 0, ',', '.') }}</td>
                                        <td><span class="badge bg-{{ $item->status_badge }}">{{ $item->status }}</span></td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="{{ route('rencana-kerja-tahunan.show', $item->id) }}" class="btn btn-info" title="Detail">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                @if($item->canEdit())
                                                    <a href="{{ route('rencana-kerja-tahunan.edit', $item->id) }}" class="btn btn-warning" title="Edit">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                @endif
                                                @if($item->canDelete())
                                                    <form action="{{ route('rencana-kerja-tahunan.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus RKT ini?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger" title="Hapus">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <i class="bi bi-inbox fs-1 d-block mb-2 text-muted"></i>
                                            <p class="text-muted">Tidak ada data RKT</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div>
                            Menampilkan {{ $rkt->firstItem() ?? 0 }} sampai {{ $rkt->lastItem() ?? 0 }} dari {{ $rkt->total() }} data
                        </div>
                        <div>
                            {{ $rkt->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
