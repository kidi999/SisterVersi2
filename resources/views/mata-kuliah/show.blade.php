@extends('layouts.app')

@section('title', 'Detail Mata Kuliah')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detail Mata Kuliah</h1>
        <div>
            <a href="{{ route('mata-kuliah.edit', $mataKuliah->id) }}" class="btn btn-warning btn-icon-split">
                <span class="icon text-white-50">
                    <i class="fas fa-edit"></i>
                </span>
                <span class="text">Edit</span>
            </a>
            <a href="{{ route('mata-kuliah.index') }}" class="btn btn-secondary btn-icon-split">
                <span class="icon text-white-50">
                    <i class="fas fa-arrow-left"></i>
                </span>
                <span class="text">Kembali</span>
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Mata Kuliah</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4"><strong>Kode Mata Kuliah</strong></div>
                        <div class="col-md-8">{{ $mataKuliah->kode_mk }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4"><strong>Nama Mata Kuliah</strong></div>
                        <div class="col-md-8">{{ $mataKuliah->nama_mk }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4"><strong>SKS</strong></div>
                        <div class="col-md-8">{{ $mataKuliah->sks }} SKS</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4"><strong>Semester</strong></div>
                        <div class="col-md-8">Semester {{ $mataKuliah->semester }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4"><strong>Jenis</strong></div>
                        <div class="col-md-8">
                            <span class="badge bg-{{ $mataKuliah->jenis == 'Wajib' ? 'success' : 'info' }}">
                                {{ $mataKuliah->jenis }}
                            </span>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4"><strong>Deskripsi</strong></div>
                        <div class="col-md-8">{{ $mataKuliah->deskripsi ?? '-' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow mb-4 border-left-{{ $mataKuliah->level_badge }}">
                <div class="card-header py-3 bg-{{ $mataKuliah->level_badge }} text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="bi bi-diagram-3"></i> Level & Scope
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted">Level Mata Kuliah</small>
                        <h5 class="mb-0">
                            <span class="badge bg-{{ $mataKuliah->level_badge }}">
                                {{ $mataKuliah->level_label }}
                            </span>
                        </h5>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">Scope</small>
                        <p class="mb-0">{{ $mataKuliah->scope_label }}</p>
                    </div>
                    
                    @if($mataKuliah->level_matkul === 'prodi' && $mataKuliah->programStudi)
                    <hr>
                    <div class="mb-2">
                        <small class="text-muted">Program Studi</small>
                        <p class="mb-0"><strong>{{ $mataKuliah->programStudi->nama_prodi }}</strong></p>
                        <small class="text-muted">{{ $mataKuliah->programStudi->jenjang }}</small>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted">Fakultas</small>
                        <p class="mb-0">{{ $mataKuliah->programStudi->fakultas->nama_fakultas ?? '-' }}</p>
                    </div>
                    @elseif($mataKuliah->level_matkul === 'fakultas' && $mataKuliah->fakultas)
                    <hr>
                    <div class="mb-2">
                        <small class="text-muted">Fakultas</small>
                        <p class="mb-0"><strong>{{ $mataKuliah->fakultas->nama_fakultas }}</strong></p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if($mataKuliah->files && $mataKuliah->files->count() > 0)
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="bi bi-paperclip"></i> Lampiran Dokumen
            </h6>
        </div>
        <div class="card-body">
            <div class="row">
                @foreach($mataKuliah->files as $file)
                <div class="col-md-4 mb-3">
                    <div class="card">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center">
                                <i class="bi {{ $file->icon_class }} fs-2 me-3 text-primary"></i>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0 text-truncate" title="{{ $file->file_name }}">
                                        {{ $file->file_name }}
                                    </h6>
                                    <small class="text-muted">{{ $file->formatted_size }}</small>
                                </div>
                            </div>
                            <div class="mt-2">
                                <a href="{{ route('api.file-upload.download', $file->id) }}" class="btn btn-sm btn-primary w-100">
                                    <i class="bi bi-download"></i> Download
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Informasi Audit</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <small class="text-muted">Dibuat oleh</small>
                    <p>{{ $mataKuliah->creator->name ?? '-' }}</p>
                    <small class="text-muted">{{ $mataKuliah->created_at ? $mataKuliah->created_at->format('d M Y H:i') : '-' }}</small>
                </div>
                <div class="col-md-6">
                    <small class="text-muted">Diupdate oleh</small>
                    <p>{{ $mataKuliah->updater->name ?? '-' }}</p>
                    <small class="text-muted">{{ $mataKuliah->updated_at ? $mataKuliah->updated_at->format('d M Y H:i') : '-' }}</small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
