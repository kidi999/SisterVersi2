@extends('layouts.app')

@section('title', 'Edit Fakultas')
@section('header', 'Edit Fakultas')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Form Edit Fakultas</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('fakultas.update', $fakultas->id) }}" method="POST" id="fakultasForm">
            @csrf
            @method('PUT')
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="kode_fakultas" class="form-label">Kode Fakultas <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('kode_fakultas') is-invalid @enderror" 
                           id="kode_fakultas" name="kode_fakultas" value="{{ old('kode_fakultas', $fakultas->kode_fakultas) }}" required>
                    @error('kode_fakultas')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="singkatan" class="form-label">Singkatan <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('singkatan') is-invalid @enderror" 
                           id="singkatan" name="singkatan" value="{{ old('singkatan', $fakultas->singkatan) }}" required>
                    @error('singkatan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-3">
                <label for="nama_fakultas" class="form-label">Nama Fakultas <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('nama_fakultas') is-invalid @enderror" 
                       id="nama_fakultas" name="nama_fakultas" value="{{ old('nama_fakultas', $fakultas->nama_fakultas) }}" required>
                @error('nama_fakultas')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="dekan_id" class="form-label">Dekan</label>
                <select class="form-select @error('dekan_id') is-invalid @enderror" id="dekan_id" name="dekan_id">
                    <option value="">Pilih Dekan</option>
                    @foreach($dosen as $d)
                        <option value="{{ $d->id }}" {{ old('dekan_id', $fakultas->dekan_id) == $d->id ? 'selected' : '' }}>
                            {{ $d->nama_dosen }} - {{ $d->nidn }}
                        </option>
                    @endforeach
                </select>
                @error('dekan_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="text-muted">
                    @if($fakultas->dekanAktif)
                        Dekan saat ini: <strong>{{ $fakultas->dekanAktif->nama_dosen }}</strong>
                    @else
                        Pilih dosen yang akan menjadi dekan fakultas ini
                    @endif
                </small>
            </div>

            <div class="mb-3">
                <label for="alamat" class="form-label">Alamat</label>
                <textarea class="form-control @error('alamat') is-invalid @enderror" 
                          id="alamat" name="alamat" rows="3">{{ old('alamat', $fakultas->alamat) }}</textarea>
                @error('alamat')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="telepon" class="form-label">Telepon</label>
                    <input type="text" class="form-control @error('telepon') is-invalid @enderror" 
                           id="telepon" name="telepon" value="{{ old('telepon', $fakultas->telepon) }}">
                    @error('telepon')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                           id="email" name="email" value="{{ old('email', $fakultas->email) }}">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <h6 class="mb-3 mt-4 text-primary"><i class="bi bi-geo-alt-fill"></i> Informasi Wilayah</h6>

            <div class="row">
                <div class="col-md-3 mb-3">
                    <label for="province_id" class="form-label">Provinsi <span class="text-danger">*</span></label>
                    <select class="form-select @error('province_id') is-invalid @enderror" id="province_id" name="province_id" required>
                        <option value="">Pilih Provinsi</option>
                        @foreach($provinces as $province)
                            <option value="{{ $province->id }}" 
                                {{ old('province_id', $fakultas->village->subRegency->regency->province_id ?? '') == $province->id ? 'selected' : '' }}>
                                {{ $province->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('province_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-3 mb-3">
                    <label for="regency_id" class="form-label">Kabupaten/Kota <span class="text-danger">*</span></label>
                    <select class="form-select @error('regency_id') is-invalid @enderror" id="regency_id" name="regency_id" required>
                        <option value="">Pilih Kabupaten/Kota</option>
                    </select>
                    @error('regency_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-3 mb-3">
                    <label for="sub_regency_id" class="form-label">Kecamatan <span class="text-danger">*</span></label>
                    <select class="form-select @error('sub_regency_id') is-invalid @enderror" id="sub_regency_id" name="sub_regency_id" required>
                        <option value="">Pilih Kecamatan</option>
                    </select>
                    @error('sub_regency_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-3 mb-3">
                    <label for="village_id" class="form-label">Desa/Kelurahan <span class="text-danger">*</span></label>
                    <select class="form-select @error('village_id') is-invalid @enderror" id="village_id" name="village_id" required>
                        <option value="">Pilih Desa/Kelurahan</option>
                    </select>
                    @error('village_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- File Upload Component -->
            @include('components.file-upload', [
                'fileableType' => 'App\\Models\\Fakultas',
                'fileableId' => $fakultas->id,
                'existingFiles' => $fakultas->files
            ])

            <div class="d-flex justify-content-between mt-4">
                <a href="{{ route('fakultas.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Update
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Load existing data
    const existingProvinceId = {{ $fakultas->village->subRegency->regency->province_id ?? 'null' }};
    const existingRegencyId = {{ $fakultas->village->subRegency->regency_id ?? 'null' }};
    const existingSubRegencyId = {{ $fakultas->village->sub_regency_id ?? 'null' }};
    const existingVillageId = {{ $fakultas->village_id ?? 'null' }};

    // Load regencies on page load if province is selected
    if (existingProvinceId) {
        loadRegencies(existingProvinceId, existingRegencyId);
    }

    function loadRegencies(provinceId, selectedId = null) {
        const regencySelect = $('#regency_id');
        $.ajax({
            url: `/api/regencies/${provinceId}`,
            type: 'GET',
            success: function(data) {
                regencySelect.html('<option value="">Pilih Kabupaten/Kota</option>').prop('disabled', false);
                data.forEach(function(regency) {
                    const selected = selectedId && regency.id == selectedId ? 'selected' : '';
                    regencySelect.append(`<option value="${regency.id}" ${selected}>${regency.name}</option>`);
                });
                
                if (selectedId) {
                    loadSubRegencies(selectedId, existingSubRegencyId);
                }
            }
        });
    }

    function loadSubRegencies(regencyId, selectedId = null) {
        const subRegencySelect = $('#sub_regency_id');
        $.ajax({
            url: `/api/sub-regencies/${regencyId}`,
            type: 'GET',
            success: function(data) {
                subRegencySelect.html('<option value="">Pilih Kecamatan</option>').prop('disabled', false);
                data.forEach(function(subRegency) {
                    const selected = selectedId && subRegency.id == selectedId ? 'selected' : '';
                    subRegencySelect.append(`<option value="${subRegency.id}" ${selected}>${subRegency.name}</option>`);
                });
                
                if (selectedId) {
                    loadVillages(selectedId, existingVillageId);
                }
            }
        });
    }

    function loadVillages(subRegencyId, selectedId = null) {
        const villageSelect = $('#village_id');
        $.ajax({
            url: `/api/villages/${subRegencyId}`,
            type: 'GET',
            success: function(data) {
                villageSelect.html('<option value="">Pilih Desa/Kelurahan</option>').prop('disabled', false);
                data.forEach(function(village) {
                    const selected = selectedId && village.id == selectedId ? 'selected' : '';
                    villageSelect.append(`<option value="${village.id}" ${selected}>${village.name}</option>`);
                });
            }
        });
    }

    // Province change
    $('#province_id').change(function() {
        const provinceId = $(this).val();
        const regencySelect = $('#regency_id');
        const subRegencySelect = $('#sub_regency_id');
        const villageSelect = $('#village_id');
        
        regencySelect.html('<option value="">Pilih Kabupaten/Kota</option>').prop('disabled', true);
        subRegencySelect.html('<option value="">Pilih Kecamatan</option>').prop('disabled', true);
        villageSelect.html('<option value="">Pilih Desa/Kelurahan</option>').prop('disabled', true);
        
        if (provinceId) {
            loadRegencies(provinceId);
        }
    });
    
    // Regency change
    $('#regency_id').change(function() {
        const regencyId = $(this).val();
        const subRegencySelect = $('#sub_regency_id');
        const villageSelect = $('#village_id');
        
        subRegencySelect.html('<option value="">Pilih Kecamatan</option>').prop('disabled', true);
        villageSelect.html('<option value="">Pilih Desa/Kelurahan</option>').prop('disabled', true);
        
        if (regencyId) {
            loadSubRegencies(regencyId);
        }
    });
    
    // Sub Regency change
    $('#sub_regency_id').change(function() {
        const subRegencyId = $(this).val();
        const villageSelect = $('#village_id');
        
        villageSelect.html('<option value="">Pilih Desa/Kelurahan</option>').prop('disabled', true);
        
        if (subRegencyId) {
            loadVillages(subRegencyId);
        }
    });

    // Debug form submit
    $('#fakultasForm').submit(function(e) {
        const fileIds = $('#fileIds').val();
        console.log('Form submitting with file_ids:', fileIds);
        
        if (!fileIds || fileIds === '[]') {
            console.warn('No files attached!');
        }
    });
});
</script>
@endpush
