@extends('layouts.app')

@section('title', 'Detail Universitas')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detail Universitas</h1>
        <div>
            @if(Auth::user()->hasRole(['super_admin', 'admin_universitas']))
            <a href="{{ route('universities.edit', $university->id) }}" class="btn btn-warning btn-icon-split">
                <span class="icon text-white-50">
                    <i class="fas fa-edit"></i>
                </span>
                <span class="text">Edit</span>
            </a>
            <button type="button" class="btn btn-danger btn-icon-split" data-toggle="modal" data-target="#deleteModal">
                <span class="icon text-white-50">
                    <i class="fas fa-trash"></i>
                </span>
                <span class="text">Hapus</span>
            </button>
            @endif
            <a href="{{ route('universities.index') }}" class="btn btn-secondary btn-icon-split">
                <span class="icon text-white-50">
                    <i class="fas fa-arrow-left"></i>
                </span>
                <span class="text">Kembali</span>
            </a>
        </div>
    </div>

    <!-- Logo and Main Info -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Universitas</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 text-center">
                            @if($university->logo_path)
                                <img src="{{ asset('storage/' . $university->logo_path) }}" alt="Logo {{ $university->nama }}" 
                                     class="img-fluid mb-3" style="max-height: 200px;">
                            @else
                                <div class="bg-light p-4 mb-3" style="height: 200px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-university fa-5x text-secondary"></i>
                                </div>
                            @endif
                            <h5 class="font-weight-bold">{{ $university->nama }}</h5>
                            @if($university->singkatan)
                                <p class="text-muted">{{ $university->singkatan }}</p>
                            @endif
                        </div>
                        <div class="col-md-9">
                            <!-- Tab Navigation -->
                            <ul class="nav nav-tabs" id="universityTab" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="identitas-tab" data-toggle="tab" href="#identitas" role="tab">
                                        <i class="fas fa-info-circle"></i> Identitas
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="akreditasi-tab" data-toggle="tab" href="#akreditasi" role="tab">
                                        <i class="fas fa-award"></i> Akreditasi
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="pendirian-tab" data-toggle="tab" href="#pendirian" role="tab">
                                        <i class="fas fa-certificate"></i> Pendirian
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="pimpinan-tab" data-toggle="tab" href="#pimpinan" role="tab">
                                        <i class="fas fa-users"></i> Pimpinan
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="kontak-tab" data-toggle="tab" href="#kontak" role="tab">
                                        <i class="fas fa-address-book"></i> Kontak & Alamat
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="info-tab" data-toggle="tab" href="#info" role="tab">
                                        <i class="fas fa-file-alt"></i> Visi/Misi/Sejarah
                                    </a>
                                </li>
                            </ul>

                            <!-- Tab Content -->
                            <div class="tab-content mt-3" id="universityTabContent">
                                <!-- Identitas Tab -->
                                <div class="tab-pane fade show active" id="identitas" role="tabpanel">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th width="30%" class="bg-light">Kode</th>
                                            <td><span class="badge badge-primary">{{ $university->kode }}</span></td>
                                        </tr>
                                        <tr>
                                            <th class="bg-light">Nama Universitas</th>
                                            <td><strong>{{ $university->nama }}</strong></td>
                                        </tr>
                                        <tr>
                                            <th class="bg-light">Singkatan</th>
                                            <td>{{ $university->singkatan ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th class="bg-light">Jenis</th>
                                            <td><span class="badge badge-info">{{ $university->jenis }}</span></td>
                                        </tr>
                                        <tr>
                                            <th class="bg-light">Status</th>
                                            <td>
                                                @if($university->status == 'Aktif')
                                                    <span class="badge badge-success">{{ $university->status }}</span>
                                                @else
                                                    <span class="badge badge-secondary">{{ $university->status }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </div>

                                <!-- Akreditasi Tab -->
                                <div class="tab-pane fade" id="akreditasi" role="tabpanel">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th width="30%" class="bg-light">Akreditasi</th>
                                            <td>
                                                @if($university->akreditasi)
                                                    <span class="badge badge-success">{{ $university->akreditasi }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="bg-light">No. SK Akreditasi</th>
                                            <td>{{ $university->no_sk_akreditasi ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th class="bg-light">Tanggal Akreditasi</th>
                                            <td>{{ $university->tanggal_akreditasi ? \Carbon\Carbon::parse($university->tanggal_akreditasi)->format('d F Y') : '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th class="bg-light">Tanggal Berakhir Akreditasi</th>
                                            <td>{{ $university->tanggal_berakhir_akreditasi ? \Carbon\Carbon::parse($university->tanggal_berakhir_akreditasi)->format('d F Y') : '-' }}</td>
                                        </tr>
                                    </table>
                                </div>

                                <!-- Pendirian Tab -->
                                <div class="tab-pane fade" id="pendirian" role="tabpanel">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th width="30%" class="bg-light">No. SK Pendirian</th>
                                            <td>{{ $university->no_sk_pendirian ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th class="bg-light">Tanggal Pendirian</th>
                                            <td>{{ $university->tanggal_pendirian ? \Carbon\Carbon::parse($university->tanggal_pendirian)->format('d F Y') : '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th class="bg-light">No. Izin Operasional</th>
                                            <td>{{ $university->no_izin_operasional ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th class="bg-light">Tanggal Izin Operasional</th>
                                            <td>{{ $university->tanggal_izin_operasional ? \Carbon\Carbon::parse($university->tanggal_izin_operasional)->format('d F Y') : '-' }}</td>
                                        </tr>
                                    </table>
                                </div>

                                <!-- Pimpinan Tab -->
                                <div class="tab-pane fade" id="pimpinan" role="tabpanel">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th width="30%" class="bg-light">Rektor</th>
                                            <td>
                                                {{ $university->rektor ?? '-' }}
                                                @if($university->nip_rektor)
                                                    <br><small class="text-muted">NIP: {{ $university->nip_rektor }}</small>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="bg-light">Wakil Rektor I</th>
                                            <td>{{ $university->wakil_rektor_1 ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th class="bg-light">Wakil Rektor II</th>
                                            <td>{{ $university->wakil_rektor_2 ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th class="bg-light">Wakil Rektor III</th>
                                            <td>{{ $university->wakil_rektor_3 ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th class="bg-light">Wakil Rektor IV</th>
                                            <td>{{ $university->wakil_rektor_4 ?? '-' }}</td>
                                        </tr>
                                    </table>
                                </div>

                                <!-- Kontak & Alamat Tab -->
                                <div class="tab-pane fade" id="kontak" role="tabpanel">
                                    <h6 class="font-weight-bold mb-3">Kontak</h6>
                                    <table class="table table-bordered">
                                        <tr>
                                            <th width="30%" class="bg-light">Email</th>
                                            <td>
                                                @if($university->email)
                                                    <a href="mailto:{{ $university->email }}">{{ $university->email }}</a>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="bg-light">Telepon</th>
                                            <td>{{ $university->telepon ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th class="bg-light">Fax</th>
                                            <td>{{ $university->fax ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th class="bg-light">Website</th>
                                            <td>
                                                @if($university->website)
                                                    <a href="{{ $university->website }}" target="_blank">{{ $university->website }}</a>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                    </table>

                                    <h6 class="font-weight-bold mb-3 mt-4">Alamat</h6>
                                    <table class="table table-bordered">
                                        <tr>
                                            <th width="30%" class="bg-light">Alamat Lengkap</th>
                                            <td>{{ $university->alamat ?? '-' }}</td>
                                        </tr>
                                        @if($university->village)
                                        <tr>
                                            <th class="bg-light">Desa/Kelurahan</th>
                                            <td>{{ $university->village->nama }}</td>
                                        </tr>
                                        <tr>
                                            <th class="bg-light">Kecamatan</th>
                                            <td>{{ $university->village->subRegency->nama }}</td>
                                        </tr>
                                        <tr>
                                            <th class="bg-light">Kabupaten/Kota</th>
                                            <td>{{ $university->village->subRegency->regency->nama }}</td>
                                        </tr>
                                        <tr>
                                            <th class="bg-light">Provinsi</th>
                                            <td>{{ $university->village->subRegency->regency->province->nama }}</td>
                                        </tr>
                                        @endif
                                        <tr>
                                            <th class="bg-light">Kode Pos</th>
                                            <td>{{ $university->kode_pos ?? '-' }}</td>
                                        </tr>
                                    </table>
                                </div>

                                <!-- Visi/Misi/Sejarah Tab -->
                                <div class="tab-pane fade" id="info" role="tabpanel">
                                    <h6 class="font-weight-bold mb-3">Visi</h6>
                                    <div class="bg-light p-3 rounded mb-3">
                                        {!! nl2br(e($university->visi ?? 'Belum ada visi')) !!}
                                    </div>

                                    <h6 class="font-weight-bold mb-3">Misi</h6>
                                    <div class="bg-light p-3 rounded mb-3">
                                        {!! nl2br(e($university->misi ?? 'Belum ada misi')) !!}
                                    </div>

                                    <h6 class="font-weight-bold mb-3">Sejarah</h6>
                                    <div class="bg-light p-3 rounded mb-3">
                                        {!! nl2br(e($university->sejarah ?? 'Belum ada sejarah')) !!}
                                    </div>

                                    @if($university->keterangan)
                                    <h6 class="font-weight-bold mb-3">Keterangan</h6>
                                    <div class="bg-light p-3 rounded">
                                        {!! nl2br(e($university->keterangan)) !!}
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Documents Section -->
    @if($university->files && $university->files->count() > 0)
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-file-alt"></i> Dokumen Pendukung ({{ $university->files->count() }})
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="40%">Nama File</th>
                                    <th width="15%">Ukuran</th>
                                    <th width="20%">Tanggal Upload</th>
                                    <th width="20%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($university->files as $index => $file)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <i class="fas fa-file text-primary"></i> {{ $file->original_name }}
                                    </td>
                                    <td>{{ number_format($file->size / 1024, 2) }} KB</td>
                                    <td>{{ $file->created_at->format('d F Y H:i') }}</td>
                                    <td>
                                        <a href="{{ asset('storage/' . $file->file_path) }}" class="btn btn-sm btn-primary" target="_blank">
                                            <i class="fas fa-download"></i> Download
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Apakah Anda yakin ingin menghapus universitas <strong>{{ $university->nama }}</strong>?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <form action="{{ route('universities.destroy', $university->id) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
