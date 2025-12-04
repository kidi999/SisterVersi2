@extends('layouts.app')

@section('title', 'Semester - Recycle Bin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="bi bi-trash"></i> Semester - Recycle Bin
        </h1>
        <a href="{{ route('semester.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-header bg-danger text-white">
            <h5 class="card-title mb-0">
                <i class="bi bi-archive"></i> Data Semester yang Dihapus
            </h5>
        </div>
        <div class="card-body">
            @if($semesters->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="semesterTrashTable">
                        <thead class="table-dark">
                            <tr>
                                <th width="5%">No</th>
                                <th width="15%">Tahun Akademik</th>
                                <th width="20%">Program Studi</th>
                                <th width="15%">Nama Semester</th>
                                <th width="10%">Nomor</th>
                                <th width="15%">Dihapus Oleh</th>
                                <th width="10%">Dihapus Pada</th>
                                <th width="10%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($semesters as $key => $semester)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>
                                        <strong>{{ $semester->tahunAkademik->kode }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $semester->tahunAkademik->nama }}</small>
                                    </td>
                                    <td>
                                        @if($semester->program_studi_id)
                                            {{ $semester->programStudi->nama_prodi }}
                                            <br>
                                            <small class="text-primary">
                                                {{ $semester->programStudi->fakultas->nama_fakultas }}
                                            </small>
                                        @else
                                            <span class="badge bg-info">
                                                <i class="bi bi-globe"></i> UNIVERSITAS
                                            </span>
                                        @endif
                                    </td>
                                    <td>{{ $semester->nama_semester }}</td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $semester->nomor_semester }}</span>
                                    </td>
                                    <td>{{ $semester->deleted_by ?? '-' }}</td>
                                    <td>
                                        @if($semester->deleted_at)
                                            {{ $semester->deleted_at->format('d/m/Y') }}
                                            <br>
                                            <small class="text-muted">{{ $semester->deleted_at->format('H:i') }}</small>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" 
                                                    class="btn btn-sm btn-success restore-btn" 
                                                    data-id="{{ $semester->id }}"
                                                    data-nama="{{ $semester->nama_semester }}"
                                                    title="Restore">
                                                <i class="bi bi-arrow-clockwise"></i>
                                            </button>
                                            <button type="button" 
                                                    class="btn btn-sm btn-danger force-delete-btn" 
                                                    data-id="{{ $semester->id }}"
                                                    data-nama="{{ $semester->nama_semester }}"
                                                    title="Hapus Permanen">
                                                <i class="bi bi-x-circle"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-info mb-0">
                    <i class="bi bi-info-circle"></i> Tidak ada data semester di tempat sampah.
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Force Delete -->
<div class="modal fade" id="forceDeleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="bi bi-exclamation-triangle"></i> Konfirmasi Hapus Permanen
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <strong>PERINGATAN!</strong><br>
                    Tindakan ini akan menghapus data secara permanen dan tidak dapat dibatalkan!
                </div>
                <p>Anda akan menghapus semester: <strong id="semesterNamaForceDelete"></strong></p>
                <p>Semua file yang terlampir juga akan dihapus dari server.</p>
                
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="confirmForceDelete">
                    <label class="form-check-label" for="confirmForceDelete">
                        Ya, saya yakin ingin menghapus permanen data ini
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form id="forceDeleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" id="forceDeleteConfirmBtn" disabled>
                        <i class="bi bi-x-circle"></i> Hapus Permanen
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize DataTable if data exists
    @if($semesters->count() > 0)
    $('#semesterTrashTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
        },
        order: [[6, 'desc']], // Sort by deleted_at column
        pageLength: 25
    });
    @endif

    // Restore Button Handler
    $('.restore-btn').on('click', function() {
        const semesterId = $(this).data('id');
        const semesterNama = $(this).data('nama');
        
        Swal.fire({
            title: 'Restore Semester',
            html: `Apakah Anda yakin ingin memulihkan semester:<br><strong>${semesterNama}</strong>?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#198754',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="bi bi-arrow-clockwise"></i> Ya, Pulihkan',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/semester/${semesterId}/restore`,
                    type: 'PATCH',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        Swal.fire({
                            title: 'Berhasil!',
                            text: response.message,
                            icon: 'success',
                            confirmButtonColor: '#0d6efd'
                        }).then(() => {
                            window.location.reload();
                        });
                    },
                    error: function(xhr) {
                        Swal.fire({
                            title: 'Error!',
                            text: xhr.responseJSON?.message || 'Gagal memulihkan semester',
                            icon: 'error',
                            confirmButtonColor: '#dc3545'
                        });
                    }
                });
            }
        });
    });

    // Force Delete Button Handler
    $('.force-delete-btn').on('click', function() {
        const semesterId = $(this).data('id');
        const semesterNama = $(this).data('nama');
        
        $('#semesterNamaForceDelete').text(semesterNama);
        $('#forceDeleteForm').attr('action', `/semester/${semesterId}/force-delete`);
        $('#confirmForceDelete').prop('checked', false);
        $('#forceDeleteConfirmBtn').prop('disabled', true);
        
        const modal = new bootstrap.Modal(document.getElementById('forceDeleteModal'));
        modal.show();
    });

    // Enable/Disable Force Delete Button based on Checkbox
    $('#confirmForceDelete').on('change', function() {
        $('#forceDeleteConfirmBtn').prop('disabled', !$(this).is(':checked'));
    });

    // Force Delete Form Submit Handler
    $('#forceDeleteForm').on('submit', function(e) {
        e.preventDefault();
        
        const form = $(this);
        const actionUrl = form.attr('action');
        
        $.ajax({
            url: actionUrl,
            type: 'DELETE',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                bootstrap.Modal.getInstance(document.getElementById('forceDeleteModal')).hide();
                
                Swal.fire({
                    title: 'Berhasil!',
                    text: response.message,
                    icon: 'success',
                    confirmButtonColor: '#0d6efd'
                }).then(() => {
                    window.location.reload();
                });
            },
            error: function(xhr) {
                bootstrap.Modal.getInstance(document.getElementById('forceDeleteModal')).hide();
                
                Swal.fire({
                    title: 'Error!',
                    text: xhr.responseJSON?.message || 'Gagal menghapus semester',
                    icon: 'error',
                    confirmButtonColor: '#dc3545'
                });
            }
        });
    });
});
</script>
@endpush
