@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="mb-0"><i class="bi bi-speedometer2"></i> Dashboard Dosen</h2>
            <p class="text-muted">{{ $dosen->nama_dosen ?? Auth::user()->name }} - NIDN: {{ $dosen->nidn ?? '-' }}</p>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Jadwal Mengajar</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $total_jadwal_mengajar }}</div>
                            <small class="text-muted">Semester ini</small>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-calendar3 fs-2 text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Mata Kuliah</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $total_mata_kuliah }}</div>
                            <small class="text-muted">Diampu semester ini</small>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-book fs-2 text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total Mahasiswa</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $total_mahasiswa_diampu }}</div>
                            <small class="text-muted">Mahasiswa diampu</small>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-people fs-2 text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Semester Aktif</div>
                            <div class="h6 mb-0 font-weight-bold text-gray-800">
                                {{ $semester_aktif->nama_semester ?? 'Tidak ada semester aktif' }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-calendar-check fs-2 text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Jadwal Mengajar -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-calendar-week"></i> Jadwal Mengajar Semester Ini</h5>
                </div>
                <div class="card-body">
                    @if($jadwal_mengajar->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Hari</th>
                                        <th>Waktu</th>
                                        <th>Mata Kuliah</th>
                                        <th>Kelas</th>
                                        <th>Ruang</th>
                                        <th>SKS</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($jadwal_mengajar as $jadwal)
                                    <tr>
                                        <td><strong>{{ $jadwal->hari }}</strong></td>
                                        <td>{{ $jadwal->jam_mulai }} - {{ $jadwal->jam_selesai }}</td>
                                        <td>
                                            {{ $jadwal->mataKuliah->nama_mata_kuliah ?? '-' }}<br>
                                            <small class="text-muted">{{ $jadwal->mataKuliah->kode_mata_kuliah ?? '-' }}</small>
                                        </td>
                                        <td>{{ $jadwal->kelas->nama_kelas ?? '-' }}</td>
                                        <td>{{ $jadwal->ruang->nama_ruang ?? '-' }}</td>
                                        <td>{{ $jadwal->mataKuliah->sks ?? 0 }} SKS</td>
                                        <td>
                                            <a href="{{ route('jadwal-kuliah.show', $jadwal->id) }}" class="btn btn-sm btn-info" title="Detail">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info mb-0">
                            <i class="bi bi-info-circle"></i> Belum ada jadwal mengajar untuk semester ini.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Links -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-link-45deg"></i> Quick Links</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('jadwal-kuliah.index') }}" class="btn btn-outline-primary btn-block">
                                <i class="bi bi-calendar3"></i> Jadwal Kuliah
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('pertemuan-kuliah.index') }}" class="btn btn-outline-success btn-block">
                                <i class="bi bi-calendar-check"></i> Pertemuan Kuliah
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('absensi-mahasiswa.index') }}" class="btn btn-outline-info btn-block">
                                <i class="bi bi-clipboard-check"></i> Absensi Mahasiswa
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('nilai.index') }}" class="btn btn-outline-warning btn-block">
                                <i class="bi bi-award"></i> Input Nilai
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('mahasiswa.index') }}" class="btn btn-outline-secondary btn-block">
                                <i class="bi bi-people"></i> Data Mahasiswa
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('krs.index') }}" class="btn btn-outline-primary btn-block">
                                <i class="bi bi-card-checklist"></i> Verifikasi KRS
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.border-left-primary { border-left: 0.25rem solid #4e73df !important; }
.border-left-success { border-left: 0.25rem solid #1cc88a !important; }
.border-left-info { border-left: 0.25rem solid #36b9cc !important; }
.border-left-warning { border-left: 0.25rem solid #f6c23e !important; }
</style>
@endsection
