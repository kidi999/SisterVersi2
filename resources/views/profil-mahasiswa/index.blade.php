@extends('layouts.app')

@section('title', 'Profil Mahasiswa - SISTER')
@section('header', 'Profil Mahasiswa')

@section('content')
<div class="row">
    <div class="col-md-12 mb-3">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h4 class="mb-0">{{ $mahasiswa->nama_mahasiswa }}</h4>
                <p class="text-muted mb-0">NIM: {{ $mahasiswa->nim }}</p>
            </div>
            <div>
                <a href="{{ route('profil-mahasiswa.edit') }}" class="btn btn-primary">
                    <i class="bi bi-pencil"></i> Edit Profil
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Data Pribadi -->
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-person-fill"></i> Data Pribadi</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <td width="40%" class="text-muted">Nama Lengkap</td>
                        <td width="5%">:</td>
                        <td><strong>{{ $mahasiswa->nama_mahasiswa }}</strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted">NIM</td>
                        <td>:</td>
                        <td><strong>{{ $mahasiswa->nim }}</strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Jenis Kelamin</td>
                        <td>:</td>
                        <td>
                            @if($mahasiswa->jenis_kelamin == 'L')
                                <span class="badge bg-info">Laki-laki</span>
                            @else
                                <span class="badge bg-pink">Perempuan</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Tempat, Tanggal Lahir</td>
                        <td>:</td>
                        <td>
                            {{ $mahasiswa->tempat_lahir ?? '-' }}, 
                            {{ $mahasiswa->tanggal_lahir ? $mahasiswa->tanggal_lahir->format('d F Y') : '-' }}
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Email</td>
                        <td>:</td>
                        <td>{{ $mahasiswa->email }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Telepon</td>
                        <td>:</td>
                        <td>{{ $mahasiswa->telepon ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted align-top">Alamat</td>
                        <td class="align-top">:</td>
                        <td>{{ $mahasiswa->alamat ?? '-' }}</td>
                    </tr>
                    @if($mahasiswa->village)
                    <tr>
                        <td class="text-muted align-top">Desa/Kelurahan</td>
                        <td class="align-top">:</td>
                        <td>{{ $mahasiswa->village->name }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted align-top">Kecamatan</td>
                        <td class="align-top">:</td>
                        <td>{{ $mahasiswa->village->subRegency->name }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted align-top">Kabupaten/Kota</td>
                        <td class="align-top">:</td>
                        <td>{{ $mahasiswa->village->subRegency->regency->name }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted align-top">Provinsi</td>
                        <td class="align-top">:</td>
                        <td>{{ $mahasiswa->village->subRegency->regency->province->name }}</td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>
    </div>

    <!-- Data Studi -->
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="bi bi-book-fill"></i> Data Studi</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <td width="40%" class="text-muted">Program Studi</td>
                        <td width="5%">:</td>
                        <td><strong>{{ $mahasiswa->programStudi->nama_program_studi ?? '-' }}</strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Jenjang</td>
                        <td>:</td>
                        <td>
                            <span class="badge bg-info">{{ $mahasiswa->programStudi->jenjang ?? '-' }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Fakultas</td>
                        <td>:</td>
                        <td>{{ $mahasiswa->programStudi->fakultas->nama_fakultas ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Tahun Masuk</td>
                        <td>:</td>
                        <td>{{ $mahasiswa->tahun_masuk }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Semester</td>
                        <td>:</td>
                        <td>
                            <span class="badge bg-primary">Semester {{ $mahasiswa->semester }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">IPK</td>
                        <td>:</td>
                        <td>
                            <h4 class="mb-0 text-primary">{{ number_format($mahasiswa->ipk, 2) }}</h4>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Status</td>
                        <td>:</td>
                        <td>
                            @if($mahasiswa->status == 'Aktif')
                                <span class="badge bg-success"><i class="bi bi-check-circle"></i> Aktif</span>
                            @elseif($mahasiswa->status == 'Cuti')
                                <span class="badge bg-warning"><i class="bi bi-pause-circle"></i> Cuti</span>
                            @elseif($mahasiswa->status == 'Lulus')
                                <span class="badge bg-primary"><i class="bi bi-mortarboard"></i> Lulus</span>
                            @else
                                <span class="badge bg-danger"><i class="bi bi-x-circle"></i> {{ $mahasiswa->status }}</span>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Data Wali -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0"><i class="bi bi-people-fill"></i> Data Wali/Orang Tua</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <td width="40%" class="text-muted">Nama Wali</td>
                        <td width="5%">:</td>
                        <td>{{ $mahasiswa->nama_wali ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Telepon Wali</td>
                        <td>:</td>
                        <td>{{ $mahasiswa->telepon_wali ?? '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <!-- Info Akun -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="bi bi-shield-lock-fill"></i> Keamanan Akun</h5>
            </div>
            <div class="card-body">
                <p class="text-muted mb-3">Jaga keamanan akun Anda dengan mengubah password secara berkala.</p>
                <a href="{{ route('profil-mahasiswa.edit-password') }}" class="btn btn-info">
                    <i class="bi bi-key"></i> Ubah Password
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .badge.bg-pink {
        background-color: #e91e63 !important;
    }
</style>
@endpush
