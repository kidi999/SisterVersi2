@extends('layouts.app')

@section('title', 'Detail Semester')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Detail Semester</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('semester.edit', $semester->id) }}" class="btn btn-warning">
                <i class="bi bi-pencil"></i> Edit
            </a>
            <a href="{{ route('semester.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-calendar3"></i> Informasi Semester
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="fw-bold">Tahun Akademik</label>
                            <p class="text-muted">
                                {{ $semester->tahunAkademik->kode }} - {{ $semester->tahunAkademik->nama }}
                                <span class="badge bg-{{ $semester->tahunAkademik->is_active ? 'success' : 'secondary' }}">
                                    {{ $semester->tahunAkademik->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </p>
                        </div>

                        <div class="col-md-6">
                            <label class="fw-bold">Program Studi</label>
                            <p class="text-muted">
                                @if($semester->program_studi_id)
                                    {{ $semester->programStudi->nama_prodi }}
                                    <br>
                                    <small class="text-primary">
                                        <i class="bi bi-building"></i> 
                                        {{ $semester->programStudi->fakultas->nama_fakultas }}
                                    </small>
                                @else
                                    <span class="badge bg-info">
                                        <i class="bi bi-globe"></i> UNIVERSITAS
                                    </span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-8">
                            <label class="fw-bold">Nama Semester</label>
                            <p class="text-muted">{{ $semester->nama_semester }}</p>
                        </div>

                        <div class="col-md-4">
                            <label class="fw-bold">Nomor Semester</label>
                            <p class="text-muted">{{ $semester->nomor_semester }}</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="fw-bold">Tanggal Mulai</label>
                            <p class="text-muted">
                                <i class="bi bi-calendar-check"></i>
                                {{ $semester->tanggal_mulai ? \Carbon\Carbon::parse($semester->tanggal_mulai)->format('d/m/Y') : '-' }}
                            </p>
                        </div>

                        <div class="col-md-6">
                            <label class="fw-bold">Tanggal Selesai</label>
                            <p class="text-muted">
                                <i class="bi bi-calendar-x"></i>
                                {{ $semester->tanggal_selesai ? \Carbon\Carbon::parse($semester->tanggal_selesai)->format('d/m/Y') : '-' }}
                            </p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-12">
                            <label class="fw-bold">Status</label>
                            <p>
                                <span class="badge bg-{{ $semester->is_active ? 'success' : 'secondary' }} fs-6">
                                    <i class="bi bi-{{ $semester->is_active ? 'check-circle' : 'x-circle' }}"></i>
                                    {{ $semester->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-calendar2-week"></i> Jadwal Lengkap
                    </h5>
                </div>
                <div class="card-body">
                    <h6 class="border-bottom pb-2 mb-3">
                        <i class="bi bi-book"></i> Perkuliahan
                    </h6>
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="fw-bold">Tanggal Mulai Perkuliahan</label>
                            <p class="text-muted">
                                {{ $semester->tanggal_mulai_perkuliahan ? \Carbon\Carbon::parse($semester->tanggal_mulai_perkuliahan)->format('d/m/Y') : '-' }}
                            </p>
                        </div>

                        <div class="col-md-6">
                            <label class="fw-bold">Tanggal Selesai Perkuliahan</label>
                            <p class="text-muted">
                                {{ $semester->tanggal_selesai_perkuliahan ? \Carbon\Carbon::parse($semester->tanggal_selesai_perkuliahan)->format('d/m/Y') : '-' }}
                            </p>
                        </div>
                    </div>

                    <h6 class="border-bottom pb-2 mb-3">
                        <i class="bi bi-clipboard-check"></i> UTS (Ujian Tengah Semester)
                    </h6>
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="fw-bold">Tanggal Mulai UTS</label>
                            <p class="text-muted">
                                {{ $semester->tanggal_mulai_uts ? \Carbon\Carbon::parse($semester->tanggal_mulai_uts)->format('d/m/Y') : '-' }}
                            </p>
                        </div>

                        <div class="col-md-6">
                            <label class="fw-bold">Tanggal Selesai UTS</label>
                            <p class="text-muted">
                                {{ $semester->tanggal_selesai_uts ? \Carbon\Carbon::parse($semester->tanggal_selesai_uts)->format('d/m/Y') : '-' }}
                            </p>
                        </div>
                    </div>

                    <h6 class="border-bottom pb-2 mb-3">
                        <i class="bi bi-clipboard2-check"></i> UAS (Ujian Akhir Semester)
                    </h6>
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="fw-bold">Tanggal Mulai UAS</label>
                            <p class="text-muted">
                                {{ $semester->tanggal_mulai_uas ? \Carbon\Carbon::parse($semester->tanggal_mulai_uas)->format('d/m/Y') : '-' }}
                            </p>
                        </div>

                        <div class="col-md-6">
                            <label class="fw-bold">Tanggal Selesai UAS</label>
                            <p class="text-muted">
                                {{ $semester->tanggal_selesai_uas ? \Carbon\Carbon::parse($semester->tanggal_selesai_uas)->format('d/m/Y') : '-' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            @if($semester->keterangan)
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-chat-left-text"></i> Keterangan
                    </h5>
                </div>
                <div class="card-body">
                    <p class="mb-0">{{ $semester->keterangan }}</p>
                </div>
            </div>
            @endif

            @if($semester->files->count() > 0)
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-file-earmark-text"></i> Dokumen Terlampir ({{ $semester->files->count() }})
                    </h5>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        @foreach($semester->files as $file)
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-file-earmark-pdf fs-4 text-danger"></i>
                                <div>
                                    <strong>{{ $file->file_name }}</strong>
                                    <br>
                                    <small class="text-muted">
                                        {{ number_format($file->file_size / 1024, 2) }} KB
                                        • Diupload: {{ $file->created_at->format('d/m/Y H:i') }}
                                    </small>
                                </div>
                            </div>
                            <a href="{{ Storage::url($file->file_path) }}" 
                               class="btn btn-sm btn-primary" 
                               target="_blank">
                                <i class="bi bi-download"></i> Download
                            </a>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <i class="bi bi-clock-history"></i> Audit Trail
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <td class="fw-bold" width="40%">Dibuat Oleh</td>
                            <td>{{ $semester->created_by ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Dibuat Pada</td>
                            <td>
                                @if($semester->created_at)
                                    {{ $semester->created_at->format('d/m/Y') }}
                                    <br>
                                    <small class="text-muted">{{ $semester->created_at->format('H:i:s') }}</small>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        @if($semester->updated_by)
                        <tr>
                            <td class="fw-bold">Diupdate Oleh</td>
                            <td>{{ $semester->updated_by }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Diupdate Pada</td>
                            <td>
                                {{ $semester->updated_at->format('d/m/Y') }}
                                <br>
                                <small class="text-muted">{{ $semester->updated_at->format('H:i:s') }}</small>
                            </td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header bg-danger text-white">
                    <i class="bi bi-exclamation-triangle"></i> Aksi Berbahaya
                </div>
                <div class="card-body">
                    <form action="{{ route('semester.destroy', $semester->id) }}" method="POST" id="deleteForm">
                        @csrf
                        @method('DELETE')
                        <p class="text-muted mb-3">
                            <small>Menghapus semester akan memindahkannya ke tempat sampah. Data dapat dipulihkan oleh super admin.</small>
                        </p>
                        <button type="submit" class="btn btn-danger w-100" onclick="return confirm('Yakin ingin menghapus semester ini?')">
                            <i class="bi bi-trash"></i> Hapus Semester
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
