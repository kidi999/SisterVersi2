@extends('layouts.app')

@section('title', 'Pendaftaran Mahasiswa Baru')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Pendaftaran Mahasiswa Baru</h1>
        <div>
            <a href="{{ route('pendaftaran-mahasiswa.exportExcel', request()->query()) }}" class="btn btn-success me-2">
                <i class="bi bi-file-earmark-excel"></i> Export Excel
            </a>
            <a href="{{ route('pendaftaran-mahasiswa.exportPdf', request()->query()) }}" class="btn btn-danger me-2">
                <i class="bi bi-file-earmark-pdf"></i> Export PDF
            </a>
            <a href="{{ route('pendaftaran-mahasiswa-trash') }}" class="btn btn-secondary me-2">
                <i class="bi bi-trash"></i> Recycle Bin
            </a>
            <a href="{{ route('pendaftaran-mahasiswa.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Tambah Pendaftaran
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filter Form -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('pendaftaran-mahasiswa.index') }}" class="row g-3">
                <div class="col-md-2">
                    <label class="form-label">Tahun Akademik</label>
                    <input type="text" name="tahun_akademik" class="form-control" placeholder="2025/2026" value="{{ request('tahun_akademik') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                        <option value="Diverifikasi" {{ request('status') == 'Diverifikasi' ? 'selected' : '' }}>Diverifikasi</option>
                        <option value="Diterima" {{ request('status') == 'Diterima' ? 'selected' : '' }}>Diterima</option>
                        <option value="Ditolak" {{ request('status') == 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
                        <option value="Dieksport" {{ request('status') == 'Dieksport' ? 'selected' : '' }}>Dieksport</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Jalur Masuk</label>
                    <select name="jalur_masuk" class="form-select">
                        <option value="">Semua Jalur</option>
                        <option value="SNBP" {{ request('jalur_masuk') == 'SNBP' ? 'selected' : '' }}>SNBP</option>
                        <option value="SNBT" {{ request('jalur_masuk') == 'SNBT' ? 'selected' : '' }}>SNBT</option>
                        <option value="Mandiri" {{ request('jalur_masuk') == 'Mandiri' ? 'selected' : '' }}>Mandiri</option>
                        <option value="Transfer" {{ request('jalur_masuk') == 'Transfer' ? 'selected' : '' }}>Transfer</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Fakultas</label>
                    <select name="fakultas_id" class="form-select">
                        <option value="">Semua Fakultas</option>
                        @foreach($fakultas as $fak)
                            <option value="{{ $fak->id }}" {{ request('fakultas_id') == $fak->id ? 'selected' : '' }}>
                                {{ $fak->nama_fakultas }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Program Studi</label>
                    <select name="program_studi_id" class="form-select">
                        <option value="">Semua Prodi</option>
                        @foreach($programStudi as $prodi)
                            <option value="{{ $prodi->id }}" {{ request('program_studi_id') == $prodi->id ? 'selected' : '' }}>
                                {{ $prodi->nama_prodi }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Pencarian</label>
                    <input type="text" name="search" class="form-control" placeholder="No/Nama/Email/NIK" value="{{ request('search') }}">
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-funnel"></i> Filter
                    </button>
                    <a href="{{ route('pendaftaran-mahasiswa.index') }}" class="btn btn-secondary">
                        <i class="bi bi-x-circle"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>No. Pendaftaran</th>
                            <th>Tanggal Daftar</th>
                            <th>Nama Lengkap</th>
                            <th>Program Studi</th>
                            <th>Jalur Masuk</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pendaftaran as $item)
                            <tr>
                                <td>
                                    <strong>{{ $item->no_pendaftaran }}</strong><br>
                                    <small class="text-muted">{{ $item->tahun_akademik }}</small>
                                </td>
                                <td>{{ $item->tanggal_daftar->format('d/m/Y H:i') }}</td>
                                <td>
                                    {{ $item->nama_lengkap }}<br>
                                    <small class="text-muted">
                                        <i class="bi bi-envelope"></i> {{ $item->email }}
                                    </small>
                                </td>
                                <td>
                                    <strong>{{ $item->programStudi->nama_prodi }}</strong><br>
                                    <small class="text-muted">{{ $item->programStudi->fakultas->nama_fakultas }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $item->jalur_badge }}">
                                        {{ $item->jalur_masuk }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $item->status_badge }}">
                                        {{ $item->status }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('pendaftaran-mahasiswa.show', $item) }}" class="btn btn-info" title="Detail">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        @if($item->status !== 'Dieksport')
                                            <a href="{{ route('pendaftaran-mahasiswa.edit', $item) }}" class="btn btn-warning" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form action="{{ route('pendaftaran-mahasiswa.destroy', $item) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus?')">
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
                                <td colspan="7" class="text-center py-4">
                                    <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                                    <p class="text-muted mt-2">Tidak ada data pendaftaran</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $pendaftaran->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
