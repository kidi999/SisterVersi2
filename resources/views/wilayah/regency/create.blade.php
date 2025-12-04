@extends('layouts.app')

@section('title', 'Tambah Kabupaten/Kota')
@section('header', 'Tambah Kabupaten/Kota')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Form Tambah Kabupaten/Kota</h5>
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

        <form action="{{ route('regency.store') }}" method="POST">
            @csrf
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="province_id" class="form-label">
                            Provinsi <span class="text-danger">*</span>
                        </label>
                        <select class="form-select @error('province_id') is-invalid @enderror" 
                                id="province_id" 
                                name="province_id" 
                                required>
                            <option value="">-- Pilih Provinsi --</option>
                            @foreach($provinces as $province)
                                <option value="{{ $province->id }}" {{ old('province_id') == $province->id ? 'selected' : '' }}>
                                    {{ $province->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('province_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="code" class="form-label">
                            Kode Kabupaten/Kota <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control @error('code') is-invalid @enderror" 
                               id="code" 
                               name="code" 
                               value="{{ old('code') }}"
                               maxlength="10"
                               placeholder="Contoh: 1101"
                               required>
                        @error('code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Kode kabupaten/kota sesuai Permendagri (maks 10 karakter)</small>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="mb-3">
                        <label for="name" class="form-label">
                            Nama Kabupaten/Kota <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control @error('name') is-invalid @enderror" 
                               id="name" 
                               name="name" 
                               value="{{ old('name') }}"
                               maxlength="100"
                               placeholder="Contoh: Kabupaten Aceh Selatan"
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
                    @include('components.file-upload', ['existingFiles' => []])
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-4">
                <a href="{{ route('regency.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Simpan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
