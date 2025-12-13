@extends('layouts.app')

@section('title', 'Jadwal Kuliah')
@section('header', 'Jadwal Kuliah')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Jadwal Kuliah</h1>
        <div>
            <a href="{{ route('jadwal-kuliah.exportExcel', request()->query()) }}" class="btn btn-success me-2">
                <i class="bi bi-file-earmark-excel"></i> Export Excel
            </a>
            <a href="{{ route('jadwal-kuliah.exportPdf', request()->query()) }}" class="btn btn-danger me-2">
                <i class="bi bi-file-earmark-pdf"></i> Export PDF
            </a>
            @if(Auth::user()->hasRole(['super_admin', 'admin_universitas', 'admin_fakultas', 'admin_prodi']))
                <a href="{{ route('jadwal-kuliah.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Tambah Jadwal
                </a>
            @endif
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0"><i class="bi bi-funnel"></i> Filter</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('jadwal-kuliah.index') }}" method="GET">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Tahun Akademik</label>
                        <select name="tahun_akademik_id" class="form-select">
                            <option value="">Semua</option>
                            @foreach($tahunAkademik as $ta)
                                <option value="{{ $ta->id }}" {{ request('tahun_akademik_id') == $ta->id ? 'selected' : '' }}>
                                    {{ $ta->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Semester</label>
                        <select name="semester_id" class="form-select">
                            <option value="">Semua</option>
                            @foreach($semesters as $sem)
                                <option value="{{ $sem->id }}" {{ request('semester_id') == $sem->id ? 'selected' : '' }}>
                                    {{ $sem->nama_semester }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Hari</label>
                        <select name="hari" class="form-select">
                            <option value="">Semua</option>
                            <option value="Senin" {{ request('hari') == 'Senin' ? 'selected' : '' }}>Senin</option>
                            <option value="Selasa" {{ request('hari') == 'Selasa' ? 'selected' : '' }}>Selasa</option>
                            <option value="Rabu" {{ request('hari') == 'Rabu' ? 'selected' : '' }}>Rabu</option>
                            <option value="Kamis" {{ request('hari') == 'Kamis' ? 'selected' : '' }}>Kamis</option>
                            <option value="Jumat" {{ request('hari') == 'Jumat' ? 'selected' : '' }}>Jumat</option>
                            <option value="Sabtu" {{ request('hari') == 'Sabtu' ? 'selected' : '' }}>Sabtu</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Ruangan</label>
                        <select name="ruang_id" class="form-select">
                            <option value="">Semua</option>
                            @foreach($ruang as $r)
                                <option value="{{ $r->id }}" {{ request('ruang_id') == $r->id ? 'selected' : '' }}>
                                    {{ $r->kode_ruang }} - {{ $r->nama_ruang }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Fakultas</label>
                        <select name="fakultas_id" class="form-select">
                            <option value="">Semua</option>
                            @foreach($fakultas as $f)
                                <option value="{{ $f->id }}" {{ request('fakultas_id') == $f->id ? 'selected' : '' }}>
                                    {{ $f->nama_fakultas }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Program Studi</label>
                        <select name="program_studi_id" class="form-select">
                            <option value="">Semua</option>
                            @foreach($programStudi as $ps)
                                <option value="{{ $ps->id }}" {{ request('program_studi_id') == $ps->id ? 'selected' : '' }}>
                                    {{ $ps->nama_prodi }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Cari</label>
                        <input type="text" name="search" class="form-control" placeholder="Nama MK / Dosen" value="{{ request('search') }}">
                    </div>
                </div>
                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search"></i> Filter
                    </button>
                    <a href="{{ route('jadwal-kuliah.index') }}" class="btn btn-secondary">
                        <i class="bi bi-x-circle"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="card">
        <div class="card-body">
            @if($jadwal->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">No</th>
                                <th>Mata Kuliah</th>
                                <th>Kelas</th>
                                <th>Dosen</th>
                                <th width="10%">Hari</th>
                                <th width="12%">Waktu</th>
                                <th>Ruangan</th>
                                <th>Semester</th>
                                <th width="12%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($jadwal as $j)
                                <tr>
                                    <td>{{ $jadwal->firstItem() + $loop->index }}</td>
                                    <td>
                                        <strong>{{ $j->kelas->mataKuliah->nama_mk }}</strong><br>
                                        <small class="text-muted">{{ $j->kelas->mataKuliah->kode_mk }}</small><br>
                                        <span class="badge bg-{{ $j->kelas->mataKuliah->level_matkul == 'universitas' ? 'primary' : ($j->kelas->mataKuliah->level_matkul == 'fakultas' ? 'info' : 'success') }}">
                                            {{ ucfirst($j->kelas->mataKuliah->level_matkul) }}
                                        </span>
                                    </td>
                                    <td>{{ $j->kelas->nama_kelas }}</td>
                                    <td>{{ $j->kelas->dosen->nama_dosen ?? '-' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $j->hari_badge }}">{{ $j->hari }}</span>
                                    </td>
                                    <td>{{ $j->jam_mulai }} - {{ $j->jam_selesai }}</td>
                                    <td>
                                        <strong>{{ $j->ruang->kode_ruang }}</strong><br>
                                        <small>{{ $j->ruang->nama_ruang }}</small>
                                    </td>
                                    <td>
                                        @if($j->semester)
                                            {{ $j->semester->nama_semester }}
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('jadwal-kuliah.show', $j) }}" class="btn btn-info" title="Detail">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            @if(Auth::user()->hasRole(['super_admin', 'admin_universitas', 'admin_fakultas', 'admin_prodi']))
                                                <a href="{{ route('jadwal-kuliah.edit', $j) }}" class="btn btn-warning" title="Edit">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <form action="{{ route('jadwal-kuliah.destroy', $j) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus jadwal ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger" title="Hapus">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $jadwal->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-calendar-x" style="font-size: 4rem; color: #ccc;"></i>
                    <p class="text-muted mt-3">Belum ada jadwal kuliah</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
