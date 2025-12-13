@extends('layouts.app')

@section('title', 'Tambah Tagihan Mahasiswa')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Tambah Tagihan Mahasiswa</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('tagihan-mahasiswa.index') }}">Tagihan Mahasiswa</a></li>
                    <li class="breadcrumb-item active">Tambah</li>
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
            <form action="{{ route('tagihan-mahasiswa.store') }}" method="POST">
                @csrf

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Mahasiswa <span class="text-danger">*</span></label>
                        <select name="mahasiswa_id" class="form-select @error('mahasiswa_id') is-invalid @enderror" required>
                            <option value="">Pilih Mahasiswa</option>
                            @foreach($mahasiswa as $m)
                                <option value="{{ $m->id }}" {{ old('mahasiswa_id') == $m->id ? 'selected' : '' }}>
                                    {{ $m->nama_mahasiswa }} ({{ $m->nim }}) - {{ $m->programStudi->nama_prodi ?? '-' }}
                                </option>
                            @endforeach
                        </select>
                        @error('mahasiswa_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Jenis Pembayaran <span class="text-danger">*</span></label>
                        <select name="jenis_pembayaran_id" class="form-select @error('jenis_pembayaran_id') is-invalid @enderror" required>
                            <option value="">Pilih Jenis Pembayaran</option>
                            @foreach($jenisPembayaran as $jp)
                                <option value="{{ $jp->id }}" {{ old('jenis_pembayaran_id') == $jp->id ? 'selected' : '' }}>
                                    {{ $jp->nama }}
                                </option>
                            @endforeach
                        </select>
                        @error('jenis_pembayaran_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Tahun Akademik <span class="text-danger">*</span></label>
                        <select name="tahun_akademik_id" class="form-select @error('tahun_akademik_id') is-invalid @enderror" required>
                            <option value="">Pilih Tahun Akademik</option>
                            @foreach($tahunAkademik as $ta)
                                <option value="{{ $ta->id }}" {{ old('tahun_akademik_id') == $ta->id ? 'selected' : '' }}>
                                    {{ $ta->nama }}
                                </option>
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
                                <option value="{{ $s->id }}" {{ old('semester_id') == $s->id ? 'selected' : '' }}>
                                    {{ $s->nama }}
                                </option>
                            @endforeach
                        </select>
                        @error('semester_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Jumlah Tagihan (Rp) <span class="text-danger">*</span></label>
                        <input type="number" name="jumlah_tagihan" class="form-control @error('jumlah_tagihan') is-invalid @enderror" value="{{ old('jumlah_tagihan') }}" min="0" step="1" required>
                        @error('jumlah_tagihan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Tanggal Tagihan <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_tagihan" class="form-control @error('tanggal_tagihan') is-invalid @enderror" value="{{ old('tanggal_tagihan', now()->toDateString()) }}" required>
                        @error('tanggal_tagihan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Jatuh Tempo <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_jatuh_tempo" class="form-control @error('tanggal_jatuh_tempo') is-invalid @enderror" value="{{ old('tanggal_jatuh_tempo', now()->addDays(30)->toDateString()) }}" required>
                        @error('tanggal_jatuh_tempo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Denda (Rp)</label>
                        <input type="number" name="denda" class="form-control @error('denda') is-invalid @enderror" value="{{ old('denda', 0) }}" min="0" step="1">
                        @error('denda')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Diskon (Rp)</label>
                        <input type="number" name="diskon" class="form-control @error('diskon') is-invalid @enderror" value="{{ old('diskon', 0) }}" min="0" step="1">
                        @error('diskon')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label">Keterangan</label>
                        <textarea name="keterangan" class="form-control @error('keterangan') is-invalid @enderror" rows="3">{{ old('keterangan') }}</textarea>
                        @error('keterangan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                @include('components.file-upload', [
                    'existingFiles' => collect(),
                    'fileableType' => \App\Models\TagihanMahasiswa::class,
                    'fileableId' => 0,
                    'maxFiles' => 10,
                ])

                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
