@extends('layouts.pmb')

@section('title', 'Form Pendaftaran - PMB')

@section('content')
<div class="container">
    <div class="hero-section">
        <h1><i class="bi bi-pencil-square"></i> Form Pendaftaran</h1>
        <p>Lengkapi formulir di bawah dengan data yang benar dan lengkap</p>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <strong><i class="bi bi-exclamation-triangle"></i> Terjadi Kesalahan!</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-triangle"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('pmb.store') }}" method="POST" id="formPendaftaran">
        @csrf
        <input type="hidden" name="tahun_akademik" value="{{ $tahunAkademik }}">

        <!-- Pilihan Program Studi -->
        <div class="card-pmb mb-4">
            <div class="card-header-pmb">
                <h5 class="mb-0"><i class="bi bi-building"></i> Pilihan Program Studi</h5>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Jalur Pendaftaran <span class="text-danger">*</span></label>
                        <select name="jalur_masuk" class="form-select @error('jalur_masuk') is-invalid @enderror" required>
                            <option value="">Pilih Jalur Pendaftaran</option>
                            <option value="SNBP" {{ old('jalur_masuk') == 'SNBP' ? 'selected' : '' }}>SNBP (Seleksi Nasional Berdasarkan Prestasi)</option>
                            <option value="SNBT" {{ old('jalur_masuk') == 'SNBT' ? 'selected' : '' }}>SNBT (Seleksi Nasional Berdasarkan Tes)</option>
                            <option value="Mandiri" {{ old('jalur_masuk') == 'Mandiri' ? 'selected' : '' }}>Jalur Mandiri</option>
                            <option value="Transfer" {{ old('jalur_masuk') == 'Transfer' ? 'selected' : '' }}>Transfer/Pindahan</option>
                        </select>
                        @error('jalur_masuk')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Program Studi <span class="text-danger">*</span></label>
                        <select name="program_studi_id" class="form-select @error('program_studi_id') is-invalid @enderror" required>
                            <option value="">Pilih Program Studi</option>
                            @foreach($programStudi as $prodi)
                                <option value="{{ $prodi->id }}" {{ old('program_studi_id') == $prodi->id ? 'selected' : '' }}>
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
        <div class="card-pmb mb-4">
            <div class="card-header-pmb">
                <h5 class="mb-0"><i class="bi bi-person"></i> Data Pribadi</h5>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="nama_lengkap" class="form-control @error('nama_lengkap') is-invalid @enderror" 
                               value="{{ old('nama_lengkap') }}" placeholder="Sesuai ijazah" required>
                        @error('nama_lengkap')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">NIK</label>
                        <input type="text" name="nik" class="form-control @error('nik') is-invalid @enderror" 
                               value="{{ old('nik') }}" placeholder="16 digit" maxlength="16">
                        @error('nik')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                        <select name="jenis_kelamin" class="form-select @error('jenis_kelamin') is-invalid @enderror" required>
                            <option value="">Pilih</option>
                            <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                        @error('jenis_kelamin')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-5">
                        <label class="form-label">Tempat Lahir</label>
                        <input type="text" name="tempat_lahir" class="form-control @error('tempat_lahir') is-invalid @enderror" 
                               value="{{ old('tempat_lahir') }}" placeholder="Kota kelahiran">
                        @error('tempat_lahir')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Tanggal Lahir</label>
                        <input type="date" name="tanggal_lahir" class="form-control @error('tanggal_lahir') is-invalid @enderror" 
                               value="{{ old('tanggal_lahir') }}">
                        @error('tanggal_lahir')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Agama</label>
                        <select name="agama" class="form-select @error('agama') is-invalid @enderror">
                            <option value="">Pilih</option>
                            <option value="Islam" {{ old('agama') == 'Islam' ? 'selected' : '' }}>Islam</option>
                            <option value="Kristen" {{ old('agama') == 'Kristen' ? 'selected' : '' }}>Kristen</option>
                            <option value="Katolik" {{ old('agama') == 'Katolik' ? 'selected' : '' }}>Katolik</option>
                            <option value="Hindu" {{ old('agama') == 'Hindu' ? 'selected' : '' }}>Hindu</option>
                            <option value="Buddha" {{ old('agama') == 'Buddha' ? 'selected' : '' }}>Buddha</option>
                            <option value="Konghucu" {{ old('agama') == 'Konghucu' ? 'selected' : '' }}>Konghucu</option>
                        </select>
                        @error('agama')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Status Perkawinan</label>
                        <select name="status_perkawinan" class="form-select @error('status_perkawinan') is-invalid @enderror">
                            <option value="Belum Kawin" {{ old('status_perkawinan') == 'Belum Kawin' ? 'selected' : '' }}>Belum Kawin</option>
                            <option value="Kawin" {{ old('status_perkawinan') == 'Kawin' ? 'selected' : '' }}>Kawin</option>
                            <option value="Cerai" {{ old('status_perkawinan') == 'Cerai' ? 'selected' : '' }}>Cerai</option>
                        </select>
                        @error('status_perkawinan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Kewarganegaraan</label>
                        <input type="text" name="kewarganegaraan" class="form-control @error('kewarganegaraan') is-invalid @enderror" 
                               value="{{ old('kewarganegaraan', 'Indonesia') }}">
                        @error('kewarganegaraan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Alamat & Kontak -->
        <div class="card-pmb mb-4">
            <div class="card-header-pmb">
                <h5 class="mb-0"><i class="bi bi-geo-alt"></i> Alamat & Kontak</h5>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label">Alamat Lengkap</label>
                        <textarea name="alamat" class="form-control @error('alamat') is-invalid @enderror" 
                                  rows="2" placeholder="Jalan, RT/RW, Kelurahan">{{ old('alamat') }}</textarea>
                        @error('alamat')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Provinsi</label>
                        <select id="province_id" class="form-select">
                            <option value="">Pilih Provinsi</option>
                            @foreach($provinces as $province)
                                <option value="{{ $province->id }}">{{ $province->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Kabupaten/Kota</label>
                        <select id="regency_id" class="form-select" disabled>
                            <option value="">Pilih Kab/Kota</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Kecamatan</label>
                        <select id="sub_regency_id" class="form-select" disabled>
                            <option value="">Pilih Kecamatan</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Desa/Kelurahan</label>
                        <select name="village_id" id="village_id" class="form-select @error('village_id') is-invalid @enderror" disabled>
                            <option value="">Pilih Desa/Kelurahan</option>
                        </select>
                        @error('village_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Kode Pos</label>
                        <input type="text" name="kode_pos" class="form-control @error('kode_pos') is-invalid @enderror" 
                               value="{{ old('kode_pos') }}" maxlength="10">
                        @error('kode_pos')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-5">
                        <label class="form-label">No. Telepon/HP</label>
                        <input type="text" name="telepon" class="form-control @error('telepon') is-invalid @enderror" 
                               value="{{ old('telepon') }}" placeholder="08xx-xxxx-xxxx">
                        @error('telepon')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                               value="{{ old('email') }}" placeholder="email@example.com" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Email akan digunakan untuk cek status pendaftaran</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pendidikan Terakhir -->
        <div class="card-pmb mb-4">
            <div class="card-header-pmb">
                <h5 class="mb-0"><i class="bi bi-book"></i> Pendidikan Terakhir</h5>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-7">
                        <label class="form-label">Asal Sekolah/Universitas</label>
                        <input type="text" name="asal_sekolah" class="form-control @error('asal_sekolah') is-invalid @enderror" 
                               value="{{ old('asal_sekolah') }}" placeholder="Nama sekolah/universitas">
                        @error('asal_sekolah')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Tahun Lulus</label>
                        <input type="text" name="tahun_lulus" class="form-control @error('tahun_lulus') is-invalid @enderror" 
                               value="{{ old('tahun_lulus') }}" placeholder="2025" maxlength="4">
                        @error('tahun_lulus')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Nilai Rata-rata</label>
                        <input type="number" step="0.01" name="nilai_rata_rata" class="form-control @error('nilai_rata_rata') is-invalid @enderror" 
                               value="{{ old('nilai_rata_rata') }}" placeholder="85.50">
                        @error('nilai_rata_rata')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Jurusan</label>
                        <input type="text" name="jurusan_sekolah" class="form-control @error('jurusan_sekolah') is-invalid @enderror" 
                               value="{{ old('jurusan_sekolah') }}" placeholder="IPA / IPS / dll">
                        @error('jurusan_sekolah')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Orang Tua/Wali -->
        <div class="card-pmb mb-4">
            <div class="card-header-pmb">
                <h5 class="mb-0"><i class="bi bi-people"></i> Data Orang Tua / Wali</h5>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Nama Ayah</label>
                        <input type="text" name="nama_ayah" class="form-control @error('nama_ayah') is-invalid @enderror" 
                               value="{{ old('nama_ayah') }}">
                        @error('nama_ayah')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Pekerjaan Ayah</label>
                        <input type="text" name="pekerjaan_ayah" class="form-control @error('pekerjaan_ayah') is-invalid @enderror" 
                               value="{{ old('pekerjaan_ayah') }}">
                        @error('pekerjaan_ayah')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Nama Ibu</label>
                        <input type="text" name="nama_ibu" class="form-control @error('nama_ibu') is-invalid @enderror" 
                               value="{{ old('nama_ibu') }}">
                        @error('nama_ibu')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Pekerjaan Ibu</label>
                        <input type="text" name="pekerjaan_ibu" class="form-control @error('pekerjaan_ibu') is-invalid @enderror" 
                               value="{{ old('pekerjaan_ibu') }}">
                        @error('pekerjaan_ibu')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Nama Wali (jika ada)</label>
                        <input type="text" name="nama_wali" class="form-control @error('nama_wali') is-invalid @enderror" 
                               value="{{ old('nama_wali') }}">
                        @error('nama_wali')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Telepon Wali</label>
                        <input type="text" name="telepon_wali" class="form-control @error('telepon_wali') is-invalid @enderror" 
                               value="{{ old('telepon_wali') }}">
                        @error('telepon_wali')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label">Alamat Wali</label>
                        <textarea name="alamat_wali" class="form-control @error('alamat_wali') is-invalid @enderror" rows="2">{{ old('alamat_wali') }}</textarea>
                        @error('alamat_wali')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Lampiran Dokumen -->
        <div class="card-pmb mb-4">
            <div class="card-header-pmb">
                <h5 class="mb-0"><i class="bi bi-paperclip"></i> Lampiran Dokumen</h5>
            </div>
            <div class="card-body p-4">
                <p class="text-muted mb-3">Upload dokumen persyaratan (KTP, Ijazah, Foto, Rapor, dll). Maks 2MB per file.</p>
                <x-file-upload 
                    :existingFiles="[]"
                    label="Upload Dokumen Persyaratan"
                />
            </div>
        </div>

        <!-- Submit -->
        <div class="card-pmb mb-5">
            <div class="card-body p-4 text-center">
                <div class="form-check d-inline-block text-start mb-3">
                    <input class="form-check-input" type="checkbox" id="agree" required>
                    <label class="form-check-label" for="agree">
                        Saya menyatakan bahwa data yang saya isi adalah benar dan dapat dipertanggungjawabkan.
                    </label>
                </div>
                <div>
                    <button type="submit" class="btn btn-primary btn-lg btn-pmb me-2">
                        <i class="bi bi-send"></i> Kirim Pendaftaran
                    </button>
                    <a href="{{ route('pmb.index') }}" class="btn btn-secondary btn-lg">
                        <i class="bi bi-x-circle"></i> Batal
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Cascade Region
    $('#province_id').on('change', function() {
        var provinceId = $(this).val();
        $('#regency_id').html('<option value="">Loading...</option>').prop('disabled', true);
        $('#sub_regency_id').html('<option value="">Pilih Kecamatan</option>').prop('disabled', true);
        $('#village_id').html('<option value="">Pilih Desa/Kelurahan</option>').prop('disabled', true);
        
        if (provinceId) {
            $.ajax({
                url: '/api/regencies/' + provinceId,
                type: 'GET',
                success: function(data) {
                    var options = '<option value="">Pilih Kabupaten/Kota</option>';
                    $.each(data, function(key, regency) {
                        options += '<option value="' + regency.id + '">' + regency.name + '</option>';
                    });
                    $('#regency_id').html(options).prop('disabled', false);
                }
            });
        }
    });

    $('#regency_id').on('change', function() {
        var regencyId = $(this).val();
        $('#sub_regency_id').html('<option value="">Loading...</option>').prop('disabled', true);
        $('#village_id').html('<option value="">Pilih Desa/Kelurahan</option>').prop('disabled', true);
        
        if (regencyId) {
            $.ajax({
                url: '/api/sub-regencies/' + regencyId,
                type: 'GET',
                success: function(data) {
                    var options = '<option value="">Pilih Kecamatan</option>';
                    $.each(data, function(key, subRegency) {
                        options += '<option value="' + subRegency.id + '">' + subRegency.name + '</option>';
                    });
                    $('#sub_regency_id').html(options).prop('disabled', false);
                }
            });
        }
    });

    $('#sub_regency_id').on('change', function() {
        var subRegencyId = $(this).val();
        $('#village_id').html('<option value="">Loading...</option>').prop('disabled', true);
        
        if (subRegencyId) {
            $.ajax({
                url: '/api/villages/' + subRegencyId,
                type: 'GET',
                success: function(data) {
                    var options = '<option value="">Pilih Desa/Kelurahan</option>';
                    $.each(data, function(key, village) {
                        options += '<option value="' + village.id + '">' + village.name + '</option>';
                    });
                    $('#village_id').html(options).prop('disabled', false);
                }
            });
        }
    });

    // Form submit
    $('#formPendaftaran').on('submit', function() {
        $('#province_id, #regency_id, #sub_regency_id, #village_id').prop('disabled', false);
    });
});
</script>
@endpush
