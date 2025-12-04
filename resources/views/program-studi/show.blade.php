@extends('layouts.app')

@section('title', 'Detail Program Studi')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detail Program Studi</h1>
        <div>
            @if(Auth::user()->hasRole(['super_admin', 'admin_universitas', 'admin_fakultas', 'admin_prodi']))
            <a href="{{ route('program-studi.edit', $programStudi->id) }}" class="btn btn-warning btn-icon-split">
                <span class="icon text-white-50">
                    <i class="fas fa-edit"></i>
                </span>
                <span class="text">Edit</span>
            </a>
            <button type="button" class="btn btn-danger btn-icon-split" data-toggle="modal" data-target="#deleteModal">
                <span class="icon text-white-50">
                    <i class="fas fa-trash"></i>
                </span>
                <span class="text">Hapus</span>
            </button>
            @endif
            <a href="{{ route('program-studi.index') }}" class="btn btn-secondary btn-icon-split">
                <span class="icon text-white-50">
                    <i class="fas fa-arrow-left"></i>
                </span>
                <span class="text">Kembali</span>
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Informasi Utama -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Program Studi</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th width="30%">Kode Program Studi</th>
                            <td>{{ $programStudi->kode_prodi }}</td>
                        </tr>
                        <tr>
                            <th>Nama Program Studi</th>
                            <td>{{ $programStudi->nama_prodi }}</td>
                        </tr>
                        <tr>
                            <th>Fakultas</th>
                            <td>{{ $programStudi->fakultas->nama_fakultas }}</td>
                        </tr>
                        <tr>
                            <th>Jenjang</th>
                            <td><span class="badge badge-info">{{ $programStudi->jenjang }}</span></td>
                        </tr>
                        <tr>
                            <th>Ketua Program Studi</th>
                            <td>{{ $programStudi->kaprodi ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Akreditasi</th>
                            <td>
                                @if($programStudi->akreditasi)
                                    <span class="badge badge-success">{{ $programStudi->akreditasi }}</span>
                                @else
                                    <span class="badge badge-secondary">Belum Terakreditasi</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Dokumen -->
            @if($programStudi->files->count() > 0)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Dokumen Lampiran</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="55%">Nama File</th>
                                    <th width="15%">Ukuran</th>
                                    <th width="15%">Tanggal</th>
                                    <th width="10%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($programStudi->files as $index => $file)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td><i class="fas fa-file-alt"></i> {{ $file->file_name }}</td>
                                    <td>{{ number_format($file->file_size / 1024, 2) }} KB</td>
                                    <td>{{ $file->created_at->format('d/m/Y') }}</td>
                                    <td>
                                        <a href="{{ route('api.file-upload.download', $file->id) }}" class="btn btn-sm btn-success" title="Download">
                                            <i class="fas fa-download"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <div class="col-lg-4">
            <!-- Statistik -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Statistik</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h5 class="text-primary">{{ $programStudi->mahasiswa->count() }}</h5>
                        <small class="text-muted">Total Mahasiswa</small>
                    </div>
                    <div class="mb-3">
                        <h5 class="text-primary">{{ $programStudi->dosen->count() }}</h5>
                        <small class="text-muted">Total Dosen</small>
                    </div>
                    <div class="mb-3">
                        <h5 class="text-primary">{{ $programStudi->files->count() }}</h5>
                        <small class="text-muted">Total Dokumen</small>
                    </div>
                </div>
            </div>

            <!-- Audit Info -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Audit</h6>
                </div>
                <div class="card-body">
                    <small class="text-muted">
                        <strong>Dibuat:</strong><br>
                        {{ $programStudi->created_at->format('d F Y H:i') }}<br>
                        @if($programStudi->creator)
                            oleh {{ $programStudi->creator->name }}
                        @endif
                    </small>
                    <hr>
                    <small class="text-muted">
                        <strong>Terakhir Diupdate:</strong><br>
                        {{ $programStudi->updated_at->format('d F Y H:i') }}<br>
                        @if($programStudi->updater)
                            oleh {{ $programStudi->updater->name }}
                        @endif
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Apakah Anda yakin ingin menghapus program studi <strong>{{ $programStudi->nama_prodi }}</strong>?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <form action="{{ route('program-studi.destroy', $programStudi->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
