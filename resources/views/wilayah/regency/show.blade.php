@extends('layouts.app')

@section('title', 'Detail Kabupaten/Kota')
@section('header', 'Detail Kabupaten/Kota')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Detail Kabupaten/Kota</h5>
        <div>
            <a href="{{ route('regency.edit', $regency) }}" class="btn btn-warning btn-sm">
                <i class="bi bi-pencil"></i> Edit
            </a>
            <a href="{{ route('regency.index') }}" class="btn btn-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-bordered">
                    <tr>
                        <th width="40%" class="bg-light">Provinsi</th>
                        <td><strong>{{ $regency->province->name }}</strong></td>
                    </tr>
                    <tr>
                        <th class="bg-light">Kode Kabupaten/Kota</th>
                        <td><span class="badge bg-primary">{{ $regency->code }}</span></td>
                    </tr>
                    <tr>
                        <th class="bg-light">Nama Kabupaten/Kota</th>
                        <td><strong>{{ $regency->name }}</strong></td>
                    </tr>
                    <tr>
                        <th class="bg-light">Jumlah Kecamatan</th>
                        <td><span class="badge bg-info">{{ $regency->subRegencies->count() }}</span></td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-bordered">
                    <tr>
                        <th width="40%" class="bg-light">Dibuat oleh</th>
                        <td>{{ $regency->created_by ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Tanggal dibuat</th>
                        <td>{{ $regency->created_at ? $regency->created_at->format('d/m/Y H:i') : '-' }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Diubah oleh</th>
                        <td>{{ $regency->updated_by ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Tanggal diubah</th>
                        <td>{{ $regency->updated_at ? $regency->updated_at->format('d/m/Y H:i') : '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>

        @if($regency->files && $regency->files->count() > 0)
        <hr>
        <h6 class="mb-3">File Pendukung</h6>
        <div class="row">
            @foreach($regency->files as $file)
            <div class="col-md-6 mb-2">
                <div class="d-flex align-items-center">
                    <i class="bi bi-file-earmark-text me-2"></i>
                    <a href="{{ route('api.file-upload.download', $file->id) }}" target="_blank">
                        {{ $file->original_name }}
                    </a>
                    <span class="badge bg-secondary ms-2">{{ number_format($file->file_size / 1024, 2) }} KB</span>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>

<div class="card mt-3">
    <div class="card-header">
        <h5 class="mb-0">Daftar Kecamatan</h5>
    </div>
    <div class="card-body">
        @if($regency->subRegencies->count() > 0)
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">No</th>
                            <th width="15%">Kode</th>
                            <th>Nama Kecamatan</th>
                            <th width="15%" class="text-center">Jml Desa/Kelurahan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($regency->subRegencies as $index => $subRegency)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td><span class="badge bg-secondary">{{ $subRegency->code }}</span></td>
                            <td>{{ $subRegency->name }}</td>
                            <td class="text-center">
                                <span class="badge bg-info">{{ $subRegency->villages->count() }}</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="alert alert-info mb-0">
                <i class="bi bi-info-circle"></i> Belum ada data kecamatan untuk kabupaten/kota ini.
            </div>
        @endif
    </div>
</div>
@endsection
