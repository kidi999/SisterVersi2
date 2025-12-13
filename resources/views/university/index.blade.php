@extends('layouts.app')

@section('title', 'Data Universitas')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Data Universitas</h1>
        @if(Auth::user()->hasRole(['super_admin', 'admin_universitas']))
            <div>
                <a href="{{ route('universities.exportExcel', request()->query()) }}" class="btn btn-success btn-icon-split mr-2">
                    <span class="icon text-white-50">
                        <i class="fas fa-file-excel"></i>
                    </span>
                    <span class="text">Export Excel</span>
                </a>
                <a href="{{ route('universities.exportPdf', request()->query()) }}" class="btn btn-danger btn-icon-split mr-2">
                    <span class="icon text-white-50">
                        <i class="fas fa-file-pdf"></i>
                    </span>
                    <span class="text">Export PDF</span>
                </a>
                <a href="{{ route('universities.create') }}" class="btn btn-primary btn-icon-split">
                    <span class="icon text-white-50">
                        <i class="fas fa-plus"></i>
                    </span>
                    <span class="text">Tambah Universitas</span>
                </a>
            </div>
        @endif
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Universitas</h6>
        </div>
        <div class="card-body">
            <!-- Filter Form -->
            <form method="GET" action="{{ route('universities.index') }}" class="mb-4">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Jenis</label>
                            <select name="jenis" class="form-control">
                                <option value="">-- Semua Jenis --</option>
                                <option value="Negeri" {{ request('jenis') == 'Negeri' ? 'selected' : '' }}>Negeri</option>
                                <option value="Swasta" {{ request('jenis') == 'Swasta' ? 'selected' : '' }}>Swasta</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Status</label>
                            <select name="status" class="form-control">
                                <option value="">-- Semua Status --</option>
                                <option value="Aktif" {{ request('status') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="Nonaktif" {{ request('status') == 'Nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Akreditasi</label>
                            <select name="akreditasi" class="form-control">
                                <option value="">-- Semua Akreditasi --</option>
                                <option value="Unggul" {{ request('akreditasi') == 'Unggul' ? 'selected' : '' }}>Unggul</option>
                                <option value="A" {{ request('akreditasi') == 'A' ? 'selected' : '' }}>A</option>
                                <option value="Baik Sekali" {{ request('akreditasi') == 'Baik Sekali' ? 'selected' : '' }}>Baik Sekali</option>
                                <option value="B" {{ request('akreditasi') == 'B' ? 'selected' : '' }}>B</option>
                                <option value="Baik" {{ request('akreditasi') == 'Baik' ? 'selected' : '' }}>Baik</option>
                                <option value="C" {{ request('akreditasi') == 'C' ? 'selected' : '' }}>C</option>
                                <option value="Belum Terakreditasi" {{ request('akreditasi') == 'Belum Terakreditasi' ? 'selected' : '' }}>Belum Terakreditasi</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Pencarian</label>
                            <input type="text" name="search" class="form-control" placeholder="Nama, Kode, Singkatan..." value="{{ request('search') }}">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                        <a href="{{ route('universities.index') }}" class="btn btn-secondary">
                            <i class="fas fa-redo"></i> Reset
                        </a>
                    </div>
                </div>
            </form>

            <!-- Data Table -->
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th width="5%">No</th>
                            <th width="10%">Kode</th>
                            <th width="25%">Nama Universitas</th>
                            <th width="10%">Singkatan</th>
                            <th width="10%">Jenis</th>
                            <th width="10%">Akreditasi</th>
                            <th width="10%">Status</th>
                            <th width="20%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($universities as $index => $university)
                            <tr>
                                <td>{{ $universities->firstItem() + $index }}</td>
                                <td>{{ $university->kode }}</td>
                                <td>{{ $university->nama }}</td>
                                <td>{{ $university->singkatan }}</td>
                                <td>
                                    <span class="badge badge-{{ $university->jenis == 'Negeri' ? 'primary' : 'info' }}">
                                        {{ $university->jenis }}
                                    </span>
                                </td>
                                <td>
                                    @if($university->akreditasi)
                                        <span class="badge badge-success">{{ $university->akreditasi }}</span>
                                    @else
                                        <span class="badge badge-secondary">-</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-{{ $university->status == 'Aktif' ? 'success' : 'danger' }}">
                                        {{ $university->status }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('universities.show', $university->id) }}" class="btn btn-info btn-sm" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if(Auth::user()->hasRole(['super_admin', 'admin_universitas']))
                                        <a href="{{ route('universities.edit', $university->id) }}" class="btn btn-warning btn-sm" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('universities.destroy', $university->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">Tidak ada data</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center">
                {{ $universities->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
