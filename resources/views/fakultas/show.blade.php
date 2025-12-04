@extends('layouts.app')

@section('title', 'Detail Fakultas')
@section('header', 'Detail Fakultas')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Detail Fakultas</h5>
        <div>
            <a href="{{ route('fakultas.edit', $fakultas->id) }}" class="btn btn-warning">
                <i class="bi bi-pencil"></i> Edit
            </a>
            <a href="{{ route('fakultas.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-bordered">
                    <tr>
                        <th width="40%" class="bg-light">Kode Fakultas</th>
                        <td><span class="badge bg-primary">{{ $fakultas->kode_fakultas }}</span></td>
                    </tr>
                    <tr>
                        <th class="bg-light">Nama Fakultas</th>
                        <td><strong>{{ $fakultas->nama_fakultas }}</strong></td>
                    </tr>
                    <tr>
                        <th class="bg-light">Singkatan</th>
                        <td>{{ $fakultas->singkatan }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Dekan Aktif</th>
                        <td>
                            @if($fakultas->dekanAktif)
                                <strong>{{ $fakultas->dekanAktif->nama_dosen }}</strong><br>
                                <small class="text-muted">NIDN: {{ $fakultas->dekanAktif->nidn }}</small>
                            @else
                                <span class="text-muted">Belum ada dekan</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th class="bg-light">Email</th>
                        <td>{{ $fakultas->email ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Telepon</th>
                        <td>{{ $fakultas->telepon ?? '-' }}</td>
                    </tr>
                </table>
            </div>
            
            <div class="col-md-6">
                <table class="table table-bordered">
                    <tr>
                        <th colspan="2" class="bg-light text-center">
                            <i class="bi bi-geo-alt-fill"></i> Informasi Alamat
                        </th>
                    </tr>
                    <tr>
                        <th width="40%" class="bg-light">Alamat Lengkap</th>
                        <td>{{ $fakultas->alamat ?? '-' }}</td>
                    </tr>
                    @if($fakultas->village)
                    <tr>
                        <th class="bg-light">Desa/Kelurahan</th>
                        <td>{{ $fakultas->village->name }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Kecamatan</th>
                        <td>{{ $fakultas->village->subRegency->name }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Kabupaten/Kota</th>
                        <td>{{ $fakultas->village->subRegency->regency->name }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Provinsi</th>
                        <td>{{ $fakultas->village->subRegency->regency->province->name }}</td>
                    </tr>
                    @else
                    <tr>
                        <th class="bg-light">Wilayah</th>
                        <td class="text-muted">Data wilayah belum diisi</td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-12">
                <h6 class="border-bottom pb-2 mb-3">
                    <i class="bi bi-list-ul"></i> Program Studi ({{ $fakultas->programStudi->count() }})
                </h6>
                
                @if($fakultas->programStudi->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Kode</th>
                                    <th>Nama Program Studi</th>
                                    <th>Jenjang</th>
                                    <th>Akreditasi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($fakultas->programStudi as $index => $prodi)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td><span class="badge bg-info">{{ $prodi->kode_prodi }}</span></td>
                                    <td>{{ $prodi->nama_prodi }}</td>
                                    <td>{{ $prodi->jenjang }}</td>
                                    <td>
                                        @if($prodi->akreditasi)
                                            <span class="badge bg-success">{{ $prodi->akreditasi }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> Belum ada program studi terdaftar
                    </div>
                @endif
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-12">
                <h6 class="border-bottom pb-2 mb-3">
                    <i class="bi bi-award"></i> Riwayat Dekan ({{ $fakultas->riwayatDekan->count() }})
                </h6>
                
                @if($fakultas->riwayatDekan->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>Nama Dekan</th>
                                    <th>NIDN</th>
                                    <th>Nomor SK</th>
                                    <th>Periode</th>
                                    <th>Durasi</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($fakultas->riwayatDekan as $jabatan)
                                <tr>
                                    <td><strong>{{ $jabatan->dosen->nama_dosen }}</strong></td>
                                    <td>{{ $jabatan->dosen->nidn }}</td>
                                    <td>
                                        <small>{{ $jabatan->nomor_sk }}</small><br>
                                        <small class="text-muted">{{ $jabatan->tanggal_sk->format('d/m/Y') }}</small>
                                    </td>
                                    <td>
                                        {{ $jabatan->tanggal_mulai->format('d/m/Y') }} - 
                                        {{ $jabatan->tanggal_selesai ? $jabatan->tanggal_selesai->format('d/m/Y') : 'Sekarang' }}
                                    </td>
                                    <td>{{ $jabatan->durasi }}</td>
                                    <td>
                                        <span class="badge bg-{{ $jabatan->status_badge }}">
                                            {{ ucfirst($jabatan->status) }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> Belum ada riwayat dekan
                    </div>
                @endif
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-12">
                <h6 class="border-bottom pb-2 mb-3">
                    <i class="bi bi-paperclip"></i> Lampiran File ({{ $fakultas->files->count() }})
                </h6>
                
                @if($fakultas->files->count() > 0)
                    <div class="row g-3">
                        @foreach($fakultas->files as $file)
                        <div class="col-md-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <div class="d-flex align-items-start">
                                        <i class="bi {{ $file->icon_class }} fs-2 text-primary me-3"></i>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1 text-truncate" title="{{ $file->file_name }}">
                                                {{ $file->file_name }}
                                            </h6>
                                            <p class="small text-muted mb-2">
                                                <i class="bi bi-hdd"></i> {{ $file->formatted_size }}<br>
                                                <i class="bi bi-calendar"></i> {{ $file->created_at ? $file->created_at->format('d/m/Y H:i') : '-' }}<br>
                                                <i class="bi bi-person"></i> {{ $file->created_by ?? '-' }}
                                            </p>
                                            @if($file->description)
                                            <p class="small mb-2">{{ $file->description }}</p>
                                            @endif
                                            <a href="{{ route('api.file-upload.download', $file->id) }}" 
                                               class="btn btn-sm btn-primary" target="_blank">
                                                <i class="bi bi-download"></i> Download
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> Tidak ada file terlampir
                    </div>
                @endif
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-12">
                <h6 class="border-bottom pb-2 mb-3">
                    <i class="bi bi-clock-history"></i> Audit Trail
                </h6>
                <div class="row">
                    <div class="col-md-6">
                        <small class="text-muted">
                            <strong>Dibuat oleh:</strong> {{ $fakultas->created_by ?? '-' }}<br>
                            <strong>Tanggal dibuat:</strong> {{ $fakultas->created_at ? $fakultas->created_at->format('d/m/Y H:i:s') : '-' }}
                        </small>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted">
                            <strong>Diupdate oleh:</strong> {{ $fakultas->updated_by ?? '-' }}<br>
                            <strong>Tanggal update:</strong> {{ $fakultas->updated_at ? $fakultas->updated_at->format('d/m/Y H:i:s') : '-' }}
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
