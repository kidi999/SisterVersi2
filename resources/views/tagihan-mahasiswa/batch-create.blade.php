@extends('layouts.app')

@section('title', 'Buat Tagihan Massal')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Buat Tagihan Massal</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('tagihan-mahasiswa.index') }}">Tagihan Mahasiswa</a></li>
                    <li class="breadcrumb-item active">Buat Massal</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('tagihan-mahasiswa.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <form action="{{ route('tagihan-mahasiswa.batch-store') }}" method="POST">
                @csrf

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Jenis Pembayaran <span class="text-danger">*</span></label>
                        <select name="jenis_pembayaran_id" class="form-select @error('jenis_pembayaran_id') is-invalid @enderror" required>
                            <option value="">Pilih Jenis Pembayaran</option>
                            @foreach($jenisPembayaran as $jp)
                                <option value="{{ $jp->id }}" {{ old('jenis_pembayaran_id') == $jp->id ? 'selected' : '' }}>{{ $jp->nama }}</option>
                            @endforeach
                        </select>
                        @error('jenis_pembayaran_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Jumlah Tagihan (Rp) <span class="text-danger">*</span></label>
                        <input type="number" name="jumlah_tagihan" class="form-control @error('jumlah_tagihan') is-invalid @enderror" value="{{ old('jumlah_tagihan') }}" min="0" step="1" required>
                        @error('jumlah_tagihan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Tahun Akademik <span class="text-danger">*</span></label>
                        <select name="tahun_akademik_id" class="form-select @error('tahun_akademik_id') is-invalid @enderror" required>
                            <option value="">Pilih Tahun Akademik</option>
                            @foreach($tahunAkademik as $ta)
                                <option value="{{ $ta->id }}" {{ old('tahun_akademik_id') == $ta->id ? 'selected' : '' }}>{{ $ta->nama }}</option>
                            @endforeach
                        </select>
                        @error('tahun_akademik_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Semester <span class="text-danger">*</span></label>
                        <select name="semester_id" class="form-select @error('semester_id') is-invalid @enderror" required>
                            <option value="">Pilih Semester</option>
                            @foreach($semesters as $s)
                                <option value="{{ $s->id }}" {{ old('semester_id') == $s->id ? 'selected' : '' }}>{{ $s->nama }}</option>
                            @endforeach
                        </select>
                        @error('semester_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Tanggal Tagihan <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_tagihan" class="form-control @error('tanggal_tagihan') is-invalid @enderror" value="{{ old('tanggal_tagihan', now()->toDateString()) }}" required>
                        @error('tanggal_tagihan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Jatuh Tempo <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_jatuh_tempo" class="form-control @error('tanggal_jatuh_tempo') is-invalid @enderror" value="{{ old('tanggal_jatuh_tempo', now()->addDays(30)->toDateString()) }}" required>
                        @error('tanggal_jatuh_tempo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label">Target Mahasiswa <span class="text-danger">*</span></label>
                        <select id="filter_type" name="filter_type" class="form-select @error('filter_type') is-invalid @enderror" required>
                            <option value="">Pilih Target</option>
                            <option value="all" {{ old('filter_type') == 'all' ? 'selected' : '' }}>Semua Mahasiswa Aktif</option>
                            <option value="fakultas" {{ old('filter_type') == 'fakultas' ? 'selected' : '' }}>Per Fakultas</option>
                            <option value="prodi" {{ old('filter_type') == 'prodi' ? 'selected' : '' }}>Per Program Studi</option>
                            <option value="semester_mahasiswa" {{ old('filter_type') == 'semester_mahasiswa' ? 'selected' : '' }}>Per Semester Mahasiswa</option>
                        </select>
                        @error('filter_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6" id="fakultas_field" style="display:none;">
                        <label class="form-label">Fakultas <span class="text-danger">*</span></label>
                        <select name="fakultas_id" class="form-select @error('fakultas_id') is-invalid @enderror">
                            <option value="">Pilih Fakultas</option>
                            @foreach($fakultas as $f)
                                <option value="{{ $f->id }}" {{ old('fakultas_id') == $f->id ? 'selected' : '' }}>{{ $f->nama_fakultas }}</option>
                            @endforeach
                        </select>
                        @error('fakultas_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6" id="prodi_field" style="display:none;">
                        <label class="form-label">Program Studi <span class="text-danger">*</span></label>
                        <select name="program_studi_id" class="form-select @error('program_studi_id') is-invalid @enderror">
                            <option value="">Pilih Program Studi</option>
                            @foreach($programStudi as $ps)
                                <option value="{{ $ps->id }}" {{ old('program_studi_id') == $ps->id ? 'selected' : '' }}>
                                    {{ $ps->nama_prodi }} ({{ $ps->fakultas->nama_fakultas ?? '-' }})
                                </option>
                            @endforeach
                        </select>
                        @error('program_studi_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6" id="semester_mhs_field" style="display:none;">
                        <label class="form-label">Semester Mahasiswa <span class="text-danger">*</span></label>
                        <input type="number" name="semester_mahasiswa" class="form-control @error('semester_mahasiswa') is-invalid @enderror" value="{{ old('semester_mahasiswa') }}" min="1" step="1">
                        @error('semester_mahasiswa')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn btn-primary" onclick="return confirm('Buat tagihan massal sesuai filter ini?')">
                        <i class="bi bi-plus-circle"></i> Buat Tagihan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function(){
    const filterType = document.getElementById('filter_type');
    const fakultasField = document.getElementById('fakultas_field');
    const prodiField = document.getElementById('prodi_field');
    const semesterMhsField = document.getElementById('semester_mhs_field');

    function updateVisibility() {
        const v = filterType.value;
        fakultasField.style.display = (v === 'fakultas' || v === 'prodi') ? 'block' : 'none';
        prodiField.style.display = (v === 'prodi') ? 'block' : 'none';
        semesterMhsField.style.display = (v === 'semester_mahasiswa') ? 'block' : 'none';
    }

    filterType.addEventListener('change', updateVisibility);
    updateVisibility();
})();
</script>
@endpush
@endsection
