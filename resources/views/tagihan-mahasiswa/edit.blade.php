@extends('layouts.app')

@section('title', 'Edit Tagihan Mahasiswa')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Edit Tagihan Mahasiswa</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('tagihan-mahasiswa.index') }}">Tagihan Mahasiswa</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('tagihan-mahasiswa.show', $tagihanMahasiswa->id) }}" class="btn btn-info me-2">
                <i class="bi bi-eye"></i> Detail
            </a>
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
            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <label class="form-label">No. Tagihan</label>
                    <input type="text" class="form-control" value="{{ $tagihanMahasiswa->nomor_tagihan }}" readonly>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Mahasiswa</label>
                    <input type="text" class="form-control" value="{{ $tagihanMahasiswa->mahasiswa->nama_mahasiswa ?? '-' }} ({{ $tagihanMahasiswa->mahasiswa->nim ?? '-' }})" readonly>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Jenis Pembayaran</label>
                    <input type="text" class="form-control" value="{{ $tagihanMahasiswa->jenisPembayaran->nama ?? '-' }}" readonly>
                </div>
            </div>

            <form action="{{ route('tagihan-mahasiswa.update', $tagihanMahasiswa->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Tahun Akademik</label>
                        <input type="text" class="form-control" value="{{ $tagihanMahasiswa->tahunAkademik->nama ?? '-' }}" readonly>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Semester</label>
                        <input type="text" class="form-control" value="{{ $tagihanMahasiswa->semester->nama ?? '-' }}" readonly>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Jumlah Tagihan (Rp) <span class="text-danger">*</span></label>
                        <input type="number" name="jumlah_tagihan" class="form-control @error('jumlah_tagihan') is-invalid @enderror" value="{{ old('jumlah_tagihan', $tagihanMahasiswa->jumlah_tagihan) }}" min="0" step="1" required>
                        @error('jumlah_tagihan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Tanggal Tagihan <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_tagihan" class="form-control @error('tanggal_tagihan') is-invalid @enderror" value="{{ old('tanggal_tagihan', optional($tagihanMahasiswa->tanggal_tagihan)->toDateString()) }}" required>
                        @error('tanggal_tagihan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Jatuh Tempo <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_jatuh_tempo" class="form-control @error('tanggal_jatuh_tempo') is-invalid @enderror" value="{{ old('tanggal_jatuh_tempo', optional($tagihanMahasiswa->tanggal_jatuh_tempo)->toDateString()) }}" required>
                        @error('tanggal_jatuh_tempo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Denda (Rp)</label>
                        <input type="number" name="denda" class="form-control @error('denda') is-invalid @enderror" value="{{ old('denda', $tagihanMahasiswa->denda ?? 0) }}" min="0" step="1">
                        @error('denda')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Diskon (Rp)</label>
                        <input type="number" name="diskon" class="form-control @error('diskon') is-invalid @enderror" value="{{ old('diskon', $tagihanMahasiswa->diskon ?? 0) }}" min="0" step="1">
                        @error('diskon')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label">Keterangan</label>
                        <textarea name="keterangan" class="form-control @error('keterangan') is-invalid @enderror" rows="3">{{ old('keterangan', $tagihanMahasiswa->keterangan) }}</textarea>
                        @error('keterangan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                @include('components.file-upload', [
                    'existingFiles' => $tagihanMahasiswa->files ?? collect(),
                    'fileableType' => \App\Models\TagihanMahasiswa::class,
                    'fileableId' => $tagihanMahasiswa->id,
                    'maxFiles' => 10,
                ])

                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
