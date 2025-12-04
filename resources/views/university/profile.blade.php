@extends('layouts.app')

@section('title', 'Profil Universitas')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-lg-12">
            <div class="card shadow-lg border-0">
                <div class="card-body text-center py-5" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    @if($university->logo_path)
                        <img src="{{ asset('storage/' . $university->logo_path) }}" alt="Logo {{ $university->nama }}" 
                             class="img-fluid mb-4" style="max-height: 150px; background: white; padding: 15px; border-radius: 10px;">
                    @else
                        <div class="mb-4">
                            <i class="fas fa-university fa-5x text-white"></i>
                        </div>
                    @endif
                    <h1 class="text-white font-weight-bold mb-2">{{ $university->nama }}</h1>
                    @if($university->singkatan)
                        <h4 class="text-white-50 mb-3">{{ $university->singkatan }}</h4>
                    @endif
                    <div class="d-inline-flex flex-wrap justify-content-center gap-2 mt-3">
                        <span class="badge badge-light badge-pill px-3 py-2 m-1">
                            <i class="fas fa-building"></i> {{ $university->jenis }}
                        </span>
                        @if($university->akreditasi)
                        <span class="badge badge-success badge-pill px-3 py-2 m-1">
                            <i class="fas fa-award"></i> Akreditasi {{ $university->akreditasi }}
                        </span>
                        @endif
                        <span class="badge badge-{{ $university->status == 'Aktif' ? 'success' : 'secondary' }} badge-pill px-3 py-2 m-1">
                            <i class="fas fa-circle"></i> {{ $university->status }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="row">
        <!-- Left Column -->
        <div class="col-lg-8">
            <!-- Visi Section -->
            @if($university->visi)
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-eye"></i> Visi</h5>
                </div>
                <div class="card-body">
                    <p class="text-justify" style="line-height: 1.8;">
                        {!! nl2br(e($university->visi)) !!}
                    </p>
                </div>
            </div>
            @endif

            <!-- Misi Section -->
            @if($university->misi)
            <div class="card shadow mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-bullseye"></i> Misi</h5>
                </div>
                <div class="card-body">
                    <p class="text-justify" style="line-height: 1.8;">
                        {!! nl2br(e($university->misi)) !!}
                    </p>
                </div>
            </div>
            @endif

            <!-- Sejarah Section -->
            @if($university->sejarah)
            <div class="card shadow mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-history"></i> Sejarah</h5>
                </div>
                <div class="card-body">
                    <p class="text-justify" style="line-height: 1.8;">
                        {!! nl2br(e($university->sejarah)) !!}
                    </p>
                </div>
            </div>
            @endif

            <!-- Pimpinan Section -->
            @if($university->rektor || $university->wakil_rektor_1 || $university->wakil_rektor_2 || $university->wakil_rektor_3 || $university->wakil_rektor_4)
            <div class="card shadow mb-4">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="fas fa-users"></i> Struktur Pimpinan</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @if($university->rektor)
                        <div class="col-md-12 mb-4">
                            <div class="text-center p-3 bg-light rounded">
                                <i class="fas fa-user-tie fa-3x text-primary mb-2"></i>
                                <h6 class="font-weight-bold">Rektor</h6>
                                <p class="mb-0">{{ $university->rektor }}</p>
                                @if($university->nip_rektor)
                                    <small class="text-muted">NIP: {{ $university->nip_rektor }}</small>
                                @endif
                            </div>
                        </div>
                        @endif

                        @if($university->wakil_rektor_1)
                        <div class="col-md-6 mb-3">
                            <div class="text-center p-3 bg-light rounded">
                                <i class="fas fa-user fa-2x text-secondary mb-2"></i>
                                <h6 class="font-weight-bold">Wakil Rektor I</h6>
                                <p class="mb-0">{{ $university->wakil_rektor_1 }}</p>
                            </div>
                        </div>
                        @endif

                        @if($university->wakil_rektor_2)
                        <div class="col-md-6 mb-3">
                            <div class="text-center p-3 bg-light rounded">
                                <i class="fas fa-user fa-2x text-secondary mb-2"></i>
                                <h6 class="font-weight-bold">Wakil Rektor II</h6>
                                <p class="mb-0">{{ $university->wakil_rektor_2 }}</p>
                            </div>
                        </div>
                        @endif

                        @if($university->wakil_rektor_3)
                        <div class="col-md-6 mb-3">
                            <div class="text-center p-3 bg-light rounded">
                                <i class="fas fa-user fa-2x text-secondary mb-2"></i>
                                <h6 class="font-weight-bold">Wakil Rektor III</h6>
                                <p class="mb-0">{{ $university->wakil_rektor_3 }}</p>
                            </div>
                        </div>
                        @endif

                        @if($university->wakil_rektor_4)
                        <div class="col-md-6 mb-3">
                            <div class="text-center p-3 bg-light rounded">
                                <i class="fas fa-user fa-2x text-secondary mb-2"></i>
                                <h6 class="font-weight-bold">Wakil Rektor IV</h6>
                                <p class="mb-0">{{ $university->wakil_rektor_4 }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Right Column -->
        <div class="col-lg-4">
            <!-- Informasi Umum -->
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0"><i class="fas fa-info-circle"></i> Informasi Umum</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td width="40%"><strong>Kode</strong></td>
                            <td>{{ $university->kode }}</td>
                        </tr>
                        <tr>
                            <td><strong>Jenis</strong></td>
                            <td><span class="badge badge-info">{{ $university->jenis }}</span></td>
                        </tr>
                        <tr>
                            <td><strong>Status</strong></td>
                            <td>
                                <span class="badge badge-{{ $university->status == 'Aktif' ? 'success' : 'secondary' }}">
                                    {{ $university->status }}
                                </span>
                            </td>
                        </tr>
                        @if($university->tanggal_pendirian)
                        <tr>
                            <td><strong>Didirikan</strong></td>
                            <td>{{ \Carbon\Carbon::parse($university->tanggal_pendirian)->format('d F Y') }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>

            <!-- Akreditasi -->
            @if($university->akreditasi)
            <div class="card shadow mb-4">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0"><i class="fas fa-award"></i> Akreditasi</h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div class="display-4 font-weight-bold text-success">{{ $university->akreditasi }}</div>
                    </div>
                    @if($university->no_sk_akreditasi)
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td width="40%"><strong>No. SK</strong></td>
                            <td>{{ $university->no_sk_akreditasi }}</td>
                        </tr>
                        @if($university->tanggal_akreditasi)
                        <tr>
                            <td><strong>Tanggal</strong></td>
                            <td>{{ \Carbon\Carbon::parse($university->tanggal_akreditasi)->format('d F Y') }}</td>
                        </tr>
                        @endif
                        @if($university->tanggal_berakhir_akreditasi)
                        <tr>
                            <td><strong>Berlaku s/d</strong></td>
                            <td>{{ \Carbon\Carbon::parse($university->tanggal_berakhir_akreditasi)->format('d F Y') }}</td>
                        </tr>
                        @endif
                    </table>
                    @endif
                </div>
            </div>
            @endif

            <!-- Kontak -->
            <div class="card shadow mb-4">
                <div class="card-header bg-warning text-dark">
                    <h6 class="mb-0"><i class="fas fa-phone"></i> Kontak</h6>
                </div>
                <div class="card-body">
                    @if($university->email)
                    <div class="mb-3">
                        <i class="fas fa-envelope text-primary"></i>
                        <strong>Email:</strong><br>
                        <a href="mailto:{{ $university->email }}">{{ $university->email }}</a>
                    </div>
                    @endif

                    @if($university->telepon)
                    <div class="mb-3">
                        <i class="fas fa-phone text-success"></i>
                        <strong>Telepon:</strong><br>
                        {{ $university->telepon }}
                    </div>
                    @endif

                    @if($university->fax)
                    <div class="mb-3">
                        <i class="fas fa-fax text-info"></i>
                        <strong>Fax:</strong><br>
                        {{ $university->fax }}
                    </div>
                    @endif

                    @if($university->website)
                    <div class="mb-0">
                        <i class="fas fa-globe text-danger"></i>
                        <strong>Website:</strong><br>
                        <a href="{{ $university->website }}" target="_blank">{{ $university->website }}</a>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Alamat -->
            <div class="card shadow mb-4">
                <div class="card-header bg-danger text-white">
                    <h6 class="mb-0"><i class="fas fa-map-marker-alt"></i> Alamat</h6>
                </div>
                <div class="card-body">
                    @if($university->alamat)
                    <p class="mb-3">{{ $university->alamat }}</p>
                    @endif

                    @if($university->village)
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td><i class="fas fa-map-pin text-primary"></i> Desa/Kelurahan</td>
                            <td>{{ $university->village->nama }}</td>
                        </tr>
                        <tr>
                            <td><i class="fas fa-map-signs text-success"></i> Kecamatan</td>
                            <td>{{ $university->village->subRegency->nama }}</td>
                        </tr>
                        <tr>
                            <td><i class="fas fa-city text-info"></i> Kabupaten/Kota</td>
                            <td>{{ $university->village->subRegency->regency->nama }}</td>
                        </tr>
                        <tr>
                            <td><i class="fas fa-flag text-warning"></i> Provinsi</td>
                            <td>{{ $university->village->subRegency->regency->province->nama }}</td>
                        </tr>
                    </table>
                    @endif

                    @if($university->kode_pos)
                    <div class="mt-2">
                        <i class="fas fa-mailbox text-secondary"></i>
                        <strong>Kode Pos:</strong> {{ $university->kode_pos }}
                    </div>
                    @endif
                </div>
            </div>

            <!-- Pendirian -->
            @if($university->no_sk_pendirian || $university->no_izin_operasional)
            <div class="card shadow mb-4">
                <div class="card-header bg-secondary text-white">
                    <h6 class="mb-0"><i class="fas fa-certificate"></i> Legalitas</h6>
                </div>
                <div class="card-body">
                    @if($university->no_sk_pendirian)
                    <div class="mb-3">
                        <strong>SK Pendirian:</strong><br>
                        {{ $university->no_sk_pendirian }}
                        @if($university->tanggal_pendirian)
                            <br><small class="text-muted">{{ \Carbon\Carbon::parse($university->tanggal_pendirian)->format('d F Y') }}</small>
                        @endif
                    </div>
                    @endif

                    @if($university->no_izin_operasional)
                    <div class="mb-0">
                        <strong>Izin Operasional:</strong><br>
                        {{ $university->no_izin_operasional }}
                        @if($university->tanggal_izin_operasional)
                            <br><small class="text-muted">{{ \Carbon\Carbon::parse($university->tanggal_izin_operasional)->format('d F Y') }}</small>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Back Button -->
    <div class="row">
        <div class="col-12 text-center mb-4">
            <a href="{{ route('universities.index') }}" class="btn btn-secondary btn-lg">
                <i class="fas fa-arrow-left"></i> Kembali ke Daftar
            </a>
        </div>
    </div>
</div>

@push('styles')
<style>
.badge-pill {
    font-size: 0.9rem;
}
.card {
    transition: transform 0.2s;
}
.card:hover {
    transform: translateY(-5px);
}
</style>
@endpush
@endsection
