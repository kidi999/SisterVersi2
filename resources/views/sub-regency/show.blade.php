@extends('layouts.app')

@section('title', 'Detail Kecamatan')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Detail Kecamatan</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('sub-regency.edit', $subRegency->id) }}" class="btn btn-warning">
                <i class="bi bi-pencil"></i> Edit
            </a>
            <a href="{{ route('sub-regency.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-signpost"></i> Informasi Kecamatan
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label class="fw-bold">Kode</label>
                            <p class="text-muted"><code>{{ $subRegency->code }}</code></p>
                        </div>
                        <div class="col-md-9">
                            <label class="fw-bold">Nama Kecamatan</label>
                            <p class="text-muted">{{ $subRegency->name }}</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="fw-bold">Kabupaten/Kota</label>
                            <p class="text-muted">
                                {{ $subRegency->regency->type }} {{ $subRegency->regency->name }}
                                <br>
                                <small class="text-primary">
                                    <i class="bi bi-geo-alt"></i> Kode: {{ $subRegency->regency->code }}
                                </small>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold">Provinsi</label>
                            <p class="text-muted">
                                {{ $subRegency->regency->province->name }}
                                <br>
                                <small class="text-primary">
                                    <i class="bi bi-geo-alt"></i> Kode: {{ $subRegency->regency->province->code }}
                                </small>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            @if($subRegency->villages->count() > 0)
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-geo-alt"></i> Daftar Desa/Kelurahan ({{ $subRegency->villages->count() }})
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="15%">Kode</th>
                                    <th width="50%">Nama Desa/Kelurahan</th>
                                    <th width="15%">Kode Pos</th>
                                    <th width="15%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($subRegency->villages as $key => $village)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td><code>{{ $village->code }}</code></td>
                                    <td>{{ $village->name }}</td>
                                    <td>{{ $village->postal_code ?? '-' }}</td>
                                    <td>
                                        <a href="#" class="btn btn-sm btn-info" title="Detail">
                                            <i class="bi bi-eye"></i>
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

            @if($subRegency->files->count() > 0)
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-file-earmark-text"></i> Dokumen Terlampir ({{ $subRegency->files->count() }})
                    </h5>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        @foreach($subRegency->files as $file)
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
                            <td>{{ $subRegency->created_by ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Dibuat Pada</td>
                            <td>
                                @if($subRegency->created_at)
                                    {{ $subRegency->created_at->format('d/m/Y') }}
                                    <br>
                                    <small class="text-muted">{{ $subRegency->created_at->format('H:i:s') }}</small>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        @if($subRegency->updated_by)
                        <tr>
                            <td class="fw-bold">Diupdate Oleh</td>
                            <td>{{ $subRegency->updated_by }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Diupdate Pada</td>
                            <td>
                                {{ $subRegency->updated_at->format('d/m/Y') }}
                                <br>
                                <small class="text-muted">{{ $subRegency->updated_at->format('H:i:s') }}</small>
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
                    <form action="{{ route('sub-regency.destroy', $subRegency->id) }}" method="POST" id="deleteForm">
                        @csrf
                        @method('DELETE')
                        <p class="text-muted mb-3">
                            <small>Menghapus kecamatan akan memindahkannya ke tempat sampah. Data dapat dipulihkan oleh super admin.</small>
                        </p>
                        <button type="submit" class="btn btn-danger w-100" onclick="return confirm('Yakin ingin menghapus kecamatan ini?')">
                            <i class="bi bi-trash"></i> Hapus Kecamatan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
