@extends('layouts.app')

@section('title', 'Recycle Bin - Tahun Akademik')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="bi bi-trash"></i> Recycle Bin - Tahun Akademik
        </h1>
        <a href="{{ route('tahun-akademik.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-circle"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="card">
        <div class="card-body">
            @if($tahunAkademiks->isEmpty())
                <div class="text-center text-muted py-5">
                    <i class="bi bi-trash" style="font-size: 3rem;"></i>
                    <p class="mt-2">Recycle bin kosong</p>
                    <small>Data yang dihapus akan muncul di sini</small>
                </div>
            @else
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle"></i>
                    <strong>Perhatian:</strong> Data di recycle bin dapat dipulihkan atau dihapus permanen.
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="trashTable">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th width="15%">Kode</th>
                                <th width="25%">Nama</th>
                                <th width="15%">Periode</th>
                                <th width="10%">Semester</th>
                                <th width="15%">Dihapus</th>
                                <th width="15%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tahunAkademiks as $index => $ta)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td><strong>{{ $ta->kode }}</strong></td>
                                <td>{{ $ta->nama }}</td>
                                <td>
                                    <div>{{ $ta->tahun_mulai }}/{{ $ta->tahun_selesai }}</div>
                                    <small class="text-muted">
                                        {{ \Carbon\Carbon::parse($ta->tanggal_mulai)->format('d/m/Y') }} - 
                                        {{ \Carbon\Carbon::parse($ta->tanggal_selesai)->format('d/m/Y') }}
                                    </small>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-info">{{ $ta->semesters->count() }} Semester</span>
                                </td>
                                <td>
                                    <div><small class="text-muted">{{ $ta->deleted_by ?? '-' }}</small></div>
                                    <div><small class="text-muted">{{ $ta->deleted_at ? $ta->deleted_at->format('d/m/Y H:i') : '-' }}</small></div>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <button type="button" 
                                                class="btn btn-success" 
                                                onclick="confirmRestore({{ $ta->id }})"
                                                title="Restore">
                                            <i class="bi bi-arrow-counterclockwise"></i>
                                        </button>
                                        <button type="button" 
                                                class="btn btn-danger" 
                                                onclick="confirmForceDelete({{ $ta->id }})"
                                                title="Hapus Permanen">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                    </div>

                                    <!-- Restore Form -->
                                    <form id="restore-form-{{ $ta->id }}" 
                                          action="{{ route('tahun-akademik.restore', $ta->id) }}" 
                                          method="POST" 
                                          style="display: none;">
                                        @csrf
                                        @method('PATCH')
                                    </form>

                                    <!-- Force Delete Form -->
                                    <form id="force-delete-form-{{ $ta->id }}" 
                                          action="{{ route('tahun-akademik.force-delete', $ta->id) }}" 
                                          method="POST" 
                                          style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@endpush

@push('scripts')
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    $('#trashTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json',
        },
        order: [[5, 'desc']], // Sort by deleted_at descending
        pageLength: 25
    });
});

function confirmRestore(id) {
    Swal.fire({
        title: 'Konfirmasi Restore',
        text: 'Yakin ingin memulihkan data ini?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Pulihkan',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('restore-form-' + id).submit();
        }
    });
}

function confirmForceDelete(id) {
    Swal.fire({
        title: 'Konfirmasi Hapus Permanen',
        html: '<strong class="text-danger">PERINGATAN!</strong><br>Data akan dihapus permanen dan tidak dapat dipulihkan.<br><br>Yakin ingin melanjutkan?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Hapus Permanen',
        cancelButtonText: 'Batal',
        input: 'checkbox',
        inputValue: 0,
        inputPlaceholder: 'Saya memahami data akan dihapus permanen',
        inputValidator: (result) => {
            return !result && 'Anda harus mencentang checkbox untuk melanjutkan'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('force-delete-form-' + id).submit();
        }
    });
}
</script>
@endpush
