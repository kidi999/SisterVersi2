@extends('layouts.app')

@section('title', 'Recycle Bin - Pendaftaran Mahasiswa')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0"><i class="bi bi-trash"></i> Recycle Bin - Pendaftaran Mahasiswa</h1>
        <a href="{{ route('pendaftaran-mahasiswa.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>No. Pendaftaran</th>
                            <th>Nama Lengkap</th>
                            <th>Program Studi</th>
                            <th>Status</th>
                            <th>Dihapus</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pendaftaran as $item)
                            <tr>
                                <td>{{ $item->no_pendaftaran }}</td>
                                <td>{{ $item->nama_lengkap }}</td>
                                <td>
                                    {{ $item->programStudi->nama_prodi }}<br>
                                    <small class="text-muted">{{ $item->programStudi->fakultas->nama_fakultas }}</small>
                                </td>
                                <td><span class="badge bg-{{ $item->status_badge }}">{{ $item->status }}</span></td>
                                <td>{{ $item->deleted_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <form action="{{ route('pendaftaran-mahasiswa.restore', $item->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-success" title="Restore">
                                                <i class="bi bi-arrow-counterclockwise"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('pendaftaran-mahasiswa.force-delete', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus permanen? Data tidak dapat dikembalikan!')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger" title="Hapus Permanen">
                                                <i class="bi bi-x-circle"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                                    <p class="text-muted mt-2">Recycle bin kosong</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $pendaftaran->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
