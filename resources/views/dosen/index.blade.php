@extends('layouts.app')

@section('title', 'Data Dosen')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Data Dosen</h1>
        @if(Auth::user()->hasRole(['super_admin', 'admin_universitas', 'admin_fakultas', 'admin_prodi']))
        <a href="{{ route('dosen.create') }}" class="btn btn-primary btn-icon-split">
            <span class="icon text-white-50">
                <i class="fas fa-plus"></i>
            </span>
            <span class="text">Tambah Dosen</span>
        </a>
        @endif
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter Data</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('dosen.index') }}">
                <div class="row">
                    <div class="col-md-2 mb-3">
                        <label for="level_dosen">Level Dosen</label>
                        <select name="level_dosen" id="level_dosen" class="form-control">
                            <option value="">-- Semua Level --</option>
                            <option value="universitas" {{ request('level_dosen') == 'universitas' ? 'selected' : '' }}>Universitas</option>
                            <option value="fakultas" {{ request('level_dosen') == 'fakultas' ? 'selected' : '' }}>Fakultas</option>
                            <option value="prodi" {{ request('level_dosen') == 'prodi' ? 'selected' : '' }}>Program Studi</option>
                        </select>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label for="fakultas_id">Fakultas</label>
                        <select name="fakultas_id" id="fakultas_id" class="form-control">
                            <option value="">-- Semua Fakultas --</option>
                            @foreach($fakultas as $fak)
                            <option value="{{ $fak->id }}" {{ request('fakultas_id') == $fak->id ? 'selected' : '' }}>
                                {{ $fak->nama_fakultas }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label for="program_studi_id">Program Studi</label>
                        <select name="program_studi_id" id="program_studi_id" class="form-control">
                            <option value="">-- Semua Program Studi --</option>
                            @foreach($programStudi as $prodi)
                            <option value="{{ $prodi->id }}" 
                                    data-fakultas="{{ $prodi->fakultas_id }}"
                                    {{ request('program_studi_id') == $prodi->id ? 'selected' : '' }}>
                                {{ $prodi->nama_prodi }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label for="status">Status</label>
                        <select name="status" id="status" class="form-control">
                            <option value="">-- Semua Status --</option>
                            <option value="Aktif" {{ request('status') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="Non-Aktif" {{ request('status') == 'Non-Aktif' ? 'selected' : '' }}>Non-Aktif</option>
                            <option value="Cuti" {{ request('status') == 'Cuti' ? 'selected' : '' }}>Cuti</option>
                        </select>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label for="jabatan_akademik">Jabatan Akademik</label>
                        <select name="jabatan_akademik" id="jabatan_akademik" class="form-control">
                            <option value="">-- Semua --</option>
                            <option value="Asisten Ahli" {{ request('jabatan_akademik') == 'Asisten Ahli' ? 'selected' : '' }}>Asisten Ahli</option>
                            <option value="Lektor" {{ request('jabatan_akademik') == 'Lektor' ? 'selected' : '' }}>Lektor</option>
                            <option value="Lektor Kepala" {{ request('jabatan_akademik') == 'Lektor Kepala' ? 'selected' : '' }}>Lektor Kepala</option>
                            <option value="Guru Besar" {{ request('jabatan_akademik') == 'Guru Besar' ? 'selected' : '' }}>Guru Besar</option>
                        </select>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label for="search">Cari</label>
                        <input type="text" name="search" id="search" class="form-control" 
                               placeholder="Nama, NIP, NIDN..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Filter
                        </button>
                        <a href="{{ route('dosen.index') }}" class="btn btn-secondary">
                            <i class="fas fa-redo"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Dosen</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th width="5%">No</th>
                            <th width="12%">NIP/NIDN</th>
                            <th width="18%">Nama Dosen</th>
                            <th width="10%">Level</th>
                            <th width="20%">Scope</th>
                            <th width="12%">Email</th>
                            <th width="8%">Status</th>
                            <th width="5%">File</th>
                            <th width="10%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dosen as $item)
                        <tr>
                            <td>{{ $dosen->firstItem() + $loop->index }}</td>
                            <td>
                                <strong>{{ $item->nip }}</strong>
                                @if($item->nidn)
                                <br><small class="text-muted">{{ $item->nidn }}</small>
                                @endif
                            </td>
                            <td>
                                <strong>{{ $item->nama_dosen }}</strong>
                                <br><small class="text-muted">{{ $item->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</small>
                                @if($item->jabatan_akademik)
                                <br><small class="badge badge-secondary">{{ $item->jabatan_akademik }}</small>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-{{ $item->level_badge }}">
                                    {{ ucfirst($item->level_dosen) }}
                                </span>
                            </td>
                            <td>
                                <small>{{ $item->scope_label }}</small>
                            </td>
                            <td>{{ $item->email }}</td>
                            <td>
                                <span class="badge badge-{{ $item->status_badge }}">
                                    {{ $item->status }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if($item->files->count() > 0)
                                <span class="badge badge-info">
                                    <i class="bi bi-paperclip"></i> {{ $item->files->count() }}
                                </span>
                                @else
                                <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('dosen.show', $item->id) }}" class="btn btn-info btn-sm" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if(Auth::user()->hasRole(['super_admin', 'admin_universitas', 'admin_fakultas', 'admin_prodi']))
                                <a href="{{ route('dosen.edit', $item->id) }}" class="btn btn-warning btn-sm" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('dosen.destroy', $item->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" title="Hapus"
                                            onclick="return confirm('Yakin ingin menghapus data ini?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center">Tidak ada data dosen</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $dosen->links() }}
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Filter program studi based on fakultas
    $('#fakultas_id').change(function() {
        var fakultasId = $(this).val();
        var prodiSelect = $('#program_studi_id');
        
        if (fakultasId) {
            prodiSelect.find('option').hide();
            prodiSelect.find('option[value=""]').show();
            prodiSelect.find('option[data-fakultas="' + fakultasId + '"]').show();
            prodiSelect.val('');
        } else {
            prodiSelect.find('option').show();
        }
    });
    
    // Trigger on page load if fakultas is selected
    if ($('#fakultas_id').val()) {
        $('#fakultas_id').trigger('change');
        @if(request('program_studi_id'))
        $('#program_studi_id').val('{{ request('program_studi_id') }}');
        @endif
    }
});
</script>
@endsection
