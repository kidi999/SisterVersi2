@extends('layouts.app')

@section('title', 'Detail Jadwal Kuliah')
@section('header', 'Detail Jadwal Kuliah')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Detail Jadwal Kuliah</h1>
        <div>
            @if(Auth::user()->hasRole(['super_admin', 'admin_universitas', 'admin_fakultas', 'admin_prodi']))
                <a href="{{ route('jadwal-kuliah.edit', $jadwalKuliah) }}" class="btn btn-warning">
                    <i class="bi bi-pencil"></i> Edit
                </a>
            @endif
            <a href="{{ route('jadwal-kuliah.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <!-- Informasi Mata Kuliah -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-book"></i> Informasi Mata Kuliah</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="40%"><strong>Kode MK</strong></td>
                                    <td>: {{ $jadwalKuliah->kelas->mataKuliah->kode_mk }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Nama MK</strong></td>
                                    <td>: {{ $jadwalKuliah->kelas->mataKuliah->nama_mk }}</td>
                                </tr>
                                <tr>
                                    <td><strong>SKS</strong></td>
                                    <td>: {{ $jadwalKuliah->kelas->mataKuliah->sks }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Level MK</strong></td>
                                    <td>: 
                                        <span class="badge bg-{{ $jadwalKuliah->kelas->mataKuliah->level_matkul == 'universitas' ? 'primary' : ($jadwalKuliah->kelas->mataKuliah->level_matkul == 'fakultas' ? 'info' : 'success') }}">
                                            {{ ucfirst($jadwalKuliah->kelas->mataKuliah->level_matkul) }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="40%"><strong>Nama Kelas</strong></td>
                                    <td>: {{ $jadwalKuliah->kelas->nama_kelas }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Kuota</strong></td>
                                    <td>: {{ $jadwalKuliah->kelas->kuota }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Program Studi</strong></td>
                                    <td>: {{ $jadwalKuliah->kelas->mataKuliah->programStudi->nama_prodi ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Fakultas</strong></td>
                                    <td>: {{ $jadwalKuliah->kelas->mataKuliah->programStudi->fakultas->nama_fakultas ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informasi Dosen -->
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="bi bi-person"></i> Informasi Dosen</h5>
                </div>
                <div class="card-body">
                    @if($jadwalKuliah->kelas->dosen)
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <td width="40%"><strong>NIDN</strong></td>
                                        <td>: {{ $jadwalKuliah->kelas->dosen->nidn }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Nama Dosen</strong></td>
                                        <td>: {{ $jadwalKuliah->kelas->dosen->nama_dosen }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <td width="40%"><strong>Email</strong></td>
                                        <td>: {{ $jadwalKuliah->kelas->dosen->email ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>No. HP</strong></td>
                                        <td>: {{ $jadwalKuliah->kelas->dosen->no_hp ?? '-' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    @else
                        <p class="text-muted mb-0">Belum ada dosen yang ditugaskan</p>
                    @endif
                </div>
            </div>

            <!-- Jadwal dan Ruangan -->
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-clock"></i> Jadwal dan Ruangan</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="40%"><strong>Hari</strong></td>
                                    <td>: <span class="badge bg-{{ $jadwalKuliah->hari_badge }}">{{ $jadwalKuliah->hari }}</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Jam Mulai</strong></td>
                                    <td>: {{ $jadwalKuliah->jam_mulai }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Jam Selesai</strong></td>
                                    <td>: {{ $jadwalKuliah->jam_selesai }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Durasi</strong></td>
                                    <td>: {{ $jadwalKuliah->waktu }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="40%"><strong>Kode Ruang</strong></td>
                                    <td>: {{ $jadwalKuliah->ruang->kode_ruang }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Nama Ruang</strong></td>
                                    <td>: {{ $jadwalKuliah->ruang->nama_ruang }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Kapasitas</strong></td>
                                    <td>: {{ $jadwalKuliah->ruang->kapasitas }} orang</td>
                                </tr>
                                <tr>
                                    <td><strong>Tipe Ruang</strong></td>
                                    <td>: 
                                        <span class="badge bg-secondary">
                                            {{ ucfirst($jadwalKuliah->ruang->tipe_ruang) }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Semester dan Tahun Akademik -->
            <div class="card mb-4">
                <div class="card-header bg-warning">
                    <h5 class="mb-0"><i class="bi bi-calendar3"></i> Semester dan Tahun Akademik</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="40%"><strong>Tahun Akademik</strong></td>
                                    <td>: {{ $jadwalKuliah->tahunAkademik->nama ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Semester</strong></td>
                                    <td>: {{ $jadwalKuliah->semester->nama_semester ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-md-4">
            <!-- Audit Trail -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="bi bi-clock-history"></i> Audit Trail</h6>
                </div>
                <div class="card-body">
                    <small class="d-block mb-3">
                        <strong>Dibuat:</strong><br>
                        {{ $jadwalKuliah->created_at->format('d/m/Y H:i') }}<br>
                        @if($jadwalKuliah->createdBy)
                            oleh <strong>{{ $jadwalKuliah->createdBy->name }}</strong>
                        @endif
                    </small>

                    @if($jadwalKuliah->updated_at != $jadwalKuliah->created_at)
                        <small class="d-block">
                            <strong>Terakhir diubah:</strong><br>
                            {{ $jadwalKuliah->updated_at->format('d/m/Y H:i') }}<br>
                            @if($jadwalKuliah->updatedBy)
                                oleh <strong>{{ $jadwalKuliah->updatedBy->name }}</strong>
                            @endif
                        </small>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            @if(Auth::user()->hasRole(['super_admin', 'admin_universitas', 'admin_fakultas', 'admin_prodi']))
                <div class="card">
                    <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="bi bi-lightning"></i> Aksi Cepat</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('jadwal-kuliah.edit', $jadwalKuliah) }}" class="btn btn-warning btn-sm">
                                <i class="bi bi-pencil"></i> Edit Jadwal
                            </a>
                            <form action="{{ route('jadwal-kuliah.destroy', $jadwalKuliah) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus jadwal ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm w-100">
                                    <i class="bi bi-trash"></i> Hapus Jadwal
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
