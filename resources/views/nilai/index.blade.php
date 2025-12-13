@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Nilai Mahasiswa</h1>
        <div>
            <a href="{{ route('nilai.exportExcel', request()->query()) }}" class="btn btn-success me-2">
                <i class="bi bi-file-earmark-excel"></i> Export Excel
            </a>
            <a href="{{ route('nilai.exportPdf', request()->query()) }}" class="btn btn-danger me-2">
                <i class="bi bi-file-earmark-pdf"></i> Export PDF
            </a>
            @if(in_array(Auth::user()->role->name, ['dosen', 'admin_prodi', 'admin_fakultas', 'admin_universitas', 'super_admin']))
            <a href="{{ route('nilai.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Input Nilai
            </a>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filter Section -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('nilai.index') }}" class="row g-3">
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control" 
                           placeholder="Cari NIM/Nama..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select name="tahun_ajaran" class="form-select">
                        <option value="">Semua Tahun Ajaran</option>
                        @foreach($tahunAjaranList as $ta)
                        <option value="{{ $ta }}" {{ request('tahun_ajaran') == $ta ? 'selected' : '' }}>
                            {{ $ta }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="semester" class="form-select">
                        <option value="">Semua Semester</option>
                        <option value="Ganjil" {{ request('semester') == 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                        <option value="Genap" {{ request('semester') == 'Genap' ? 'selected' : '' }}>Genap</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search"></i> Filter
                    </button>
                    <a href="{{ route('nilai.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-clockwise"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Nilai List -->
    <div class="card">
        <div class="card-body">
            @if($nilai->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            @if(Auth::user()->role->name != 'mahasiswa')
                            <th>NIM</th>
                            <th>Nama Mahasiswa</th>
                            @endif
                            <th>Mata Kuliah</th>
                            <th>SKS</th>
                            <th>Tahun Ajaran</th>
                            <th>Semester</th>
                            <th>Tugas</th>
                            <th>UTS</th>
                            <th>UAS</th>
                            <th>Nilai Akhir</th>
                            <th>Huruf</th>
                            <th>Bobot</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($nilai as $item)
                        <tr>
                            <td>{{ $loop->iteration + ($nilai->currentPage() - 1) * $nilai->perPage() }}</td>
                            @if(Auth::user()->role->name != 'mahasiswa')
                            <td>{{ $item->krs->mahasiswa->nim }}</td>
                            <td>{{ $item->krs->mahasiswa->nama }}</td>
                            @endif
                            <td>
                                <strong>{{ $item->krs->kelas->mataKuliah->nama }}</strong>
                                <br>
                                <small class="text-muted">{{ $item->krs->kelas->kode_kelas }}</small>
                            </td>
                            <td>{{ $item->krs->kelas->mataKuliah->sks }}</td>
                            <td>{{ $item->krs->tahun_ajaran }}</td>
                            <td>
                                <span class="badge {{ $item->krs->semester == 'Ganjil' ? 'bg-info' : 'bg-warning' }}">
                                    {{ $item->krs->semester }}
                                </span>
                            </td>
                            <td>{{ number_format($item->nilai_tugas, 2) }}</td>
                            <td>{{ number_format($item->nilai_uts, 2) }}</td>
                            <td>{{ number_format($item->nilai_uas, 2) }}</td>
                            <td><strong>{{ number_format($item->nilai_akhir, 2) }}</strong></td>
                            <td>
                                <span class="badge 
                                    @if(in_array($item->nilai_huruf, ['A', 'A-'])) bg-success
                                    @elseif(in_array($item->nilai_huruf, ['B+', 'B', 'B-'])) bg-primary
                                    @elseif(in_array($item->nilai_huruf, ['C+', 'C'])) bg-warning
                                    @else bg-danger
                                    @endif">
                                    {{ $item->nilai_huruf }}
                                </span>
                            </td>
                            <td>{{ number_format($item->bobot, 2) }}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('nilai.show', $item->id) }}" 
                                       class="btn btn-sm btn-info" title="Lihat Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    
                                    @if(in_array(Auth::user()->role->name, ['dosen', 'admin_prodi', 'admin_fakultas', 'admin_universitas', 'super_admin']))
                                        <a href="{{ route('nilai.edit', $item->id) }}" 
                                           class="btn btn-sm btn-warning" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        
                                        <form action="{{ route('nilai.destroy', $item->id) }}" 
                                              method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" 
                                                    title="Hapus" onclick="return confirm('Hapus nilai ini?')">
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
                {{ $nilai->links() }}
            </div>
            @else
            <div class="text-center py-5">
                <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                <p class="mt-3 text-muted">Belum ada data nilai</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
