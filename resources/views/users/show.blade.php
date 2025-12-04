@extends('layouts.app')

@section('title', 'Detail User')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Detail User</h2>
        <div>
            <a href="{{ route('users.edit', $user->id) }}" class="btn btn-warning">
                <i class="bi bi-pencil me-2"></i>Edit
            </a>
            <a href="{{ route('users.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-2"></i>Kembali
            </a>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="card-title border-bottom pb-2 mb-3">Informasi User</h5>
            
            <div class="row mb-3">
                <div class="col-md-4 fw-bold">Nama Lengkap</div>
                <div class="col-md-8">{{ $user->name }}</div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4 fw-bold">Email</div>
                <div class="col-md-8">{{ $user->email }}</div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4 fw-bold">Role</div>
                <div class="col-md-8">
                    <span class="badge bg-secondary">{{ $user->role->display_name ?? '-' }}</span>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4 fw-bold">Status</div>
                <div class="col-md-8">
                    @if($user->is_active)
                        <span class="badge bg-success">Aktif</span>
                    @else
                        <span class="badge bg-danger">Nonaktif</span>
                    @endif
                </div>
            </div>

            <h5 class="card-title border-bottom pb-2 mb-3 mt-4">Informasi Afiliasi</h5>

            <div class="row mb-3">
                <div class="col-md-4 fw-bold">Fakultas</div>
                <div class="col-md-8">{{ $user->fakultas->nama_fakultas ?? '-' }}</div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4 fw-bold">Program Studi</div>
                <div class="col-md-8">{{ $user->programStudi->nama_prodi ?? '-' }}</div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4 fw-bold">Dosen</div>
                <div class="col-md-8">
                    @if($user->dosen)
                        {{ $user->dosen->nama_dosen }} ({{ $user->dosen->nidn }})
                    @else
                        -
                    @endif
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4 fw-bold">Mahasiswa</div>
                <div class="col-md-8">
                    @if($user->mahasiswa)
                        {{ $user->mahasiswa->nama_mahasiswa }} ({{ $user->mahasiswa->nim }})
                    @else
                        -
                    @endif
                </div>
            </div>

            <h5 class="card-title border-bottom pb-2 mb-3 mt-4">Dokumen Pendukung</h5>

            @if($user->files && $user->files->count() > 0)
                <div class="row mb-3">
                    <div class="col-12">
                        <div class="list-group">
                            @foreach($user->files as $file)
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="bi bi-file-earmark me-2"></i>
                                            <strong>{{ $file->original_filename }}</strong>
                                            <small class="text-muted ms-2">({{ $file->formatted_size }})</small>
                                        </div>
                                        <a href="{{ route('api.file-upload.download', $file->id) }}" 
                                           class="btn btn-sm btn-primary"
                                           target="_blank">
                                            <i class="bi bi-download"></i> Download
                                        </a>
                                    </div>
                                    <small class="text-muted">
                                        Diupload oleh: {{ $file->created_by ?? '-' }} | 
                                        {{ $file->created_at ? $file->created_at->format('d-m-Y H:i') : '-' }}
                                    </small>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @else
                <div class="row mb-3">
                    <div class="col-12">
                        <p class="text-muted">Tidak ada dokumen yang diupload.</p>
                    </div>
                </div>
            @endif

            <h5 class="card-title border-bottom pb-2 mb-3 mt-4">Informasi Audit</h5>

            <div class="row mb-3">
                <div class="col-md-4 fw-bold">Dibuat Oleh</div>
                <div class="col-md-8">{{ $user->created_by ?? '-' }}</div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4 fw-bold">Dibuat Pada</div>
                <div class="col-md-8">{{ $user->created_at ? $user->created_at->format('d-m-Y H:i:s') : '-' }}</div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4 fw-bold">Diperbarui Oleh</div>
                <div class="col-md-8">{{ $user->updated_by ?? '-' }}</div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4 fw-bold">Diperbarui Pada</div>
                <div class="col-md-8">{{ $user->updated_at ? $user->updated_at->format('d-m-Y H:i:s') : '-' }}</div>
            </div>
        </div>
    </div>
</div>
@endsection
