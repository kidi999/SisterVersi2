@extends('layouts.app')

@section('title', 'Tambah Kecamatan')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Tambah Kecamatan</h1>
        <a href="{{ route('sub-regency.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('sub-regency.store') }}" method="POST" id="subRegencyForm">
                        @csrf

                        <div class="mb-3">
                            <label for="province_id" class="form-label">
                                Provinsi <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('province_id') is-invalid @enderror" 
                                    id="province_id" 
                                    name="province_id" 
                                    required>
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

                        <div class="mb-3">
                            <label for="regency_id" class="form-label">
                                Kabupaten/Kota <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('regency_id') is-invalid @enderror" 
                                    id="regency_id" 
                                    name="regency_id" 
                                    required>
                                <option value="">Pilih Kabupaten/Kota</option>
                            </select>
                            @error('regency_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="code" class="form-label">
                                Kode Kecamatan <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('code') is-invalid @enderror" 
                                   id="code" 
                                   name="code" 
                                   value="{{ old('code') }}"
                                   maxlength="10"
                                   required>
                            <small class="text-muted">Maksimal 10 karakter</small>
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="name" class="form-label">
                                Nama Kecamatan <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name') }}"
                                   maxlength="100"
                                   required>
                            <small class="text-muted">Maksimal 100 karakter</small>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- File Upload Component -->
                        @include('components.file-upload', [
                            'fileableType' => 'App\\Models\\SubRegency',
                            'fileableId' => 0
                        ])
                    </form>

                    <div class="d-flex gap-2 mt-3">
                        <button type="submit" class="btn btn-primary" form="subRegencyForm">
                            <i class="bi bi-save"></i> Simpan
                        </button>
                        <a href="{{ route('sub-regency.index') }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i> Batal
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <i class="bi bi-info-circle"></i> Panduan
                </div>
                <div class="card-body">
                    <h6>Informasi</h6>
                    <ul class="small">
                        <li>Pilih provinsi terlebih dahulu untuk memfilter kabupaten/kota</li>
                        <li>Kode kecamatan harus unik</li>
                        <li>Semua field bertanda <span class="text-danger">*</span> wajib diisi</li>
                        <li>Upload dokumen pendukung jika diperlukan</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Load regencies when province is selected
    $('#province_id').on('change', function() {
        const provinceId = $(this).val();
        const $regencySelect = $('#regency_id');
        
        $regencySelect.html('<option value="">Memuat...</option>');
        
        if (provinceId) {
            $.ajax({
                url: `/regencies-by-province/${provinceId}`,
                type: 'GET',
                success: function(data) {
                    $regencySelect.html('<option value="">Pilih Kabupaten/Kota</option>');
                    data.forEach(function(regency) {
                        $regencySelect.append(
                            `<option value="${regency.id}">${regency.type} ${regency.name}</option>`
                        );
                    });
                },
                error: function() {
                    $regencySelect.html('<option value="">Error memuat data</option>');
                }
            });
        } else {
            $regencySelect.html('<option value="">Pilih Kabupaten/Kota</option>');
        }
    });

    // Trigger if old province value exists
    @if(old('province_id'))
        $('#province_id').trigger('change');
        setTimeout(function() {
            $('#regency_id').val('{{ old('regency_id') }}');
        }, 500);
    @endif
});
</script>
@endpush
