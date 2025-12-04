@extends('layouts.app')

@section('title', 'Recycle Bin - Jadwal Kuliah')
@section('header', 'Recycle Bin - Jadwal Kuliah')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Recycle Bin - Jadwal Kuliah</h1>
        <a href="{{ route('jadwal-kuliah.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            @if($jadwal->count() > 0)
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> Data yang dihapus akan tersimpan di recycle bin. Anda dapat memulihkan atau menghapus permanen.
                </div>

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
                                <th width="15%">Dihapus</th>
                                <th width="15%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($jadwal as $j)
                                <tr>
                                    <td>{{ $jadwal->firstItem() + $loop->index }}</td>
                                    <td>
                                        <strong>{{ $j->kelas->mataKuliah->nama_mk }}</strong><br>
                                        <small class="text-muted">{{ $j->kelas->mataKuliah->kode_mk }}</small>
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
                                        <small>
                                            {{ $j->deleted_at->format('d/m/Y H:i') }}<br>
                                            @if($j->deletedBy)
                                                oleh {{ $j->deletedBy->name }}
                                            @endif
                                        </small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <form action="{{ route('jadwal-kuliah.restore', $j->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin memulihkan jadwal ini?')">
                                                @csrf
                                                <button type="submit" class="btn btn-success" title="Pulihkan">
                                                    <i class="bi bi-arrow-counterclockwise"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('jadwal-kuliah.force-delete', $j->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus permanen? Data tidak dapat dikembalikan!')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger" title="Hapus Permanen">
                                                    <i class="bi bi-x-circle"></i>
                                                </button>
                                            </form>
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
                    <i class="bi bi-trash" style="font-size: 4rem; color: #ccc;"></i>
                    <p class="text-muted mt-3">Recycle bin kosong</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
