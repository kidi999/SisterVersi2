@extends('layouts.app')

@section('title', 'Edit Desa/Kelurahan')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Edit Desa/Kelurahan</h1>
        <a href="{{ route('village.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('village.update', $village->id) }}" method="POST" id="villageFormEdit">
                        @csrf
                        @method('PUT')

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
                                    <option value="{{ $province->id }}" 
                                            {{ old('province_id', $village->subRegency->regency->province_id) == $province->id ? 'selected' : '' }}>
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
                            <label for="sub_regency_id" class="form-label">
                                Kecamatan <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('sub_regency_id') is-invalid @enderror" 
                                    id="sub_regency_id" 
                                    name="sub_regency_id" 
                                    required>
                                <option value="">Pilih Kecamatan</option>
                            </select>
                            @error('sub_regency_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="code" class="form-label">
                                        Kode Desa/Kelurahan <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('code') is-invalid @enderror" 
                                           id="code" 
                                           name="code" 
                                           value="{{ old('code', $village->code) }}"
                                           maxlength="10"
                                           required>
                                    <small class="text-muted">Maksimal 10 karakter</small>
                                    @error('code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="name" class="form-label">
                                        Nama Desa/Kelurahan <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('name') is-invalid @enderror" 
                                           id="name" 
                                           name="name" 
                                           value="{{ old('name', $village->name) }}"
                                           maxlength="100"
                                           required>
                                    <small class="text-muted">Maksimal 100 karakter</small>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="postal_code" class="form-label">Kode Pos</label>
                            <input type="text" 
                                   class="form-control @error('postal_code') is-invalid @enderror" 
                                   id="postal_code" 
                                   name="postal_code" 
                                   value="{{ old('postal_code', $village->postal_code) }}"
                                   maxlength="10"
                                   placeholder="Contoh: 12345">
                            <small class="text-muted">Opsional, maksimal 10 karakter</small>
                            @error('postal_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- File Upload Component -->
                        @include('components.file-upload', [
                            'fileableType' => 'App\\Models\\Village',
                            'fileableId' => $village->id,
                            'existingFiles' => $village->files
                        ])
                    </form>

                    <div class="d-flex gap-2 mt-3">
                        <button type="submit" class="btn btn-primary" form="villageFormEdit">
                            <i class="bi bi-save"></i> Update
                        </button>
                        <a href="{{ route('village.index') }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i> Batal
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <i class="bi bi-info-circle"></i> Informasi
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <td class="fw-bold" width="40%">Dibuat Oleh</td>
                            <td>{{ $village->created_by ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Dibuat Pada</td>
                            <td>{{ $village->created_at ? $village->created_at->format('d/m/Y H:i') : '-' }}</td>
                        </tr>
                        @if($village->updated_by)
                        <tr>
                            <td class="fw-bold">Diupdate Oleh</td>
                            <td>{{ $village->updated_by }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Diupdate Pada</td>
                            <td>{{ $village->updated_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    const currentRegencyId = '{{ old('regency_id', $village->subRegency->regency_id) }}';
    const currentSubRegencyId = '{{ old('sub_regency_id', $village->sub_regency_id) }}';

    // Load regencies when province is selected
    $('#province_id').on('change', function() {
        const provinceId = $(this).val();
        const $regencySelect = $('#regency_id');
        const $subRegencySelect = $('#sub_regency_id');
        
        $regencySelect.html('<option value="">Memuat...</option>');
        $subRegencySelect.html('<option value="">Pilih Kecamatan</option>');
        
        if (provinceId) {
            $.ajax({
                url: `/regencies-by-province/${provinceId}`,
                type: 'GET',
                success: function(data) {
                    $regencySelect.html('<option value="">Pilih Kabupaten/Kota</option>');
                    data.forEach(function(regency) {
                        const selected = regency.id == currentRegencyId ? 'selected' : '';
                        $regencySelect.append(
                            `<option value="${regency.id}" ${selected}>${regency.type} ${regency.name}</option>`
                        );
                    });
                    
                    // Trigger regency change to load sub-regencies
                    if (currentRegencyId) {
                        $regencySelect.trigger('change');
                    }
                },
                error: function() {
                    $regencySelect.html('<option value="">Error memuat data</option>');
                }
            });
        } else {
            $regencySelect.html('<option value="">Pilih Kabupaten/Kota</option>');
        }
    });

    // Load sub-regencies when regency is selected
    $('#regency_id').on('change', function() {
        const regencyId = $(this).val();
        const $subRegencySelect = $('#sub_regency_id');
        
        $subRegencySelect.html('<option value="">Memuat...</option>');
        
        if (regencyId) {
            $.ajax({
                url: `/sub-regencies-by-regency/${regencyId}`,
                type: 'GET',
                success: function(data) {
                    $subRegencySelect.html('<option value="">Pilih Kecamatan</option>');
                    data.forEach(function(subRegency) {
                        const selected = subRegency.id == currentSubRegencyId ? 'selected' : '';
                        $subRegencySelect.append(
                            `<option value="${subRegency.id}" ${selected}>${subRegency.name}</option>`
                        );
                    });
                },
                error: function() {
                    $subRegencySelect.html('<option value="">Error memuat data</option>');
                }
            });
        } else {
            $subRegencySelect.html('<option value="">Pilih Kecamatan</option>');
        }
    });

    // Trigger on page load
    $('#province_id').trigger('change');
});
</script>
@endpush
