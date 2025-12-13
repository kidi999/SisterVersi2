@extends('layouts.public')

@section('title', 'Profil Universitas')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body text-center py-4">
                    @if($university->logo_path)
                        <img
                            src="{{ asset('storage/' . $university->logo_path) }}"
                            alt="Logo {{ $university->nama }}"
                            class="img-fluid mb-3 bg-white border rounded p-2"
                            style="max-height: 140px;"
                        >
                    @else
                        <div class="mb-3">
                            <i class="bi bi-mortarboard-fill text-primary" style="font-size: 3rem;"></i>
                        </div>
                    @endif

                    <h1 class="h3 mb-1">{{ $university->nama }}</h1>
                    @if($university->singkatan)
                        <div class="text-muted mb-3">{{ $university->singkatan }}</div>
                    @endif

                    <div class="d-flex flex-wrap justify-content-center gap-2">
                        @if($university->jenis)
                            <span class="badge text-bg-secondary">{{ $university->jenis }}</span>
                        @endif
                        @if($university->akreditasi)
                            <span class="badge text-bg-success">Akreditasi {{ $university->akreditasi }}</span>
                        @endif
                        <span class="badge text-bg-{{ $university->status === 'Aktif' ? 'primary' : 'secondary' }}">{{ $university->status }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="row g-4">
        <div class="col-lg-8">
            <!-- Visi Section -->
            @if($university->visi)
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-eye-fill"></i> Visi</h5>
                </div>
                <div class="card-body">
                    <div class="text-body" style="line-height: 1.8;">
                        {!! nl2br(e($university->visi)) !!}
                    </div>
                </div>
            </div>
            @endif

            <!-- Misi Section -->
            @if($university->misi)
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-bullseye"></i> Misi</h5>
                </div>
                <div class="card-body">
                    <div class="text-body" style="line-height: 1.8;">
                        {!! nl2br(e($university->misi)) !!}
                    </div>
                </div>
            </div>
            @endif

            <!-- Sejarah Section -->
            @if($university->sejarah)
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="bi bi-clock-history"></i> Sejarah</h5>
                </div>
                <div class="card-body">
                    <div class="text-body" style="line-height: 1.8;">
                        {!! nl2br(e($university->sejarah)) !!}
                    </div>
                </div>
            </div>
            @endif

            <!-- Pimpinan Section -->
            @if($university->rektor || $university->wakil_rektor_1 || $university->wakil_rektor_2 || $university->wakil_rektor_3 || $university->wakil_rektor_4)
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="bi bi-people-fill"></i> Struktur Pimpinan</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @if($university->rektor)
                        <div class="col-12">
                            <div class="text-center p-3 bg-light rounded">
                                <i class="bi bi-person-badge-fill text-primary" style="font-size: 2.25rem;"></i>
                                <h6 class="fw-semibold mt-2">Rektor</h6>
                                <p class="mb-0">{{ $university->rektor }}</p>
                                @if($university->nip_rektor)
                                    <small class="text-muted">NIP: {{ $university->nip_rektor }}</small>
                                @endif
                            </div>
                        </div>
                        @endif

                        @if($university->wakil_rektor_1)
                        <div class="col-md-6">
                            <div class="text-center p-3 bg-light rounded">
                                <i class="bi bi-person-fill text-secondary" style="font-size: 1.75rem;"></i>
                                <h6 class="fw-semibold mt-2">Wakil Rektor I</h6>
                                <p class="mb-0">{{ $university->wakil_rektor_1 }}</p>
                            </div>
                        </div>
                        @endif

                        @if($university->wakil_rektor_2)
                        <div class="col-md-6">
                            <div class="text-center p-3 bg-light rounded">
                                <i class="bi bi-person-fill text-secondary" style="font-size: 1.75rem;"></i>
                                <h6 class="fw-semibold mt-2">Wakil Rektor II</h6>
                                <p class="mb-0">{{ $university->wakil_rektor_2 }}</p>
                            </div>
                        </div>
                        @endif

                        @if($university->wakil_rektor_3)
                        <div class="col-md-6">
                            <div class="text-center p-3 bg-light rounded">
                                <i class="bi bi-person-fill text-secondary" style="font-size: 1.75rem;"></i>
                                <h6 class="fw-semibold mt-2">Wakil Rektor III</h6>
                                <p class="mb-0">{{ $university->wakil_rektor_3 }}</p>
                            </div>
                        </div>
                        @endif

                        @if($university->wakil_rektor_4)
                        <div class="col-md-6">
                            <div class="text-center p-3 bg-light rounded">
                                <i class="bi bi-person-fill text-secondary" style="font-size: 1.75rem;"></i>
                                <h6 class="fw-semibold mt-2">Wakil Rektor IV</h6>
                                <p class="mb-0">{{ $university->wakil_rektor_4 }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0"><i class="bi bi-info-circle-fill"></i> Informasi Umum</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm mb-0">
                        <tr>
                            <td width="40%"><strong>Kode</strong></td>
                            <td>{{ $university->kode }}</td>
                        </tr>
                        <tr>
                            <td><strong>Jenis</strong></td>
                            <td><span class="badge text-bg-secondary">{{ $university->jenis }}</span></td>
                        </tr>
                        <tr>
                            <td><strong>Status</strong></td>
                            <td>
                                <span class="badge text-bg-{{ $university->status === 'Aktif' ? 'primary' : 'secondary' }}">
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
            <div class="card shadow-sm mt-4">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0"><i class="bi bi-award-fill"></i> Akreditasi</h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div class="display-5 fw-bold text-success">{{ $university->akreditasi }}</div>
                    </div>
                    @if($university->no_sk_akreditasi)
                    <table class="table table-sm mb-0">
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
            <div class="card shadow-sm mt-4">
                <div class="card-header bg-warning text-dark">
                    <h6 class="mb-0"><i class="bi bi-telephone-fill"></i> Kontak</h6>
                </div>
                <div class="card-body">
                    @if($university->email)
                    <div class="mb-3">
                        <i class="bi bi-envelope-fill text-primary"></i>
                        <strong>Email:</strong><br>
                        <a href="mailto:{{ $university->email }}">{{ $university->email }}</a>
                    </div>
                    @endif

                    @if($university->telepon)
                    <div class="mb-3">
                        <i class="bi bi-telephone-fill text-success"></i>
                        <strong>Telepon:</strong><br>
                        {{ $university->telepon }}
                    </div>
                    @endif

                    @if($university->fax)
                    <div class="mb-3">
                        <i class="bi bi-printer-fill text-info"></i>
                        <strong>Fax:</strong><br>
                        {{ $university->fax }}
                    </div>
                    @endif

                    @if($university->website)
                    <div class="mb-0">
                        <i class="bi bi-globe2 text-danger"></i>
                        <strong>Website:</strong><br>
                        <a href="{{ $university->website }}" target="_blank">{{ $university->website }}</a>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Alamat -->
            <div class="card shadow-sm mt-4">
                <div class="card-header bg-danger text-white">
                    <h6 class="mb-0"><i class="bi bi-geo-alt-fill"></i> Alamat</h6>
                </div>
                <div class="card-body">
                    @if($university->alamat)
                    <p class="mb-3">{{ $university->alamat }}</p>
                    @endif

                    @if($university->village)
                    <table class="table table-sm mb-0">
                        <tr>
                            <td><i class="bi bi-pin-map-fill text-primary"></i> Desa/Kelurahan</td>
                            <td>{{ $university->village->name }}</td>
                        </tr>
                        <tr>
                            <td><i class="bi bi-signpost-2-fill text-success"></i> Kecamatan</td>
                            <td>{{ $university->village->subRegency->name }}</td>
                        </tr>
                        <tr>
                            <td><i class="bi bi-buildings-fill text-info"></i> Kabupaten/Kota</td>
                            <td>{{ $university->village->subRegency->regency->name }}</td>
                        </tr>
                        <tr>
                            <td><i class="bi bi-flag-fill text-warning"></i> Provinsi</td>
                            <td>{{ $university->village->subRegency->regency->province->name }}</td>
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
            <div class="card shadow-sm mt-4">
                <div class="card-header bg-secondary text-white">
                    <h6 class="mb-0"><i class="bi bi-patch-check-fill"></i> Legalitas</h6>
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

    <div class="row">
        <div class="col-12 text-center mt-4">
            <a href="{{ route('welcome') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Kembali ke Beranda
            </a>
        </div>
    </div>
</div>
@endsection
