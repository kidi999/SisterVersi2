@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Edit User</h2>
        <a href="{{ route('users.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-2"></i>Kembali
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <h6 class="alert-heading"><i class="bi bi-exclamation-triangle me-2"></i>Terdapat kesalahan pada form:</h6>
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <div class="alert alert-info" role="alert">
                    <strong>Field yang wajib diisi:</strong>
                    <ul class="mb-0 mt-2">
                        <li><strong>Nama Lengkap</strong> - Wajib diisi, maksimal 255 karakter</li>
                        <li><strong>Email</strong> - Wajib diisi, format email valid, harus unik</li>
                        <li><strong>Role</strong> - Wajib dipilih</li>
                    </ul>
                    <strong class="mt-2 d-block">Field opsional:</strong>
                    <ul class="mb-0">
                        <li><strong>Password Baru</strong> - Opsional, kosongkan jika tidak ingin mengubah password</li>
                        <li><strong>Konfirmasi Password</strong> - Wajib jika password baru diisi, harus sama dengan password baru</li>
                        <li>Fakultas, Program Studi, Status Aktif</li>
                    </ul>
                </div>
            @endif

            <form action="{{ route('users.update', $user->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name', $user->name) }}" 
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email', $user->email) }}" 
                                   required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="password" class="form-label">Password Baru</label>
                            <input type="password" 
                                   class="form-control @error('password') is-invalid @enderror" 
                                   id="password" 
                                   name="password">
                            <small class="form-text text-muted">Kosongkan jika tidak ingin mengubah password</small>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                            <input type="password" 
                                   class="form-control" 
                                   id="password_confirmation" 
                                   name="password_confirmation">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="role_id" class="form-label">Role <span class="text-danger">*</span></label>
                            <select class="form-select @error('role_id') is-invalid @enderror" 
                                    id="role_id" 
                                    name="role_id" 
                                    required>
                                <option value="">-- Pilih Role --</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                                        {{ $role->display_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('role_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="fakultas_id" class="form-label">Fakultas</label>
                            <select class="form-select @error('fakultas_id') is-invalid @enderror" 
                                    id="fakultas_id" 
                                    name="fakultas_id">
                                <option value="">-- Pilih Fakultas --</option>
                                @foreach($fakultas as $fak)
                                    <option value="{{ $fak->id }}" {{ old('fakultas_id', $user->fakultas_id) == $fak->id ? 'selected' : '' }}>
                                        {{ $fak->nama_fakultas }}
                                    </option>
                                @endforeach
                            </select>
                            @error('fakultas_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="program_studi_id" class="form-label">Program Studi</label>
                            <select class="form-select @error('program_studi_id') is-invalid @enderror" 
                                    id="program_studi_id" 
                                    name="program_studi_id">
                                <option value="">-- Pilih Program Studi --</option>
                                @foreach($programStudis as $prodi)
                                    <option value="{{ $prodi->id }}" {{ old('program_studi_id', $user->program_studi_id) == $prodi->id ? 'selected' : '' }}>
                                        {{ $prodi->nama_prodi }}
                                    </option>
                                @endforeach
                            </select>
                            @error('program_studi_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <div class="form-check mt-4">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="is_active" 
                                       name="is_active" 
                                       {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Aktifkan User
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- File Upload Section -->
                <div class="row mt-3">
                    <div class="col-12">
                        <h5 class="border-bottom pb-2 mb-3">Dokumen Pendukung</h5>
                        <x-file-upload 
                            :fileableType="'App\Models\User'" 
                            :fileableId="$user->id"
                            :category="'user_documents'"
                            :existingFiles="$user->files"
                        />
                    </div>
                </div>

                <div class="border-top pt-3 mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-2"></i>Update
                    </button>
                    <a href="{{ route('users.index') }}" class="btn btn-secondary">
                        <i class="bi bi-x-circle me-2"></i>Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        // Load Program Studi based on Fakultas
        $('#fakultas_id').on('change', function() {
            const fakultasId = $(this).val();
            const programStudiSelect = $('#program_studi_id');
            const currentProdi = '{{ old('program_studi_id', $user->program_studi_id) }}';
            
            programStudiSelect.html('<option value="">-- Pilih Program Studi --</option>');
            
            if (fakultasId) {
                $.ajax({
                    url: `/users/program-studi/${fakultasId}`,
                    type: 'GET',
                    success: function(data) {
                        data.forEach(function(prodi) {
                            const selected = prodi.id == currentProdi ? 'selected' : '';
                            programStudiSelect.append(
                                `<option value="${prodi.id}" ${selected}>${prodi.nama_prodi}</option>`
                            );
                        });
                    }
                });
            }
        });
    });
</script>
@endpush
@endsection
