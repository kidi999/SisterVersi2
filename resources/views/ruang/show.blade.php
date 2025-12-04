@extends('layouts.app')

@section('title', 'Detail Ruang')
@section('header', 'Detail Ruang')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Detail Ruang</h1>
        <div>
            <a href="{{ route('ruang.edit', $ruang) }}" class="btn btn-warning">
                <i class="bi bi-pencil"></i> Edit
            </a>
            <a href="{{ route('ruang.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Info Utama -->
        <div class="col-lg-6">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-door-open"></i> Informasi Ruang</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td width="40%"><strong>Kode Ruang</strong></td>
                            <td><span class="badge bg-primary fs-6">{{ $ruang->kode_ruang }}</span></td>
                        </tr>
                        <tr>
                            <td><strong>Nama Ruang</strong></td>
                            <td>{{ $ruang->nama_ruang }}</td>
                        </tr>
                        <tr>
                            <td><strong>Gedung</strong></td>
                            <td>{{ $ruang->gedung ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Lantai</strong></td>
                            <td>{{ $ruang->lantai ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Lokasi Lengkap</strong></td>
                            <td>
                                @if($ruang->gedung || $ruang->lantai)
                                    Gedung {{ $ruang->gedung ?? '-' }}, Lantai {{ $ruang->lantai ?? '-' }}
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-sliders"></i> Spesifikasi</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td width="40%"><strong>Kapasitas</strong></td>
                            <td>
                                <i class="bi bi-people-fill text-primary"></i> 
                                <strong>{{ $ruang->kapasitas }}</strong> orang
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Jenis Ruang</strong></td>
                            <td><span class="badge bg-info">{{ $ruang->jenis_ruang }}</span></td>
                        </tr>
                        <tr>
                            <td><strong>Status</strong></td>
                            <td>
                                <span class="badge bg-{{ $ruang->status_badge }}">
                                    {{ $ruang->status }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Fasilitas</strong></td>
                            <td>{{ $ruang->fasilitas ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Kepemilikan & Info Tambahan -->
        <div class="col-lg-6">
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="bi bi-building"></i> Kepemilikan</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td width="40%"><strong>Tingkat Kepemilikan</strong></td>
                            <td>{!! $ruang->kepemilikan_display !!}</td>
                        </tr>
                        @if($ruang->tingkat_kepemilikan === 'Fakultas' && $ruang->fakultas)
                            <tr>
                                <td><strong>Fakultas</strong></td>
                                <td>{{ $ruang->fakultas->nama_fakultas }}</td>
                            </tr>
                            <tr>
                                <td><strong>Kode Fakultas</strong></td>
                                <td>{{ $ruang->fakultas->kode_fakultas }}</td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <div class="alert alert-info mb-0 mt-2">
                                        <i class="bi bi-info-circle"></i> 
                                        Ruang ini dapat digunakan oleh semua program studi di <strong>{{ $ruang->fakultas->nama_fakultas }}</strong>
                                    </div>
                                </td>
                            </tr>
                        @elseif($ruang->tingkat_kepemilikan === 'Prodi' && $ruang->programStudi)
                            <tr>
                                <td><strong>Program Studi</strong></td>
                                <td>{{ $ruang->programStudi->nama_prodi }}</td>
                            </tr>
                            <tr>
                                <td><strong>Kode Prodi</strong></td>
                                <td>{{ $ruang->programStudi->kode_prodi }}</td>
                            </tr>
                            <tr>
                                <td><strong>Fakultas</strong></td>
                                <td>{{ $ruang->programStudi->fakultas->nama_fakultas }}</td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <div class="alert alert-success mb-0 mt-2">
                                        <i class="bi bi-check-circle"></i> 
                                        Ruang ini hanya dapat digunakan oleh <strong>{{ $ruang->programStudi->nama_prodi }}</strong>
                                    </div>
                                </td>
                            </tr>
                        @else
                            <tr>
                                <td colspan="2">
                                    <div class="alert alert-primary mb-0 mt-2">
                                        <i class="bi bi-building"></i> 
                                        Ruang ini dapat digunakan oleh <strong>seluruh fakultas dan program studi</strong> di universitas
                                    </div>
                                </td>
                            </tr>
                        @endif
                    </table>
                </div>
            </div>

            @if($ruang->keterangan)
            <div class="card mb-4">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="bi bi-file-text"></i> Keterangan</h5>
                </div>
                <div class="card-body">
                    <p class="mb-0">{{ $ruang->keterangan }}</p>
                </div>
            </div>
            @endif

            <!-- File Lampiran -->
            @if($ruang->files && $ruang->files->count() > 0)
            <div class="card mb-4">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="bi bi-paperclip"></i> File Lampiran ({{ $ruang->files->count() }})</h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        @foreach($ruang->files as $file)
                            <div class="list-group-item px-0">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <i class="{{ $file->icon_class }} fs-2"></i>
                                    </div>
                                    <div class="col">
                                        <strong>{{ $file->file_name }}</strong><br>
                                        <small class="text-muted">
                                            {{ $file->formatted_size }}
                                            @if($file->description)
                                                <br>{{ $file->description }}
                                            @endif
                                        </small>
                                    </div>
                                    <div class="col-auto">
                                        <a href="{{ Storage::url($file->file_path) }}" 
                                           target="_blank" 
                                           class="btn btn-sm btn-primary" 
                                           download>
                                            <i class="bi bi-download"></i> Unduh
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="bi bi-clock-history"></i> Audit Trail</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td width="40%"><strong>Dibuat oleh</strong></td>
                            <td>{{ $ruang->createdBy->name ?? 'System' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Dibuat pada</strong></td>
                            <td>{{ $ruang->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <td><strong>Diubah oleh</strong></td>
                            <td>{{ $ruang->updatedBy->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Diubah pada</strong></td>
                            <td>{{ $ruang->updated_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
