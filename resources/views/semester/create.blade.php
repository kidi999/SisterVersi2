@extends('layouts.app')

@section('title', 'Tambah Semester')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Tambah Semester</h1>
        <a href="{{ route('semester.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('semester.store') }}" method="POST" id="semesterForm">
                        @csrf

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="tahun_akademik_id" class="form-label">
                                    Tahun Akademik <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('tahun_akademik_id') is-invalid @enderror" 
                                        id="tahun_akademik_id" 
                                        name="tahun_akademik_id" 
                                        required>
                                    <option value="">Pilih Tahun Akademik</option>
                                    @foreach($tahunAkademiks as $ta)
                                        <option value="{{ $ta->id }}" 
                                                {{ old('tahun_akademik_id', $tahunAkademikId) == $ta->id ? 'selected' : '' }}>
                                            {{ $ta->kode }} - {{ $ta->nama }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('tahun_akademik_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="program_studi_id" class="form-label">Program Studi</label>
                                <select class="form-select @error('program_studi_id') is-invalid @enderror" 
                                        id="program_studi_id" 
                                        name="program_studi_id">
                                    <option value="">Universitas (Tanpa Prodi)</option>
                                    @foreach($programStudis as $prodi)
                                        <option value="{{ $prodi->id }}" {{ old('program_studi_id') == $prodi->id ? 'selected' : '' }}>
                                            {{ $prodi->nama_prodi }} ({{ $prodi->fakultas->nama_fakultas }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('program_studi_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Kosongkan jika semester berlaku untuk seluruh universitas</small>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-8">
                                <label for="nama_semester" class="form-label">
                                    Nama Semester <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control @error('nama_semester') is-invalid @enderror" 
                                       id="nama_semester" 
                                       name="nama_semester" 
                                       value="{{ old('nama_semester') }}"
                                       placeholder="Contoh: Semester Ganjil"
                                       required>
                                @error('nama_semester')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="nomor_semester" class="form-label">
                                    Nomor Semester <span class="text-danger">*</span>
                                </label>
                                <input type="number" 
                                       class="form-control @error('nomor_semester') is-invalid @enderror" 
                                       id="nomor_semester" 
                                       name="nomor_semester" 
                                       value="{{ old('nomor_semester', 1) }}"
                                       min="1"
                                       max="20"
                                       required>
                                @error('nomor_semester')
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

                        <h6 class="border-bottom pb-2 mb-3">Jadwal Perkuliahan</h6>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="tanggal_mulai_perkuliahan" class="form-label">Tanggal Mulai Perkuliahan</label>
                                <input type="date" 
                                       class="form-control @error('tanggal_mulai_perkuliahan') is-invalid @enderror" 
                                       id="tanggal_mulai_perkuliahan" 
                                       name="tanggal_mulai_perkuliahan" 
                                       value="{{ old('tanggal_mulai_perkuliahan') }}">
                                @error('tanggal_mulai_perkuliahan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="tanggal_selesai_perkuliahan" class="form-label">Tanggal Selesai Perkuliahan</label>
                                <input type="date" 
                                       class="form-control @error('tanggal_selesai_perkuliahan') is-invalid @enderror" 
                                       id="tanggal_selesai_perkuliahan" 
                                       name="tanggal_selesai_perkuliahan" 
                                       value="{{ old('tanggal_selesai_perkuliahan') }}">
                                @error('tanggal_selesai_perkuliahan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <h6 class="border-bottom pb-2 mb-3">Jadwal UTS</h6>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="tanggal_mulai_uts" class="form-label">Tanggal Mulai UTS</label>
                                <input type="date" 
                                       class="form-control @error('tanggal_mulai_uts') is-invalid @enderror" 
                                       id="tanggal_mulai_uts" 
                                       name="tanggal_mulai_uts" 
                                       value="{{ old('tanggal_mulai_uts') }}">
                                @error('tanggal_mulai_uts')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="tanggal_selesai_uts" class="form-label">Tanggal Selesai UTS</label>
                                <input type="date" 
                                       class="form-control @error('tanggal_selesai_uts') is-invalid @enderror" 
                                       id="tanggal_selesai_uts" 
                                       name="tanggal_selesai_uts" 
                                       value="{{ old('tanggal_selesai_uts') }}">
                                @error('tanggal_selesai_uts')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <h6 class="border-bottom pb-2 mb-3">Jadwal UAS</h6>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="tanggal_mulai_uas" class="form-label">Tanggal Mulai UAS</label>
                                <input type="date" 
                                       class="form-control @error('tanggal_mulai_uas') is-invalid @enderror" 
                                       id="tanggal_mulai_uas" 
                                       name="tanggal_mulai_uas" 
                                       value="{{ old('tanggal_mulai_uas') }}">
                                @error('tanggal_mulai_uas')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="tanggal_selesai_uas" class="form-label">Tanggal Selesai UAS</label>
                                <input type="date" 
                                       class="form-control @error('tanggal_selesai_uas') is-invalid @enderror" 
                                       id="tanggal_selesai_uas" 
                                       name="tanggal_selesai_uas" 
                                       value="{{ old('tanggal_selesai_uas') }}">
                                @error('tanggal_selesai_uas')
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
                            <strong>Catatan:</strong> Semester baru akan dibuat dengan status nonaktif. Aktifkan melalui menu daftar semester.
                        </div>

                        <!-- File Upload Component -->
                        @include('components.file-upload', [
                            'fileableType' => 'App\\Models\\Semester',
                            'fileableId' => 0
                        ])
                    </form>

                    <div class="d-flex gap-2 mt-3">
                        <button type="submit" class="btn btn-primary" form="semesterForm">
                            <i class="bi bi-save"></i> Simpan
                        </button>
                        <a href="{{ route('semester.index') }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i> Batal
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <i class="bi bi-lightbulb"></i> Panduan
                </div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <strong>Tahun Akademik:</strong> Pilih tahun akademik untuk semester ini
                        </li>
                        <li class="mb-2">
                            <strong>Program Studi:</strong> Kosongkan jika semester berlaku untuk seluruh universitas
                        </li>
                        <li class="mb-2">
                            <strong>Nomor Semester:</strong> Urutan semester (1 = Ganjil, 2 = Genap, dst)
                        </li>
                        <li class="mb-2">
                            <strong>Tanggal Mulai/Selesai:</strong> Periode keseluruhan semester
                        </li>
                        <li class="mb-2">
                            <strong>Jadwal Detail:</strong> Tentukan jadwal perkuliahan, UTS, dan UAS (opsional)
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
