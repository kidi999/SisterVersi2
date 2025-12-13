@extends('layouts.app')

@section('title', 'Kecamatan')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Manajemen Kecamatan</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('sub-regency.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Tambah Kecamatan
            </a>
            <a href="{{ route('sub-regency.exportExcel', request()->all()) }}" class="btn btn-success">
                <i class="bi bi-file-earmark-excel"></i> Export Excel
            </a>
            <a href="{{ route('sub-regency.exportCsv', request()->all()) }}" class="btn btn-success">
                <i class="bi bi-file-earmark-excel"></i> Export CSV
            </a>
            <a href="{{ route('sub-regency.exportPdf', request()->all()) }}" class="btn btn-danger">
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
            <i class="bi bi-signpost"></i> Data Kecamatan
        </div>
        <div class="card-body">
            <!-- Filter Form -->
            <form method="GET" action="{{ route('sub-regency.index') }}" class="mb-4">
                <div class="row g-3">
                    <div class="col-md-3">
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
                    <div class="col-md-4">
                        <label for="search" class="form-label">Pencarian</label>
                        <input type="text" class="form-control" id="search" name="search" 
                               placeholder="Cari nama atau kode..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search"></i> Filter
                            </button>
                            <a href="{{ route('sub-regency.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-striped table-hover" id="subRegencyTable">
                    <thead class="table-dark">
                        <tr>
                            <th width="5%">No</th>
                            <th width="10%">Kode</th>
                            <th width="20%">Nama Kecamatan</th>
                            <th width="20%">Kabupaten/Kota</th>
                            <th width="15%">Provinsi</th>
                            <th width="10%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @include('wilayah.sub-regency._table', ['subRegencies' => $subRegencies])
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center align-items-center mt-3" id="pagination-wrapper">
                <nav aria-label="Paginasi data kecamatan">
                    {{ $subRegencies->appends(request()->except('page'))->links() }}
                </nav>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize DataTable if data exists
    @if($subRegencies->count() > 0)
    $('#subRegencyTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
        },
        order: [[2, 'asc']], // Sort by name
        pageLength: 25
    });
    @endif

    // Filter regencies by province
    $('#province_id').on('change', function() {
        const provinceId = $(this).val();
        const $regencySelect = $('#regency_id');
        
        if (provinceId) {
            // Show only regencies from selected province
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
            
            // Reset regency selection if current selection is not in province
            const selectedRegency = $regencySelect.val();
            if (selectedRegency) {
                const selectedOption = $regencySelect.find('option[value="' + selectedRegency + '"]');
                if (selectedOption.data('province') != provinceId) {
                    $regencySelect.val('');
                }
            }
        } else {
            // Show all regencies
            $regencySelect.find('option').show();
        }
    });

    // Trigger on page load if province is selected
            });
</script>
@endpush
        }
