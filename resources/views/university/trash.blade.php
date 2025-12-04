@extends('layouts.app')

@section('title', 'Universitas - Recycle Bin')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-trash"></i> Universitas - Recycle Bin
        </h1>
        <a href="{{ route('universities.index') }}" class="btn btn-secondary btn-icon-split">
            <span class="icon text-white-50">
                <i class="fas fa-arrow-left"></i>
            </span>
            <span class="text">Kembali</span>
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-danger text-white">
            <h6 class="m-0 font-weight-bold">
                <i class="fas fa-archive"></i> Data Universitas yang Dihapus
            </h6>
        </div>
        <div class="card-body">
            @if($universities->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="universityTrashTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th width="8%">Kode</th>
                                <th width="25%">Nama Universitas</th>
                                <th width="10%">Singkatan</th>
                                <th width="8%">Jenis</th>
                                <th width="10%">Akreditasi</th>
                                <th width="12%">Dihapus Oleh</th>
                                <th width="12%">Dihapus Pada</th>
                                <th width="10%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($universities as $index => $university)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td><span class="badge badge-secondary">{{ $university->kode }}</span></td>
                                    <td>{{ $university->nama }}</td>
                                    <td>{{ $university->singkatan ?? '-' }}</td>
                                    <td><span class="badge badge-info">{{ $university->jenis }}</span></td>
                                    <td>
                                        @if($university->akreditasi)
                                            <span class="badge badge-success">{{ $university->akreditasi }}</span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ $university->deleted_by ?? '-' }}</td>
                                    <td>
                                        @if($university->deleted_at)
                                            {{ $university->deleted_at->format('d/m/Y') }}
                                            <br>
                                            <small class="text-muted">{{ $university->deleted_at->format('H:i') }}</small>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" 
                                                    class="btn btn-sm btn-success restore-btn" 
                                                    data-id="{{ $university->id }}"
                                                    data-name="{{ $university->nama }}"
                                                    title="Restore">
                                                <i class="fas fa-undo"></i>
                                            </button>
                                            <button type="button" 
                                                    class="btn btn-sm btn-danger force-delete-btn" 
                                                    data-id="{{ $university->id }}"
                                                    data-name="{{ $university->nama }}"
                                                    title="Hapus Permanen">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>
                        Menampilkan {{ $universities->firstItem() }} sampai {{ $universities->lastItem() }} dari {{ $universities->total() }} data
                    </div>
                    <div>
                        {{ $universities->links() }}
                    </div>
                </div>
            @else
                <div class="alert alert-info mb-0">
                    <i class="fas fa-info-circle"></i> Tidak ada data universitas di tempat sampah.
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Restore -->
<div class="modal fade" id="restoreModal" tabindex="-1" role="dialog" aria-labelledby="restoreModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="restoreModalLabel">
                    <i class="fas fa-undo"></i> Konfirmasi Restore
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Anda akan memulihkan universitas: <strong id="universityNameRestore"></strong></p>
                <p>Data akan dikembalikan ke daftar universitas.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <form id="restoreForm" method="POST" style="display: inline;">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-undo"></i> Restore
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Force Delete -->
<div class="modal fade" id="forceDeleteModal" tabindex="-1" role="dialog" aria-labelledby="forceDeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="forceDeleteModalLabel">
                    <i class="fas fa-exclamation-triangle"></i> Konfirmasi Hapus Permanen
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <strong>PERINGATAN!</strong><br>
                    Tindakan ini akan menghapus data secara permanen dan tidak dapat dibatalkan!
                </div>
                <p>Anda akan menghapus universitas: <strong id="universityNameForceDelete"></strong></p>
                <p>Semua file yang terlampir juga akan dihapus dari server.</p>
                
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="confirmForceDelete">
                    <label class="form-check-label" for="confirmForceDelete">
                        Ya, saya yakin ingin menghapus permanen data ini
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <form id="forceDeleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" id="forceDeleteConfirmBtn" disabled>
                        <i class="fas fa-trash"></i> Hapus Permanen
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
    @if($universities->count() > 0)
    $('#universityTrashTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
        },
        order: [[7, 'desc']], // Sort by deleted_at column
        pageLength: 25,
        columnDefs: [
            { orderable: false, targets: [8] } // Disable sorting for action column
        ]
    });
    @endif

    // Restore Button Handler
    $('.restore-btn').on('click', function() {
        const universityId = $(this).data('id');
        const universityName = $(this).data('name');
        
        $('#universityNameRestore').text(universityName);
        $('#restoreForm').attr('action', `/universities/${universityId}/restore`);
        $('#restoreModal').modal('show');
    });

    // Force Delete Button Handler
    $('.force-delete-btn').on('click', function() {
        const universityId = $(this).data('id');
        const universityName = $(this).data('name');
        
        $('#universityNameForceDelete').text(universityName);
        $('#forceDeleteForm').attr('action', `/universities/${universityId}/force-delete`);
        $('#confirmForceDelete').prop('checked', false);
        $('#forceDeleteConfirmBtn').prop('disabled', true);
        $('#forceDeleteModal').modal('show');
    });

    // Enable/Disable Force Delete Button based on Checkbox
    $('#confirmForceDelete').on('change', function() {
        $('#forceDeleteConfirmBtn').prop('disabled', !$(this).is(':checked'));
    });

    // Auto dismiss alerts after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
});
</script>
@endpush
