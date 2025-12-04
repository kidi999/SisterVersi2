@extends('layouts.app')

@section('title', 'Program Studi - Recycle Bin')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-trash"></i> Program Studi - Recycle Bin
        </h1>
        <a href="{{ route('program-studi.index') }}" class="btn btn-secondary btn-icon-split">
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
                <i class="fas fa-archive"></i> Data Program Studi yang Dihapus
            </h6>
        </div>
        <div class="card-body">
            @if($programStudi->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" width="100%">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th width="10%">Kode</th>
                                <th width="25%">Nama Program Studi</th>
                                <th width="20%">Fakultas</th>
                                <th width="8%">Jenjang</th>
                                <th width="15%">Dihapus Pada</th>
                                <th width="17%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($programStudi as $index => $prodi)
                            <tr>
                                <td>{{ $programStudi->firstItem() + $index }}</td>
                                <td>{{ $prodi->kode_prodi }}</td>
                                <td>{{ $prodi->nama_prodi }}</td>
                                <td>{{ $prodi->fakultas->nama_fakultas }}</td>
                                <td><span class="badge badge-info">{{ $prodi->jenjang }}</span></td>
                                <td>{{ $prodi->deleted_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <form action="{{ route('program-studi.restore', $prodi->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-success" title="Pulihkan" 
                                                onclick="return confirm('Apakah Anda yakin ingin memulihkan data ini?')">
                                            <i class="fas fa-undo"></i> Pulihkan
                                        </button>
                                    </form>
                                    <button type="button" class="btn btn-sm btn-danger" title="Hapus Permanen"
                                            onclick="confirmForceDelete({{ $prodi->id }}, '{{ $prodi->nama_prodi }}')">
                                        <i class="fas fa-trash-alt"></i> Hapus Permanen
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-3">
                    {{ $programStudi->links() }}
                </div>
            @else
                <div class="alert alert-info text-center">
                    <i class="fas fa-info-circle"></i> Tidak ada data program studi yang dihapus
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Force Delete Modal -->
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
            <form id="forceDeleteForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Peringatan!</strong> Tindakan ini tidak dapat dibatalkan!
                    </div>
                    <p>Apakah Anda yakin ingin menghapus permanen program studi <strong id="prodiName"></strong>?</p>
                    <p class="text-danger">Semua data terkait termasuk dokumen akan dihapus secara permanen.</p>
                    
                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="confirmDelete" required>
                            <label class="custom-control-label" for="confirmDelete">
                                Ya, saya mengerti dan ingin menghapus data ini secara permanen
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger" id="confirmForceDeleteBtn" disabled>
                        <i class="fas fa-trash-alt"></i> Hapus Permanen
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function confirmForceDelete(id, name) {
    $('#prodiName').text(name);
    $('#forceDeleteForm').attr('action', '{{ url("program-studi") }}/' + id + '/force-delete');
    $('#confirmDelete').prop('checked', false);
    $('#confirmForceDeleteBtn').prop('disabled', true);
    $('#forceDeleteModal').modal('show');
}

$('#confirmDelete').change(function() {
    $('#confirmForceDeleteBtn').prop('disabled', !this.checked);
});
</script>
@endpush
@endsection
