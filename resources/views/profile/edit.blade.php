@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')
<div class="container-fluid">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- Left Column: User Profile -->
        <div class="col-lg-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-person-circle me-2"></i>Profil Pengguna</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        @if($user->avatar)
                            <img src="{{ $user->avatar }}" alt="Avatar" class="rounded-circle" style="width: 120px; height: 120px; object-fit: cover;">
                        @else
                            <div class="user-avatar-large mx-auto">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                        @endif
                        <h5 class="mt-3 mb-1">{{ $user->name }}</h5>
                        <span class="badge bg-primary">{{ $user->getRoleNames()->first() }}</span>
                    </div>

                    <form action="{{ route('profile.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Role</label>
                            <input type="text" class="form-control" value="{{ $user->getRoleNames()->first() }}" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Terdaftar Sejak</label>
                            <input type="text" class="form-control" value="{{ $user->created_at?->format('d M Y') }}" readonly>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-1"></i>Simpan Perubahan
                            </button>
                            <a href="{{ route('profile.edit-password') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-key me-1"></i>Ganti Password
                            </a>
                            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-1"></i>Kembali ke Dashboard
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Right Column: Academic Information -->
        <div class="col-lg-8">
            @if($mahasiswaData)
                <!-- Informasi Mahasiswa -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="bi bi-person-badge me-2"></i>Informasi Akademik</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td width="40%"><strong>NIM</strong></td>
                                        <td>: {{ $mahasiswaData['nim'] }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Nama</strong></td>
                                        <td>: {{ $mahasiswaData['nama'] }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Program Studi</strong></td>
                                        <td>: {{ $mahasiswaData['program_studi'] }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Fakultas</strong></td>
                                        <td>: {{ $mahasiswaData['fakultas'] }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td width="40%"><strong>Semester</strong></td>
                                        <td>: {{ $mahasiswaData['semester'] }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Tahun Masuk</strong></td>
                                        <td>: {{ $mahasiswaData['tahun_masuk'] }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Status</strong></td>
                                        <td>: <span class="badge bg-success">{{ $mahasiswaData['status'] }}</span></td>
                                    </tr>
                                    <tr>
                                        <td><strong>IPK</strong></td>
                                        <td>: <span class="badge bg-primary fs-6">{{ number_format($mahasiswaData['ipk'], 2) }}</span></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                @if($statistikAkademik)
                <!-- Statistik Semester Aktif -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card shadow-sm border-primary">
                            <div class="card-body text-center">
                                <i class="bi bi-book-half fs-1 text-primary"></i>
                                <h3 class="mt-2 mb-1">{{ $statistikAkademik['total_mk_semester'] }}</h3>
                                <p class="text-muted mb-0">Mata Kuliah Semester Ini</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card shadow-sm border-success">
                            <div class="card-body text-center">
                                <i class="bi bi-award fs-1 text-success"></i>
                                <h3 class="mt-2 mb-1">{{ $statistikAkademik['total_sks_semester'] }}</h3>
                                <p class="text-muted mb-0">Total SKS Semester Ini</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card shadow-sm border-info">
                            <div class="card-body text-center">
                                <i class="bi bi-calendar-check fs-1 text-info"></i>
                                <h3 class="mt-2 mb-1">{{ $statistikAkademik['semester_aktif'] }}</h3>
                                <p class="text-muted mb-0">{{ $statistikAkademik['tahun_ajaran'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                @if(count($mataKuliahData) > 0)
                <!-- Mata Kuliah & Nilai Semester Aktif -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="bi bi-journal-bookmark me-2"></i>Mata Kuliah & Nilai Semester Aktif</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th width="5%">No</th>
                                        <th width="15%">Kode MK</th>
                                        <th width="30%">Mata Kuliah</th>
                                        <th width="8%" class="text-center">SKS</th>
                                        <th width="22%">Dosen</th>
                                        <th width="10%" class="text-center">Nilai</th>
                                        <th width="10%" class="text-center">Bobot</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($mataKuliahData as $index => $mk)
                                    <tr>
                                        <td class="text-center">{{ $index + 1 }}</td>
                                        <td>{{ $mk['kode'] }}</td>
                                        <td>{{ $mk['nama'] }}</td>
                                        <td class="text-center">{{ $mk['sks'] }}</td>
                                        <td>{{ $mk['dosen'] }}</td>
                                        <td class="text-center">
                                            @if($mk['nilai_huruf'] != '-')
                                                <span class="badge 
                                                    @if($mk['nilai_huruf'] == 'A' || $mk['nilai_huruf'] == 'A-') bg-success
                                                    @elseif($mk['nilai_huruf'] == 'B+' || $mk['nilai_huruf'] == 'B' || $mk['nilai_huruf'] == 'B-') bg-primary
                                                    @elseif($mk['nilai_huruf'] == 'C+' || $mk['nilai_huruf'] == 'C') bg-warning
                                                    @elseif($mk['nilai_huruf'] == 'D') bg-danger
                                                    @else bg-secondary
                                                    @endif
                                                ">{{ $mk['nilai_huruf'] }}</span>
                                                <br><small class="text-muted">{{ is_numeric($mk['nilai_angka']) ? number_format($mk['nilai_angka'], 2) : $mk['nilai_angka'] }}</small>
                                            @else
                                                <span class="badge bg-secondary">Belum Ada</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($mk['bobot'] != '-')
                                                {{ number_format($mk['bobot'], 2) }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <th colspan="3" class="text-end">Total</th>
                                        <th class="text-center">{{ collect($mataKuliahData)->sum('sks') }}</th>
                                        <th colspan="3"></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                @endif

                @if(count($jadwalData) > 0)
                <!-- Jadwal Kuliah Semester Aktif -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0"><i class="bi bi-calendar-week me-2"></i>Jadwal Kuliah Semester Aktif</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th width="5%">No</th>
                                        <th width="12%">Hari</th>
                                        <th width="15%">Waktu</th>
                                        <th width="15%">Kode MK</th>
                                        <th width="25%">Mata Kuliah</th>
                                        <th width="8%" class="text-center">SKS</th>
                                        <th width="20%">Dosen</th>
                                        <th width="10%">Ruang</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $hariOrder = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
                                        $jadwalSorted = collect($jadwalData)->sortBy(function($item) use ($hariOrder) {
                                            return array_search($item['hari'], $hariOrder);
                                        })->values();
                                    @endphp
                                    @foreach($jadwalSorted as $index => $jadwal)
                                    <tr>
                                        <td class="text-center">{{ $index + 1 }}</td>
                                        <td>{{ $jadwal['hari'] }}</td>
                                        <td>{{ $jadwal['jam_mulai'] }} - {{ $jadwal['jam_selesai'] }}</td>
                                        <td>{{ $jadwal['kode_mk'] }}</td>
                                        <td>{{ $jadwal['mata_kuliah'] }}</td>
                                        <td class="text-center">{{ $jadwal['sks'] }}</td>
                                        <td>{{ $jadwal['dosen'] }}</td>
                                        <td>{{ $jadwal['ruang'] }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endif

                @if(count($mataKuliahData) == 0 && count($jadwalData) == 0)
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    Belum ada data mata kuliah atau jadwal untuk semester aktif. Silakan isi KRS terlebih dahulu.
                </div>
                @endif

            @elseif($dosenData)
                <!-- Informasi Dosen -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="bi bi-person-workspace me-2"></i>Informasi Dosen</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td width="40%"><strong>NIDN</strong></td>
                                        <td>: {{ $dosenData['nidn'] }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Nama</strong></td>
                                        <td>: {{ $dosenData['gelar_depan'] }} {{ $dosenData['nama'] }} {{ $dosenData['gelar_belakang'] }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Fakultas</strong></td>
                                        <td>: {{ $dosenData['fakultas'] }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td width="40%"><strong>Email</strong></td>
                                        <td>: {{ $dosenData['email'] }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Telepon</strong></td>
                                        <td>: {{ $dosenData['telepon'] }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <!-- Informasi Umum untuk Admin -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0"><i class="bi bi-shield-check me-2"></i>Informasi Administrator</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-0">
                            <i class="bi bi-info-circle me-2"></i>
                            Anda login sebagai <strong>{{ $user->getRoleNames()->first() }}</strong>.
                            Gunakan menu navigasi untuk mengakses modul-modul yang tersedia.
                        </p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.user-avatar-large {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
    font-size: 3rem;
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
}
</style>
@endsection
