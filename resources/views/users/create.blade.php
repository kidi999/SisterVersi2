@extends('layouts.app')

@section('title', 'Tambah User')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Tambah User</h2>
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
                        <li><strong>Password</strong> - Wajib diisi, minimal 8 karakter</li>
                        <li><strong>Konfirmasi Password</strong> - Wajib diisi, harus sama dengan password</li>
                        <li><strong>Role</strong> - Wajib dipilih</li>
                    </ul>
                    <strong class="mt-2 d-block">Field opsional:</strong>
                    <ul class="mb-0">
                        <li>Fakultas, Program Studi, Status Aktif</li>
                    </ul>
                </div>
            @endif

            <form action="{{ route('users.store') }}" method="POST">
                @csrf

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name') }}" 
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
                                   value="{{ old('email') }}" 
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
                            <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                            <input type="password" 
                                   class="form-control @error('password') is-invalid @enderror" 
                                   id="password" 
                                   name="password" 
                                   required>
                            <small class="form-text text-muted">Minimal 8 karakter</small>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Konfirmasi Password <span class="text-danger">*</span></label>
                            <input type="password" 
                                   class="form-control" 
                                   id="password_confirmation" 
                                   name="password_confirmation" 
                                   required>
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
                                    <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
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
                                    <option value="{{ $fak->id }}" {{ old('fakultas_id') == $fak->id ? 'selected' : '' }}>
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
                                       {{ old('is_active', true) ? 'checked' : '' }}>
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
                            :fileableId="null"
                            :category="'user_documents'"
                        />
                    </div>
                </div>

                <div class="border-top pt-3 mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-2"></i>Simpan
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
            
            programStudiSelect.html('<option value="">-- Pilih Program Studi --</option>');
            
            if (fakultasId) {
                $.ajax({
                    url: `/users/program-studi/${fakultasId}`,
                    type: 'GET',
                    success: function(data) {
                        data.forEach(function(prodi) {
                            programStudiSelect.append(
                                `<option value="${prodi.id}">${prodi.nama_prodi}</option>`
                            );
                        });
                    }
                });
            }
        });

        // Trigger on page load if fakultas is selected
        @if(old('fakultas_id'))
            $('#fakultas_id').trigger('change');
            @if(old('program_studi_id'))
                setTimeout(() => {
                    $('#program_studi_id').val('{{ old('program_studi_id') }}');
                }, 300);
            @endif
        @endif
    });
</script>
@endpush
@endsection
