@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Sampah Kelas</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('kelas.index') }}">Kelas</a></li>
                    <li class="breadcrumb-item active">Sampah</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('kelas.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Kode Kelas</th>
                            <th>Nama Kelas</th>
                            <th>Mata Kuliah</th>
                            <th>Dosen</th>
                            <th>Tahun Ajaran</th>
                            <th>Dihapus oleh</th>
                            <th>Tanggal Hapus</th>
                            <th width="150">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($kelas as $item)
                            <tr>
                                <td><span class="badge bg-secondary">{{ $item->kode_kelas }}</span></td>
                                <td>{{ $item->nama_kelas }}</td>
                                <td>
                                    {{ $item->mataKuliah->nama }}<br>
                                    <small class="text-muted">{{ $item->mataKuliah->programStudi->nama }}</small>
                                </td>
                                <td>{{ $item->dosen->nama }}</td>
                                <td>{{ $item->tahun_ajaran }} - {{ $item->semester }}</td>
                                <td>{{ $item->deleted_by ?? '-' }}</td>
                                <td>{{ $item->deleted_at ? $item->deleted_at->format('d/m/Y H:i') : '-' }}</td>
                                <td>
                                    <form action="{{ route('kelas.restore', $item->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-success btn-sm" title="Pulihkan">
                                            <i class="bi bi-arrow-counterclockwise"></i>
                                        </button>
                                    </form>
                                    <button type="button" class="btn btn-danger btn-sm" title="Hapus Permanen"
                                            onclick="confirmForceDelete('{{ $item->id }}', '{{ $item->nama_kelas }}')">
                                        <i class="bi bi-trash-fill"></i>
                                    </button>

                                    <form id="force-delete-form-{{ $item->id }}" 
                                          action="{{ route('kelas.force-delete', $item->id) }}" 
                                          method="POST" class="d-none">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                                    <p class="mt-2 text-muted">Tidak ada data di sampah</p>
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
function confirmForceDelete(id, nama) {
    if (confirm('Apakah Anda yakin ingin menghapus permanen kelas "' + nama + '"?\n\nData yang dihapus permanen tidak dapat dipulihkan!')) {
        document.getElementById('force-delete-form-' + id).submit();
    }
}
</script>
@endpush
@endsection
