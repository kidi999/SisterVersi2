@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Input Nilai Batch - {{ $kelas->nama_kelas }}</h1>
        <a href="{{ route('nilai.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card mb-3">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td width="150"><strong>Kode Kelas</strong></td>
                            <td>: <span class="badge bg-secondary">{{ $kelas->kode_kelas }}</span></td>
                        </tr>
                        <tr>
                            <td><strong>Nama Kelas</strong></td>
                            <td>: {{ $kelas->nama_kelas }}</td>
                        </tr>
                        <tr>
                            <td><strong>Mata Kuliah</strong></td>
                            <td>: {{ $kelas->mataKuliah->nama }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td width="150"><strong>Dosen</strong></td>
                            <td>: {{ $kelas->dosen->nama ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td><strong>SKS</strong></td>
                            <td>: {{ $kelas->mataKuliah->sks }}</td>
                        </tr>
                        <tr>
                            <td><strong>Jumlah Mahasiswa</strong></td>
                            <td>: {{ $krsList->count() }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @if($krsList->count() > 0)
    <div class="card">
        <div class="card-body">
            <form action="{{ route('nilai.storeBatch', $kelas->id) }}" method="POST">
                @csrf
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th width="40">No</th>
                                <th width="100">NIM</th>
                                <th>Nama Mahasiswa</th>
                                <th width="120">Tugas (30%)</th>
                                <th width="120">UTS (30%)</th>
                                <th width="120">UAS (40%)</th>
                                <th width="100">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($krsList as $krs)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td>{{ $krs->mahasiswa->nim }}</td>
                                <td>{{ $krs->mahasiswa->nama }}</td>
                                <td>
                                    <input type="hidden" name="nilai[{{ $loop->index }}][krs_id]" value="{{ $krs->id }}">
                                    <input type="number" step="0.01" min="0" max="100" 
                                           class="form-control form-control-sm" 
                                           name="nilai[{{ $loop->index }}][nilai_tugas]" 
                                           value="{{ $krs->nilai->nilai_tugas ?? '' }}"
                                           placeholder="0-100" required>
                                </td>
                                <td>
                                    <input type="number" step="0.01" min="0" max="100" 
                                           class="form-control form-control-sm" 
                                           name="nilai[{{ $loop->index }}][nilai_uts]" 
                                           value="{{ $krs->nilai->nilai_uts ?? '' }}"
                                           placeholder="0-100" required>
                                </td>
                                <td>
                                    <input type="number" step="0.01" min="0" max="100" 
                                           class="form-control form-control-sm" 
                                           name="nilai[{{ $loop->index }}][nilai_uas]" 
                                           value="{{ $krs->nilai->nilai_uas ?? '' }}"
                                           placeholder="0-100" required>
                                </td>
                                <td class="text-center">
                                    @if($krs->nilai)
                                        <span class="badge bg-success">Sudah Input</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Belum Input</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-grid gap-2 mt-4">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="bi bi-save"></i> Simpan Semua Nilai
                    </button>
                </div>
            </form>
        </div>
    </div>
    @else
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
            <p class="mt-3 text-muted">Tidak ada mahasiswa terdaftar di kelas ini</p>
        </div>
    </div>
    @endif
</div>
@endsection
