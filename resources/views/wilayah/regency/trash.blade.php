@extends('layouts.app')

@section('title', 'Trash Kabupaten/Kota')
@section('header', 'Trash Kabupaten/Kota')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="bi bi-trash"></i> Kabupaten/Kota yang Dihapus
        </h5>
        <a href="{{ route('regency.index') }}" class="btn btn-secondary btn-sm">
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
                        <th width="12%">Kode</th>
                        <th width="20%">Provinsi</th>
                        <th>Nama Kabupaten/Kota</th>
                        <th width="15%">Dihapus Oleh</th>
                        <th width="15%">Tanggal Hapus</th>
                        <th width="15%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($regencies as $index => $regency)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td><span class="badge bg-secondary">{{ $regency->code }}</span></td>
                        <td>{{ $regency->province->name }}</td>
                        <td>{{ $regency->name }}</td>
                        <td>{{ $regency->deleted_by ?? '-' }}</td>
                        <td>{{ $regency->deleted_at ? $regency->deleted_at->format('d/m/Y H:i') : '-' }}</td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm" role="group">
                                <form action="{{ route('regency.restore', $regency->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin memulihkan kabupaten/kota ini?')">
                                    @csrf
                                    <button type="submit" class="btn btn-success" title="Restore">
                                        <i class="bi bi-arrow-counterclockwise"></i> Restore
                                    </button>
                                </form>
                                <form action="{{ route('regency.force-delete', $regency->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus permanen? Data tidak dapat dikembalikan!')">
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
                        <td colspan="7" class="text-center text-muted">Tidak ada data kabupaten/kota yang dihapus</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
