@extends('layouts.app')

@section('title', 'Recycle Bin - Mata Kuliah')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Recycle Bin - Mata Kuliah</h1>
        <a href="{{ route('mata-kuliah.index') }}" class="btn btn-secondary btn-icon-split">
            <span class="icon text-white-50">
                <i class="fas fa-arrow-left"></i>
            </span>
            <span class="text">Kembali</span>
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Data Mata Kuliah yang Dihapus</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">No</th>
                            <th width="10%">Kode MK</th>
                            <th width="25%">Nama Mata Kuliah</th>
                            <th width="8%">SKS</th>
                            <th width="10%">Jenis</th>
                            <th width="10%">Level</th>
                            <th width="17%">Scope</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($mataKuliah as $mk)
                        <tr>
                            <td>{{ $mataKuliah->firstItem() + $loop->index }}</td>
                            <td><strong>{{ $mk->kode_mk }}</strong></td>
                            <td>{{ $mk->nama_mk }}</td>
                            <td class="text-center">{{ $mk->sks }}</td>
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
                                <form action="{{ route('mata-kuliah.restore', $mk->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin memulihkan data ini?')">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm" title="Pulihkan">
                                        <i class="bi bi-arrow-counterclockwise"></i> Pulihkan
                                    </button>
                                </form>
                                <form action="{{ route('mata-kuliah.force-delete', $mk->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus PERMANEN? Data tidak bisa dikembalikan!')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" title="Hapus Permanen">
                                        <i class="bi bi-trash3"></i> Hapus Permanen
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center">Tidak ada data di recycle bin</td>
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
