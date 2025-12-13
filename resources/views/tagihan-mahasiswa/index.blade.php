@extends('layouts.app')

@section('title', 'Tagihan Mahasiswa')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Tagihan Mahasiswa</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Tagihan Mahasiswa</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('tagihan-mahasiswa.exportExcel', request()->query()) }}" class="btn btn-success me-2">
                <i class="bi bi-file-earmark-excel"></i> Export Excel
            </a>
            <a href="{{ route('tagihan-mahasiswa.exportPdf', request()->query()) }}" class="btn btn-danger me-2">
                <i class="bi bi-file-earmark-pdf"></i> Export PDF
            </a>
            <a href="{{ route('tagihan-mahasiswa.batch-create') }}" class="btn btn-success me-2">
                <i class="bi bi-plus-circle"></i> Buat Massal
            </a>
            <a href="{{ route('tagihan-mahasiswa.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Tambah Tagihan
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filter Card -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('tagihan-mahasiswa.index') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <input type="text" class="form-control" name="search" placeholder="Cari NIM/Nama/No. Tagihan" value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <select class="form-select" name="status">
                            <option value="">Semua Status</option>
                            <option value="Belum Dibayar" {{ request('status') == 'Belum Dibayar' ? 'selected' : '' }}>Belum Dibayar</option>
                            <option value="Dibayar Sebagian" {{ request('status') == 'Dibayar Sebagian' ? 'selected' : '' }}>Dibayar Sebagian</option>
                            <option value="Lunas" {{ request('status') == 'Lunas' ? 'selected' : '' }}>Lunas</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select" name="jenis_pembayaran_id">
                            <option value="">Semua Jenis</option>
                            @foreach($jenisPembayaran as $jp)
                                <option value="{{ $jp->id }}" {{ request('jenis_pembayaran_id') == $jp->id ? 'selected' : '' }}>{{ $jp->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select" name="tahun_akademik_id">
                            <option value="">Semua Tahun</option>
                            @foreach($tahunAkademik as $ta)
                                <option value="{{ $ta->id }}" {{ request('tahun_akademik_id') == $ta->id ? 'selected' : '' }}>{{ $ta->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select" name="semester_id">
                            <option value="">Semua Semester</option>
                            @foreach($semesters as $s)
                                <option value="{{ $s->id }}" {{ request('semester_id') == $s->id ? 'selected' : '' }}>{{ $s->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-1">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>No. Tagihan</th>
                            <th>Mahasiswa</th>
                            <th>Jenis Pembayaran</th>
                            <th>TA/Semester</th>
                            <th>Jumlah</th>
                            <th>Dibayar</th>
                            <th>Sisa</th>
                            <th>Jatuh Tempo</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tagihan as $t)
                        <tr>
                            <td><small class="font-monospace">{{ $t->nomor_tagihan }}</small></td>
                            <td>
                                <strong>{{ $t->mahasiswa->nama_mahasiswa }}</strong><br>
                                <small class="text-muted">{{ $t->mahasiswa->nim }}</small><br>
                                <small class="text-muted">{{ $t->mahasiswa->programStudi->nama_prodi }}</small>
                            </td>
                            <td>{{ $t->jenisPembayaran->nama }}</td>
                            <td>
                                <small>{{ $t->tahunAkademik->nama }}<br>{{ $t->semester->nama }}</small>
                            </td>
                            <td>Rp {{ number_format($t->jumlah_tagihan, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($t->jumlah_dibayar, 0, ',', '.') }}</td>
                            <td>
                                @if($t->sisa_tagihan > 0)
                                    <span class="text-danger fw-bold">Rp {{ number_format($t->sisa_tagihan, 0, ',', '.') }}</span>
                                @else
                                    <span class="text-success">Rp 0</span>
                                @endif
                            </td>
                            <td>
                                {{ $t->tanggal_jatuh_tempo->format('d/m/Y') }}
                                @if($t->tanggal_jatuh_tempo->isPast() && $t->status != 'Lunas')
                                    <br><span class="badge bg-danger">Lewat</span>
                                @endif
                            </td>
                            <td>
                                @if($t->status == 'Belum Dibayar')
                                    <span class="badge bg-danger">{{ $t->status }}</span>
                                @elseif($t->status == 'Dibayar Sebagian')
                                    <span class="badge bg-warning">{{ $t->status }}</span>
                                @elseif($t->status == 'Lunas')
                                    <span class="badge bg-success">{{ $t->status }}</span>
                                @else
                                    <span class="badge bg-secondary">{{ $t->status }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('tagihan-mahasiswa.show', $t->id) }}" class="btn btn-info" title="Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('tagihan-mahasiswa.edit', $t->id) }}" class="btn btn-warning" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    @if($t->jumlah_dibayar == 0)
                                    <form action="{{ route('tagihan-mahasiswa.destroy', $t->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus tagihan ini?')">
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
                        @empty
                        <tr>
                            <td colspan="10" class="text-center py-4">
                                <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                                <p class="text-muted mt-2">Tidak ada data tagihan</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $tagihan->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
