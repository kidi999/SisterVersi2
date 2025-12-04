@extends('layouts.app')

@section('title', 'Tambah Akreditasi Program Studi')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Tambah Akreditasi Program Studi</h1>
        <a href="{{ route('akreditasi-program-studi.index') }}" class="btn btn-secondary btn-icon-split">
            <span class="icon text-white-50">
                <i class="fas fa-arrow-left"></i>
            </span>
            <span class="text">Kembali</span>
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Form Tambah Akreditasi Program Studi</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('akreditasi-program-studi.store') }}" method="POST" id="akreditasiForm">
                @csrf

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="fakultas_id">Fakultas <span class="text-danger">*</span></label>
                            <select class="form-control @error('fakultas_id') is-invalid @enderror" 
                                    id="fakultas_id" name="fakultas_id">
                                <option value="">-- Pilih Fakultas --</option>
                                @foreach($fakultasList as $fak)
                                    <option value="{{ $fak->id }}" {{ old('fakultas_id') == $fak->id ? 'selected' : '' }}>
                                        {{ $fak->nama_fakultas }}
                                    </option>
                                @endforeach
                            </select>
                            @error('fakultas_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="program_studi_id">Program Studi <span class="text-danger">*</span></label>
                            <select class="form-control @error('program_studi_id') is-invalid @enderror" 
                                    id="program_studi_id" name="program_studi_id" required>
                                <option value="">-- Pilih Program Studi --</option>
                                @foreach($prodiList as $prodi)
                                    <option value="{{ $prodi->id }}" data-fakultas="{{ $prodi->fakultas_id }}" {{ old('program_studi_id') == $prodi->id ? 'selected' : '' }}>
                                        {{ $prodi->nama_prodi }} ({{ $prodi->jenjang }})
                                    </option>
                                @endforeach
                            </select>
                            @error('program_studi_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="lembaga_akreditasi">Lembaga Akreditasi <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('lembaga_akreditasi') is-invalid @enderror" 
                                   id="lembaga_akreditasi" name="lembaga_akreditasi" value="{{ old('lembaga_akreditasi') }}" 
                                   placeholder="Contoh: BAN-PT, LAMDIK, dll" required>
                            @error('lembaga_akreditasi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="nomor_sk">Nomor SK <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nomor_sk') is-invalid @enderror" 
                                   id="nomor_sk" name="nomor_sk" value="{{ old('nomor_sk') }}" required>
                            @error('nomor_sk')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="tanggal_sk">Tanggal SK <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('tanggal_sk') is-invalid @enderror" 
                                   id="tanggal_sk" name="tanggal_sk" value="{{ old('tanggal_sk') }}" required>
                            @error('tanggal_sk')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="tanggal_berakhir">Tanggal Berakhir</label>
                            <input type="date" class="form-control @error('tanggal_berakhir') is-invalid @enderror" 
                                   id="tanggal_berakhir" name="tanggal_berakhir" value="{{ old('tanggal_berakhir') }}">
                            @error('tanggal_berakhir')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="peringkat">Peringkat <span class="text-danger">*</span></label>
                            <select class="form-control @error('peringkat') is-invalid @enderror" 
                                    id="peringkat" name="peringkat" required>
                                <option value="">-- Pilih Peringkat --</option>
                                <option value="Unggul" {{ old('peringkat') == 'Unggul' ? 'selected' : '' }}>Unggul</option>
                                <option value="A" {{ old('peringkat') == 'A' ? 'selected' : '' }}>A</option>
                                <option value="Baik Sekali" {{ old('peringkat') == 'Baik Sekali' ? 'selected' : '' }}>Baik Sekali</option>
                                <option value="B" {{ old('peringkat') == 'B' ? 'selected' : '' }}>B</option>
                                <option value="Baik" {{ old('peringkat') == 'Baik' ? 'selected' : '' }}>Baik</option>
                                <option value="C" {{ old('peringkat') == 'C' ? 'selected' : '' }}>C</option>
                                <option value="Belum Terakreditasi" {{ old('peringkat') == 'Belum Terakreditasi' ? 'selected' : '' }}>Belum Terakreditasi</option>
                            </select>
                            @error('peringkat')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="tahun_akreditasi">Tahun <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('tahun_akreditasi') is-invalid @enderror" 
                                   id="tahun_akreditasi" name="tahun_akreditasi" value="{{ old('tahun_akreditasi', date('Y')) }}" 
                                   min="1900" max="{{ date('Y') + 1 }}" required>
                            @error('tahun_akreditasi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="status">Status <span class="text-danger">*</span></label>
                            <select class="form-control @error('status') is-invalid @enderror" 
                                    id="status" name="status" required>
                                <option value="">-- Pilih Status --</option>
                                <option value="Aktif" {{ old('status', 'Aktif') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="Kadaluarsa" {{ old('status') == 'Kadaluarsa' ? 'selected' : '' }}>Kadaluarsa</option>
                                <option value="Dalam Proses" {{ old('status') == 'Dalam Proses' ? 'selected' : '' }}>Dalam Proses</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="catatan">Catatan</label>
                            <textarea class="form-control @error('catatan') is-invalid @enderror" 
                                      id="catatan" name="catatan" rows="3">{{ old('catatan') }}</textarea>
                            @error('catatan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <!-- File Upload Component -->
                @include('components.file-upload', [
                    'fileableType' => 'App\\Models\\AkreditasiProgramStudi',
                    'fileableId' => 0
                ])

                <hr class="my-4">

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                    <a href="{{ route('akreditasi-program-studi.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Filter program studi based on fakultas
    $('#fakultas_id').change(function() {
        var fakultasId = $(this).val();
        var prodiSelect = $('#program_studi_id');
        
        prodiSelect.find('option').each(function() {
            if ($(this).val() === '') {
                $(this).show();
                return;
            }
            
            if (fakultasId === '' || $(this).data('fakultas') == fakultasId) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
        
        // Reset selection if current selected prodi is not in the filtered list
        if (prodiSelect.find('option:selected').is(':hidden')) {
            prodiSelect.val('');
        }
    });
});
</script>
@endpush
@endsection
