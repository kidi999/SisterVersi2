@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">KRS (Kartu Rencana Studi)</h1>
        @if(Auth::user()->role->name == 'mahasiswa')
        <a href="{{ route('krs.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Isi KRS
        </a>
        @endif
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            {{ session('warning') }}
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
            <form method="GET" action="{{ route('krs.index') }}" class="row g-3">
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
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="Menunggu" {{ request('status') == 'Menunggu' ? 'selected' : '' }}>Menunggu</option>
                        <option value="Disetujui" {{ request('status') == 'Disetujui' ? 'selected' : '' }}>Disetujui</option>
                        <option value="Ditolak" {{ request('status') == 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search"></i> Filter
                    </button>
                    <a href="{{ route('krs.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-clockwise"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- KRS List -->
    <div class="card">
        <div class="card-body">
            @if($krs->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            @if(Auth::user()->role->name != 'mahasiswa')
                            <th>NIM</th>
                            <th>Nama Mahasiswa</th>
                            <th>Program Studi</th>
                            @endif
                            <th>Kode Kelas</th>
                            <th>Mata Kuliah</th>
                            <th>SKS</th>
                            <th>Tahun Ajaran</th>
                            <th>Semester</th>
                            <th>Status</th>
                            <th>Tanggal Pengajuan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($krs as $item)
                        <tr>
                            <td>{{ $loop->iteration + ($krs->currentPage() - 1) * $krs->perPage() }}</td>
                            @if(Auth::user()->role->name != 'mahasiswa')
                            <td>{{ $item->mahasiswa->nim }}</td>
                            <td>{{ $item->mahasiswa->nama }}</td>
                            <td>{{ $item->mahasiswa->programStudi->nama }}</td>
                            @endif
                            <td>
                                <span class="badge bg-secondary">{{ $item->kelas->kode_kelas }}</span>
                            </td>
                            <td>{{ $item->kelas->mataKuliah->nama }}</td>
                            <td>{{ $item->kelas->mataKuliah->sks }}</td>
                            <td>{{ $item->tahun_ajaran }}</td>
                            <td>
                                <span class="badge {{ $item->semester == 'Ganjil' ? 'bg-info' : 'bg-warning' }}">
                                    {{ $item->semester }}
                                </span>
                            </td>
                            <td>
                                @if($item->status == 'Menunggu')
                                    <span class="badge bg-warning text-dark">Menunggu</span>
                                @elseif($item->status == 'Disetujui')
                                    <span class="badge bg-success">Disetujui</span>
                                @else
                                    <span class="badge bg-danger">Ditolak</span>
                                @endif
                            </td>
                            <td>{{ \Carbon\Carbon::parse($item->tanggal_pengajuan)->format('d/m/Y') }}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('krs.show', $item->id) }}" 
                                       class="btn btn-sm btn-info" title="Lihat Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    
                                    @if(in_array(Auth::user()->role->name, ['admin_prodi', 'admin_fakultas', 'admin_universitas', 'dosen']) && $item->status == 'Menunggu')
                                        <form action="{{ route('krs.approve', $item->id) }}" 
                                              method="POST" style="display:inline;">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="btn btn-sm btn-success" 
                                                    title="Setujui" onclick="return confirm('Setujui KRS ini?')">
                                                <i class="bi bi-check-circle"></i>
                                            </button>
                                        </form>
                                        
                                        <form action="{{ route('krs.reject', $item->id) }}" 
                                              method="POST" style="display:inline;">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="btn btn-sm btn-danger" 
                                                    title="Tolak" onclick="return confirm('Tolak KRS ini?')">
                                                <i class="bi bi-x-circle"></i>
                                            </button>
                                        </form>
                                    @endif
                                    
                                    @if(Auth::user()->role->name == 'mahasiswa' && $item->status == 'Menunggu')
                                        <form action="{{ route('krs.destroy', $item->id) }}" 
                                              method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" 
                                                    title="Batalkan" onclick="return confirm('Batalkan KRS ini?')">
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
                {{ $krs->links() }}
            </div>
            @else
            <div class="text-center py-5">
                <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                <p class="mt-3 text-muted">Belum ada data KRS</p>
                @if(Auth::user()->role->name == 'mahasiswa')
                <a href="{{ route('krs.create') }}" class="btn btn-primary mt-2">
                    <i class="bi bi-plus-circle"></i> Isi KRS Sekarang
                </a>
                @endif
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
