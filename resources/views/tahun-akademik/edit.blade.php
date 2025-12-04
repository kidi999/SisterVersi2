@extends('layouts.app')

@section('title', 'Edit Tahun Akademik')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Edit Tahun Akademik</h1>
        <a href="{{ route('tahun-akademik.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('tahun-akademik.update', $tahunAkademik->id) }}" method="POST" id="tahunAkademikFormEdit">
                        @csrf
                        @method('PUT')

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="kode" class="form-label">
                                    Kode <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control @error('kode') is-invalid @enderror" 
                                       id="kode" 
                                       name="kode" 
                                       value="{{ old('kode', $tahunAkademik->kode) }}"
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
                                       value="{{ old('nama', $tahunAkademik->nama) }}"
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
                                       value="{{ old('tahun_mulai', $tahunAkademik->tahun_mulai) }}"
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
                                       value="{{ old('tahun_selesai', $tahunAkademik->tahun_selesai) }}"
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
                                       value="{{ old('tanggal_mulai', $tahunAkademik->tanggal_mulai ? \Carbon\Carbon::parse($tahunAkademik->tanggal_mulai)->format('Y-m-d') : '') }}"
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
                                       value="{{ old('tanggal_selesai', $tahunAkademik->tanggal_selesai ? \Carbon\Carbon::parse($tahunAkademik->tanggal_selesai)->format('Y-m-d') : '') }}"
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
                                      placeholder="Keterangan tambahan (opsional)">{{ old('keterangan', $tahunAkademik->keterangan) }}</textarea>
                            @error('keterangan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle"></i> 
                            <strong>Perhatian:</strong> Perubahan data tahun akademik akan mempengaruhi 
                            {{ $tahunAkademik->semesters->count() }} semester yang terkait.
                        </div>

                        <!-- File Upload Component -->
                        @include('components.file-upload', [
                            'fileableType' => 'App\\Models\\TahunAkademik',
                            'fileableId' => $tahunAkademik->id,
                            'existingFiles' => $tahunAkademik->files
                        ])
                    </form>

                    <div class="d-flex gap-2 mt-3">
                        <button type="submit" class="btn btn-primary" form="tahunAkademikFormEdit">
                            <i class="bi bi-save"></i> Update
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
                    <i class="bi bi-info-circle"></i> Informasi
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <td class="fw-bold">Status</td>
                            <td>
                                <span class="badge bg-{{ $tahunAkademik->is_active ? 'success' : 'secondary' }}">
                                    {{ $tahunAkademik->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Jumlah Semester</td>
                            <td>{{ $tahunAkademik->semesters->count() }} semester</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Dibuat Oleh</td>
                            <td>{{ $tahunAkademik->created_by ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Dibuat Pada</td>
                            <td>{{ $tahunAkademik->created_at ? $tahunAkademik->created_at->format('d/m/Y H:i') : '-' }}</td>
                        </tr>
                        @if($tahunAkademik->updated_by)
                        <tr>
                            <td class="fw-bold">Diupdate Oleh</td>
                            <td>{{ $tahunAkademik->updated_by }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Diupdate Pada</td>
                            <td>{{ $tahunAkademik->updated_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header bg-warning text-dark">
                    <i class="bi bi-lightbulb"></i> Panduan
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success"></i> 
                            Pastikan kode tahun akademik unik
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success"></i> 
                            Tahun selesai harus lebih besar dari tahun mulai
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success"></i> 
                            Tanggal selesai harus setelah tanggal mulai
                        </li>
                        <li class="mb-0">
                            <i class="bi bi-check-circle text-success"></i> 
                            Ubah status aktif dari halaman daftar
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
