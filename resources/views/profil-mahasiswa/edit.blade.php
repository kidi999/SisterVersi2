@extends('layouts.app')

@section('title', 'Edit Profil - SISTER')
@section('header', 'Edit Profil Mahasiswa')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-pencil-square"></i> Edit Profil</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('profil-mahasiswa.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Data yang tidak bisa diubah -->
                    <div class="alert alert-info">
                        <strong>Info:</strong> Data NIM, Nama, dan informasi akademik tidak dapat diubah melalui halaman ini. 
                        Silakan hubungi administrator jika ada kesalahan data.
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">NIM <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" value="{{ $mahasiswa->nim }}" disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" value="{{ $mahasiswa->nama_mahasiswa }}" disabled>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                            <select name="jenis_kelamin" class="form-select @error('jenis_kelamin') is-invalid @enderror" required>
                                <option value="">Pilih...</option>
                                <option value="L" {{ old('jenis_kelamin', $mahasiswa->jenis_kelamin) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="P" {{ old('jenis_kelamin', $mahasiswa->jenis_kelamin) == 'P' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                            @error('jenis_kelamin')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Tempat Lahir <span class="text-danger">*</span></label>
                            <input type="text" name="tempat_lahir" class="form-control @error('tempat_lahir') is-invalid @enderror" 
                                   value="{{ old('tempat_lahir', $mahasiswa->tempat_lahir) }}" required>
                            @error('tempat_lahir')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Tanggal Lahir <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_lahir" class="form-control @error('tanggal_lahir') is-invalid @enderror" 
                                   value="{{ old('tanggal_lahir', $mahasiswa->tanggal_lahir ? $mahasiswa->tanggal_lahir->format('Y-m-d') : '') }}" required>
                            @error('tanggal_lahir')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                                   value="{{ old('email', $mahasiswa->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Telepon <span class="text-danger">*</span></label>
                            <input type="text" name="telepon" class="form-control @error('telepon') is-invalid @enderror" 
                                   value="{{ old('telepon', $mahasiswa->telepon) }}" required>
                            @error('telepon')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Alamat <span class="text-danger">*</span></label>
                        <textarea name="alamat" class="form-control @error('alamat') is-invalid @enderror" rows="3" required>{{ old('alamat', $mahasiswa->alamat) }}</textarea>
                        @error('alamat')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label class="form-label">Provinsi <span class="text-danger">*</span></label>
                            <select id="provinsi" class="form-select" required>
                                <option value="">Pilih Provinsi</option>
                                @foreach($provinsiList as $prov)
                                    <option value="{{ $prov->id }}" {{ $selectedProvinsi == $prov->id ? 'selected' : '' }}>
                                        {{ $prov->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Kabupaten/Kota <span class="text-danger">*</span></label>
                            <select id="regency" class="form-select" required>
                                <option value="">Pilih Kabupaten/Kota</option>
                                @foreach($regencyList as $reg)
                                    <option value="{{ $reg->id }}" {{ $selectedRegency == $reg->id ? 'selected' : '' }}>
                                        {{ $reg->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Kecamatan <span class="text-danger">*</span></label>
                            <select id="sub_regency" class="form-select" required>
                                <option value="">Pilih Kecamatan</option>
                                @foreach($subRegencyList as $sub)
                                    <option value="{{ $sub->id }}" {{ $selectedSubRegency == $sub->id ? 'selected' : '' }}>
                                        {{ $sub->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Desa/Kelurahan <span class="text-danger">*</span></label>
                            <select name="village_id" id="village" class="form-select @error('village_id') is-invalid @enderror" required>
                                <option value="">Pilih Desa/Kelurahan</option>
                                @foreach($villageList as $vil)
                                    <option value="{{ $vil->id }}" {{ old('village_id', $mahasiswa->village_id) == $vil->id ? 'selected' : '' }}>
                                        {{ $vil->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('village_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <hr class="my-4">
                    <h5 class="mb-3">Data Wali/Orang Tua</h5>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Nama Wali</label>
                            <input type="text" name="nama_wali" class="form-control @error('nama_wali') is-invalid @enderror" 
                                   value="{{ old('nama_wali', $mahasiswa->nama_wali) }}">
                            @error('nama_wali')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Telepon Wali</label>
                            <input type="text" name="telepon_wali" class="form-control @error('telepon_wali') is-invalid @enderror" 
                                   value="{{ old('telepon_wali', $mahasiswa->telepon_wali) }}">
                            @error('telepon_wali')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <hr class="my-4">

                    @include('components.file-upload', [
                        'fileableType' => 'App\\Models\\Mahasiswa',
                        'fileableId' => $mahasiswa->id,
                        'existingFiles' => $mahasiswa->files
                    ])

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Simpan Perubahan
                        </button>
                        <a href="{{ route('profil-mahasiswa.index') }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i> Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Load Kabupaten/Kota when Provinsi changes
    $('#provinsi').change(function() {
        var provinsiId = $(this).val();
        if (provinsiId) {
            $.ajax({
                url: '{{ route("profil-mahasiswa.regencies", ":id") }}'.replace(':id', provinsiId),
                type: 'GET',
                success: function(data) {
                    $('#regency').empty().append('<option value="">Pilih Kabupaten/Kota</option>');
                    $('#sub_regency').empty().append('<option value="">Pilih Kecamatan</option>');
                    $('#village').empty().append('<option value="">Pilih Desa/Kelurahan</option>');
                    
                    $.each(data, function(key, value) {
                        $('#regency').append('<option value="'+ value.id +'">'+ value.name +'</option>');
                    });
                }
            });
        } else {
            $('#regency').empty().append('<option value="">Pilih Kabupaten/Kota</option>');
            $('#sub_regency').empty().append('<option value="">Pilih Kecamatan</option>');
            $('#village').empty().append('<option value="">Pilih Desa/Kelurahan</option>');
        }
    });

    // Load Kecamatan when Kabupaten/Kota changes
    $('#regency').change(function() {
        var regencyId = $(this).val();
        if (regencyId) {
            $.ajax({
                url: '{{ route("profil-mahasiswa.sub-regencies", ":id") }}'.replace(':id', regencyId),
                type: 'GET',
                success: function(data) {
                    $('#sub_regency').empty().append('<option value="">Pilih Kecamatan</option>');
                    $('#village').empty().append('<option value="">Pilih Desa/Kelurahan</option>');
                    
                    $.each(data, function(key, value) {
                        $('#sub_regency').append('<option value="'+ value.id +'">'+ value.name +'</option>');
                    });
                }
            });
        } else {
            $('#sub_regency').empty().append('<option value="">Pilih Kecamatan</option>');
            $('#village').empty().append('<option value="">Pilih Desa/Kelurahan</option>');
        }
    });

    // Load Desa/Kelurahan when Kecamatan changes
    $('#sub_regency').change(function() {
        var subRegencyId = $(this).val();
        if (subRegencyId) {
            $.ajax({
                url: '{{ route("profil-mahasiswa.villages", ":id") }}'.replace(':id', subRegencyId),
                type: 'GET',
                success: function(data) {
                    $('#village').empty().append('<option value="">Pilih Desa/Kelurahan</option>');
                    
                    $.each(data, function(key, value) {
                        $('#village').append('<option value="'+ value.id +'">'+ value.name +'</option>');
                    });
                }
            });
        } else {
            $('#village').empty().append('<option value="">Pilih Desa/Kelurahan</option>');
        }
    });
});
</script>
@endpush
