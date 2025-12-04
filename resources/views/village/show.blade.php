@extends('layouts.app')

@section('title', 'Detail Desa/Kelurahan')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Detail Desa/Kelurahan</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('village.edit', $village->id) }}" class="btn btn-warning">
                <i class="bi bi-pencil"></i> Edit
            </a>
            <a href="{{ route('village.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-geo-alt"></i> Informasi Desa/Kelurahan
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label class="fw-bold">Kode</label>
                            <p class="text-muted"><code>{{ $village->code }}</code></p>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold">Nama Desa/Kelurahan</label>
                            <p class="text-muted">{{ $village->name }}</p>
                        </div>
                        <div class="col-md-3">
                            <label class="fw-bold">Kode Pos</label>
                            <p class="text-muted">
                                @if($village->postal_code)
                                    <span class="badge bg-info fs-6">{{ $village->postal_code }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="fw-bold">Kecamatan</label>
                            <p class="text-muted">
                                {{ $village->subRegency->name }}
                                <br>
                                <small class="text-primary">
                                    <i class="bi bi-geo-alt"></i> Kode: {{ $village->subRegency->code }}
                                </small>
                            </p>
                        </div>
                        <div class="col-md-4">
                            <label class="fw-bold">Kabupaten/Kota</label>
                            <p class="text-muted">
                                {{ $village->subRegency->regency->type }} {{ $village->subRegency->regency->name }}
                                <br>
                                <small class="text-primary">
                                    <i class="bi bi-geo-alt"></i> Kode: {{ $village->subRegency->regency->code }}
                                </small>
                            </p>
                        </div>
                        <div class="col-md-4">
                            <label class="fw-bold">Provinsi</label>
                            <p class="text-muted">
                                {{ $village->subRegency->regency->province->name }}
                                <br>
                                <small class="text-primary">
                                    <i class="bi bi-geo-alt"></i> Kode: {{ $village->subRegency->regency->province->code }}
                                </small>
                            </p>
                        </div>
                    </div>

                    <div class="alert alert-info">
                        <strong><i class="bi bi-map"></i> Alamat Lengkap:</strong><br>
                        {{ $village->full_address }}
                    </div>
                </div>
            </div>

            @if($village->files->count() > 0)
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-file-earmark-text"></i> Dokumen Terlampir ({{ $village->files->count() }})
                    </h5>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        @foreach($village->files as $file)
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-file-earmark-pdf fs-4 text-danger"></i>
                                <div>
                                    <strong>{{ $file->file_name }}</strong>
                                    <br>
                                    <small class="text-muted">
                                        {{ number_format($file->file_size / 1024, 2) }} KB
                                        • Diupload: {{ $file->created_at->format('d/m/Y H:i') }}
                                    </small>
                                </div>
                            </div>
                            <a href="{{ Storage::url($file->file_path) }}" 
                               class="btn btn-sm btn-primary" 
                               target="_blank">
                                <i class="bi bi-download"></i> Download
                            </a>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>

        <div class="col-lg-4">
            <div class="card mb-3">
                <div class="card-header bg-secondary text-white">
                    <i class="bi bi-clock-history"></i> Audit Trail
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <td class="fw-bold" width="40%">Dibuat Oleh</td>
                            <td>{{ $village->created_by ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Dibuat Pada</td>
                            <td>
                                @if($village->created_at)
                                    {{ $village->created_at->format('d/m/Y') }}
                                    <br>
                                    <small class="text-muted">{{ $village->created_at->format('H:i:s') }}</small>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        @if($village->updated_by)
                        <tr>
                            <td class="fw-bold">Diupdate Oleh</td>
                            <td>{{ $village->updated_by }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Diupdate Pada</td>
                            <td>
                                {{ $village->updated_at->format('d/m/Y') }}
                                <br>
                                <small class="text-muted">{{ $village->updated_at->format('H:i:s') }}</small>
                            </td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-danger text-white">
                    <i class="bi bi-exclamation-triangle"></i> Aksi Berbahaya
                </div>
                <div class="card-body">
                    <form action="{{ route('village.destroy', $village->id) }}" method="POST" id="deleteForm">
                        @csrf
                        @method('DELETE')
                        <p class="text-muted mb-3">
                            <small>Menghapus desa/kelurahan akan memindahkannya ke tempat sampah. Data dapat dipulihkan oleh super admin.</small>
                        </p>
                        <button type="submit" class="btn btn-danger w-100" onclick="return confirm('Yakin ingin menghapus desa/kelurahan ini?')">
                            <i class="bi bi-trash"></i> Hapus Desa/Kelurahan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
