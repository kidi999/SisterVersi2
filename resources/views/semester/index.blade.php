@extends('layouts.app')

@section('title', 'Semester')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Daftar Semester</h1>
        <a href="{{ route('semester.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Tambah Semester
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Filter -->
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('semester.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Tahun Akademik</label>
                    <select name="tahun_akademik_id" class="form-select">
                        <option value="">Semua Tahun Akademik</option>
                        @foreach($tahunAkademiks as $ta)
                            <option value="{{ $ta->id }}" {{ request('tahun_akademik_id') == $ta->id ? 'selected' : '' }}>
                                {{ $ta->kode }} - {{ $ta->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Program Studi</label>
                    <select name="program_studi_id" class="form-select">
                        <option value="">Semua Program Studi</option>
                        <option value="" {{ request('type') == 'universitas' ? 'selected' : '' }}>Universitas (Tanpa Prodi)</option>
                        @foreach($programStudis as $prodi)
                            <option value="{{ $prodi->id }}" {{ request('program_studi_id') == $prodi->id ? 'selected' : '' }}>
                                {{ $prodi->nama_program_studi }} ({{ $prodi->fakultas->nama_fakultas }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="bi bi-search"></i> Filter
                    </button>
                    <a href="{{ route('semester.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-clockwise"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="semesterTable">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th width="15%">Tahun Akademik</th>
                            <th width="20%">Program Studi</th>
                            <th width="15%">Nama Semester</th>
                            <th width="8%">Nomor</th>
                            <th width="12%">Periode</th>
                            <th width="10%">Status</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($semesters as $index => $semester)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $semester->tahunAkademik->kode }}</td>
                            <td>
                                @if($semester->program_studi_id)
                                    <div>{{ $semester->programStudi->nama_prodi }}</div>
                                    <small class="text-primary">{{ $semester->programStudi->fakultas->nama_fakultas }}</small>
                                @else
                                    <span class="badge bg-primary">UNIVERSITAS</span>
                                @endif
                            </td>
                            <td>{{ $semester->nama_semester }}</td>
                            <td class="text-center">
                                <span class="badge bg-info">{{ $semester->nomor_semester }}</span>
                            </td>
                            <td>
                                <small>
                                    {{ \Carbon\Carbon::parse($semester->tanggal_mulai)->format('d/m/Y') }} -
                                    {{ \Carbon\Carbon::parse($semester->tanggal_selesai)->format('d/m/Y') }}
                                </small>
                            </td>
                            <td>
                                <div class="form-check form-switch">
                                    <input class="form-check-input toggle-active" type="checkbox" 
                                           id="status{{ $semester->id }}" 
                                           data-id="{{ $semester->id }}"
                                           {{ $semester->is_active ? 'checked' : '' }}>
                                    <label class="form-check-label" for="status{{ $semester->id }}">
                                        <span class="badge bg-{{ $semester->is_active ? 'success' : 'secondary' }}">
                                            {{ $semester->is_active ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                    </label>
                                </div>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('semester.show', $semester->id) }}" 
                                       class="btn btn-info" title="Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('semester.edit', $semester->id) }}" 
                                       class="btn btn-warning" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" class="btn btn-danger" 
                                            onclick="confirmDelete({{ $semester->id }})" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>

                                <form id="delete-form-{{ $semester->id }}" 
                                      action="{{ route('semester.destroy', $semester->id) }}" 
                                      method="POST" style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <div class="py-3">
                                    <i class="bi bi-inbox" style="font-size: 3rem; display: block;"></i>
                                    <p class="mt-2 mb-0">Belum ada data semester</p>
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
    @if($semesters->isNotEmpty())
    $('#semesterTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json',
        },
        order: [[1, 'desc'], [4, 'desc']],
        pageLength: 25
    });
    @endif

    // Toggle Active Status
    $('.toggle-active').on('change', function() {
        const checkbox = $(this);
        const semesterId = checkbox.data('id');
        const isActive = checkbox.is(':checked');
        
        Swal.fire({
            title: 'Konfirmasi',
            text: isActive ? 
                  'Mengaktifkan semester ini akan menonaktifkan semester lain dengan tahun akademik dan prodi yang sama. Lanjutkan?' : 
                  'Yakin ingin menonaktifkan semester ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Lanjutkan',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/semester/' + semesterId + '/toggle-active',
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
