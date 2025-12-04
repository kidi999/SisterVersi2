@extends('layouts.app')

@section('title', 'Edit Dosen')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Dosen</h1>
        <a href="{{ route('dosen.index') }}" class="btn btn-secondary btn-icon-split">
            <span class="icon text-white-50">
                <i class="fas fa-arrow-left"></i>
            </span>
            <span class="text">Kembali</span>
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Form Edit Dosen</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('dosen.update', $dosen->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="alert alert-info">
                    <strong><i class="fas fa-info-circle"></i> Informasi Level Dosen:</strong>
                    <ul class="mb-0 mt-2">
                        <li><strong>Dosen Universitas:</strong> Dapat mengajar di semua fakultas dan program studi</li>
                        <li><strong>Dosen Fakultas:</strong> Dapat mengajar di semua program studi dalam 1 fakultas</li>
                        <li><strong>Dosen Program Studi:</strong> Hanya dapat mengajar di program studi tertentu</li>
                    </ul>
                </div>

                <div class="mb-3">
                    <label for="level_dosen">Level Dosen <span class="text-danger">*</span></label>
                    <select name="level_dosen" id="level_dosen" class="form-control @error('level_dosen') is-invalid @enderror" required>
                        <option value="">-- Pilih Level --</option>
                        <option value="universitas" {{ old('level_dosen', $dosen->level_dosen) == 'universitas' ? 'selected' : '' }}>Dosen Universitas</option>
                        <option value="fakultas" {{ old('level_dosen', $dosen->level_dosen) == 'fakultas' ? 'selected' : '' }}>Dosen Fakultas</option>
                        <option value="prodi" {{ old('level_dosen', $dosen->level_dosen) == 'prodi' ? 'selected' : '' }}>Dosen Program Studi</option>
                    </select>
                    @error('level_dosen')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="fakultas_id">Fakultas <span class="text-danger fakultas-required">*</span></label>
                        <select name="fakultas_id" id="fakultas_id" class="form-control @error('fakultas_id') is-invalid @enderror">
                            <option value="">-- Pilih Fakultas --</option>
                            @foreach($fakultas as $fak)
                            <option value="{{ $fak->id }}" {{ old('fakultas_id', $dosen->fakultas_id ?? $dosen->programStudi->fakultas_id ?? '') == $fak->id ? 'selected' : '' }}>
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
                                    {{ old('program_studi_id', $dosen->program_studi_id) == $prodi->id ? 'selected' : '' }}>
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
                    <div class="col-md-6 mb-3">
                        <label for="nip">NIP <span class="text-danger">*</span></label>
                        <input type="text" name="nip" id="nip" class="form-control @error('nip') is-invalid @enderror" 
                               value="{{ old('nip', $dosen->nip) }}" required maxlength="20">
                        @error('nip')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="nidn">NIDN</label>
                        <input type="text" name="nidn" id="nidn" class="form-control @error('nidn') is-invalid @enderror" 
                               value="{{ old('nidn', $dosen->nidn) }}" maxlength="20">
                        @error('nidn')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-8 mb-3">
                        <label for="nama_dosen">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="nama_dosen" id="nama_dosen" class="form-control @error('nama_dosen') is-invalid @enderror" 
                               value="{{ old('nama_dosen', $dosen->nama_dosen) }}" required maxlength="100">
                        @error('nama_dosen')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="jenis_kelamin">Jenis Kelamin <span class="text-danger">*</span></label>
                        <select name="jenis_kelamin" id="jenis_kelamin" class="form-control @error('jenis_kelamin') is-invalid @enderror" required>
                            <option value="">-- Pilih --</option>
                            <option value="L" {{ old('jenis_kelamin', $dosen->jenis_kelamin) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P" {{ old('jenis_kelamin', $dosen->jenis_kelamin) == 'P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                        @error('jenis_kelamin')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="tempat_lahir">Tempat Lahir</label>
                        <input type="text" name="tempat_lahir" id="tempat_lahir" class="form-control @error('tempat_lahir') is-invalid @enderror" 
                               value="{{ old('tempat_lahir', $dosen->tempat_lahir) }}" maxlength="50">
                        @error('tempat_lahir')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="tanggal_lahir">Tanggal Lahir</label>
                        <input type="date" name="tanggal_lahir" id="tanggal_lahir" class="form-control @error('tanggal_lahir') is-invalid @enderror" 
                               value="{{ old('tanggal_lahir', $dosen->tanggal_lahir ? $dosen->tanggal_lahir->format('Y-m-d') : '') }}">
                        @error('tanggal_lahir')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="alamat">Alamat</label>
                    <textarea name="alamat" id="alamat" rows="3" class="form-control @error('alamat') is-invalid @enderror">{{ old('alamat', $dosen->alamat) }}</textarea>
                    @error('alamat')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="province_id">Provinsi</label>
                        <select name="province_id" id="province_id" class="form-control @error('province_id') is-invalid @enderror">
                            <option value="">-- Pilih Provinsi --</option>
                            @foreach($provinces as $province)
                            <option value="{{ $province->id }}" {{ old('province_id', $dosen->village->subRegency->regency->province_id ?? '') == $province->id ? 'selected' : '' }}>
                                {{ $province->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('province_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3 mb-3">
                        <label for="regency_id">Kabupaten/Kota</label>
                        <select name="regency_id" id="regency_id" class="form-control @error('regency_id') is-invalid @enderror">
                            <option value="">-- Pilih Kabupaten/Kota --</option>
                        </select>
                        @error('regency_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3 mb-3">
                        <label for="sub_regency_id">Kecamatan</label>
                        <select name="sub_regency_id" id="sub_regency_id" class="form-control @error('sub_regency_id') is-invalid @enderror">
                            <option value="">-- Pilih Kecamatan --</option>
                        </select>
                        @error('sub_regency_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3 mb-3">
                        <label for="village_id">Kelurahan/Desa</label>
                        <select name="village_id" id="village_id" class="form-control @error('village_id') is-invalid @enderror">
                            <option value="">-- Pilih Kelurahan/Desa --</option>
                        </select>
                        @error('village_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="telepon">Telepon</label>
                        <input type="text" name="telepon" id="telepon" class="form-control @error('telepon') is-invalid @enderror" 
                               value="{{ old('telepon', $dosen->telepon) }}" maxlength="20">
                        @error('telepon')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="email">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" 
                               value="{{ old('email', $dosen->email) }}" required maxlength="100">
                        @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="pendidikan_terakhir">Pendidikan Terakhir</label>
                        <select name="pendidikan_terakhir" id="pendidikan_terakhir" class="form-control @error('pendidikan_terakhir') is-invalid @enderror">
                            <option value="">-- Pilih --</option>
                            <option value="S1" {{ old('pendidikan_terakhir', $dosen->pendidikan_terakhir) == 'S1' ? 'selected' : '' }}>S1</option>
                            <option value="S2" {{ old('pendidikan_terakhir', $dosen->pendidikan_terakhir) == 'S2' ? 'selected' : '' }}>S2</option>
                            <option value="S3" {{ old('pendidikan_terakhir', $dosen->pendidikan_terakhir) == 'S3' ? 'selected' : '' }}>S3</option>
                        </select>
                        @error('pendidikan_terakhir')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="jabatan_akademik">Jabatan Akademik</label>
                        <select name="jabatan_akademik" id="jabatan_akademik" class="form-control @error('jabatan_akademik') is-invalid @enderror">
                            <option value="">-- Pilih --</option>
                            <option value="Asisten Ahli" {{ old('jabatan_akademik', $dosen->jabatan_akademik) == 'Asisten Ahli' ? 'selected' : '' }}>Asisten Ahli</option>
                            <option value="Lektor" {{ old('jabatan_akademik', $dosen->jabatan_akademik) == 'Lektor' ? 'selected' : '' }}>Lektor</option>
                            <option value="Lektor Kepala" {{ old('jabatan_akademik', $dosen->jabatan_akademik) == 'Lektor Kepala' ? 'selected' : '' }}>Lektor Kepala</option>
                            <option value="Guru Besar" {{ old('jabatan_akademik', $dosen->jabatan_akademik) == 'Guru Besar' ? 'selected' : '' }}>Guru Besar</option>
                        </select>
                        @error('jabatan_akademik')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="status">Status <span class="text-danger">*</span></label>
                        <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required>
                            <option value="Aktif" {{ old('status', $dosen->status) == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="Non-Aktif" {{ old('status', $dosen->status) == 'Non-Aktif' ? 'selected' : '' }}>Non-Aktif</option>
                            <option value="Cuti" {{ old('status', $dosen->status) == 'Cuti' ? 'selected' : '' }}>Cuti</option>
                        </select>
                        @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <hr>

                <div class="mb-3">
                    <label class="form-label">Lampiran Dokumen</label>
                    @include('components.file-upload', [
                        'fileableType' => 'App\Models\Dosen',
                        'fileableId' => $dosen->id,
                        'existingFiles' => $dosen->files
                    ])
                </div>

                <hr>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('dosen.index') }}" class="btn btn-secondary">
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
    console.log('=== Dosen Edit Form Initialized ===');
    
    // Toggle fields based on level
    function toggleFields() {
        var level = $('#level_dosen').val();
        console.log('Toggle fields for level:', level);
        
        if (level === 'universitas') {
            // Disable both fields
            $('#fakultas_id').prop('disabled', true).prop('required', false).val('');
            $('#program_studi_id').prop('disabled', true).prop('required', false).val('');
            $('.fakultas-required, .prodi-required').hide();
        } else if (level === 'fakultas') {
            // Enable fakultas, disable prodi
            $('#fakultas_id').prop('disabled', false).prop('required', true);
            $('#program_studi_id').prop('disabled', true).prop('required', false).val('');
            $('.fakultas-required').show();
            $('.prodi-required').hide();
        } else if (level === 'prodi') {
            // Enable both fields
            $('#fakultas_id').prop('disabled', false).prop('required', true);
            $('#program_studi_id').prop('disabled', false).prop('required', true);
            $('.fakultas-required, .prodi-required').show();
        } else {
            $('#fakultas_id').prop('disabled', true).prop('required', false);
            $('#program_studi_id').prop('disabled', true).prop('required', false);
        }
    }
    
    $('#level_dosen').change(toggleFields);
    
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
    var initialLevel = $('#level_dosen').val();
    if (initialLevel === 'prodi') {
        $('#fakultas_id').trigger('change');
        $('#program_studi_id').val('{{ old('program_studi_id', $dosen->program_studi_id) }}');
    }
    toggleFields();
    
    // Before submit, enable disabled fields temporarily to submit empty values
    $('form').submit(function() {
        console.log('Form submitting...');
        $('#fakultas_id').prop('disabled', false);
        $('#program_studi_id').prop('disabled', false);
    });
    
    // Cascade for region
    $('#province_id').on('change', function() {
        var provinceId = $(this).val();
        console.log('Province changed:', provinceId);
        $('#regency_id, #sub_regency_id, #village_id').html('<option value="">-- Pilih --</option>');
        
        if (provinceId) {
            console.log('Loading regencies for province:', provinceId);
            $.ajax({
                url: '/api/regencies/' + provinceId,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    console.log('Regencies loaded:', data);
                    if (data && data.length > 0) {
                        $.each(data, function(key, value) {
                            $('#regency_id').append('<option value="'+ value.id +'">'+ value.name +'</option>');
                        });
                        
                        @if($dosen->village)
                        $('#regency_id').val('{{ $dosen->village->subRegency->regency_id ?? '' }}').trigger('change');
                        @endif
                    } else {
                        console.warn('No regencies found');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error loading regencies:', error);
                    console.error('Response:', xhr.responseText);
                }
            });
        }
    });
    
    $('#regency_id').on('change', function() {
        var regencyId = $(this).val();
        console.log('Regency changed:', regencyId);
        $('#sub_regency_id, #village_id').html('<option value="">-- Pilih --</option>');
        
        if (regencyId) {
            console.log('Loading sub-regencies for regency:', regencyId);
            $.ajax({
                url: '/api/sub-regencies/' + regencyId,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    console.log('Sub-regencies loaded:', data);
                    if (data && data.length > 0) {
                        $.each(data, function(key, value) {
                            $('#sub_regency_id').append('<option value="'+ value.id +'">'+ value.name +'</option>');
                        });
                        
                        @if($dosen->village)
                        $('#sub_regency_id').val('{{ $dosen->village->sub_regency_id ?? '' }}').trigger('change');
                        @endif
                    } else {
                        console.warn('No sub-regencies found');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error loading sub-regencies:', error);
                    console.error('Response:', xhr.responseText);
                }
            });
        }
    });
    
    $('#sub_regency_id').on('change', function() {
        var subRegencyId = $(this).val();
        console.log('Sub-regency changed:', subRegencyId);
        $('#village_id').html('<option value="">-- Pilih --</option>');
        
        if (subRegencyId) {
            console.log('Loading villages for sub-regency:', subRegencyId);
            $.ajax({
                url: '/api/villages/' + subRegencyId,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    console.log('Villages loaded:', data);
                    if (data && data.length > 0) {
                        $.each(data, function(key, value) {
                            $('#village_id').append('<option value="'+ value.id +'">'+ value.name +'</option>');
                        });
                        
                        @if($dosen->village)
                        $('#village_id').val('{{ $dosen->village_id }}');
                        @endif
                    } else {
                        console.warn('No villages found');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error loading villages:', error);
                    console.error('Response:', xhr.responseText);
                }
            });
        }
    });
    
    // Load initial data
    @if($dosen->village)
    console.log('Loading initial region data...');
    $('#province_id').trigger('change');
    @endif
    
    console.log('Region cascade initialized');
});
</script>
@endpush
