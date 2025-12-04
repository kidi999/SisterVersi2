@extends('layouts.app')

@section('title', 'Edit Program Studi')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Program Studi</h1>
        <a href="{{ route('program-studi.index') }}" class="btn btn-secondary btn-icon-split">
            <span class="icon text-white-50">
                <i class="fas fa-arrow-left"></i>
            </span>
            <span class="text">Kembali</span>
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Form Edit Program Studi</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('program-studi.update', $programStudi->id) }}" method="POST" id="programStudiForm">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="fakultas_id">Fakultas <span class="text-danger">*</span></label>
                            <select class="form-control @error('fakultas_id') is-invalid @enderror" 
                                    id="fakultas_id" name="fakultas_id" required>
                                <option value="">-- Pilih Fakultas --</option>
                                @foreach($fakultas as $fak)
                                    <option value="{{ $fak->id }}" {{ old('fakultas_id', $programStudi->fakultas_id) == $fak->id ? 'selected' : '' }}>
                                        {{ $fak->nama_fakultas }}
                                    </option>
                                @endforeach
                            </select>
                            @error('fakultas_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="kode_prodi">Kode Program Studi <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('kode_prodi') is-invalid @enderror" 
                                   id="kode_prodi" name="kode_prodi" value="{{ old('kode_prodi', $programStudi->kode_prodi) }}" 
                                   maxlength="10" required>
                            @error('kode_prodi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="jenjang">Jenjang <span class="text-danger">*</span></label>
                            <select class="form-control @error('jenjang') is-invalid @enderror" 
                                    id="jenjang" name="jenjang" required>
                                <option value="">-- Pilih Jenjang --</option>
                                <option value="D3" {{ old('jenjang', $programStudi->jenjang) == 'D3' ? 'selected' : '' }}>D3</option>
                                <option value="D4" {{ old('jenjang', $programStudi->jenjang) == 'D4' ? 'selected' : '' }}>D4</option>
                                <option value="S1" {{ old('jenjang', $programStudi->jenjang) == 'S1' ? 'selected' : '' }}>S1</option>
                                <option value="S2" {{ old('jenjang', $programStudi->jenjang) == 'S2' ? 'selected' : '' }}>S2</option>
                                <option value="S3" {{ old('jenjang', $programStudi->jenjang) == 'S3' ? 'selected' : '' }}>S3</option>
                            </select>
                            @error('jenjang')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="nama_prodi">Nama Program Studi <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nama_prodi') is-invalid @enderror" 
                                   id="nama_prodi" name="nama_prodi" value="{{ old('nama_prodi', $programStudi->nama_prodi) }}" required>
                            @error('nama_prodi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <label for="kaprodi">Ketua Program Studi</label>
                            <input type="text" class="form-control @error('kaprodi') is-invalid @enderror" 
                                   id="kaprodi" name="kaprodi" value="{{ old('kaprodi', $programStudi->kaprodi) }}">
                            @error('kaprodi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="akreditasi">Akreditasi</label>
                            <select class="form-control @error('akreditasi') is-invalid @enderror" 
                                    id="akreditasi" name="akreditasi">
                                <option value="">-- Pilih Akreditasi --</option>
                                <option value="Unggul" {{ old('akreditasi', $programStudi->akreditasi) == 'Unggul' ? 'selected' : '' }}>Unggul</option>
                                <option value="A" {{ old('akreditasi', $programStudi->akreditasi) == 'A' ? 'selected' : '' }}>A</option>
                                <option value="Baik Sekali" {{ old('akreditasi', $programStudi->akreditasi) == 'Baik Sekali' ? 'selected' : '' }}>Baik Sekali</option>
                                <option value="B" {{ old('akreditasi', $programStudi->akreditasi) == 'B' ? 'selected' : '' }}>B</option>
                                <option value="Baik" {{ old('akreditasi', $programStudi->akreditasi) == 'Baik' ? 'selected' : '' }}>Baik</option>
                                <option value="C" {{ old('akreditasi', $programStudi->akreditasi) == 'C' ? 'selected' : '' }}>C</option>
                                <option value="Belum Terakreditasi" {{ old('akreditasi', $programStudi->akreditasi) == 'Belum Terakreditasi' ? 'selected' : '' }}>Belum Terakreditasi</option>
                            </select>
                            @error('akreditasi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <!-- File Upload Component -->
                @include('components.file-upload', [
                    'fileableType' => 'App\\Models\\ProgramStudi',
                    'fileableId' => $programStudi->id,
                    'existingFiles' => $programStudi->files
                ])

                <hr class="my-4">

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update
                    </button>
                    <a href="{{ route('program-studi.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>


@endsection
