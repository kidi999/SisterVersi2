@extends('layouts.app')

@section('title', 'Data Provinsi')
@section('header', 'Data Provinsi')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Daftar Provinsi</h5>
        <div class="d-flex gap-2">
            @if(Auth::user()->hasRole(['super_admin']))
            <a href="{{ route('provinsi.trash') }}" class="btn btn-warning btn-sm">
                <i class="bi bi-trash"></i> Trash
            </a>
            @endif
            <a href="{{ route('provinsi.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-circle"></i> Tambah Provinsi
            </a>
            <a href="{{ route('provinsi.exportExcel', ['search' => request('search')]) }}" class="btn btn-success btn-sm">
                <i class="bi bi-file-earmark-excel"></i> Export Excel
            </a>
            <a href="{{ route('provinsi.exportCsv', ['search' => request('search')]) }}" class="btn btn-success btn-sm">
                <i class="bi bi-file-earmark-excel"></i> Export CSV
            </a>
            <a href="{{ route('provinsi.exportPdf', ['search' => request('search')]) }}" class="btn btn-danger btn-sm">
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
                <input type="text" name="search" id="searchInput" class="form-control" placeholder="Cari kode/nama provinsi..." value="{{ request('search') }}" autocomplete="off">
            </div>
        </div>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            var input = document.getElementById('searchInput');
            var tbody = document.querySelector('table tbody');
            var pagin = document.querySelector('.mt-3');
            var timer = null;
            function fetchData(val, page) {
                var url = "{{ route('provinsi.searchAjax') }}?search=" + encodeURIComponent(val || '');
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
                        <th width="15%">Kode</th>
                        <th>Nama Provinsi</th>
                        <th width="15%" class="text-center">Jml Kabupaten/Kota</th>
                        <th width="20%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @include('wilayah.provinsi._table', ['provinces' => $provinces])
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-center align-items-center mt-3" id="pagination-wrapper">
            <nav aria-label="Paginasi data provinsi">
                {{ $provinces->appends(['search' => request('search')])->links() }}
            </nav>
        </div>
    @push('styles')
    <style>
    #pagination-wrapper nav {
        display: flex;
        justify-content: center;
    }
    .pagination {
        margin-bottom: 0;
        flex-wrap: wrap;
    }
    .pagination .page-item .page-link {
        border-radius: 0.25rem;
        margin: 0 2px;
        min-width: 36px;
        text-align: center;
    }
    .pagination .page-item.active .page-link {
        background-color: #0d6efd;
        border-color: #0d6efd;
        color: #fff;
    }
    .pagination .page-item.disabled .page-link {
        color: #6c757d;
    }
    @media (max-width: 576px) {
        .pagination .page-item .page-link {
            min-width: 28px;
            font-size: 0.9rem;
            padding: 0.25rem 0.5rem;
        }
    }
    </style>
    @endpush
    </div>
</div>
@endsection
