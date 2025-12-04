@extends('layouts.app')

@section('title', 'Detail Akreditasi Program Studi')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detail Akreditasi Program Studi</h1>
        <div>
            @if(Auth::user()->hasRole(['super_admin', 'admin_universitas', 'admin_fakultas', 'admin_prodi']))
            <a href="{{ route('akreditasi-program-studi.edit', $akreditasiProgramStudi->id) }}" class="btn btn-warning btn-icon-split mr-2">
                <span class="icon text-white-50">
                    <i class="fas fa-edit"></i>
                </span>
                <span class="text">Edit</span>
            </a>
            @endif
            <a href="{{ route('akreditasi-program-studi.index') }}" class="btn btn-secondary btn-icon-split">
                <span class="icon text-white-50">
                    <i class="fas fa-arrow-left"></i>
                </span>
                <span class="text">Kembali</span>
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Akreditasi</h6>
                    <div>
                        <span class="badge badge-{{ $akreditasiProgramStudi->status_badge }} badge-lg">
                            {{ $akreditasiProgramStudi->status }}
                        </span>
                        <span class="badge badge-{{ $akreditasiProgramStudi->peringkat_badge }} badge-lg ml-2">
                            {{ $akreditasiProgramStudi->peringkat }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th width="30%">Program Studi</th>
                            <td>: <strong>{{ $akreditasiProgramStudi->programStudi->nama_prodi ?? '-' }}</strong></td>
                        </tr>
                        <tr>
                            <th>Jenjang</th>
                            <td>: {{ $akreditasiProgramStudi->programStudi->jenjang ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Fakultas</th>
                            <td>: {{ $akreditasiProgramStudi->programStudi->fakultas->nama_fakultas ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Lembaga Akreditasi</th>
                            <td>: {{ $akreditasiProgramStudi->lembaga_akreditasi }}</td>
                        </tr>
                        <tr>
                            <th>Nomor SK</th>
                            <td>: {{ $akreditasiProgramStudi->nomor_sk }}</td>
                        </tr>
                        <tr>
                            <th>Tanggal SK</th>
                            <td>: {{ $akreditasiProgramStudi->tanggal_sk->format('d F Y') }}</td>
                        </tr>
                        <tr>
                            <th>Tanggal Berakhir</th>
                            <td>: {{ $akreditasiProgramStudi->tanggal_berakhir ? $akreditasiProgramStudi->tanggal_berakhir->format('d F Y') : '-' }}</td>
                        </tr>
                        <tr>
                            <th>Tahun Akreditasi</th>
                            <td>: {{ $akreditasiProgramStudi->tahun_akreditasi }}</td>
                        </tr>
                        <tr>
                            <th>Peringkat</th>
                            <td>: 
                                <span class="badge badge-{{ $akreditasiProgramStudi->peringkat_badge }}">
                                    {{ $akreditasiProgramStudi->peringkat }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>: 
                                <span class="badge badge-{{ $akreditasiProgramStudi->status_badge }}">
                                    {{ $akreditasiProgramStudi->status }}
                                </span>
                            </td>
                        </tr>
                        @if($akreditasiProgramStudi->catatan)
                        <tr>
                            <th>Catatan</th>
                            <td>: {{ $akreditasiProgramStudi->catatan }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Tambahan</h6>
                </div>
                <div class="card-body">
                    <p class="mb-2"><small class="text-muted">Dibuat pada:</small><br>
                        <strong>{{ $akreditasiProgramStudi->created_at->format('d F Y H:i') }}</strong>
                    </p>
                    @if($akreditasiProgramStudi->updated_at != $akreditasiProgramStudi->created_at)
                    <p class="mb-2"><small class="text-muted">Diperbarui pada:</small><br>
                        <strong>{{ $akreditasiProgramStudi->updated_at->format('d F Y H:i') }}</strong>
                    </p>
                    @endif
                </div>
            </div>

            @if($akreditasiProgramStudi->tanggal_berakhir)
            <div class="card shadow mb-4 border-left-{{ $akreditasiProgramStudi->tanggal_berakhir->isFuture() ? 'success' : 'danger' }}">
                <div class="card-body">
                    <div class="text-center">
                        <i class="fas fa-calendar-alt fa-3x text-{{ $akreditasiProgramStudi->tanggal_berakhir->isFuture() ? 'success' : 'danger' }} mb-3"></i>
                        <h5 class="font-weight-bold text-{{ $akreditasiProgramStudi->tanggal_berakhir->isFuture() ? 'success' : 'danger' }}">
                            @if($akreditasiProgramStudi->tanggal_berakhir->isFuture())
                                Berlaku hingga
                            @else
                                Sudah Berakhir
                            @endif
                        </h5>
                        <p class="mb-0">{{ $akreditasiProgramStudi->tanggal_berakhir->format('d F Y') }}</p>
                        <small class="text-muted">
                            {{ $akreditasiProgramStudi->tanggal_berakhir->diffForHumans() }}
                        </small>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    @if($akreditasiProgramStudi->files->count() > 0)
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="bi bi-paperclip"></i> Dokumen Pendukung ({{ $akreditasiProgramStudi->files->count() }})
            </h6>
        </div>
        <div class="card-body">
            <div class="row">
                @foreach($akreditasiProgramStudi->files as $file)
                <div class="col-md-4 mb-3">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <i class="bi {{ $file->icon_class }} fs-2 me-3"></i>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1 text-truncate" title="{{ $file->file_name }}">
                                        {{ $file->file_name }}
                                    </h6>
                                    <small class="text-muted">{{ $file->formatted_size }}</small>
                                </div>
                            </div>
                            <div class="mt-3">
                                <a href="{{ asset('storage/' . $file->file_path) }}" 
                                   class="btn btn-sm btn-primary" target="_blank">
                                    <i class="bi bi-download"></i> Download
                                </a>
                                @if($file->is_image)
                                <a href="{{ asset('storage/' . $file->file_path) }}" 
                                   class="btn btn-sm btn-info" target="_blank">
                                    <i class="bi bi-eye"></i> Lihat
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
