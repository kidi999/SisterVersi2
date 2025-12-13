@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Manajemen Kelas</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Kelas</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('kelas.exportExcel', request()->query()) }}" class="btn btn-success me-2">
                <i class="bi bi-file-earmark-excel"></i> Export Excel
            </a>
            <a href="{{ route('kelas.exportPdf', request()->query()) }}" class="btn btn-danger me-2">
                <i class="bi bi-file-earmark-pdf"></i> Export PDF
            </a>
            <a href="{{ route('kelas.trash') }}" class="btn btn-outline-secondary me-2">
                <i class="bi bi-trash"></i> Sampah
            </a>
            <a href="{{ route('kelas.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> Tambah Kelas
            </a>
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

    <div class="card">
        <div class="card-body">
            <form method="GET" action="{{ route('kelas.index') }}" class="mb-4">
                <div class="row g-3">
                    <div class="col-md-4">
                        <input type="text" name="search" class="form-control" placeholder="Cari kelas..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <select name="tahun_ajaran" class="form-select">
                            <option value="">Semua Tahun Ajaran</option>
                            @foreach($tahunAjaranList as $ta)
                                <option value="{{ $ta }}" {{ request('tahun_ajaran') == $ta ? 'selected' : '' }}>
                                    {{ $ta }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="semester" class="form-select">
                            <option value="">Semua Semester</option>
                            <option value="Ganjil" {{ request('semester') == 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                            <option value="Genap" {{ request('semester') == 'Genap' ? 'selected' : '' }}>Genap</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-search"></i> Filter
                        </button>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Kode Kelas</th>
                            <th>Nama Kelas</th>
                            <th>Mata Kuliah</th>
                            <th>Dosen</th>
                            <th>Tahun Ajaran</th>
                            <th>Semester</th>
                            <th>Kapasitas</th>
                            <th>Terisi</th>
                            <th width="150">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($kelas as $item)
                            <tr>
                                <td><span class="badge bg-primary">{{ $item->kode_kelas }}</span></td>
                                <td>{{ $item->nama_kelas }}</td>
                                <td>
                                    {{ $item->mataKuliah->nama }}
                                    <br><small class="text-muted">{{ $item->mataKuliah->programStudi->nama }}</small>
                                </td>
                                <td>{{ $item->dosen->nama }}</td>
                                <td>{{ $item->tahun_ajaran }}</td>
                                <td>
                                    <span class="badge {{ $item->semester == 'Ganjil' ? 'bg-info' : 'bg-success' }}">
                                        {{ $item->semester }}
                                    </span>
                                </td>
                                <td>{{ $item->kapasitas }}</td>
                                <td>
                                    <span class="badge {{ $item->terisi >= $item->kapasitas ? 'bg-danger' : 'bg-secondary' }}">
                                        {{ $item->terisi }}/{{ $item->kapasitas }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('kelas.show', $item) }}" class="btn btn-info" title="Detail">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('kelas.edit', $item) }}" class="btn btn-warning" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button" class="btn btn-danger" title="Hapus" 
                                                onclick="confirmDelete('{{ $item->id }}', '{{ $item->nama_kelas }}')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>

                                    <form id="delete-form-{{ $item->id }}" 
                                          action="{{ route('kelas.destroy', $item) }}" 
                                          method="POST" class="d-none">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4">
                                    <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                                    <p class="mt-2 text-muted">Tidak ada data kelas</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-3">
                <div>
                    Menampilkan {{ $kelas->firstItem() ?? 0 }} - {{ $kelas->lastItem() ?? 0 }} dari {{ $kelas->total() }} kelas
                </div>
                <div>
                    {{ $kelas->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function confirmDelete(id, nama) {
    if (confirm('Apakah Anda yakin ingin menghapus kelas "' + nama + '"?')) {
        document.getElementById('delete-form-' + id).submit();
    }
}
</script>
@endpush
@endsection
