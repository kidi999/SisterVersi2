@extends('layouts.app')

@section('title', 'Data Program Studi')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Data Program Studi</h1>
        @if(Auth::user()->hasRole(['super_admin', 'admin_universitas', 'admin_fakultas', 'admin_prodi']))
            <div>
                <a href="{{ route('program-studi.exportExcel', request()->query()) }}" class="btn btn-success btn-icon-split mr-2">
                    <span class="icon text-white-50">
                        <i class="fas fa-file-excel"></i>
                    </span>
                    <span class="text">Export Excel</span>
                </a>
                <a href="{{ route('program-studi.exportPdf', request()->query()) }}" class="btn btn-danger btn-icon-split mr-2">
                    <span class="icon text-white-50">
                        <i class="fas fa-file-pdf"></i>
                    </span>
                    <span class="text">Export PDF</span>
                </a>
                <a href="{{ route('program-studi.create') }}" class="btn btn-primary btn-icon-split">
                    <span class="icon text-white-50">
                        <i class="fas fa-plus"></i>
                    </span>
                    <span class="text">Tambah Program Studi</span>
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
            <h6 class="m-0 font-weight-bold text-primary">Daftar Program Studi</h6>
        </div>
        <div class="card-body">
            <!-- Filter Form -->
            <form method="GET" action="{{ route('program-studi.index') }}" class="mb-4">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Fakultas</label>
                            <select name="fakultas_id" class="form-control">
                                <option value="">-- Semua Fakultas --</option>
                                @foreach($fakultas as $fak)
                                    <option value="{{ $fak->id }}" {{ request('fakultas_id') == $fak->id ? 'selected' : '' }}>
                                        {{ $fak->nama_fakultas }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Jenjang</label>
                            <select name="jenjang" class="form-control">
                                <option value="">-- Semua Jenjang --</option>
                                <option value="D3" {{ request('jenjang') == 'D3' ? 'selected' : '' }}>D3</option>
                                <option value="D4" {{ request('jenjang') == 'D4' ? 'selected' : '' }}>D4</option>
                                <option value="S1" {{ request('jenjang') == 'S1' ? 'selected' : '' }}>S1</option>
                                <option value="S2" {{ request('jenjang') == 'S2' ? 'selected' : '' }}>S2</option>
                                <option value="S3" {{ request('jenjang') == 'S3' ? 'selected' : '' }}>S3</option>
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
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Pencarian</label>
                            <input type="text" name="search" class="form-control" placeholder="Nama, Kode, Kaprodi..." value="{{ request('search') }}">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                        <a href="{{ route('program-studi.index') }}" class="btn btn-secondary">
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
                            <th width="8%">Kode</th>
                            <th width="25%">Nama Program Studi</th>
                            <th width="15%">Fakultas</th>
                            <th width="8%">Jenjang</th>
                            <th width="15%">Kaprodi</th>
                            <th width="10%">Akreditasi</th>
                            <th width="14%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($programStudi as $index => $prodi)
                            <tr>
                                <td>{{ $programStudi->firstItem() + $index }}</td>
                                <td>{{ $prodi->kode_prodi }}</td>
                                <td>{{ $prodi->nama_prodi }}</td>
                                <td>{{ $prodi->fakultas->nama_fakultas }}</td>
                                <td>
                                    <span class="badge badge-info">{{ $prodi->jenjang }}</span>
                                </td>
                                <td>{{ $prodi->kaprodi ?? '-' }}</td>
                                <td>
                                    @if($prodi->akreditasi)
                                        <span class="badge badge-success">{{ $prodi->akreditasi }}</span>
                                    @else
                                        <span class="badge badge-secondary">-</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('program-studi.show', $prodi->id) }}" class="btn btn-info btn-sm" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if(Auth::user()->hasRole(['super_admin', 'admin_universitas', 'admin_fakultas', 'admin_prodi']))
                                        <a href="{{ route('program-studi.edit', $prodi->id) }}" class="btn btn-warning btn-sm" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('program-studi.destroy', $prodi->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
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
                {{ $programStudi->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
