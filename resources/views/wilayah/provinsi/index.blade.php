@extends('layouts.app')

@section('title', 'Data Provinsi')
@section('header', 'Data Provinsi')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Daftar Provinsi</h5>
        <div>
            @if(Auth::user()->hasRole(['super_admin']))
            <a href="{{ route('provinsi.trash') }}" class="btn btn-warning btn-sm">
                <i class="bi bi-trash"></i> Trash
            </a>
            @endif
            <a href="{{ route('provinsi.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-circle"></i> Tambah Provinsi
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
                        <th width="15%">Kode</th>
                        <th>Nama Provinsi</th>
                        <th width="15%" class="text-center">Jml Kabupaten/Kota</th>
                        <th width="20%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($provinces as $index => $province)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td><span class="badge bg-primary">{{ $province->code }}</span></td>
                        <td>{{ $province->name }}</td>
                        <td class="text-center">
                            <span class="badge bg-info">{{ $province->regencies_count }}</span>
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm" role="group">
                                <a href="{{ route('provinsi.show', $province) }}" class="btn btn-info" title="Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('provinsi.edit', $province) }}" class="btn btn-warning" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('provinsi.destroy', $province) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus provinsi ini?')">
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
                        <td colspan="5" class="text-center text-muted">Tidak ada data provinsi</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
