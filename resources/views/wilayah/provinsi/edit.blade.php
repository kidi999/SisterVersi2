@extends('layouts.app')

@section('title', 'Edit Provinsi')
@section('header', 'Edit Provinsi')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Form Edit Provinsi</h5>
    </div>
    <div class="card-body">
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Error!</strong> Terdapat kesalahan pada input:
                <ul class="mb-0 mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form action="{{ route('provinsi.update', $province) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="code" class="form-label">
                            Kode Provinsi <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control @error('code') is-invalid @enderror" 
                               id="code" 
                               name="code" 
                               value="{{ old('code', $province->code) }}"
                               maxlength="10"
                               placeholder="Contoh: 11"
                               required>
                        @error('code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Kode provinsi sesuai Permendagri (maks 10 karakter)</small>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="name" class="form-label">
                            Nama Provinsi <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control @error('name') is-invalid @enderror" 
                               id="name" 
                               name="name" 
                               value="{{ old('name', $province->name) }}"
                               maxlength="100"
                               placeholder="Contoh: Aceh"
                               required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- File Upload Component -->
            <div class="row mt-4">
                <div class="col-12">
                    <label class="form-label">File Pendukung (Opsional)</label>
                    @include('components.file-upload', [
                        'fileableType' => 'App\\Models\\Province',
                        'fileableId' => $province->id,
                        'existingFiles' => $province->files
                    ])
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-4">
                <a href="{{ route('provinsi.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Update
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card mt-3">
    <div class="card-header">
        <h5 class="mb-0">Informasi Audit</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-sm">
                    <tr>
                        <th width="40%">Dibuat oleh</th>
                        <td>{{ $province->created_by ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Tanggal dibuat</th>
                        <td>{{ $province->created_at ? $province->created_at->format('d/m/Y H:i') : '-' }}</td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-sm">
                    <tr>
                        <th width="40%">Diubah oleh</th>
                        <td>{{ $province->updated_by ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Tanggal diubah</th>
                        <td>{{ $province->updated_at ? $province->updated_at->format('d/m/Y H:i') : '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
