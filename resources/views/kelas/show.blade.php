@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Detail Kelas</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('kelas.index') }}">Kelas</a></li>
                    <li class="breadcrumb-item active">Detail</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('kelas.edit', $kela) }}" class="btn btn-warning">
                <i class="bi bi-pencil"></i> Edit
            </a>
            <a href="{{ route('kelas.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-info-circle"></i> Informasi Kelas</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th width="180">Kode Kelas:</th>
                            <td><span class="badge bg-primary fs-6">{{ $kela->kode_kelas }}</span></td>
                        </tr>
                        <tr>
                            <th>Nama Kelas:</th>
                            <td><strong>{{ $kela->nama_kelas }}</strong></td>
                        </tr>
                        <tr>
                            <th>Mata Kuliah:</th>
                            <td>
                                <strong>{{ $kela->mataKuliah->nama }}</strong><br>
                                <small class="text-muted">Kode: {{ $kela->mataKuliah->kode }} | SKS: {{ $kela->mataKuliah->sks }}</small>
                            </td>
                        </tr>
                        <tr>
                            <th>Program Studi:</th>
                            <td>{{ $kela->mataKuliah->programStudi->nama }}</td>
                        </tr>
                        <tr>
                            <th>Dosen Pengampu:</th>
                            <td>
                                <strong>{{ $kela->dosen->nama }}</strong><br>
                                <small class="text-muted">NIDN: {{ $kela->dosen->nidn }}</small>
                            </td>
                        </tr>
                        <tr>
                            <th>Tahun Ajaran:</th>
                            <td>{{ $kela->tahun_ajaran }}</td>
                        </tr>
                        <tr>
                            <th>Semester:</th>
                            <td>
                                <span class="badge {{ $kela->semester == 'Ganjil' ? 'bg-info' : 'bg-success' }}">
                                    {{ $kela->semester }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Kapasitas:</th>
                            <td>{{ $kela->kapasitas }} mahasiswa</td>
                        </tr>
                        <tr>
                            <th>Terisi:</th>
                            <td>
                                <span class="badge {{ $kela->terisi >= $kela->kapasitas ? 'bg-danger' : 'bg-success' }} fs-6">
                                    {{ $kela->terisi }}/{{ $kela->kapasitas }}
                                </span>
                                @if($kela->terisi >= $kela->kapasitas)
                                    <span class="text-danger ms-2"><i class="bi bi-exclamation-triangle"></i> Penuh</span>
                                @else
                                    <span class="text-muted ms-2">({{ $kela->kapasitas - $kela->terisi }} slot tersisa)</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="bi bi-clock-history"></i> Audit Trail</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless table-sm">
                        <tr>
                            <th width="150">Dibuat oleh:</th>
                            <td>{{ $kela->created_by ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Tanggal dibuat:</th>
                            <td>{{ $kela->created_at ? $kela->created_at->format('d/m/Y H:i') : '-' }}</td>
                        </tr>
                        <tr>
                            <th>Diupdate oleh:</th>
                            <td>{{ $kela->updated_by ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Tanggal update:</th>
                            <td>{{ $kela->updated_at ? $kela->updated_at->format('d/m/Y H:i') : '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0"><i class="bi bi-paperclip"></i> Lampiran</h5>
        </div>
        <div class="card-body">
            @if(($kela->files ?? collect())->count() > 0)
                <div class="list-group">
                    @foreach($kela->files as $file)
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <i class="{{ $file->icon_class }} fs-4 me-2"></i>
                                <div>
                                    <div><strong>{{ $file->file_name }}</strong></div>
                                    <small class="text-muted">{{ $file->formatted_size }}</small>
                                </div>
                            </div>
                            <a class="btn btn-sm btn-outline-primary" href="{{ route('api.file-upload.download', $file->id) }}">
                                <i class="bi bi-download"></i> Unduh
                            </a>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="alert alert-info mb-0">
                    <i class="bi bi-info-circle"></i> Belum ada lampiran.
                </div>
            @endif
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0"><i class="bi bi-people-fill"></i> Daftar Mahasiswa Terdaftar ({{ $kela->krsItems->count() }})</h5>
        </div>
        <div class="card-body">
            @if($kela->krsItems->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>NIM</th>
                                <th>Nama Mahasiswa</th>
                                <th>Program Studi</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($kela->krsItems as $index => $krs)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td><span class="badge bg-secondary">{{ $krs->mahasiswa->nim }}</span></td>
                                    <td>{{ $krs->mahasiswa->nama }}</td>
                                    <td>{{ $krs->mahasiswa->programStudi->nama }}</td>
                                    <td>
                                        @if($krs->status == 'Disetujui')
                                            <span class="badge bg-success">{{ $krs->status }}</span>
                                        @elseif($krs->status == 'Ditolak')
                                            <span class="badge bg-danger">{{ $krs->status }}</span>
                                        @else
                                            <span class="badge bg-warning">{{ $krs->status }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-4">
                    <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                    <p class="mt-2 text-muted">Belum ada mahasiswa terdaftar di kelas ini</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
