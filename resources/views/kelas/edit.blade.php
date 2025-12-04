@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Edit Kelas</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('kelas.index') }}">Kelas</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('kelas.update', $kela) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="mata_kuliah_id" class="form-label">Mata Kuliah <span class="text-danger">*</span></label>
                            <select name="mata_kuliah_id" id="mata_kuliah_id" class="form-select @error('mata_kuliah_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Mata Kuliah --</option>
                                @foreach($mataKuliah as $mk)
                                    <option value="{{ $mk->id }}" {{ old('mata_kuliah_id', $kela->mata_kuliah_id) == $mk->id ? 'selected' : '' }}>
                                        {{ $mk->kode }} - {{ $mk->nama }} ({{ $mk->sks }} SKS) - {{ $mk->programStudi->nama }}
                                    </option>
                                @endforeach
                            </select>
                            @error('mata_kuliah_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="dosen_id" class="form-label">Dosen Pengampu <span class="text-danger">*</span></label>
                            <select name="dosen_id" id="dosen_id" class="form-select @error('dosen_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Dosen --</option>
                                @foreach($dosen as $d)
                                    <option value="{{ $d->id }}" {{ old('dosen_id', $kela->dosen_id) == $d->id ? 'selected' : '' }}>
                                        {{ $d->nidn }} - {{ $d->nama }}
                                    </option>
                                @endforeach
                            </select>
                            @error('dosen_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="kode_kelas" class="form-label">Kode Kelas <span class="text-danger">*</span></label>
                                    <input type="text" name="kode_kelas" id="kode_kelas" 
                                           class="form-control @error('kode_kelas') is-invalid @enderror" 
                                           value="{{ old('kode_kelas', $kela->kode_kelas) }}" required>
                                    @error('kode_kelas')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nama_kelas" class="form-label">Nama Kelas <span class="text-danger">*</span></label>
                                    <input type="text" name="nama_kelas" id="nama_kelas" 
                                           class="form-control @error('nama_kelas') is-invalid @enderror" 
                                           value="{{ old('nama_kelas', $kela->nama_kelas) }}" required>
                                    @error('nama_kelas')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tahun_ajaran" class="form-label">Tahun Ajaran <span class="text-danger">*</span></label>
                                    <select name="tahun_ajaran" id="tahun_ajaran" class="form-select @error('tahun_ajaran') is-invalid @enderror" required>
                                        <option value="">-- Pilih Tahun Ajaran --</option>
                                        @foreach($tahunAkademik as $ta)
                                            <option value="{{ $ta->tahun_ajaran }}" {{ old('tahun_ajaran', $kela->tahun_ajaran) == $ta->tahun_ajaran ? 'selected' : '' }}>
                                                {{ $ta->tahun_ajaran }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('tahun_ajaran')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="semester" class="form-label">Semester <span class="text-danger">*</span></label>
                                    <select name="semester" id="semester" class="form-select @error('semester') is-invalid @enderror" required>
                                        <option value="">-- Pilih Semester --</option>
                                        <option value="Ganjil" {{ old('semester', $kela->semester) == 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                                        <option value="Genap" {{ old('semester', $kela->semester) == 'Genap' ? 'selected' : '' }}>Genap</option>
                                    </select>
                                    @error('semester')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="kapasitas" class="form-label">Kapasitas Mahasiswa <span class="text-danger">*</span></label>
                            <input type="number" name="kapasitas" id="kapasitas" 
                                   class="form-control @error('kapasitas') is-invalid @enderror" 
                                   value="{{ old('kapasitas', $kela->kapasitas) }}" 
                                   min="{{ $kela->terisi }}" max="100" required>
                            @error('kapasitas')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Minimal {{ $kela->terisi }} (jumlah mahasiswa terdaftar saat ini)</small>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('kelas.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card bg-light">
                <div class="card-body">
                    <h5 class="card-title"><i class="bi bi-info-circle"></i> Status Kelas</h5>
                    <table class="table table-sm">
                        <tr>
                            <th>Kapasitas:</th>
                            <td>{{ $kela->kapasitas }} mahasiswa</td>
                        </tr>
                        <tr>
                            <th>Terisi:</th>
                            <td>{{ $kela->terisi }} mahasiswa</td>
                        </tr>
                        <tr>
                            <th>Sisa Slot:</th>
                            <td>{{ $kela->kapasitas - $kela->terisi }} mahasiswa</td>
                        </tr>
                    </table>
                    <div class="alert alert-warning mb-0" role="alert">
                        <small><i class="bi bi-exclamation-triangle"></i> Kapasitas tidak boleh kurang dari jumlah mahasiswa terdaftar</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
