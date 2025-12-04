@extends('layouts.app')

@section('title', 'Trash Provinsi')
@section('header', 'Trash Provinsi')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="bi bi-trash"></i> Provinsi yang Dihapus
        </h5>
        <a href="{{ route('provinsi.index') }}" class="btn btn-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
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
                        <th width="15%">Dihapus Oleh</th>
                        <th width="15%">Tanggal Hapus</th>
                        <th width="15%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($provinces as $index => $province)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td><span class="badge bg-secondary">{{ $province->code }}</span></td>
                        <td>{{ $province->name }}</td>
                        <td>{{ $province->deleted_by ?? '-' }}</td>
                        <td>{{ $province->deleted_at ? $province->deleted_at->format('d/m/Y H:i') : '-' }}</td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm" role="group">
                                <form action="{{ route('provinsi.restore', $province->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin memulihkan provinsi ini?')">
                                    @csrf
                                    <button type="submit" class="btn btn-success" title="Restore">
                                        <i class="bi bi-arrow-counterclockwise"></i> Restore
                                    </button>
                                </form>
                                <form action="{{ route('provinsi.force-delete', $province->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus permanen? Data tidak dapat dikembalikan!')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" title="Hapus Permanen">
                                        <i class="bi bi-trash-fill"></i> Hapus Permanen
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted">Tidak ada data provinsi yang dihapus</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
