@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Edit Nilai</h1>
        <a href="{{ route('nilai.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Form Edit Nilai</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('nilai.update', $nilai->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label fw-bold">NIM</label>
                            <p class="form-control-plaintext">{{ $nilai->krs->mahasiswa->nim }}</p>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Nama Mahasiswa</label>
                            <p class="form-control-plaintext">{{ $nilai->krs->mahasiswa->nama }}</p>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Mata Kuliah</label>
                            <p class="form-control-plaintext">
                                {{ $nilai->krs->kelas->mataKuliah->nama }}
                                <span class="badge bg-secondary ms-2">{{ $nilai->krs->kelas->kode_kelas }}</span>
                            </p>
                        </div>

                        <hr>

                        <div class="mb-3">
                            <label for="nilai_tugas" class="form-label">
                                Nilai Tugas (30%) <span class="text-danger">*</span>
                            </label>
                            <input type="number" step="0.01" min="0" max="100" 
                                   class="form-control @error('nilai_tugas') is-invalid @enderror" 
                                   id="nilai_tugas" name="nilai_tugas" 
                                   value="{{ old('nilai_tugas', $nilai->nilai_tugas) }}" required>
                            @error('nilai_tugas')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="nilai_uts" class="form-label">
                                Nilai UTS (30%) <span class="text-danger">*</span>
                            </label>
                            <input type="number" step="0.01" min="0" max="100" 
                                   class="form-control @error('nilai_uts') is-invalid @enderror" 
                                   id="nilai_uts" name="nilai_uts" 
                                   value="{{ old('nilai_uts', $nilai->nilai_uts) }}" required>
                            @error('nilai_uts')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="nilai_uas" class="form-label">
                                Nilai UAS (40%) <span class="text-danger">*</span>
                            </label>
                            <input type="number" step="0.01" min="0" max="100" 
                                   class="form-control @error('nilai_uas') is-invalid @enderror" 
                                   id="nilai_uas" name="nilai_uas" 
                                   value="{{ old('nilai_uas', $nilai->nilai_uas) }}" required>
                            @error('nilai_uas')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        @include('components.file-upload', [
                            'existingFiles' => $nilai->files ?? collect(),
                            'fileableType' => \App\Models\Nilai::class,
                            'fileableId' => $nilai->id,
                        ])

                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Update Nilai
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-calculator"></i> Nilai Saat Ini
                    </h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <td><strong>Tugas</strong></td>
                            <td>{{ number_format($nilai->nilai_tugas, 2) }}</td>
                        </tr>
                        <tr>
                            <td><strong>UTS</strong></td>
                            <td>{{ number_format($nilai->nilai_uts, 2) }}</td>
                        </tr>
                        <tr>
                            <td><strong>UAS</strong></td>
                            <td>{{ number_format($nilai->nilai_uas, 2) }}</td>
                        </tr>
                        <tr>
                            <td><strong>Nilai Akhir</strong></td>
                            <td><strong>{{ number_format($nilai->nilai_akhir, 2) }}</strong></td>
                        </tr>
                        <tr>
                            <td><strong>Nilai Huruf</strong></td>
                            <td>
                                <span class="badge 
                                    @if(in_array($nilai->nilai_huruf, ['A', 'A-'])) bg-success
                                    @elseif(in_array($nilai->nilai_huruf, ['B+', 'B', 'B-'])) bg-primary
                                    @elseif(in_array($nilai->nilai_huruf, ['C+', 'C'])) bg-warning
                                    @else bg-danger
                                    @endif">
                                    {{ $nilai->nilai_huruf }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Bobot</strong></td>
                            <td>{{ number_format($nilai->bobot, 2) }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-info-circle"></i> Komponen Penilaian
                    </h5>
                </div>
                <div class="card-body">
                    <ul class="small mb-0">
                        <li>Tugas: 30%</li>
                        <li>UTS: 30%</li>
                        <li>UAS: 40%</li>
                        <li>Nilai akan otomatis dihitung</li>
                        <li>Konversi huruf dan bobot otomatis</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
