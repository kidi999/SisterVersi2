@extends('layouts.app')

@section('title', 'Edit Kecamatan')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Edit Kecamatan</h1>
        <a href="{{ route('sub-regency.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('sub-regency.update', $subRegency->id) }}" method="POST" id="subRegencyFormEdit">
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
                                            {{ old('province_id', $subRegency->regency->province_id) == $province->id ? 'selected' : '' }}>
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
                                @foreach($regencies as $regency)
                                    <option value="{{ $regency->id }}" 
                                            data-province="{{ $regency->province_id }}"
                                            {{ old('regency_id', $subRegency->regency_id) == $regency->id ? 'selected' : '' }}>
                                        {{ $regency->type }} {{ $regency->name }}
                                    </option>
                                @endforeach
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
                                   value="{{ old('code', $subRegency->code) }}"
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
                                   value="{{ old('name', $subRegency->name) }}"
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
                            'fileableId' => $subRegency->id,
                            'existingFiles' => $subRegency->files
                        ])
                    </form>

                    <div class="d-flex gap-2 mt-3">
                        <button type="submit" class="btn btn-primary" form="subRegencyFormEdit">
                            <i class="bi bi-save"></i> Update
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
                    <i class="bi bi-info-circle"></i> Informasi
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <td class="fw-bold" width="40%">Dibuat Oleh</td>
                            <td>{{ $subRegency->created_by ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Dibuat Pada</td>
                            <td>{{ $subRegency->created_at ? $subRegency->created_at->format('d/m/Y H:i') : '-' }}</td>
                        </tr>
                        @if($subRegency->updated_by)
                        <tr>
                            <td class="fw-bold">Diupdate Oleh</td>
                            <td>{{ $subRegency->updated_by }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Diupdate Pada</td>
                            <td>{{ $subRegency->updated_at->format('d/m/Y H:i') }}</td>
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
    // Load regencies when province is selected
    $('#province_id').on('change', function() {
        const provinceId = $(this).val();
        const $regencySelect = $('#regency_id');
        const currentRegencyId = '{{ old('regency_id', $subRegency->regency_id) }}';
        
        $regencySelect.html('<option value="">Memuat...</option>');
        
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
                },
                error: function() {
                    $regencySelect.html('<option value="">Error memuat data</option>');
                }
            });
        } else {
            $regencySelect.html('<option value="">Pilih Kabupaten/Kota</option>');
        }
    });

    // Trigger on page load
    $('#province_id').trigger('change');
});
</script>
@endpush
