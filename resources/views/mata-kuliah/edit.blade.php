@extends('layouts.app')

@section('title', 'Edit Mata Kuliah')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Mata Kuliah</h1>
        <a href="{{ route('mata-kuliah.index') }}" class="btn btn-secondary btn-icon-split">
            <span class="icon text-white-50">
                <i class="fas fa-arrow-left"></i>
            </span>
            <span class="text">Kembali</span>
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Form Edit Mata Kuliah</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('mata-kuliah.update', $mataKuliah->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="alert alert-info">
                    <strong><i class="fas fa-info-circle"></i> Informasi Level Mata Kuliah:</strong>
                    <ul class="mb-0 mt-2">
                        <li><strong>Mata Kuliah Universitas:</strong> Dapat diambil oleh semua fakultas dan program studi</li>
                        <li><strong>Mata Kuliah Fakultas:</strong> Dapat diambil oleh semua program studi dalam 1 fakultas</li>
                        <li><strong>Mata Kuliah Program Studi:</strong> Hanya dapat diambil oleh program studi tertentu</li>
                    </ul>
                </div>

                <div class="mb-3">
                    <label for="level_matkul">Level Mata Kuliah <span class="text-danger">*</span></label>
                    <select name="level_matkul" id="level_matkul" class="form-control @error('level_matkul') is-invalid @enderror" required>
                        <option value="">-- Pilih Level --</option>
                        <option value="universitas" {{ old('level_matkul', $mataKuliah->level_matkul) == 'universitas' ? 'selected' : '' }}>Mata Kuliah Universitas</option>
                        <option value="fakultas" {{ old('level_matkul', $mataKuliah->level_matkul) == 'fakultas' ? 'selected' : '' }}>Mata Kuliah Fakultas</option>
                        <option value="prodi" {{ old('level_matkul', $mataKuliah->level_matkul) == 'prodi' ? 'selected' : '' }}>Mata Kuliah Program Studi</option>
                    </select>
                    @error('level_matkul')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="fakultas_id">Fakultas <span class="text-danger fakultas-required">*</span></label>
                        <select name="fakultas_id" id="fakultas_id" class="form-control @error('fakultas_id') is-invalid @enderror">
                            <option value="">-- Pilih Fakultas --</option>
                            @foreach($fakultas as $fak)
                            <option value="{{ $fak->id }}" {{ old('fakultas_id', $mataKuliah->fakultas_id) == $fak->id ? 'selected' : '' }}>
                                {{ $fak->nama_fakultas }}
                            </option>
                            @endforeach
                        </select>
                        @error('fakultas_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="program_studi_id">Program Studi <span class="text-danger prodi-required">*</span></label>
                        <select name="program_studi_id" id="program_studi_id" class="form-control @error('program_studi_id') is-invalid @enderror">
                            <option value="">-- Pilih Program Studi --</option>
                            @foreach($programStudi as $prodi)
                            <option value="{{ $prodi->id }}" 
                                    data-fakultas="{{ $prodi->fakultas_id }}"
                                    {{ old('program_studi_id', $mataKuliah->program_studi_id) == $prodi->id ? 'selected' : '' }}>
                                {{ $prodi->nama_prodi }} ({{ $prodi->jenjang }})
                            </option>
                            @endforeach
                        </select>
                        @error('program_studi_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="kode_mk">Kode Mata Kuliah <span class="text-danger">*</span></label>
                        <input type="text" name="kode_mk" id="kode_mk" class="form-control @error('kode_mk') is-invalid @enderror" 
                               value="{{ old('kode_mk', $mataKuliah->kode_mk) }}" required maxlength="20">
                        @error('kode_mk')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-8 mb-3">
                        <label for="nama_mk">Nama Mata Kuliah <span class="text-danger">*</span></label>
                        <input type="text" name="nama_mk" id="nama_mk" class="form-control @error('nama_mk') is-invalid @enderror" 
                               value="{{ old('nama_mk', $mataKuliah->nama_mk) }}" required maxlength="100">
                        @error('nama_mk')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="sks">SKS <span class="text-danger">*</span></label>
                        <input type="number" name="sks" id="sks" class="form-control @error('sks') is-invalid @enderror" 
                               value="{{ old('sks', $mataKuliah->sks) }}" required min="1" max="6">
                        @error('sks')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3 mb-3">
                        <label for="semester">Semester <span class="text-danger">*</span></label>
                        <input type="number" name="semester" id="semester" class="form-control @error('semester') is-invalid @enderror" 
                               value="{{ old('semester', $mataKuliah->semester) }}" required min="1" max="14">
                        @error('semester')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="jenis">Jenis Mata Kuliah <span class="text-danger">*</span></label>
                        <select name="jenis" id="jenis" class="form-control @error('jenis') is-invalid @enderror" required>
                            <option value="">-- Pilih --</option>
                            <option value="Wajib" {{ old('jenis', $mataKuliah->jenis) == 'Wajib' ? 'selected' : '' }}>Wajib</option>
                            <option value="Pilihan" {{ old('jenis', $mataKuliah->jenis) == 'Pilihan' ? 'selected' : '' }}>Pilihan</option>
                        </select>
                        @error('jenis')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="deskripsi">Deskripsi</label>
                    <textarea name="deskripsi" id="deskripsi" rows="4" class="form-control @error('deskripsi') is-invalid @enderror">{{ old('deskripsi', $mataKuliah->deskripsi) }}</textarea>
                    @error('deskripsi')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <hr>

                <div class="mb-3">
                    <label class="form-label">Lampiran Dokumen</label>
                    @include('components.file-upload', [
                        'fileableType' => 'App\Models\MataKuliah',
                        'fileableId' => $mataKuliah->id,
                        'existingFiles' => $mataKuliah->files
                    ])
                </div>

                <hr>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('mata-kuliah.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    console.log('=== Mata Kuliah Edit Form Initialized ===');
    
    // Toggle fields based on level
    function toggleFields() {
        var level = $('#level_matkul').val();
        console.log('Toggle fields for level:', level);
        
        if (level === 'universitas') {
            $('#fakultas_id').prop('disabled', true).prop('required', false).val('');
            $('#program_studi_id').prop('disabled', true).prop('required', false).val('');
            $('.fakultas-required, .prodi-required').hide();
        } else if (level === 'fakultas') {
            $('#fakultas_id').prop('disabled', false).prop('required', true);
            $('#program_studi_id').prop('disabled', true).prop('required', false).val('');
            $('.fakultas-required').show();
            $('.prodi-required').hide();
        } else if (level === 'prodi') {
            $('#fakultas_id').prop('disabled', false).prop('required', true);
            $('#program_studi_id').prop('disabled', false).prop('required', true);
            $('.fakultas-required, .prodi-required').show();
        } else {
            $('#fakultas_id').prop('disabled', true).prop('required', false);
            $('#program_studi_id').prop('disabled', true).prop('required', false);
        }
    }
    
    $('#level_matkul').change(toggleFields);
    
    // Filter program studi based on fakultas
    $('#fakultas_id').change(function() {
        var fakultasId = $(this).val();
        var prodiSelect = $('#program_studi_id');
        
        if (fakultasId) {
            prodiSelect.find('option').hide();
            prodiSelect.find('option[value=""]').show();
            prodiSelect.find('option[data-fakultas="' + fakultasId + '"]').show();
        } else {
            prodiSelect.find('option').show();
        }
    });
    
    // Initial setup
    var initialLevel = $('#level_matkul').val();
    if (initialLevel === 'prodi') {
        $('#fakultas_id').trigger('change');
        $('#program_studi_id').val('{{ old('program_studi_id', $mataKuliah->program_studi_id) }}');
    }
    toggleFields();
    
    // Before submit, enable disabled fields
    $('form').submit(function() {
        console.log('Form submitting...');
        $('#fakultas_id').prop('disabled', false);
        $('#program_studi_id').prop('disabled', false);
    });
});
</script>
@endpush
