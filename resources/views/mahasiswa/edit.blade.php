@extends('layouts.app')

@section('title', 'Edit Mahasiswa')
@section('header', 'Edit Mahasiswa')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Form Edit Mahasiswa</h5>
        <a href="{{ route('mahasiswa.show', $mahasiswa) }}" class="btn btn-info btn-sm">
            <i class="bi bi-eye"></i> Lihat Detail
        </a>
    </div>
    <div class="card-body">
        <form action="{{ route('mahasiswa.update', $mahasiswa) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="nim" class="form-label">NIM <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('nim') is-invalid @enderror" 
                           id="nim" name="nim" value="{{ old('nim', $mahasiswa->nim) }}" required>
                    @error('nim')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="nama_mahasiswa" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('nama_mahasiswa') is-invalid @enderror" 
                           id="nama_mahasiswa" name="nama_mahasiswa" value="{{ old('nama_mahasiswa', $mahasiswa->nama_mahasiswa) }}" required>
                    @error('nama_mahasiswa')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="program_studi_id" class="form-label">Program Studi <span class="text-danger">*</span></label>
                    <select class="form-select @error('program_studi_id') is-invalid @enderror" 
                            id="program_studi_id" name="program_studi_id" required>
                        <option value="">Pilih Program Studi</option>
                        @foreach($programStudi as $prodi)
                            <option value="{{ $prodi->id }}" {{ old('program_studi_id', $mahasiswa->program_studi_id) == $prodi->id ? 'selected' : '' }}>
                                {{ $prodi->nama_prodi }} ({{ $prodi->jenjang }})
                            </option>
                        @endforeach
                    </select>
                    @error('program_studi_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="jenis_kelamin" class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                    <select class="form-select @error('jenis_kelamin') is-invalid @enderror" 
                            id="jenis_kelamin" name="jenis_kelamin" required>
                        <option value="">Pilih Jenis Kelamin</option>
                        <option value="L" {{ old('jenis_kelamin', $mahasiswa->jenis_kelamin) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="P" {{ old('jenis_kelamin', $mahasiswa->jenis_kelamin) == 'P' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                    @error('jenis_kelamin')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="tempat_lahir" class="form-label">Tempat Lahir</label>
                    <input type="text" class="form-control @error('tempat_lahir') is-invalid @enderror" 
                           id="tempat_lahir" name="tempat_lahir" value="{{ old('tempat_lahir', $mahasiswa->tempat_lahir) }}">
                    @error('tempat_lahir')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="tanggal_lahir" class="form-label">Tanggal Lahir</label>
                    <input type="date" class="form-control @error('tanggal_lahir') is-invalid @enderror" 
                           id="tanggal_lahir" name="tanggal_lahir" 
                           value="{{ old('tanggal_lahir', $mahasiswa->tanggal_lahir ? $mahasiswa->tanggal_lahir->format('Y-m-d') : '') }}">
                    @error('tanggal_lahir')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-3">
                <label for="alamat" class="form-label">Alamat</label>
                <textarea class="form-control @error('alamat') is-invalid @enderror" 
                          id="alamat" name="alamat" rows="2">{{ old('alamat', $mahasiswa->alamat) }}</textarea>
                @error('alamat')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="telepon" class="form-label">Telepon</label>
                    <input type="text" class="form-control @error('telepon') is-invalid @enderror" 
                           id="telepon" name="telepon" value="{{ old('telepon', $mahasiswa->telepon) }}">
                    @error('telepon')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4 mb-3">
                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                           id="email" name="email" value="{{ old('email', $mahasiswa->email) }}" required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4 mb-3">
                    <label for="tahun_masuk" class="form-label">Tahun Masuk <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('tahun_masuk') is-invalid @enderror" 
                           id="tahun_masuk" name="tahun_masuk" value="{{ old('tahun_masuk', $mahasiswa->tahun_masuk) }}" 
                           pattern="[0-9]{4}" maxlength="4" required>
                    @error('tahun_masuk')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="semester" class="form-label">Semester <span class="text-danger">*</span></label>
                    <input type="number" class="form-control @error('semester') is-invalid @enderror" 
                           id="semester" name="semester" value="{{ old('semester', $mahasiswa->semester ?? 1) }}" 
                           min="1" max="14" required>
                    @error('semester')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4 mb-3">
                    <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                    <select class="form-select @error('status') is-invalid @enderror" 
                            id="status" name="status" required>
                        <option value="">Pilih Status</option>
                        <option value="Aktif" {{ old('status', $mahasiswa->status) == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="Cuti" {{ old('status', $mahasiswa->status) == 'Cuti' ? 'selected' : '' }}>Cuti</option>
                        <option value="Lulus" {{ old('status', $mahasiswa->status) == 'Lulus' ? 'selected' : '' }}>Lulus</option>
                        <option value="DO" {{ old('status', $mahasiswa->status) == 'DO' ? 'selected' : '' }}>DO</option>
                        <option value="Mengundurkan Diri" {{ old('status', $mahasiswa->status) == 'Mengundurkan Diri' ? 'selected' : '' }}>Mengundurkan Diri</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4 mb-3">
                    <label for="ipk" class="form-label">IPK</label>
                    <input type="number" step="0.01" class="form-control" 
                           id="ipk" value="{{ $mahasiswa->ipk }}" readonly>
                    <small class="text-muted">IPK dihitung otomatis dari nilai</small>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="nama_wali" class="form-label">Nama Wali/Orang Tua</label>
                    <input type="text" class="form-control @error('nama_wali') is-invalid @enderror" 
                           id="nama_wali" name="nama_wali" value="{{ old('nama_wali', $mahasiswa->nama_wali) }}">
                    @error('nama_wali')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="telepon_wali" class="form-label">Telepon Wali</label>
                    <input type="text" class="form-control @error('telepon_wali') is-invalid @enderror" 
                           id="telepon_wali" name="telepon_wali" value="{{ old('telepon_wali', $mahasiswa->telepon_wali) }}">
                    @error('telepon_wali')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            @include('components.file-upload', [
                'existingFiles' => $mahasiswa->files ?? collect(),
                'fileableType' => \App\Models\Mahasiswa::class,
                'fileableId' => $mahasiswa->id,
                'maxFiles' => 10,
            ])

            <div class="d-flex justify-content-between">
                <a href="{{ route('mahasiswa.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Update
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
