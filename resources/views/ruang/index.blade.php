@extends('layouts.app')

@section('title', 'Data Ruang')
@section('header', 'Data Ruang')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Data Ruang</h1>
        <div>
            <a href="{{ route('ruang.exportExcel', request()->query()) }}" class="btn btn-success">
                <i class="bi bi-file-earmark-excel"></i> Export Excel
            </a>
            <a href="{{ route('ruang.exportPdf', request()->query()) }}" class="btn btn-danger">
                <i class="bi bi-file-earmark-pdf"></i> Export PDF
            </a>
            <a href="{{ route('ruang.trash') }}" class="btn btn-secondary">
                <i class="bi bi-trash"></i> Trash
            </a>
            <a href="{{ route('ruang.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> Tambah Ruang
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('ruang.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" class="form-control" 
                           value="{{ request('search') }}" placeholder="Kode/Nama Ruang/Gedung...">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Kepemilikan</label>
                    <select name="tingkat_kepemilikan" class="form-select">
                        <option value="">Semua</option>
                        <option value="Universitas" {{ request('tingkat_kepemilikan') == 'Universitas' ? 'selected' : '' }}>Universitas</option>
                        <option value="Fakultas" {{ request('tingkat_kepemilikan') == 'Fakultas' ? 'selected' : '' }}>Fakultas</option>
                        <option value="Prodi" {{ request('tingkat_kepemilikan') == 'Prodi' ? 'selected' : '' }}>Prodi</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Jenis Ruang</label>
                    <select name="jenis_ruang" class="form-select">
                        <option value="">Semua</option>
                        <option value="Kelas" {{ request('jenis_ruang') == 'Kelas' ? 'selected' : '' }}>Kelas</option>
                        <option value="Lab" {{ request('jenis_ruang') == 'Lab' ? 'selected' : '' }}>Lab</option>
                        <option value="Perpustakaan" {{ request('jenis_ruang') == 'Perpustakaan' ? 'selected' : '' }}>Perpustakaan</option>
                        <option value="Aula" {{ request('jenis_ruang') == 'Aula' ? 'selected' : '' }}>Aula</option>
                        <option value="Ruang Seminar" {{ request('jenis_ruang') == 'Ruang Seminar' ? 'selected' : '' }}>Ruang Seminar</option>
                        <option value="Ruang Rapat" {{ request('jenis_ruang') == 'Ruang Rapat' ? 'selected' : '' }}>Ruang Rapat</option>
                        <option value="Lainnya" {{ request('jenis_ruang') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">Semua</option>
                        <option value="Aktif" {{ request('status') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="Tidak Aktif" {{ request('status') == 'Tidak Aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                        <option value="Dalam Perbaikan" {{ request('status') == 'Dalam Perbaikan' ? 'selected' : '' }}>Dalam Perbaikan</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Fakultas</label>
                    <select name="fakultas_id" class="form-select">
                        <option value="">Semua Fakultas</option>
                        @foreach($fakultas as $f)
                            <option value="{{ $f->id }}" {{ request('fakultas_id') == $f->id ? 'selected' : '' }}>
                                {{ $f->nama_fakultas }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search"></i> Filter
                    </button>
                    <a href="{{ route('ruang.index') }}" class="btn btn-secondary">
                        <i class="bi bi-x-circle"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabel Data -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Kode</th>
                            <th>Nama Ruang</th>
                            <th>Gedung/Lantai</th>
                            <th>Jenis</th>
                            <th>Kapasitas</th>
                            <th>Kepemilikan</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ruang as $item)
                            <tr>
                                <td><strong>{{ $item->kode_ruang }}</strong></td>
                                <td>{{ $item->nama_ruang }}</td>
                                <td>
                                    {{ $item->gedung ?? '-' }}
                                    @if($item->lantai)
                                        <span class="text-muted">/ Lt. {{ $item->lantai }}</span>
                                    @endif
                                </td>
                                <td><span class="badge bg-info">{{ $item->jenis_ruang }}</span></td>
                                <td>
                                    <i class="bi bi-people-fill"></i> {{ $item->kapasitas }} orang
                                </td>
                                <td>{!! $item->kepemilikan_display !!}</td>
                                <td>
                                    <span class="badge bg-{{ $item->status_badge }}">
                                        {{ $item->status }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('ruang.show', $item) }}" class="btn btn-info" title="Detail">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('ruang.edit', $item) }}" class="btn btn-warning" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('ruang.destroy', $item) }}" method="POST" class="d-inline"
                                              onsubmit="return confirm('Yakin ingin menghapus ruang ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger" title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                                    <p class="text-muted mt-2">Tidak ada data ruang</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $ruang->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
