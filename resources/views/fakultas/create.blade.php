@extends('layouts.app')

@section('title', 'Tambah Fakultas')
@section('header', 'Tambah Fakultas')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Form Tambah Fakultas</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('fakultas.store') }}" method="POST" id="fakultasForm">
            @csrf
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="kode_fakultas" class="form-label">Kode Fakultas <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('kode_fakultas') is-invalid @enderror" 
                           id="kode_fakultas" name="kode_fakultas" value="{{ old('kode_fakultas') }}" required>
                    @error('kode_fakultas')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="singkatan" class="form-label">Singkatan <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('singkatan') is-invalid @enderror" 
                           id="singkatan" name="singkatan" value="{{ old('singkatan') }}" required>
                    @error('singkatan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-3">
                <label for="nama_fakultas" class="form-label">Nama Fakultas <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('nama_fakultas') is-invalid @enderror" 
                       id="nama_fakultas" name="nama_fakultas" value="{{ old('nama_fakultas') }}" required>
                @error('nama_fakultas')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="dekan_id" class="form-label">Dekan</label>
                <select class="form-select @error('dekan_id') is-invalid @enderror" id="dekan_id" name="dekan_id">
                    <option value="">Pilih Dekan</option>
                    @foreach($dosen as $d)
                        <option value="{{ $d->id }}" {{ old('dekan_id') == $d->id ? 'selected' : '' }}>
                            {{ $d->nama_dosen }} - {{ $d->nidn }}
                        </option>
                    @endforeach
                </select>
                @error('dekan_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="text-muted">Pilih dosen yang akan menjadi dekan fakultas ini</small>
            </div>

            <div class="mb-3">
                <label for="alamat" class="form-label">Alamat</label>
                <textarea class="form-control @error('alamat') is-invalid @enderror" 
                          id="alamat" name="alamat" rows="3">{{ old('alamat') }}</textarea>
                @error('alamat')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="telepon" class="form-label">Telepon</label>
                    <input type="text" class="form-control @error('telepon') is-invalid @enderror" 
                           id="telepon" name="telepon" value="{{ old('telepon') }}">
                    @error('telepon')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                           id="email" name="email" value="{{ old('email') }}">
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
                            <option value="{{ $province->id }}" {{ old('province_id') == $province->id ? 'selected' : '' }}>
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
                    <select class="form-select @error('regency_id') is-invalid @enderror" id="regency_id" name="regency_id" required disabled>
                        <option value="">Pilih Kabupaten/Kota</option>
                    </select>
                    @error('regency_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-3 mb-3">
                    <label for="sub_regency_id" class="form-label">Kecamatan <span class="text-danger">*</span></label>
                    <select class="form-select @error('sub_regency_id') is-invalid @enderror" id="sub_regency_id" name="sub_regency_id" required disabled>
                        <option value="">Pilih Kecamatan</option>
                    </select>
                    @error('sub_regency_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-3 mb-3">
                    <label for="village_id" class="form-label">Desa/Kelurahan <span class="text-danger">*</span></label>
                    <select class="form-select @error('village_id') is-invalid @enderror" id="village_id" name="village_id" required disabled>
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
                'fileableId' => 0
            ])

            <div class="d-flex justify-content-between mt-4">
                <a href="{{ route('fakultas.index') }}" class="btn btn-secondary">
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

@push('scripts')
<script>
$(document).ready(function() {
    // Province change
    $('#province_id').change(function() {
        const provinceId = $(this).val();
        const regencySelect = $('#regency_id');
        const subRegencySelect = $('#sub_regency_id');
        const villageSelect = $('#village_id');
        
        // Reset dependent dropdowns
        regencySelect.html('<option value="">Pilih Kabupaten/Kota</option>').prop('disabled', true);
        subRegencySelect.html('<option value="">Pilih Kecamatan</option>').prop('disabled', true);
        villageSelect.html('<option value="">Pilih Desa/Kelurahan</option>').prop('disabled', true);
        
        if (provinceId) {
            $.ajax({
                url: `/api/regencies/${provinceId}`,
                type: 'GET',
                success: function(data) {
                    regencySelect.prop('disabled', false);
                    data.forEach(function(regency) {
                        regencySelect.append(`<option value="${regency.id}">${regency.name}</option>`);
                    });
                }
            });
        }
    });
    
    // Regency change
    $('#regency_id').change(function() {
        const regencyId = $(this).val();
        const subRegencySelect = $('#sub_regency_id');
        const villageSelect = $('#village_id');
        
        // Reset dependent dropdowns
        subRegencySelect.html('<option value="">Pilih Kecamatan</option>').prop('disabled', true);
        villageSelect.html('<option value="">Pilih Desa/Kelurahan</option>').prop('disabled', true);
        
        if (regencyId) {
            $.ajax({
                url: `/api/sub-regencies/${regencyId}`,
                type: 'GET',
                success: function(data) {
                    subRegencySelect.prop('disabled', false);
                    data.forEach(function(subRegency) {
                        subRegencySelect.append(`<option value="${subRegency.id}">${subRegency.name}</option>`);
                    });
                }
            });
        }
    });
    
    // Sub Regency change
    $('#sub_regency_id').change(function() {
        const subRegencyId = $(this).val();
        const villageSelect = $('#village_id');
        
        // Reset village dropdown
        villageSelect.html('<option value="">Pilih Desa/Kelurahan</option>').prop('disabled', true);
        
        if (subRegencyId) {
            $.ajax({
                url: `/api/villages/${subRegencyId}`,
                type: 'GET',
                success: function(data) {
                    villageSelect.prop('disabled', false);
                    data.forEach(function(village) {
                        villageSelect.append(`<option value="${village.id}">${village.name}</option>`);
                    });
                }
            });
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
