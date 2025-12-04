@extends('layouts.app')

@section('title', 'Detail Provinsi')
@section('header', 'Detail Provinsi')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Detail Provinsi</h5>
        <div>
            <a href="{{ route('provinsi.edit', $province) }}" class="btn btn-warning btn-sm">
                <i class="bi bi-pencil"></i> Edit
            </a>
            <a href="{{ route('provinsi.index') }}" class="btn btn-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-bordered">
                    <tr>
                        <th width="40%" class="bg-light">Kode Provinsi</th>
                        <td><span class="badge bg-primary">{{ $province->code }}</span></td>
                    </tr>
                    <tr>
                        <th class="bg-light">Nama Provinsi</th>
                        <td><strong>{{ $province->name }}</strong></td>
                    </tr>
                    <tr>
                        <th class="bg-light">Jumlah Kabupaten/Kota</th>
                        <td><span class="badge bg-info">{{ $province->regencies->count() }}</span></td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-bordered">
                    <tr>
                        <th width="40%" class="bg-light">Dibuat oleh</th>
                        <td>{{ $province->created_by ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Tanggal dibuat</th>
                        <td>{{ $province->created_at ? $province->created_at->format('d/m/Y H:i') : '-' }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Diubah oleh</th>
                        <td>{{ $province->updated_by ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Tanggal diubah</th>
                        <td>{{ $province->updated_at ? $province->updated_at->format('d/m/Y H:i') : '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>

        @if($province->files && $province->files->count() > 0)
        <hr>
        <h6 class="mb-3">File Pendukung</h6>
        <div class="row">
            @foreach($province->files as $file)
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
        <h5 class="mb-0">Daftar Kabupaten/Kota</h5>
    </div>
    <div class="card-body">
        @if($province->regencies->count() > 0)
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">No</th>
                            <th width="15%">Kode</th>
                            <th>Nama Kabupaten/Kota</th>
                            <th width="15%" class="text-center">Jml Kecamatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($province->regencies as $index => $regency)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td><span class="badge bg-secondary">{{ $regency->code }}</span></td>
                            <td>{{ $regency->name }}</td>
                            <td class="text-center">
                                <span class="badge bg-info">{{ $regency->subRegencies->count() }}</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="alert alert-info mb-0">
                <i class="bi bi-info-circle"></i> Belum ada data kabupaten/kota untuk provinsi ini.
            </div>
        @endif
    </div>
</div>
@endsection
