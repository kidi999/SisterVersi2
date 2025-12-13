@extends('layouts.app')

@section('title', 'Desa/Kelurahan')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Manajemen Desa/Kelurahan</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('village.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Tambah Desa/Kelurahan
            </a>
            <a href="{{ route('village.exportExcel', request()->all()) }}" class="btn btn-success">
                <i class="bi bi-file-earmark-excel"></i> Export Excel
            </a>
            <a href="{{ route('village.exportCsv', request()->all()) }}" class="btn btn-success">
                <i class="bi bi-file-earmark-excel"></i> Export CSV
            </a>
            <a href="{{ route('village.exportPdf', request()->all()) }}" class="btn btn-danger">
                <i class="bi bi-file-earmark-pdf"></i> Export PDF
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-header bg-primary text-white">
            <i class="bi bi-geo-alt"></i> Data Desa/Kelurahan
        </div>
        <div class="card-body">
            <!-- Filter Form -->
            <form method="GET" action="{{ route('village.index') }}" class="mb-4">
                <div class="row g-3">
                    <div class="col-md-2">
                        <label for="province_id" class="form-label">Provinsi</label>
                        <select class="form-select" id="province_id" name="province_id">
                            <option value="">Semua Provinsi</option>
                            @foreach($provinces as $province)
                                <option value="{{ $province->id }}" {{ request('province_id') == $province->id ? 'selected' : '' }}>
                                    {{ $province->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="regency_id" class="form-label">Kabupaten/Kota</label>
                        <select class="form-select" id="regency_id" name="regency_id">
                            <option value="">Semua Kabupaten/Kota</option>
                            @foreach($regencies as $regency)
                                <option value="{{ $regency->id }}" 
                                        data-province="{{ $regency->province_id }}"
                                        {{ request('regency_id') == $regency->id ? 'selected' : '' }}>
                                    {{ $regency->type }} {{ $regency->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="sub_regency_id" class="form-label">Kecamatan</label>
                        <select class="form-select" id="sub_regency_id" name="sub_regency_id">
                            <option value="">Semua Kecamatan</option>
                            @foreach($subRegencies as $subRegency)
                                <option value="{{ $subRegency->id }}" 
                                        data-regency="{{ $subRegency->regency_id }}"
                                        {{ request('sub_regency_id') == $subRegency->id ? 'selected' : '' }}>
                                    {{ $subRegency->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="search" class="form-label">Pencarian</label>
                        <input type="text" class="form-control" id="search" name="search" 
                               placeholder="Cari nama, kode, atau kode pos..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search"></i> Filter
                            </button>
                            <a href="{{ route('village.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </form>

            @if($villages->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th width="4%">No</th>
                                <th width="8%">Kode</th>
                                <th width="18%">Nama Desa/Kelurahan</th>
                                <th width="15%">Kecamatan</th>
                                <th width="15%">Kabupaten/Kota</th>
                                <th width="12%">Provinsi</th>
                                <th width="8%">Kode Pos</th>
                                <th width="8%">Dokumen</th>
                                <th width="12%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($villages as $key => $village)
                                <tr>
                                    <td>{{ ($villages->currentPage() - 1) * $villages->perPage() + $key + 1 }}</td>
                                    <td><code>{{ $village->code }}</code></td>
                                    <td><strong>{{ $village->name }}</strong></td>
                                    <td>{{ $village->subRegency->name }}</td>
                                    <td>{{ $village->subRegency->regency->type }} {{ $village->subRegency->regency->name }}</td>
                                    <td>{{ $village->subRegency->regency->province->name }}</td>
                                    <td>
                                        @if($village->postal_code)
                                            <span class="badge bg-info">{{ $village->postal_code }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($village->files->count() > 0)
                                            <span class="badge bg-success">
                                                <i class="bi bi-file-earmark-check"></i> {{ $village->files->count() }}
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">
                                                <i class="bi bi-file-earmark"></i> 0
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('village.show', $village->id) }}" 
                                               class="btn btn-sm btn-info" title="Detail">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('village.edit', $village->id) }}" 
                                               class="btn btn-sm btn-warning" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form action="{{ route('village.destroy', $village->id) }}" 
                                                  method="POST" class="d-inline"
                                                  onsubmit="return confirm('Yakin ingin menghapus desa/kelurahan ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                                    <i class="bi bi-trash"></i>
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
                    {{ $villages->links() }}
                </div>
            @else
                <div class="alert alert-info mb-0">
                    <i class="bi bi-info-circle"></i> Tidak ada data desa/kelurahan.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Filter regencies by province
    $('#province_id').on('change', function() {
        const provinceId = $(this).val();
        const $regencySelect = $('#regency_id');
        const $subRegencySelect = $('#sub_regency_id');
        if (provinceId) {
            $regencySelect.find('option').each(function() {
                const $option = $(this);
                if ($option.val() === '') {
                    $option.show();
                } else if ($option.data('province') == provinceId) {
                    $option.show();
                } else {
                    $option.hide();
                }
            });
            const selectedRegency = $regencySelect.val();
            if (selectedRegency) {
                const selectedOption = $regencySelect.find('option[value="' + selectedRegency + '"]');
                if (selectedOption.data('province') != provinceId) {
                    $regencySelect.val('');
                    $subRegencySelect.val('');
                }
            }
        } else {
            $regencySelect.find('option').show();
        }
    });
    // Filter sub-regencies by regency
    $('#regency_id').on('change', function() {
        const regencyId = $(this).val();
        const $subRegencySelect = $('#sub_regency_id');
        if (regencyId) {
            $subRegencySelect.find('option').each(function() {
                const $option = $(this);
                if ($option.val() === '') {
                    $option.show();
                } else if ($option.data('regency') == regencyId) {
                    $option.show();
                } else {
                    $option.hide();
                }
            });
            const selectedSubRegency = $subRegencySelect.val();
            if (selectedSubRegency) {
                const selectedOption = $subRegencySelect.find('option[value="' + selectedSubRegency + '"]');
                if (selectedOption.data('regency') != regencyId) {
                    $subRegencySelect.val('');
                }
            }
        } else {
            $subRegencySelect.find('option').show();
        }
    });
    // Trigger on page load if filters are selected
    if ($('#province_id').val()) {
        $('#province_id').trigger('change');
    }
    if ($('#regency_id').val()) {
        $('#regency_id').trigger('change');
    }
});
</script>
@endpush
