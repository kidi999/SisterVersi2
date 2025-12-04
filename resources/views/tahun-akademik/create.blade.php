@extends('layouts.app')

@section('title', 'Tambah Tahun Akademik')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Tambah Tahun Akademik</h1>
        <a href="{{ route('tahun-akademik.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('tahun-akademik.store') }}" method="POST" id="tahunAkademikForm">
                        @csrf

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="kode" class="form-label">
                                    Kode <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control @error('kode') is-invalid @enderror" 
                                       id="kode" 
                                       name="kode" 
                                       value="{{ old('kode') }}"
                                       placeholder="Contoh: 2024/2025"
                                       required>
                                @error('kode')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Format: YYYY/YYYY</small>
                            </div>

                            <div class="col-md-6">
                                <label for="nama" class="form-label">
                                    Nama Tahun Akademik <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control @error('nama') is-invalid @enderror" 
                                       id="nama" 
                                       name="nama" 
                                       value="{{ old('nama') }}"
                                       placeholder="Contoh: Tahun Akademik 2024/2025"
                                       required>
                                @error('nama')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="tahun_mulai" class="form-label">
                                    Tahun Mulai <span class="text-danger">*</span>
                                </label>
                                <input type="number" 
                                       class="form-control @error('tahun_mulai') is-invalid @enderror" 
                                       id="tahun_mulai" 
                                       name="tahun_mulai" 
                                       value="{{ old('tahun_mulai', date('Y')) }}"
                                       min="2000"
                                       max="2100"
                                       required>
                                @error('tahun_mulai')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="tahun_selesai" class="form-label">
                                    Tahun Selesai <span class="text-danger">*</span>
                                </label>
                                <input type="number" 
                                       class="form-control @error('tahun_selesai') is-invalid @enderror" 
                                       id="tahun_selesai" 
                                       name="tahun_selesai" 
                                       value="{{ old('tahun_selesai', date('Y') + 1) }}"
                                       min="2000"
                                       max="2100"
                                       required>
                                @error('tahun_selesai')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="tanggal_mulai" class="form-label">
                                    Tanggal Mulai <span class="text-danger">*</span>
                                </label>
                                <input type="date" 
                                       class="form-control @error('tanggal_mulai') is-invalid @enderror" 
                                       id="tanggal_mulai" 
                                       name="tanggal_mulai" 
                                       value="{{ old('tanggal_mulai') }}"
                                       required>
                                @error('tanggal_mulai')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="tanggal_selesai" class="form-label">
                                    Tanggal Selesai <span class="text-danger">*</span>
                                </label>
                                <input type="date" 
                                       class="form-control @error('tanggal_selesai') is-invalid @enderror" 
                                       id="tanggal_selesai" 
                                       name="tanggal_selesai" 
                                       value="{{ old('tanggal_selesai') }}"
                                       required>
                                @error('tanggal_selesai')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="keterangan" class="form-label">Keterangan</label>
                            <textarea class="form-control @error('keterangan') is-invalid @enderror" 
                                      id="keterangan" 
                                      name="keterangan" 
                                      rows="3"
                                      placeholder="Keterangan tambahan (opsional)">{{ old('keterangan') }}</textarea>
                            @error('keterangan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> 
                            <strong>Catatan:</strong> Tahun akademik baru akan dibuat dengan status nonaktif. 
                            Aktifkan melalui menu daftar tahun akademik.
                        </div>

                        <!-- File Upload Component -->
                        @include('components.file-upload', [
                            'fileableType' => 'App\\Models\\TahunAkademik',
                            'fileableId' => 0
                        ])
                    </form>

                    <div class="d-flex gap-2 mt-3">
                        <button type="submit" class="btn btn-primary" form="tahunAkademikForm">
                            <i class="bi bi-save"></i> Simpan
                        </button>
                        <a href="{{ route('tahun-akademik.index') }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i> Batal
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <i class="bi bi-lightbulb"></i> Panduan Pengisian
                </div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <strong>Kode:</strong> Identitas unik tahun akademik, format YYYY/YYYY
                        </li>
                        <li class="mb-2">
                            <strong>Nama:</strong> Nama lengkap tahun akademik
                        </li>
                        <li class="mb-2">
                            <strong>Tahun Mulai/Selesai:</strong> Periode tahun akademik dalam angka
                        </li>
                        <li class="mb-2">
                            <strong>Tanggal Mulai/Selesai:</strong> Tanggal efektif berlaku
                        </li>
                        <li class="mb-2">
                            <strong>Status:</strong> Tahun akademik baru akan dibuat dengan status nonaktif
                        </li>
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
    // Auto generate kode based on tahun_mulai and tahun_selesai
    $('#tahun_mulai, #tahun_selesai').on('change', function() {
        const tahunMulai = $('#tahun_mulai').val();
        const tahunSelesai = $('#tahun_selesai').val();
        
        if (tahunMulai && tahunSelesai) {
            $('#kode').val(tahunMulai + '/' + tahunSelesai);
            
            if (!$('#nama').val()) {
                $('#nama').val('Tahun Akademik ' + tahunMulai + '/' + tahunSelesai);
            }
        }
    });

    // Validate tahun_selesai must be greater than tahun_mulai
    $('#tahun_selesai').on('change', function() {
        const tahunMulai = parseInt($('#tahun_mulai').val());
        const tahunSelesai = parseInt($(this).val());
        
        if (tahunSelesai <= tahunMulai) {
            alert('Tahun selesai harus lebih besar dari tahun mulai');
            $(this).val(tahunMulai + 1);
        }
    });

    // Validate tanggal_selesai must be after tanggal_mulai
    $('#tanggal_selesai').on('change', function() {
        const tanggalMulai = new Date($('#tanggal_mulai').val());
        const tanggalSelesai = new Date($(this).val());
        
        if (tanggalSelesai <= tanggalMulai) {
            alert('Tanggal selesai harus lebih besar dari tanggal mulai');
            $(this).val('');
        }
    });
});
</script>
@endpush
