@extends('layouts.app')

@section('title', 'Recycle Bin - Akreditasi Fakultas')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-trash-restore"></i> Recycle Bin - Akreditasi Fakultas
        </h1>
        <a href="{{ route('akreditasi-fakultas.index') }}" class="btn btn-secondary btn-icon-split">
            <span class="icon text-white-50">
                <i class="fas fa-arrow-left"></i>
            </span>
            <span class="text">Kembali</span>
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Data yang Dihapus</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                    <thead class="bg-secondary text-white">
                        <tr>
                            <th width="5%">No</th>
                            <th width="20%">Fakultas</th>
                            <th width="15%">Lembaga</th>
                            <th width="15%">No. SK</th>
                            <th width="10%">Tanggal SK</th>
                            <th width="10%">Peringkat</th>
                            <th width="10%">Dihapus</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($akreditasi as $item)
                        <tr>
                            <td>{{ $akreditasi->firstItem() + $loop->index }}</td>
                            <td>
                                <strong>{{ $item->fakultas->nama_fakultas ?? '-' }}</strong>
                                <br><small class="text-muted">Tahun: {{ $item->tahun_akreditasi }}</small>
                            </td>
                            <td>{{ $item->lembaga_akreditasi }}</td>
                            <td>{{ $item->nomor_sk }}</td>
                            <td>{{ $item->tanggal_sk->format('d M Y') }}</td>
                            <td>
                                <span class="badge badge-{{ $item->peringkat_badge }}">
                                    {{ $item->peringkat }}
                                </span>
                            </td>
                            <td>
                                <small>{{ $item->deleted_at->format('d M Y H:i') }}</small>
                            </td>
                            <td>
                                <form action="{{ route('akreditasi-fakultas.restore', $item->id) }}" 
                                      method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm" 
                                            title="Pulihkan"
                                            onclick="return confirm('Pulihkan data ini?')">
                                        <i class="fas fa-undo"></i>
                                    </button>
                                </form>
                                <form action="{{ route('akreditasi-fakultas.force-delete', $item->id) }}" 
                                      method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" 
                                            title="Hapus Permanen"
                                            onclick="return confirm('Hapus permanen? Data tidak dapat dikembalikan!')">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center">Tidak ada data yang dihapus</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $akreditasi->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
