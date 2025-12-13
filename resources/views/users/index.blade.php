@extends('layouts.app')

@section('title', 'Daftar User')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Daftar User</h2>
        <div>
            <a href="{{ route('users.exportExcel', request()->query()) }}" class="btn btn-success me-2">
                <i class="bi bi-file-earmark-excel me-2"></i>Export Excel
            </a>
            <a href="{{ route('users.exportPdf', request()->query()) }}" class="btn btn-danger me-2">
                <i class="bi bi-file-earmark-pdf me-2"></i>Export PDF
            </a>
            <a href="{{ route('users.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>Tambah User
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="userTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Fakultas</th>
                            <th>Program Studi</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $index => $user)
                            <tr>
                                <td>{{ ($users->firstItem() ?? 0) + $index }}</td>
                                <td>
                                    <a href="{{ route('users.show', $user->id) }}" class="text-decoration-none">
                                        {{ $user->name }}
                                    </a>
                                </td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    <span class="badge bg-secondary">
                                        {{ $user->role->display_name ?? '-' }}
                                    </span>
                                </td>
                                <td>{{ $user->fakultas->nama_fakultas ?? '-' }}</td>
                                <td>{{ $user->programStudi->nama_prodi ?? '-' }}</td>
                                <td>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input toggle-active" 
                                               type="checkbox" 
                                               data-user-id="{{ $user->id }}"
                                               {{ $user->is_active ? 'checked' : '' }}>
                                        <label class="form-check-label">
                                            {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                                        </label>
                                    </div>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('users.show', $user->id) }}" 
                                           class="btn btn-sm btn-info" 
                                           title="Lihat Detail">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('users.edit', $user->id) }}" 
                                           class="btn btn-sm btn-warning" 
                                           title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('users.destroy', $user->id) }}" 
                                              method="POST" 
                                              class="d-inline delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="btn btn-sm btn-danger" 
                                                    title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted">
                                    Belum ada data user.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-3">
                <div>
                    Menampilkan {{ $users->firstItem() ?? 0 }} sampai {{ $users->lastItem() ?? 0 }} dari {{ $users->total() }} data
                </div>
                <div>
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const csrfToken = '{{ csrf_token() }}';

        // Delete confirmation
        document.querySelectorAll('.delete-form').forEach((form) => {
            form.addEventListener('submit', (e) => {
                if (!confirm('Apakah Anda yakin ingin menghapus user ini?')) {
                    e.preventDefault();
                }
            });
        });

        // Toggle active status
        document.querySelectorAll('.toggle-active').forEach((checkbox) => {
            checkbox.addEventListener('change', async () => {
                const userId = checkbox.getAttribute('data-user-id');
                const label = checkbox.parentElement.querySelector('label');
                const previousValue = !checkbox.checked;

                try {
                    const response = await fetch(`/users/${userId}/toggle-active`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({})
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        checkbox.checked = previousValue;
                        showAlert(data?.error || 'Terjadi kesalahan', 'danger');
                        return;
                    }

                    if (data.success) {
                        if (label) label.textContent = data.is_active ? 'Aktif' : 'Nonaktif';
                        showAlert(data.message, data.is_active ? 'success' : 'warning');
                    }
                } catch (e) {
                    checkbox.checked = previousValue;
                    showAlert('Terjadi kesalahan', 'danger');
                }
            });
        });

        function showAlert(message, type) {
            const wrapper = document.createElement('div');
            wrapper.innerHTML = `
                <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            const container = document.querySelector('.container-fluid');
            container?.prepend(wrapper.firstElementChild);

            setTimeout(() => {
                const alert = container?.querySelector('.alert');
                alert?.classList.remove('show');
                alert?.classList.add('fade');
                setTimeout(() => alert?.remove(), 300);
            }, 3000);
        }
    });
</script>
@endpush
@endsection
