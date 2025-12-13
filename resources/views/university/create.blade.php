@extends('layouts.app')

@section('title', 'Tambah Universitas')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Tambah Universitas</h1>
        <a href="{{ route('universities.index') }}" class="btn btn-secondary btn-icon-split">
            <span class="icon text-white-50">
                <i class="fas fa-arrow-left"></i>
            </span>
            <span class="text">Kembali</span>
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Form Tambah Universitas</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('universities.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- Identitas Universitas -->
                <h5 class="text-primary mb-3">Identitas Universitas</h5>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="kode">Kode <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('kode') is-invalid @enderror" 
                                   id="kode" name="kode" value="{{ old('kode') }}" required>
                            @error('kode')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="jenis">Jenis <span class="text-danger">*</span></label>
                            <select class="form-control @error('jenis') is-invalid @enderror" 
                                    id="jenis" name="jenis" required>
                                <option value="">-- Pilih Jenis --</option>
                                <option value="Negeri" {{ old('jenis') == 'Negeri' ? 'selected' : '' }}>Negeri</option>
                                <option value="Swasta" {{ old('jenis') == 'Swasta' ? 'selected' : '' }}>Swasta</option>
                            </select>
                            @error('jenis')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="status">Status <span class="text-danger">*</span></label>
                            <select class="form-control @error('status') is-invalid @enderror" 
                                    id="status" name="status" required>
                                <option value="">-- Pilih Status --</option>
                                <option value="Aktif" {{ old('status') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="Nonaktif" {{ old('status') == 'Nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="nama">Nama Universitas <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nama') is-invalid @enderror" 
                                   id="nama" name="nama" value="{{ old('nama') }}" required>
                            @error('nama')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="singkatan">Singkatan</label>
                            <input type="text" class="form-control @error('singkatan') is-invalid @enderror" 
                                   id="singkatan" name="singkatan" value="{{ old('singkatan') }}">
                            @error('singkatan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="logo">Logo Universitas</label>
                            <input type="file" class="form-control-file @error('logo') is-invalid @enderror" 
                                   id="logo" name="logo" accept="image/jpeg,image/png,image/jpg">
                            <small class="form-text text-muted">Format: JPG, PNG. Max: 2MB</small>
                            @error('logo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <!-- Akreditasi -->
                <h5 class="text-primary mb-3">Akreditasi</h5>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="akreditasi">Akreditasi</label>
                            <select class="form-control @error('akreditasi') is-invalid @enderror" 
                                    id="akreditasi" name="akreditasi">
                                <option value="">-- Pilih Akreditasi --</option>
                                <option value="Unggul" {{ old('akreditasi') == 'Unggul' ? 'selected' : '' }}>Unggul</option>
                                <option value="A" {{ old('akreditasi') == 'A' ? 'selected' : '' }}>A</option>
                                <option value="Baik Sekali" {{ old('akreditasi') == 'Baik Sekali' ? 'selected' : '' }}>Baik Sekali</option>
                                <option value="B" {{ old('akreditasi') == 'B' ? 'selected' : '' }}>B</option>
                                <option value="Baik" {{ old('akreditasi') == 'Baik' ? 'selected' : '' }}>Baik</option>
                                <option value="C" {{ old('akreditasi') == 'C' ? 'selected' : '' }}>C</option>
                                <option value="Belum Terakreditasi" {{ old('akreditasi') == 'Belum Terakreditasi' ? 'selected' : '' }}>Belum Terakreditasi</option>
                            </select>
                            @error('akreditasi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="no_sk_akreditasi">No. SK Akreditasi</label>
                            <input type="text" class="form-control @error('no_sk_akreditasi') is-invalid @enderror" 
                                   id="no_sk_akreditasi" name="no_sk_akreditasi" value="{{ old('no_sk_akreditasi') }}">
                            @error('no_sk_akreditasi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="tanggal_akreditasi">Tanggal Akreditasi</label>
                            <input type="date" class="form-control @error('tanggal_akreditasi') is-invalid @enderror" 
                                   id="tanggal_akreditasi" name="tanggal_akreditasi" value="{{ old('tanggal_akreditasi') }}">
                            @error('tanggal_akreditasi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="tanggal_berakhir_akreditasi">Tanggal Berakhir Akreditasi</label>
                            <input type="date" class="form-control @error('tanggal_berakhir_akreditasi') is-invalid @enderror" 
                                   id="tanggal_berakhir_akreditasi" name="tanggal_berakhir_akreditasi" value="{{ old('tanggal_berakhir_akreditasi') }}">
                            @error('tanggal_berakhir_akreditasi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <!-- Pendirian -->
                <h5 class="text-primary mb-3">Pendirian</h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="no_sk_pendirian">No. SK Pendirian</label>
                            <input type="text" class="form-control @error('no_sk_pendirian') is-invalid @enderror" 
                                   id="no_sk_pendirian" name="no_sk_pendirian" value="{{ old('no_sk_pendirian') }}">
                            @error('no_sk_pendirian')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="tanggal_pendirian">Tanggal Pendirian</label>
                            <input type="date" class="form-control @error('tanggal_pendirian') is-invalid @enderror" 
                                   id="tanggal_pendirian" name="tanggal_pendirian" value="{{ old('tanggal_pendirian') }}">
                            @error('tanggal_pendirian')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="no_izin_operasional">No. Izin Operasional</label>
                            <input type="text" class="form-control @error('no_izin_operasional') is-invalid @enderror" 
                                   id="no_izin_operasional" name="no_izin_operasional" value="{{ old('no_izin_operasional') }}">
                            @error('no_izin_operasional')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="tanggal_izin_operasional">Tanggal Izin Operasional</label>
                            <input type="date" class="form-control @error('tanggal_izin_operasional') is-invalid @enderror" 
                                   id="tanggal_izin_operasional" name="tanggal_izin_operasional" value="{{ old('tanggal_izin_operasional') }}">
                            @error('tanggal_izin_operasional')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <!-- Pimpinan -->
                <h5 class="text-primary mb-3">Pimpinan</h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="rektor">Rektor</label>
                            <input type="text" class="form-control @error('rektor') is-invalid @enderror" 
                                   id="rektor" name="rektor" value="{{ old('rektor') }}">
                            @error('rektor')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="nip_rektor">NIP Rektor</label>
                            <input type="text" class="form-control @error('nip_rektor') is-invalid @enderror" 
                                   id="nip_rektor" name="nip_rektor" value="{{ old('nip_rektor') }}">
                            @error('nip_rektor')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="wakil_rektor_1">Wakil Rektor I</label>
                            <input type="text" class="form-control @error('wakil_rektor_1') is-invalid @enderror" 
                                   id="wakil_rektor_1" name="wakil_rektor_1" value="{{ old('wakil_rektor_1') }}">
                            @error('wakil_rektor_1')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="wakil_rektor_2">Wakil Rektor II</label>
                            <input type="text" class="form-control @error('wakil_rektor_2') is-invalid @enderror" 
                                   id="wakil_rektor_2" name="wakil_rektor_2" value="{{ old('wakil_rektor_2') }}">
                            @error('wakil_rektor_2')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="wakil_rektor_3">Wakil Rektor III</label>
                            <input type="text" class="form-control @error('wakil_rektor_3') is-invalid @enderror" 
                                   id="wakil_rektor_3" name="wakil_rektor_3" value="{{ old('wakil_rektor_3') }}">
                            @error('wakil_rektor_3')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="wakil_rektor_4">Wakil Rektor IV</label>
                            <input type="text" class="form-control @error('wakil_rektor_4') is-invalid @enderror" 
                                   id="wakil_rektor_4" name="wakil_rektor_4" value="{{ old('wakil_rektor_4') }}">
                            @error('wakil_rektor_4')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <!-- Kontak -->
                <h5 class="text-primary mb-3">Kontak</h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" value="{{ old('email') }}">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="telepon">Telepon</label>
                            <input type="text" class="form-control @error('telepon') is-invalid @enderror" 
                                   id="telepon" name="telepon" value="{{ old('telepon') }}">
                            @error('telepon')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="fax">Fax</label>
                            <input type="text" class="form-control @error('fax') is-invalid @enderror" 
                                   id="fax" name="fax" value="{{ old('fax') }}">
                            @error('fax')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="website">Website</label>
                            <input type="url" class="form-control @error('website') is-invalid @enderror" 
                                   id="website" name="website" value="{{ old('website') }}">
                            @error('website')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <!-- Alamat -->
                <h5 class="text-primary mb-3">Alamat</h5>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="alamat">Alamat Lengkap</label>
                            <textarea class="form-control @error('alamat') is-invalid @enderror" 
                                      id="alamat" name="alamat" rows="3">{{ old('alamat') }}</textarea>
                            @error('alamat')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="province_id">Provinsi</label>
                            <select class="form-control @error('province_id') is-invalid @enderror" id="province_id">
                                <option value="">-- Pilih Provinsi --</option>
                                @foreach($provinces as $province)
                                    <option value="{{ $province->id }}">{{ $province->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="regency_id">Kabupaten/Kota</label>
                            <select class="form-control @error('regency_id') is-invalid @enderror" id="regency_id" disabled>
                                <option value="">-- Pilih Kab/Kota --</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="sub_regency_id">Kecamatan</label>
                            <select class="form-control @error('sub_regency_id') is-invalid @enderror" id="sub_regency_id" disabled>
                                <option value="">-- Pilih Kecamatan --</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="village_id">Desa/Kelurahan</label>
                            <select class="form-control @error('village_id') is-invalid @enderror" 
                                    id="village_id" name="village_id" disabled>
                                <option value="">-- Pilih Desa/Kelurahan --</option>
                            </select>
                            @error('village_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="kode_pos">Kode Pos</label>
                            <input type="text" class="form-control @error('kode_pos') is-invalid @enderror" 
                                   id="kode_pos" name="kode_pos" value="{{ old('kode_pos') }}">
                            @error('kode_pos')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <!-- Visi, Misi, Sejarah -->
                <h5 class="text-primary mb-3">Informasi Tambahan</h5>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="visi">Visi</label>
                            <textarea class="form-control @error('visi') is-invalid @enderror" 
                                      id="visi" name="visi" rows="3">{{ old('visi') }}</textarea>
                            @error('visi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="misi">Misi</label>
                            <textarea class="form-control @error('misi') is-invalid @enderror" 
                                      id="misi" name="misi" rows="4">{{ old('misi') }}</textarea>
                            @error('misi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="sejarah">Sejarah</label>
                            <textarea class="form-control @error('sejarah') is-invalid @enderror" 
                                      id="sejarah" name="sejarah" rows="4">{{ old('sejarah') }}</textarea>
                            @error('sejarah')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="keterangan">Keterangan</label>
                            <textarea class="form-control @error('keterangan') is-invalid @enderror" 
                                      id="keterangan" name="keterangan" rows="3">{{ old('keterangan') }}</textarea>
                            @error('keterangan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <!-- File Upload -->
                <h5 class="text-primary mb-3">Lampiran Dokumen</h5>
                <div class="row" id="fileUploadSection">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Upload Dokumen</label>
                            <input type="file" class="form-control-file" name="file_upload[]" multiple>
                            <small class="form-text text-muted">
                                Anda dapat mengupload beberapa file sekaligus (SK, Dokumen Akreditasi, dll)
                            </small>
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                    <a href="{{ route('universities.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Load regencies when province is selected
    $('#province_id').change(function() {
        var provinceId = $(this).val();
        $('#regency_id').prop('disabled', true).html('<option value="">-- Pilih Kab/Kota --</option>');
        $('#sub_regency_id').prop('disabled', true).html('<option value="">-- Pilih Kecamatan --</option>');
        $('#village_id').prop('disabled', true).html('<option value="">-- Pilih Desa/Kelurahan --</option>');
        
        if (provinceId) {
            $.ajax({
                url: '/api/regencies/' + provinceId,
                type: 'GET',
                success: function(data) {
                    $('#regency_id').prop('disabled', false);
                    $.each(data, function(key, regency) {
                        $('#regency_id').append('<option value="' + regency.id + '">' + regency.name + '</option>');
                    });
                }
            });
        }
    });

    // Load sub-regencies when regency is selected
    $('#regency_id').change(function() {
        var regencyId = $(this).val();
        $('#sub_regency_id').prop('disabled', true).html('<option value="">-- Pilih Kecamatan --</option>');
        $('#village_id').prop('disabled', true).html('<option value="">-- Pilih Desa/Kelurahan --</option>');
        
        if (regencyId) {
            $.ajax({
                url: '/api/sub-regencies/' + regencyId,
                type: 'GET',
                success: function(data) {
                    $('#sub_regency_id').prop('disabled', false);
                    $.each(data, function(key, subRegency) {
                        $('#sub_regency_id').append('<option value="' + subRegency.id + '">' + subRegency.name + '</option>');
                    });
                }
            });
        }
    });

    // Load villages when sub-regency is selected
    $('#sub_regency_id').change(function() {
        var subRegencyId = $(this).val();
        $('#village_id').prop('disabled', true).html('<option value="">-- Pilih Desa/Kelurahan --</option>');
        
        if (subRegencyId) {
            $.ajax({
                url: '/api/villages/' + subRegencyId,
                type: 'GET',
                success: function(data) {
                    $('#village_id').prop('disabled', false);
                    $.each(data, function(key, village) {
                        $('#village_id').append('<option value="' + village.id + '">' + village.name + '</option>');
                    });
                }
            });
        }
    });
});
</script>
@endpush
