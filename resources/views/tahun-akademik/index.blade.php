@extends('layouts.app')

@section('title', 'Tahun Akademik')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Tahun Akademik</h1>
        <a href="{{ route('tahun-akademik.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Tambah Tahun Akademik
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
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="tahunAkademikTable">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th width="15%">Kode</th>
                            <th width="25%">Nama</th>
                            <th width="15%">Periode</th>
                            <th width="10%">Semester</th>
                            <th width="10%">Status</th>
                            <th width="20%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tahunAkademiks as $index => $ta)
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
                                <span class="badge bg-info">{{ $ta->semesters_count }} Semester</span>
                            </td>
                            <td>
                                <div class="form-check form-switch">
                                    <input class="form-check-input toggle-active" type="checkbox" 
                                           id="status{{ $ta->id }}" 
                                           data-id="{{ $ta->id }}"
                                           {{ $ta->is_active ? 'checked' : '' }}>
                                    <label class="form-check-label" for="status{{ $ta->id }}">
                                        <span class="badge bg-{{ $ta->is_active ? 'success' : 'secondary' }}">
                                            {{ $ta->is_active ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                    </label>
                                </div>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('tahun-akademik.show', $ta->id) }}" 
                                       class="btn btn-info" title="Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('tahun-akademik.edit', $ta->id) }}" 
                                       class="btn btn-warning" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" class="btn btn-danger" 
                                            onclick="confirmDelete({{ $ta->id }})" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>

                                <form id="delete-form-{{ $ta->id }}" 
                                      action="{{ route('tahun-akademik.destroy', $ta->id) }}" 
                                      method="POST" style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <div class="py-3">
                                    <i class="bi bi-inbox" style="font-size: 3rem; display: block;"></i>
                                    <p class="mt-2 mb-0">Belum ada data tahun akademik</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
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
    // Initialize DataTable
    @if($tahunAkademiks->isNotEmpty())
    $('#tahunAkademikTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json',
        },
        order: [[3, 'desc']], // Sort by periode (tahun mulai) descending
        pageLength: 25
    });
    @endif

    // Toggle Active Status
    $('.toggle-active').on('change', function() {
        const checkbox = $(this);
        const tahunAkademikId = checkbox.data('id');
        const isActive = checkbox.is(':checked');
        
        Swal.fire({
            title: 'Konfirmasi',
            text: isActive ? 
                  'Mengaktifkan tahun akademik ini akan menonaktifkan tahun akademik yang lain. Lanjutkan?' : 
                  'Yakin ingin menonaktifkan tahun akademik ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Lanjutkan',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/tahun-akademik/' + tahunAkademikId + '/toggle-active',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        Swal.fire({
                            title: 'Berhasil!',
                            text: response.message,
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        checkbox.prop('checked', !isActive);
                        Swal.fire({
                            title: 'Error!',
                            text: xhr.responseJSON?.message || 'Terjadi kesalahan',
                            icon: 'error'
                        });
                    }
                });
            } else {
                checkbox.prop('checked', !isActive);
            }
        });
    });
});

function confirmDelete(id) {
    Swal.fire({
        title: 'Konfirmasi Hapus',
        text: 'Data akan dipindahkan ke recycle bin. Yakin ingin menghapus?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('delete-form-' + id).submit();
        }
    });
}
</script>
@endpush
