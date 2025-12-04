@extends('layouts.app')

@section('title', 'Detail Tahun Akademik')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Detail Tahun Akademik</h1>
        <div class="btn-group">
            <a href="{{ route('tahun-akademik.edit', $tahunAkademik->id) }}" class="btn btn-warning">
                <i class="bi bi-pencil"></i> Edit
            </a>
            <a href="{{ route('tahun-akademik.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <i class="bi bi-info-circle"></i> Informasi Tahun Akademik
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <td class="fw-bold" width="40%">Kode</td>
                            <td>{{ $tahunAkademik->kode }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Nama</td>
                            <td>{{ $tahunAkademik->nama }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Periode</td>
                            <td>{{ $tahunAkademik->tahun_mulai }} / {{ $tahunAkademik->tahun_selesai }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Tanggal Mulai</td>
                            <td>{{ \Carbon\Carbon::parse($tahunAkademik->tanggal_mulai)->format('d F Y') }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Tanggal Selesai</td>
                            <td>{{ \Carbon\Carbon::parse($tahunAkademik->tanggal_selesai)->format('d F Y') }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Status</td>
                            <td>
                                <span class="badge bg-{{ $tahunAkademik->is_active ? 'success' : 'secondary' }}">
                                    {{ $tahunAkademik->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Keterangan</td>
                            <td>{{ $tahunAkademik->keterangan ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            @if($tahunAkademik->files->isNotEmpty())
            <div class="card mt-3">
                <div class="card-header bg-info text-white">
                    <i class="bi bi-file-earmark-text"></i> Dokumen Pendukung ({{ $tahunAkademik->files->count() }})
                </div>
                <div class="card-body">
                    <div class="list-group">
                        @foreach($tahunAkademik->files as $file)
                        <a href="{{ Storage::url($file->file_path) }}" 
                           target="_blank" 
                           class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div>
                                <i class="{{ $file->icon_class }} me-2"></i>
                                <strong>{{ $file->original_filename }}</strong>
                                <br>
                                <small class="text-muted">
                                    {{ $file->formatted_size }} • 
                                    Diupload {{ $file->created_at->diffForHumans() }}
                                </small>
                            </div>
                            <span class="badge bg-primary rounded-pill">
                                <i class="bi bi-download"></i>
                            </span>
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <div class="card mt-3">
                <div class="card-header bg-secondary text-white">
                    <i class="bi bi-clock-history"></i> Audit Trail
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <td class="fw-bold" width="40%">Dibuat Oleh</td>
                            <td>{{ $tahunAkademik->created_by ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Dibuat Pada</td>
                            <td>{{ $tahunAkademik->created_at ? $tahunAkademik->created_at->format('d/m/Y H:i:s') : '-' }}</td>
                        </tr>
                        @if($tahunAkademik->updated_by)
                        <tr>
                            <td class="fw-bold">Diupdate Oleh</td>
                            <td>{{ $tahunAkademik->updated_by }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Diupdate Pada</td>
                            <td>{{ $tahunAkademik->updated_at->format('d/m/Y H:i:s') }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <span>
                        <i class="bi bi-calendar2-week"></i> Daftar Semester ({{ $tahunAkademik->semesters->count() }})
                    </span>
                    <a href="{{ route('semester.create', ['tahun_akademik' => $tahunAkademik->id]) }}" 
                       class="btn btn-sm btn-light">
                        <i class="bi bi-plus-circle"></i> Tambah Semester
                    </a>
                </div>
                <div class="card-body">
                    @if($tahunAkademik->semesters->isEmpty())
                        <div class="text-center text-muted py-5">
                            <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                            <p class="mt-2">Belum ada semester untuk tahun akademik ini</p>
                            <a href="{{ route('semester.create', ['tahun_akademik' => $tahunAkademik->id]) }}" 
                               class="btn btn-primary">
                                <i class="bi bi-plus-circle"></i> Tambah Semester
                            </a>
                        </div>
                    @else
                        <!-- Semester Universitas (tidak terikat prodi) -->
                        @php
                            $semesterUniversitas = $tahunAkademik->semesters->whereNull('program_studi_id');
                        @endphp
                        @if($semesterUniversitas->isNotEmpty())
                        <div class="mb-4">
                            <h5 class="border-bottom pb-2">
                                <i class="bi bi-building"></i> Semester Universitas
                            </h5>
                            <div class="table-responsive">
                                <table class="table table-sm table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="5%">No</th>
                                            <th>Nama Semester</th>
                                            <th>Nomor</th>
                                            <th>Tanggal Mulai</th>
                                            <th>Tanggal Selesai</th>
                                            <th>Status</th>
                                            <th width="15%">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($semesterUniversitas as $index => $semester)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $semester->nama_semester }}</td>
                                            <td>
                                                <span class="badge bg-primary">{{ $semester->nomor_semester }}</span>
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($semester->tanggal_mulai)->format('d/m/Y') }}</td>
                                            <td>{{ \Carbon\Carbon::parse($semester->tanggal_selesai)->format('d/m/Y') }}</td>
                                            <td>
                                                <span class="badge bg-{{ $semester->is_active ? 'success' : 'secondary' }}">
                                                    {{ $semester->is_active ? 'Aktif' : 'Nonaktif' }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('semester.show', $semester->id) }}" 
                                                       class="btn btn-info" title="Detail">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <a href="{{ route('semester.edit', $semester->id) }}" 
                                                       class="btn btn-warning" title="Edit">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @endif

                        <!-- Semester Per Program Studi (Grouped by Fakultas) -->
                        @php
                            $semesterPerProdi = $tahunAkademik->semesters->whereNotNull('program_studi_id');
                            $groupedByFakultas = $semesterPerProdi->groupBy(function($semester) {
                                return $semester->programStudi->fakultas->id ?? 0;
                            });
                        @endphp
                        @foreach($groupedByFakultas as $fakultasId => $semestersInFakultas)
                        @php
                            $firstSemester = $semestersInFakultas->first();
                            $fakultas = $firstSemester->programStudi->fakultas ?? null;
                        @endphp
                        @if($fakultas)
                        <div class="mb-4">
                            <h5 class="border-bottom pb-2">
                                <i class="bi bi-bank"></i> {{ $fakultas->nama_fakultas }}
                            </h5>
                            
                            @php
                                $groupedByProdi = $semestersInFakultas->groupBy('program_studi_id');
                            @endphp
                            @foreach($groupedByProdi as $prodiId => $semesterProdi)
                            <div class="ms-4 mb-3">
                                <h6 class="text-muted">
                                    <i class="bi bi-mortarboard"></i> 
                                    {{ $semesterProdi->first()->programStudi->nama_prodi ?? 'Program Studi' }}
                                </h6>
                                <div class="table-responsive">
                                    <table class="table table-sm table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th width="5%">No</th>
                                                <th>Nama Semester</th>
                                                <th>Nomor</th>
                                                <th>Tanggal Mulai</th>
                                                <th>Tanggal Selesai</th>
                                                <th>Status</th>
                                                <th width="15%">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($semesterProdi as $index => $semester)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $semester->nama_semester }}</td>
                                                <td>
                                                    <span class="badge bg-primary">{{ $semester->nomor_semester }}</span>
                                                </td>
                                                <td>{{ \Carbon\Carbon::parse($semester->tanggal_mulai)->format('d/m/Y') }}</td>
                                                <td>{{ \Carbon\Carbon::parse($semester->tanggal_selesai)->format('d/m/Y') }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $semester->is_active ? 'success' : 'secondary' }}">
                                                        {{ $semester->is_active ? 'Aktif' : 'Nonaktif' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="{{ route('semester.show', $semester->id) }}" 
                                                           class="btn btn-info" title="Detail">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                        <a href="{{ route('semester.edit', $semester->id) }}" 
                                                           class="btn btn-warning" title="Edit">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @endif
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
