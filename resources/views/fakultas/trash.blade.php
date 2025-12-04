@extends('layouts.app')

@section('title', 'Data Fakultas Terhapus')
@section('header', 'Data Fakultas Terhapus')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-trash"></i> Daftar Fakultas Terhapus (Soft Delete)</h5>
        <a href="{{ route('fakultas.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali ke Daftar Aktif
        </a>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="alert alert-info">
            <i class="bi bi-info-circle me-2"></i>
            <strong>Info:</strong> Hanya Super Admin yang dapat melihat dan me-restore data terhapus. 
            Data yang di-restore akan kembali ke daftar aktif.
        </div>

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
                        <th>Dihapus Oleh</th>
                        <th>Tanggal Dihapus</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($fakultas as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td><span class="badge bg-secondary">{{ $item->kode_fakultas }}</span></td>
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
                        <td>
                            <small>{{ $item->deleted_by ?? '-' }}</small>
                        </td>
                        <td>
                            <small>{{ $item->deleted_at ? $item->deleted_at->format('d/m/Y H:i') : '-' }}</small>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <form action="{{ route('fakultas.restore', $item->id) }}" method="POST" class="d-inline" 
                                      onsubmit="return confirm('Yakin ingin me-restore data ini?')">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success" title="Restore">
                                        <i class="bi bi-arrow-counterclockwise"></i> Restore
                                    </button>
                                </form>
                                <form action="{{ route('fakultas.force-delete', $item->id) }}" method="POST" class="d-inline" 
                                      onsubmit="return confirm('PERINGATAN: Data akan dihapus PERMANEN dan tidak dapat dikembalikan! Yakin ingin melanjutkan?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Hapus Permanen">
                                        <i class="bi bi-x-circle"></i> Hapus Permanen
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted">
                            <i class="bi bi-inbox"></i> Tidak ada data fakultas terhapus
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
