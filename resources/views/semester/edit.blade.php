@extends('layouts.app')

@section('title', 'Edit Semester')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Edit Semester</h1>
        <a href="{{ route('semester.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('semester.update', $semester->id) }}" method="POST" id="semesterFormEdit">
                        @csrf
                        @method('PUT')

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
                                                {{ old('tahun_akademik_id', $semester->tahun_akademik_id) == $ta->id ? 'selected' : '' }}>
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
                                        <option value="{{ $prodi->id }}" 
                                                {{ old('program_studi_id', $semester->program_studi_id) == $prodi->id ? 'selected' : '' }}>
                                            {{ $prodi->nama_prodi }} ({{ $prodi->fakultas->nama_fakultas }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('program_studi_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
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
                                       value="{{ old('nama_semester', $semester->nama_semester) }}"
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
                                       value="{{ old('nomor_semester', $semester->nomor_semester) }}"
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
                                       value="{{ old('tanggal_mulai', $semester->tanggal_mulai ? \Carbon\Carbon::parse($semester->tanggal_mulai)->format('Y-m-d') : '') }}"
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
                                       value="{{ old('tanggal_selesai', $semester->tanggal_selesai ? \Carbon\Carbon::parse($semester->tanggal_selesai)->format('Y-m-d') : '') }}"
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
                                       value="{{ old('tanggal_mulai_perkuliahan', $semester->tanggal_mulai_perkuliahan ? \Carbon\Carbon::parse($semester->tanggal_mulai_perkuliahan)->format('Y-m-d') : '') }}">
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
                                       value="{{ old('tanggal_selesai_perkuliahan', $semester->tanggal_selesai_perkuliahan ? \Carbon\Carbon::parse($semester->tanggal_selesai_perkuliahan)->format('Y-m-d') : '') }}">
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
                                       value="{{ old('tanggal_mulai_uts', $semester->tanggal_mulai_uts ? \Carbon\Carbon::parse($semester->tanggal_mulai_uts)->format('Y-m-d') : '') }}">
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
                                       value="{{ old('tanggal_selesai_uts', $semester->tanggal_selesai_uts ? \Carbon\Carbon::parse($semester->tanggal_selesai_uts)->format('Y-m-d') : '') }}">
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
                                       value="{{ old('tanggal_mulai_uas', $semester->tanggal_mulai_uas ? \Carbon\Carbon::parse($semester->tanggal_mulai_uas)->format('Y-m-d') : '') }}">
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
                                       value="{{ old('tanggal_selesai_uas', $semester->tanggal_selesai_uas ? \Carbon\Carbon::parse($semester->tanggal_selesai_uas)->format('Y-m-d') : '') }}">
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
                                      rows="3">{{ old('keterangan', $semester->keterangan) }}</textarea>
                            @error('keterangan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- File Upload Component -->
                        @include('components.file-upload', [
                            'fileableType' => 'App\\Models\\Semester',
                            'fileableId' => $semester->id,
                            'existingFiles' => $semester->files
                        ])
                    </form>

                    <div class="d-flex gap-2 mt-3">
                        <button type="submit" class="btn btn-primary" form="semesterFormEdit">
                            <i class="bi bi-save"></i> Update
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
                    <i class="bi bi-info-circle"></i> Informasi
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <td class="fw-bold" width="40%">Status</td>
                            <td>
                                <span class="badge bg-{{ $semester->is_active ? 'success' : 'secondary' }}">
                                    {{ $semester->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Dibuat Oleh</td>
                            <td>{{ $semester->created_by ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Dibuat Pada</td>
                            <td>{{ $semester->created_at ? $semester->created_at->format('d/m/Y H:i') : '-' }}</td>
                        </tr>
                        @if($semester->updated_by)
                        <tr>
                            <td class="fw-bold">Diupdate Oleh</td>
                            <td>{{ $semester->updated_by }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Diupdate Pada</td>
                            <td>{{ $semester->updated_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
