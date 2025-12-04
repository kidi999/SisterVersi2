@extends('layouts.app')

@section('title', 'Data Kabupaten/Kota')
@section('header', 'Data Kabupaten/Kota')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Daftar Kabupaten/Kota</h5>
        <div>
            @if(Auth::user()->hasRole(['super_admin']))
            <a href="{{ route('regency.trash') }}" class="btn btn-warning btn-sm">
                <i class="bi bi-trash"></i> Trash
            </a>
            @endif
            <a href="{{ route('regency.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-circle"></i> Tambah Kabupaten/Kota
            </a>
        </div>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-light">
                    <tr>
                        <th width="5%">No</th>
                        <th width="12%">Kode</th>
                        <th width="25%">Provinsi</th>
                        <th>Nama Kabupaten/Kota</th>
                        <th width="12%" class="text-center">Jml Kecamatan</th>
                        <th width="15%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($regencies as $index => $regency)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td><span class="badge bg-primary">{{ $regency->code }}</span></td>
                        <td>{{ $regency->province->name }}</td>
                        <td>{{ $regency->name }}</td>
                        <td class="text-center">
                            <span class="badge bg-info">{{ $regency->sub_regencies_count }}</span>
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm" role="group">
                                <a href="{{ route('regency.show', $regency) }}" class="btn btn-info" title="Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('regency.edit', $regency) }}" class="btn btn-warning" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('regency.destroy', $regency) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus kabupaten/kota ini?')">
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
                        <td colspan="6" class="text-center text-muted">Tidak ada data kabupaten/kota</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
