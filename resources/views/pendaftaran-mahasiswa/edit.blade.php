@extends('layouts.app')

@section('title', 'Edit Pendaftaran Mahasiswa')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Edit Pendaftaran Mahasiswa</h1>
        <div>
            <a href="{{ route('pendaftaran-mahasiswa.show', $pendaftaranMahasiswa) }}" class="btn btn-info">
                <i class="bi bi-eye"></i> Detail
            </a>
            <a href="{{ route('pendaftaran-mahasiswa.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <strong>Error!</strong> Silakan perbaiki kesalahan berikut:
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('pendaftaran-mahasiswa.update', $pendaftaranMahasiswa) }}" method="POST" id="formPendaftaran">
        @csrf
        @method('PUT')

        <!-- Info Pendaftaran -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-info-circle"></i> Informasi Pendaftaran</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">No. Pendaftaran</label>
                        <input type="text" class="form-control" value="{{ $pendaftaranMahasiswa->no_pendaftaran }}" readonly>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Tahun Akademik <span class="text-danger">*</span></label>
                        <input type="text" name="tahun_akademik" class="form-control @error('tahun_akademik') is-invalid @enderror" 
                               value="{{ old('tahun_akademik', $pendaftaranMahasiswa->tahun_akademik) }}" required>
                        @error('tahun_akademik')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Jalur Masuk <span class="text-danger">*</span></label>
                        <select name="jalur_masuk" class="form-select @error('jalur_masuk') is-invalid @enderror" required>
                            <option value="">Pilih Jalur Masuk</option>
                            <option value="SNBP" {{ old('jalur_masuk', $pendaftaranMahasiswa->jalur_masuk) == 'SNBP' ? 'selected' : '' }}>SNBP</option>
                            <option value="SNBT" {{ old('jalur_masuk', $pendaftaranMahasiswa->jalur_masuk) == 'SNBT' ? 'selected' : '' }}>SNBT</option>
                            <option value="Mandiri" {{ old('jalur_masuk', $pendaftaranMahasiswa->jalur_masuk) == 'Mandiri' ? 'selected' : '' }}>Mandiri</option>
                            <option value="Transfer" {{ old('jalur_masuk', $pendaftaranMahasiswa->jalur_masuk) == 'Transfer' ? 'selected' : '' }}>Transfer</option>
                        </select>
                        @error('jalur_masuk')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Program Studi <span class="text-danger">*</span></label>
                        <select name="program_studi_id" class="form-select @error('program_studi_id') is-invalid @enderror" required>
                            <option value="">Pilih Program Studi</option>
                            @foreach($programStudi as $prodi)
                                <option value="{{ $prodi->id }}" {{ old('program_studi_id', $pendaftaranMahasiswa->program_studi_id) == $prodi->id ? 'selected' : '' }}>
                                    {{ $prodi->fakultas->nama_fakultas }} - {{ $prodi->nama_prodi }}
                                </option>
                            @endforeach
                        </select>
                        @error('program_studi_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Pribadi -->
        <div class="card mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="bi bi-person"></i> Data Pribadi</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="nama_lengkap" class="form-control @error('nama_lengkap') is-invalid @enderror" 
                               value="{{ old('nama_lengkap', $pendaftaranMahasiswa->nama_lengkap) }}" required>
                        @error('nama_lengkap')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">NIK</label>
                        <input type="text" name="nik" class="form-control @error('nik') is-invalid @enderror" 
                               value="{{ old('nik', $pendaftaranMahasiswa->nik) }}" maxlength="16">
                        @error('nik')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                        <select name="jenis_kelamin" class="form-select @error('jenis_kelamin') is-invalid @enderror" required>
                            <option value="">Pilih</option>
                            <option value="L" {{ old('jenis_kelamin', $pendaftaranMahasiswa->jenis_kelamin) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P" {{ old('jenis_kelamin', $pendaftaranMahasiswa->jenis_kelamin) == 'P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                        @error('jenis_kelamin')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Tempat Lahir</label>
                        <input type="text" name="tempat_lahir" class="form-control @error('tempat_lahir') is-invalid @enderror" 
                               value="{{ old('tempat_lahir', $pendaftaranMahasiswa->tempat_lahir) }}">
                        @error('tempat_lahir')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Tanggal Lahir</label>
                        <input type="date" name="tanggal_lahir" class="form-control @error('tanggal_lahir') is-invalid @enderror" 
                               value="{{ old('tanggal_lahir', $pendaftaranMahasiswa->tanggal_lahir ? $pendaftaranMahasiswa->tanggal_lahir->format('Y-m-d') : '') }}">
                        @error('tanggal_lahir')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Agama</label>
                        <select name="agama" class="form-select @error('agama') is-invalid @enderror">
                            <option value="">Pilih</option>
                            <option value="Islam" {{ old('agama', $pendaftaranMahasiswa->agama) == 'Islam' ? 'selected' : '' }}>Islam</option>
                            <option value="Kristen" {{ old('agama', $pendaftaranMahasiswa->agama) == 'Kristen' ? 'selected' : '' }}>Kristen</option>
                            <option value="Katolik" {{ old('agama', $pendaftaranMahasiswa->agama) == 'Katolik' ? 'selected' : '' }}>Katolik</option>
                            <option value="Hindu" {{ old('agama', $pendaftaranMahasiswa->agama) == 'Hindu' ? 'selected' : '' }}>Hindu</option>
                            <option value="Buddha" {{ old('agama', $pendaftaranMahasiswa->agama) == 'Buddha' ? 'selected' : '' }}>Buddha</option>
                            <option value="Konghucu" {{ old('agama', $pendaftaranMahasiswa->agama) == 'Konghucu' ? 'selected' : '' }}>Konghucu</option>
                        </select>
                        @error('agama')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Status Perkawinan</label>
                        <select name="status_perkawinan" class="form-select @error('status_perkawinan') is-invalid @enderror">
                            <option value="Belum Kawin" {{ old('status_perkawinan', $pendaftaranMahasiswa->status_perkawinan) == 'Belum Kawin' ? 'selected' : '' }}>Belum Kawin</option>
                            <option value="Kawin" {{ old('status_perkawinan', $pendaftaranMahasiswa->status_perkawinan) == 'Kawin' ? 'selected' : '' }}>Kawin</option>
                            <option value="Cerai" {{ old('status_perkawinan', $pendaftaranMahasiswa->status_perkawinan) == 'Cerai' ? 'selected' : '' }}>Cerai</option>
                        </select>
                        @error('status_perkawinan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Alamat & Kontak -->
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="bi bi-geo-alt"></i> Alamat & Kontak</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label">Alamat Lengkap</label>
                        <textarea name="alamat" class="form-control @error('alamat') is-invalid @enderror" rows="2">{{ old('alamat', $pendaftaranMahasiswa->alamat) }}</textarea>
                        @error('alamat')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Provinsi</label>
                        <select id="province_id" class="form-select">
                            <option value="">Pilih Provinsi</option>
                            @foreach($provinces as $province)
                                <option value="{{ $province->id }}" 
                                    {{ $pendaftaranMahasiswa->village && $pendaftaranMahasiswa->village->subRegency->regency->province_id == $province->id ? 'selected' : '' }}>
                                    {{ $province->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Kabupaten/Kota</label>
                        <select id="regency_id" class="form-select" 
                            {{ $pendaftaranMahasiswa->village ? '' : 'disabled' }}>
                            <option value="">Pilih Kab/Kota</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Kecamatan</label>
                        <select id="sub_regency_id" class="form-select" 
                            {{ $pendaftaranMahasiswa->village ? '' : 'disabled' }}>
                            <option value="">Pilih Kecamatan</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Desa/Kelurahan</label>
                        <select name="village_id" id="village_id" class="form-select @error('village_id') is-invalid @enderror" 
                            {{ $pendaftaranMahasiswa->village ? '' : 'disabled' }}>
                            <option value="">Pilih Desa/Kelurahan</option>
                        </select>
                        @error('village_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Kode Pos</label>
                        <input type="text" name="kode_pos" class="form-control @error('kode_pos') is-invalid @enderror" 
                               value="{{ old('kode_pos', $pendaftaranMahasiswa->kode_pos) }}" maxlength="10">
                        @error('kode_pos')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-5">
                        <label class="form-label">No. Telepon</label>
                        <input type="text" name="telepon" class="form-control @error('telepon') is-invalid @enderror" 
                               value="{{ old('telepon', $pendaftaranMahasiswa->telepon) }}">
                        @error('telepon')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                               value="{{ old('email', $pendaftaranMahasiswa->email) }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Pendidikan Terakhir -->
        <div class="card mb-4">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0"><i class="bi bi-book"></i> Pendidikan Terakhir</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Asal Sekolah</label>
                        <input type="text" name="asal_sekolah" class="form-control @error('asal_sekolah') is-invalid @enderror" 
                               value="{{ old('asal_sekolah', $pendaftaranMahasiswa->asal_sekolah) }}">
                        @error('asal_sekolah')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Jurusan</label>
                        <input type="text" name="jurusan_sekolah" class="form-control @error('jurusan_sekolah') is-invalid @enderror" 
                               value="{{ old('jurusan_sekolah', $pendaftaranMahasiswa->jurusan_sekolah) }}">
                        @error('jurusan_sekolah')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Tahun Lulus</label>
                        <input type="text" name="tahun_lulus" class="form-control @error('tahun_lulus') is-invalid @enderror" 
                               value="{{ old('tahun_lulus', $pendaftaranMahasiswa->tahun_lulus) }}" maxlength="4">
                        @error('tahun_lulus')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-1">
                        <label class="form-label">Nilai</label>
                        <input type="number" step="0.01" name="nilai_rata_rata" class="form-control @error('nilai_rata_rata') is-invalid @enderror" 
                               value="{{ old('nilai_rata_rata', $pendaftaranMahasiswa->nilai_rata_rata) }}">
                        @error('nilai_rata_rata')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Orang Tua/Wali -->
        <div class="card mb-4">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0"><i class="bi bi-people"></i> Data Orang Tua / Wali</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Nama Ayah</label>
                        <input type="text" name="nama_ayah" class="form-control @error('nama_ayah') is-invalid @enderror" 
                               value="{{ old('nama_ayah', $pendaftaranMahasiswa->nama_ayah) }}">
                        @error('nama_ayah')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Pekerjaan Ayah</label>
                        <input type="text" name="pekerjaan_ayah" class="form-control @error('pekerjaan_ayah') is-invalid @enderror" 
                               value="{{ old('pekerjaan_ayah', $pendaftaranMahasiswa->pekerjaan_ayah) }}">
                        @error('pekerjaan_ayah')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Nama Ibu</label>
                        <input type="text" name="nama_ibu" class="form-control @error('nama_ibu') is-invalid @enderror" 
                               value="{{ old('nama_ibu', $pendaftaranMahasiswa->nama_ibu) }}">
                        @error('nama_ibu')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Pekerjaan Ibu</label>
                        <input type="text" name="pekerjaan_ibu" class="form-control @error('pekerjaan_ibu') is-invalid @enderror" 
                               value="{{ old('pekerjaan_ibu', $pendaftaranMahasiswa->pekerjaan_ibu) }}">
                        @error('pekerjaan_ibu')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Nama Wali</label>
                        <input type="text" name="nama_wali" class="form-control @error('nama_wali') is-invalid @enderror" 
                               value="{{ old('nama_wali', $pendaftaranMahasiswa->nama_wali) }}">
                        @error('nama_wali')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Telepon Wali</label>
                        <input type="text" name="telepon_wali" class="form-control @error('telepon_wali') is-invalid @enderror" 
                               value="{{ old('telepon_wali', $pendaftaranMahasiswa->telepon_wali) }}">
                        @error('telepon_wali')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Alamat Wali</label>
                        <input type="text" name="alamat_wali" class="form-control @error('alamat_wali') is-invalid @enderror" 
                               value="{{ old('alamat_wali', $pendaftaranMahasiswa->alamat_wali) }}">
                        @error('alamat_wali')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Status & Verifikasi -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-check-circle"></i> Status & Verifikasi</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select @error('status') is-invalid @enderror">
                            <option value="Pending" {{ old('status', $pendaftaranMahasiswa->status) == 'Pending' ? 'selected' : '' }}>Pending</option>
                            <option value="Diverifikasi" {{ old('status', $pendaftaranMahasiswa->status) == 'Diverifikasi' ? 'selected' : '' }}>Diverifikasi</option>
                            <option value="Diterima" {{ old('status', $pendaftaranMahasiswa->status) == 'Diterima' ? 'selected' : '' }}>Diterima</option>
                            <option value="Ditolak" {{ old('status', $pendaftaranMahasiswa->status) == 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Catatan</label>
                        <textarea name="catatan" class="form-control @error('catatan') is-invalid @enderror" rows="3">{{ old('catatan', $pendaftaranMahasiswa->catatan) }}</textarea>
                        @error('catatan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Lampiran -->
        <div class="card mb-4">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0"><i class="bi bi-paperclip"></i> Lampiran Dokumen</h5>
            </div>
            <div class="card-body">
                <x-file-upload 
                    :existingFiles="$pendaftaranMahasiswa->files"
                    label="Upload Dokumen (KTP, Ijazah, Foto, dll)"
                />
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="bi bi-save"></i> Update Data
                </button>
                <a href="{{ route('pendaftaran-mahasiswa.show', $pendaftaranMahasiswa) }}" class="btn btn-secondary btn-lg">
                    <i class="bi bi-x-circle"></i> Batal
                </a>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    let initialProvinceId = {{ $pendaftaranMahasiswa->village ? $pendaftaranMahasiswa->village->subRegency->regency->province_id : 'null' }};
    let initialRegencyId = {{ $pendaftaranMahasiswa->village ? $pendaftaranMahasiswa->village->subRegency->regency_id : 'null' }};
    let initialSubRegencyId = {{ $pendaftaranMahasiswa->village ? $pendaftaranMahasiswa->village->sub_regency_id : 'null' }};
    let initialVillageId = {{ $pendaftaranMahasiswa->village_id ?? 'null' }};

    // Load initial data if exists
    if (initialProvinceId) {
        loadRegencies(initialProvinceId, initialRegencyId);
    }

    function loadRegencies(provinceId, selectedRegency = null) {
        $.ajax({
            url: '/api/regencies/' + provinceId,
            type: 'GET',
            success: function(data) {
                var options = '<option value="">Pilih Kabupaten/Kota</option>';
                $.each(data, function(key, regency) {
                    var selected = selectedRegency && regency.id == selectedRegency ? 'selected' : '';
                    options += '<option value="' + regency.id + '" ' + selected + '>' + regency.name + '</option>';
                });
                $('#regency_id').html(options).prop('disabled', false);
                
                if (selectedRegency) {
                    loadSubRegencies(selectedRegency, initialSubRegencyId);
                }
            }
        });
    }

    function loadSubRegencies(regencyId, selectedSubRegency = null) {
        $.ajax({
            url: '/api/sub-regencies/' + regencyId,
            type: 'GET',
            success: function(data) {
                var options = '<option value="">Pilih Kecamatan</option>';
                $.each(data, function(key, subRegency) {
                    var selected = selectedSubRegency && subRegency.id == selectedSubRegency ? 'selected' : '';
                    options += '<option value="' + subRegency.id + '" ' + selected + '>' + subRegency.name + '</option>';
                });
                $('#sub_regency_id').html(options).prop('disabled', false);
                
                if (selectedSubRegency) {
                    loadVillages(selectedSubRegency, initialVillageId);
                }
            }
        });
    }

    function loadVillages(subRegencyId, selectedVillage = null) {
        $.ajax({
            url: '/api/villages/' + subRegencyId,
            type: 'GET',
            success: function(data) {
                var options = '<option value="">Pilih Desa/Kelurahan</option>';
                $.each(data, function(key, village) {
                    var selected = selectedVillage && village.id == selectedVillage ? 'selected' : '';
                    options += '<option value="' + village.id + '" ' + selected + '>' + village.name + '</option>';
                });
                $('#village_id').html(options).prop('disabled', false);
            }
        });
    }

    // Cascade Region
    $('#province_id').on('change', function() {
        var provinceId = $(this).val();
        $('#regency_id').html('<option value="">Loading...</option>').prop('disabled', true);
        $('#sub_regency_id').html('<option value="">Pilih Kecamatan</option>').prop('disabled', true);
        $('#village_id').html('<option value="">Pilih Desa/Kelurahan</option>').prop('disabled', true);
        
        if (provinceId) {
            loadRegencies(provinceId);
        }
    });

    $('#regency_id').on('change', function() {
        var regencyId = $(this).val();
        $('#sub_regency_id').html('<option value="">Loading...</option>').prop('disabled', true);
        $('#village_id').html('<option value="">Pilih Desa/Kelurahan</option>').prop('disabled', true);
        
        if (regencyId) {
            loadSubRegencies(regencyId);
        }
    });

    $('#sub_regency_id').on('change', function() {
        var subRegencyId = $(this).val();
        $('#village_id').html('<option value="">Loading...</option>').prop('disabled', true);
        
        if (subRegencyId) {
            loadVillages(subRegencyId);
        }
    });

    // Form submit - enable all disabled fields
    $('#formPendaftaran').on('submit', function() {
        $('#province_id, #regency_id, #sub_regency_id, #village_id').prop('disabled', false);
    });
});
</script>
@endpush
