@extends('layouts.app')

@section('title', 'Trash Ruang')
@section('header', 'Trash Ruang')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="bi bi-trash"></i> Trash Ruang
        </h1>
        <a href="{{ route('ruang.index') }}" class="btn btn-secondary">
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
            @if($ruang->count() > 0)
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> 
                    Data yang ada di trash dapat dipulihkan atau dihapus permanen.
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Kode</th>
                                <th>Nama Ruang</th>
                                <th>Gedung/Lantai</th>
                                <th>Kepemilikan</th>
                                <th>Dihapus Pada</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ruang as $item)
                                <tr>
                                    <td><strong>{{ $item->kode_ruang }}</strong></td>
                                    <td>{{ $item->nama_ruang }}</td>
                                    <td>
                                        {{ $item->gedung ?? '-' }}
                                        @if($item->lantai)
                                            <span class="text-muted">/ Lt. {{ $item->lantai }}</span>
                                        @endif
                                    </td>
                                    <td>{!! $item->kepemilikan_display !!}</td>
                                    <td>{{ $item->deleted_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <form action="{{ route('ruang.restore', $item->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success" 
                                                    onclick="return confirm('Pulihkan data ruang ini?')">
                                                <i class="bi bi-arrow-counterclockwise"></i> Pulihkan
                                            </button>
                                        </form>
                                        <form action="{{ route('ruang.force-delete', $item->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" 
                                                    onclick="return confirm('PERHATIAN! Data akan dihapus permanen dan tidak dapat dipulihkan. Lanjutkan?')">
                                                <i class="bi bi-x-circle"></i> Hapus Permanen
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $ruang->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-inbox" style="font-size: 4rem; color: #ccc;"></i>
                    <p class="text-muted mt-3 mb-0">Trash kosong</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
