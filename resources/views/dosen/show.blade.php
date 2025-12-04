@extends('layouts.app')

@section('title', 'Detail Dosen')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detail Dosen</h1>
        <div>
            @if(Auth::user()->hasRole(['super_admin', 'admin_universitas', 'admin_fakultas', 'admin_prodi']))
            <a href="{{ route('dosen.edit', $dosen->id) }}" class="btn btn-warning btn-icon-split mr-2">
                <span class="icon text-white-50">
                    <i class="fas fa-edit"></i>
                </span>
                <span class="text">Edit</span>
            </a>
            @endif
            <a href="{{ route('dosen.index') }}" class="btn btn-secondary btn-icon-split">
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
                    <h6 class="m-0 font-weight-bold text-primary">Data Diri</h6>
                    <span class="badge badge-{{ $dosen->status_badge }} badge-lg">
                        {{ $dosen->status }}
                    </span>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th width="30%">NIP</th>
                            <td>: <strong>{{ $dosen->nip }}</strong></td>
                        </tr>
                        @if($dosen->nidn)
                        <tr>
                            <th>NIDN</th>
                            <td>: {{ $dosen->nidn }}</td>
                        </tr>
                        @endif
                        <tr>
                            <th>Nama Lengkap</th>
                            <td>: <strong>{{ $dosen->nama_dosen }}</strong></td>
                        </tr>
                        <tr>
                            <th>Jenis Kelamin</th>
                            <td>: {{ $dosen->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                        </tr>
                        @if($dosen->tempat_lahir || $dosen->tanggal_lahir)
                        <tr>
                            <th>Tempat, Tanggal Lahir</th>
                            <td>: {{ $dosen->tempat_lahir }}{{ $dosen->tanggal_lahir ? ', ' . $dosen->tanggal_lahir->format('d F Y') : '' }}</td>
                        </tr>
                        @endif
                        @if($dosen->alamat)
                        <tr>
                            <th>Alamat</th>
                            <td>: {{ $dosen->alamat }}</td>
                        </tr>
                        @endif
                        @if($dosen->telepon)
                        <tr>
                            <th>Telepon</th>
                            <td>: {{ $dosen->telepon }}</td>
                        </tr>
                        @endif
                        <tr>
                            <th>Email</th>
                            <td>: <a href="mailto:{{ $dosen->email }}">{{ $dosen->email }}</a></td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Akademik</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th width="30%">Level Dosen</th>
                            <td>: 
                                <span class="badge badge-{{ $dosen->level_badge }} badge-lg">
                                    {{ $dosen->level_label }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Scope Pengajaran</th>
                            <td>: <strong>{{ $dosen->scope_label }}</strong></td>
                        </tr>
                        @if($dosen->level_dosen === 'fakultas' && $dosen->fakultas)
                        <tr>
                            <th>Fakultas</th>
                            <td>: {{ $dosen->fakultas->nama_fakultas }}</td>
                        </tr>
                        @endif
                        @if($dosen->level_dosen === 'prodi' && $dosen->programStudi)
                        <tr>
                            <th>Program Studi</th>
                            <td>: <strong>{{ $dosen->programStudi->nama_prodi }}</strong></td>
                        </tr>
                        <tr>
                            <th>Jenjang</th>
                            <td>: {{ $dosen->programStudi->jenjang }}</td>
                        </tr>
                        <tr>
                            <th>Fakultas</th>
                            <td>: {{ $dosen->programStudi->fakultas->nama_fakultas ?? '-' }}</td>
                        </tr>
                        @endif
                        @if($dosen->pendidikan_terakhir)
                        <tr>
                            <th>Pendidikan Terakhir</th>
                            <td>: {{ $dosen->pendidikan_terakhir }}</td>
                        </tr>
                        @endif
                        @if($dosen->jabatan_akademik)
                        <tr>
                            <th>Jabatan Akademik</th>
                            <td>: <strong>{{ $dosen->jabatan_akademik }}</strong></td>
                        </tr>
                        @endif
                        <tr>
                            <th>Status</th>
                            <td>: 
                                <span class="badge badge-{{ $dosen->status_badge }}">
                                    {{ $dosen->status }}
                                </span>
                            </td>
                        </tr>
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
                        <strong>{{ $dosen->created_at->format('d F Y H:i') }}</strong>
                    </p>
                    @if($dosen->updated_at != $dosen->created_at)
                    <p class="mb-2"><small class="text-muted">Diperbarui pada:</small><br>
                        <strong>{{ $dosen->updated_at->format('d F Y H:i') }}</strong>
                    </p>
                    @endif
                </div>
            </div>

            <div class="card shadow mb-4 border-left-{{ $dosen->level_badge }}">
                <div class="card-body">
                    <div class="text-center">
                        <i class="fas fa-user-tie fa-3x text-{{ $dosen->level_badge }} mb-3"></i>
                        <h5 class="font-weight-bold text-{{ $dosen->level_badge }}">
                            {{ $dosen->level_label }}
                        </h5>
                        <p class="mb-2"><small class="text-muted">{{ $dosen->scope_label }}</small></p>
                        @if($dosen->jabatan_akademik)
                        <p class="mb-0">
                            <span class="badge badge-secondary">{{ $dosen->jabatan_akademik }}</span>
                        </p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="card shadow mb-4 border-left-{{ $dosen->status_badge }}">
                <div class="card-body">
                    <div class="text-center">
                        <i class="fas fa-circle fa-2x text-{{ $dosen->status_badge }} mb-3"></i>
                        <h6 class="font-weight-bold text-{{ $dosen->status_badge }}">
                            Status: {{ $dosen->status }}
                        </h6>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($dosen->files->count() > 0)
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="bi bi-paperclip"></i> Dokumen Pendukung ({{ $dosen->files->count() }})
            </h6>
        </div>
        <div class="card-body">
            <div class="row">
                @foreach($dosen->files as $file)
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
