@extends('layouts.app')

@section('title', 'Data Mahasiswa')
@section('header', 'Data Mahasiswa')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Daftar Mahasiswa</h5>
        <a href="{{ route('mahasiswa.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Tambah Mahasiswa
        </a>
    </div>
    <div class="card-body">
        <!-- Search Form -->
        <form action="{{ route('mahasiswa.index') }}" method="GET" class="mb-3">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Cari berdasarkan NIM atau Nama..." 
                       value="{{ request('search') }}">
                <button class="btn btn-outline-secondary" type="submit">
                    <i class="bi bi-search"></i> Cari
                </button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>NIM</th>
                        <th>Nama</th>
                        <th>Program Studi</th>
                        <th>Semester</th>
                        <th>IPK</th>
                        <th>Status</th>
                        <th>Akun User</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($mahasiswa as $index => $item)
                    <tr>
                        <td>{{ $mahasiswa->firstItem() + $index }}</td>
                        <td><span class="badge bg-primary">{{ $item->nim }}</span></td>
                        <td>{{ $item->nama_mahasiswa }}</td>
                        <td>{{ $item->programStudi->nama_prodi }}</td>
                        <td>{{ $item->semester }}</td>
                        <td>{{ number_format($item->ipk, 2) }}</td>
                        <td>
                            @if($item->status == 'Aktif')
                                <span class="badge bg-success">{{ $item->status }}</span>
                            @elseif($item->status == 'Cuti')
                                <span class="badge bg-warning">{{ $item->status }}</span>
                            @elseif($item->status == 'Lulus')
                                <span class="badge bg-info">{{ $item->status }}</span>
                            @else
                                <span class="badge bg-danger">{{ $item->status }}</span>
                            @endif
                        </td>
                        <td>
                            @if($item->user)
                                <span class="badge bg-success" 
                                      data-bs-toggle="tooltip" 
                                      data-bs-html="true"
                                      title="<strong>Email:</strong> {{ $item->user->email }}<br><strong>Status:</strong> {{ $item->user->is_active ? 'Aktif' : 'Non-Aktif' }}<br><strong>Role:</strong> {{ $item->user->role->display_name }}">
                                    <i class="bi bi-check-circle"></i> Terhubung
                                </span>
                                <br>
                                <small class="text-muted">{{ $item->user->email }}</small>
                            @else
                                <span class="badge bg-secondary">
                                    <i class="bi bi-x-circle"></i> Belum
                                </span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('mahasiswa.show', $item->id) }}" class="btn btn-sm btn-info" title="Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('mahasiswa.edit', $item->id) }}" class="btn btn-sm btn-warning" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @if(!$item->user)
                                    <form action="{{ route('mahasiswa.generate-user', $item->id) }}" method="POST" class="d-inline" 
                                          onsubmit="return confirm('Generate akun user?\n\nEmail: {{ $item->email }}\nPassword: Mhs{{ $item->nim }}')">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success" title="Generate Akun User">
                                            <i class="bi bi-key"></i>
                                        </button>
                                    </form>
                                @endif
                                <form action="{{ route('mahasiswa.destroy', $item->id) }}" method="POST" class="d-inline" 
                                      onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center">Belum ada data mahasiswa</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center">
            {{ $mahasiswa->links() }}
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Initialize Bootstrap tooltips
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endpush

@endsection
