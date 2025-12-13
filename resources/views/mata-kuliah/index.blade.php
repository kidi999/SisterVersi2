@extends('layouts.app')

@section('title', 'Data Mata Kuliah')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Data Mata Kuliah</h1>
        <div>
            <a href="{{ route('mata-kuliah.exportExcel', request()->query()) }}" class="btn btn-success btn-icon-split">
                <span class="icon text-white-50">
                    <i class="fas fa-file-excel"></i>
                </span>
                <span class="text">Export Excel</span>
            </a>
            <a href="{{ route('mata-kuliah.exportPdf', request()->query()) }}" class="btn btn-danger btn-icon-split">
                <span class="icon text-white-50">
                    <i class="fas fa-file-pdf"></i>
                </span>
                <span class="text">Export PDF</span>
            </a>
            <a href="{{ route('mata-kuliah.trash') }}" class="btn btn-warning btn-icon-split">
                <span class="icon text-white-50">
                    <i class="fas fa-trash"></i>
                </span>
                <span class="text">Recycle Bin</span>
            </a>
            <a href="{{ route('mata-kuliah.create') }}" class="btn btn-primary btn-icon-split">
                <span class="icon text-white-50">
                    <i class="fas fa-plus"></i>
                </span>
                <span class="text">Tambah Mata Kuliah</span>
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter Data</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('mata-kuliah.index') }}">
                <div class="row">
                    <div class="col-md-2">
                        <select name="level_matkul" class="form-control form-control-sm">
                            <option value="">-- Semua Level --</option>
                            <option value="universitas" {{ request('level_matkul') == 'universitas' ? 'selected' : '' }}>Universitas</option>
                            <option value="fakultas" {{ request('level_matkul') == 'fakultas' ? 'selected' : '' }}>Fakultas</option>
                            <option value="prodi" {{ request('level_matkul') == 'prodi' ? 'selected' : '' }}>Program Studi</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="fakultas_id" class="form-control form-control-sm">
                            <option value="">-- Semua Fakultas --</option>
                            @foreach($fakultas as $fak)
                            <option value="{{ $fak->id }}" {{ request('fakultas_id') == $fak->id ? 'selected' : '' }}>
                                {{ $fak->nama_fakultas }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="program_studi_id" class="form-control form-control-sm">
                            <option value="">-- Semua Prodi --</option>
                            @foreach($programStudi as $prodi)
                            <option value="{{ $prodi->id }}" {{ request('program_studi_id') == $prodi->id ? 'selected' : '' }}>
                                {{ $prodi->nama_prodi }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="jenis" class="form-control form-control-sm">
                            <option value="">-- Semua Jenis --</option>
                            <option value="Wajib" {{ request('jenis') == 'Wajib' ? 'selected' : '' }}>Wajib</option>
                            <option value="Pilihan" {{ request('jenis') == 'Pilihan' ? 'selected' : '' }}>Pilihan</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari kode/nama..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-1">
                        <button type="submit" class="btn btn-primary btn-sm w-100">Filter</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Mata Kuliah</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">No</th>
                            <th width="10%">Kode MK</th>
                            <th width="20%">Nama Mata Kuliah</th>
                            <th width="8%">SKS</th>
                            <th width="8%">Semester</th>
                            <th width="10%">Jenis</th>
                            <th width="10%">Level</th>
                            <th width="19%">Scope</th>
                            <th width="10%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($mataKuliah as $mk)
                        <tr>
                            <td>{{ $mataKuliah->firstItem() + $loop->index }}</td>
                            <td><strong>{{ $mk->kode_mk }}</strong></td>
                            <td>{{ $mk->nama_mk }}</td>
                            <td class="text-center">{{ $mk->sks }}</td>
                            <td class="text-center">{{ $mk->semester }}</td>
                            <td>
                                <span class="badge bg-{{ $mk->jenis == 'Wajib' ? 'success' : 'info' }}">
                                    {{ $mk->jenis }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $mk->level_badge }}">
                                    {{ $mk->level_label }}
                                </span>
                            </td>
                            <td><small>{{ $mk->scope_label }}</small></td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('mata-kuliah.show', $mk->id) }}" class="btn btn-info btn-sm" title="Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('mata-kuliah.edit', $mk->id) }}" class="btn btn-warning btn-sm" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('mata-kuliah.destroy', $mk->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center">Tidak ada data</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div>
                    Menampilkan {{ $mataKuliah->firstItem() ?? 0 }} - {{ $mataKuliah->lastItem() ?? 0 }} dari {{ $mataKuliah->total() }} data
                </div>
                <div>
                    {{ $mataKuliah->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
