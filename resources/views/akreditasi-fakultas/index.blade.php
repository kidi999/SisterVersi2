@extends('layouts.app')

@section('title', 'Data Akreditasi Fakultas')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Data Akreditasi Fakultas</h1>
        @if(Auth::user()->hasRole(['super_admin', 'admin_universitas', 'admin_fakultas']))
        <a href="{{ route('akreditasi-fakultas.create') }}" class="btn btn-primary btn-icon-split">
            <span class="icon text-white-50">
                <i class="fas fa-plus"></i>
            </span>
            <span class="text">Tambah Data</span>
        </a>
        @endif
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
            <h6 class="m-0 font-weight-bold text-primary">Filter Data</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('akreditasi-fakultas.index') }}" method="GET" class="form-inline">
                <div class="form-group mr-2 mb-2">
                    <select name="fakultas_id" class="form-control">
                        <option value="">-- Semua Fakultas --</option>
                        @foreach($fakultasList as $fak)
                            <option value="{{ $fak->id }}" {{ request('fakultas_id') == $fak->id ? 'selected' : '' }}>
                                {{ $fak->nama_fakultas }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group mr-2 mb-2">
                    <select name="status" class="form-control">
                        <option value="">-- Semua Status --</option>
                        <option value="Aktif" {{ request('status') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="Kadaluarsa" {{ request('status') == 'Kadaluarsa' ? 'selected' : '' }}>Kadaluarsa</option>
                        <option value="Dalam Proses" {{ request('status') == 'Dalam Proses' ? 'selected' : '' }}>Dalam Proses</option>
                    </select>
                </div>
                <div class="form-group mr-2 mb-2">
                    <select name="peringkat" class="form-control">
                        <option value="">-- Semua Peringkat --</option>
                        <option value="Unggul" {{ request('peringkat') == 'Unggul' ? 'selected' : '' }}>Unggul</option>
                        <option value="A" {{ request('peringkat') == 'A' ? 'selected' : '' }}>A</option>
                        <option value="Baik Sekali" {{ request('peringkat') == 'Baik Sekali' ? 'selected' : '' }}>Baik Sekali</option>
                        <option value="B" {{ request('peringkat') == 'B' ? 'selected' : '' }}>B</option>
                        <option value="Baik" {{ request('peringkat') == 'Baik' ? 'selected' : '' }}>Baik</option>
                        <option value="C" {{ request('peringkat') == 'C' ? 'selected' : '' }}>C</option>
                    </select>
                </div>
                <div class="form-group mr-2 mb-2">
                    <input type="number" name="tahun" class="form-control" placeholder="Tahun" 
                           value="{{ request('tahun') }}" min="1900" max="{{ date('Y') + 1 }}">
                </div>
                <div class="form-group mr-2 mb-2">
                    <input type="text" name="search" class="form-control" placeholder="Cari..." 
                           value="{{ request('search') }}">
                </div>
                <button type="submit" class="btn btn-primary mb-2 mr-2">
                    <i class="fas fa-search"></i> Filter
                </button>
                <a href="{{ route('akreditasi-fakultas.index') }}" class="btn btn-secondary mb-2">
                    <i class="fas fa-redo"></i> Reset
                </a>
            </form>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Akreditasi Fakultas</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th width="5%">No</th>
                            <th width="20%">Fakultas</th>
                            <th width="15%">Lembaga</th>
                            <th width="15%">No. SK</th>
                            <th width="10%">Tanggal SK</th>
                            <th width="10%">Peringkat</th>
                            <th width="10%">Status</th>
                            <th width="5%">File</th>
                            <th width="10%">Aksi</th>
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
                                <span class="badge badge-{{ $item->status_badge }}">
                                    {{ $item->status }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if($item->files->count() > 0)
                                    <span class="badge badge-info">
                                        <i class="bi bi-paperclip"></i> {{ $item->files->count() }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('akreditasi-fakultas.show', $item->id) }}" 
                                       class="btn btn-info btn-sm" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if(Auth::user()->hasRole(['super_admin', 'admin_universitas', 'admin_fakultas']))
                                    <a href="{{ route('akreditasi-fakultas.edit', $item->id) }}" 
                                       class="btn btn-warning btn-sm" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('akreditasi-fakultas.destroy', $item->id) }}" 
                                          method="POST" class="d-inline"
                                          onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center">Tidak ada data</td>
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
