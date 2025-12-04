@extends('layouts.app')

@section('title', 'Data Fakultas')
@section('header', 'Data Fakultas')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Daftar Fakultas</h5>
        <div>
            @if(Auth::user()->isSuperAdmin())
                <a href="{{ route('fakultas.trash') }}" class="btn btn-secondary me-2">
                    <i class="bi bi-trash"></i> Data Terhapus
                </a>
            @endif
            <a href="{{ route('fakultas.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Tambah Fakultas
            </a>
        </div>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>Kode</th>
                        <th>Nama Fakultas</th>
                        <th>Singkatan</th>
                        <th>Dekan</th>
                        <th>Wilayah</th>
                        <th>Jumlah Prodi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($fakultas as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td><span class="badge bg-primary">{{ $item->kode_fakultas }}</span></td>
                        <td>{{ $item->nama_fakultas }}</td>
                        <td>{{ $item->singkatan }}</td>
                        <td>{{ $item->dekan ?? '-' }}</td>
                        <td>
                            @if($item->village)
                                <small class="text-muted">
                                    {{ $item->village->name }}, {{ $item->village->subRegency->name }},<br>
                                    {{ $item->village->subRegency->regency->name }}, {{ $item->village->subRegency->regency->province->name }}
                                </small>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td><span class="badge bg-info">{{ $item->programStudi->count() }}</span></td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('fakultas.show', $item->id) }}" class="btn btn-sm btn-info" title="Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('fakultas.edit', $item->id) }}" class="btn btn-sm btn-warning" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('fakultas.destroy', $item->id) }}" method="POST" class="d-inline" 
                                      onsubmit="return confirm('Yakin ingin menghapus data ini? Data akan di-soft delete.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center">Belum ada data fakultas</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
