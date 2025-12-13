@extends('layouts.app')

@section('title', 'Data Kabupaten/Kota')
@section('header', 'Data Kabupaten/Kota')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Daftar Kabupaten/Kota</h5>
        <div class="d-flex gap-2">
            @if(Auth::user()->hasRole(['super_admin']))
            <a href="{{ route('regency.trash') }}" class="btn btn-warning btn-sm">
                <i class="bi bi-trash"></i> Trash
            </a>
            @endif
            <a href="{{ route('regency.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-circle"></i> Tambah Kabupaten/Kota
            </a>
            <a href="{{ route('regency.exportExcel', ['search' => request('search'), 'sort' => request('sort'), 'order' => request('order')]) }}" class="btn btn-success btn-sm">
                <i class="bi bi-file-earmark-excel"></i> Export Excel
            </a>
            <a href="{{ route('regency.exportCsv', ['search' => request('search'), 'sort' => request('sort'), 'order' => request('order')]) }}" class="btn btn-success btn-sm">
                <i class="bi bi-file-earmark-excel"></i> Export CSV
            </a>
            <a href="{{ route('regency.exportPdf', ['search' => request('search'), 'sort' => request('sort'), 'order' => request('order')]) }}" class="btn btn-danger btn-sm">
                <i class="bi bi-file-earmark-pdf"></i> Export PDF
            </a>
        </div>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="mb-3">
            <div class="input-group">
                <input type="text" name="search" id="searchInput" class="form-control" placeholder="Cari kode/nama kabupaten/kota/provinsi..." value="{{ request('search') }}" autocomplete="off">
            </div>
        </div>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            var input = document.getElementById('searchInput');
            var tbody = document.querySelector('table tbody');
            var pagin = document.querySelector('.mt-3');
            var timer = null;
            function fetchData(val, page) {
                var url = "{{ route('regency.searchAjax') }}?search=" + encodeURIComponent(val || '');
                if(page) url += "&page="+page;
                fetch(url)
                    .then(res => res.json())
                    .then(data => {
                        tbody.innerHTML = data.html;
                        // fetch paginasi baru
                        fetch(url+"&pagination=1").then(r=>r.text()).then(html=>{
                            var temp = document.createElement('div');
                            temp.innerHTML = html;
                            var newPagin = temp.querySelector('.mt-3');
                            if(newPagin && pagin) pagin.innerHTML = newPagin.innerHTML;
                        });
                    });
            }
            input.addEventListener('input', function() {
                var val = input.value;
                clearTimeout(timer);
                if(val.length >= 2 || val.length === 0) {
                    timer = setTimeout(function(){ fetchData(val); }, 350);
                }
            });
            // handle paginasi ajax
            document.addEventListener('click', function(e){
                if(e.target.closest('.pagination a')){
                    e.preventDefault();
                    var a = e.target.closest('a');
                    var page = a.href.split('page=')[1];
                    fetchData(input.value, page);
                }
            });
        });
        </script>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-light">
                    <tr>
                        <th width="5%">No</th>
                        <th width="12%">Kode</th>
                        <th width="25%">Provinsi</th>
                        <th>Nama Kabupaten/Kota</th>
                        <th width="12%" class="text-center">Jml Kecamatan</th>
                        <th width="15%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @include('wilayah.regency._table', ['regencies' => $regencies])
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-center align-items-center mt-3" id="pagination-wrapper">
            <nav aria-label="Paginasi data kabupaten/kota">
                {{ $regencies->appends(['search' => request('search'), 'sort' => request('sort'), 'order' => request('order')])->links() }}
            </nav>
        </div>
    </div>
</div>
@endsection
