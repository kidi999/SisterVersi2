@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Detail KRS</h1>
        <a href="{{ route('krs.index') }}" class="btn btn-secondary">
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

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Informasi KRS</h5>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th width="200">NIM</th>
                            <td>{{ $kr->mahasiswa->nim }}</td>
                        </tr>
                        <tr>
                            <th>Nama Mahasiswa</th>
                            <td>{{ $kr->mahasiswa->nama }}</td>
                        </tr>
                        <tr>
                            <th>Program Studi</th>
                            <td>{{ $kr->mahasiswa->programStudi->nama }}</td>
                        </tr>
                        <tr>
                            <th>Kode Kelas</th>
                            <td>
                                <span class="badge bg-secondary">{{ $kr->kelas->kode_kelas }}</span>
                            </td>
                        </tr>
                        <tr>
                            <th>Nama Kelas</th>
                            <td>{{ $kr->kelas->nama_kelas }}</td>
                        </tr>
                        <tr>
                            <th>Mata Kuliah</th>
                            <td>
                                <strong>{{ $kr->kelas->mataKuliah->nama }}</strong>
                                <br>
                                <small class="text-muted">{{ $kr->kelas->mataKuliah->kode }}</small>
                            </td>
                        </tr>
                        <tr>
                            <th>SKS</th>
                            <td>{{ $kr->kelas->mataKuliah->sks }}</td>
                        </tr>
                        <tr>
                            <th>Dosen Pengampu</th>
                            <td>
                                {{ $kr->kelas->dosen->nama ?? '-' }}
                                @if($kr->kelas->dosen)
                                <br>
                                <small class="text-muted">NIDN: {{ $kr->kelas->dosen->nidn }}</small>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Tahun Ajaran</th>
                            <td>{{ $kr->tahun_ajaran }}</td>
                        </tr>
                        <tr>
                            <th>Semester</th>
                            <td>
                                <span class="badge {{ $kr->semester == 'Ganjil' ? 'bg-info' : 'bg-warning' }}">
                                    {{ $kr->semester }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                @if($kr->status == 'Menunggu')
                                    <span class="badge bg-warning text-dark">Menunggu Persetujuan</span>
                                @elseif($kr->status == 'Disetujui')
                                    <span class="badge bg-success">Disetujui</span>
                                @else
                                    <span class="badge bg-danger">Ditolak</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Tanggal Pengajuan</th>
                            <td>{{ \Carbon\Carbon::parse($kr->tanggal_pengajuan)->format('d F Y H:i') }}</td>
                        </tr>
                        @if($kr->tanggal_persetujuan)
                        <tr>
                            <th>Tanggal Persetujuan</th>
                            <td>{{ \Carbon\Carbon::parse($kr->tanggal_persetujuan)->format('d F Y H:i') }}</td>
                        </tr>
                        @endif
                    </table>

                    @if($kr->nilai)
                    <div class="mt-4">
                        <h6>Nilai</h6>
                        <table class="table table-bordered">
                            <tr>
                                <th width="200">Nilai Angka</th>
                                <td>{{ $kr->nilai->nilai }}</td>
                            </tr>
                            <tr>
                                <th>Nilai Huruf</th>
                                <td>
                                    <span class="badge bg-primary">{{ $kr->nilai->grade }}</span>
                                </td>
                            </tr>
                        </table>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Action Buttons for Admin/Dosen -->
            @if(in_array(Auth::user()->role->name, ['admin_prodi', 'admin_fakultas', 'admin_universitas', 'dosen']) && $kr->status == 'Menunggu')
            <div class="card">
                <div class="card-header bg-warning">
                    <h5 class="card-title mb-0">Aksi Persetujuan</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex gap-2">
                        <form action="{{ route('krs.approve', $kr->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="btn btn-success" 
                                    onclick="return confirm('Setujui KRS ini?')">
                                <i class="bi bi-check-circle"></i> Setujui
                            </button>
                        </form>
                        
                        <form action="{{ route('krs.reject', $kr->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="btn btn-danger" 
                                    onclick="return confirm('Tolak KRS ini?')">
                                <i class="bi bi-x-circle"></i> Tolak
                            </button>
                        </form>
                    </div>
                    <small class="text-muted d-block mt-2">
                        <i class="bi bi-info-circle"></i> Pastikan Anda memeriksa kelayakan mata kuliah yang diambil mahasiswa
                    </small>
                </div>
            </div>
            @endif
        </div>

        <div class="col-md-4">
            <!-- Audit Trail -->
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-clock-history"></i> Audit Trail
                    </h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <td><strong>Dibuat oleh</strong></td>
                            <td>{{ $kr->inserted_by ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Dibuat pada</strong></td>
                            <td>
                                @if($kr->inserted_at)
                                {{ \Carbon\Carbon::parse($kr->inserted_at)->format('d/m/Y H:i') }}
                                @else
                                -
                                @endif
                            </td>
                        </tr>
                        @if($kr->updated_by)
                        <tr>
                            <td><strong>Diubah oleh</strong></td>
                            <td>{{ $kr->updated_by }}</td>
                        </tr>
                        <tr>
                            <td><strong>Diubah pada</strong></td>
                            <td>{{ \Carbon\Carbon::parse($kr->updated_at)->format('d/m/Y H:i') }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>

            @if(Auth::user()->role->name == 'mahasiswa' && $kr->status == 'Menunggu')
            <div class="card mt-3">
                <div class="card-header bg-danger text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-exclamation-triangle"></i> Batalkan KRS
                    </h5>
                </div>
                <div class="card-body">
                    <p class="small">Anda dapat membatalkan KRS selama status masih Menunggu</p>
                    <form action="{{ route('krs.destroy', $kr->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger w-100" 
                                onclick="return confirm('Yakin ingin membatalkan KRS ini?')">
                            <i class="bi bi-trash"></i> Batalkan KRS
                        </button>
                    </form>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
