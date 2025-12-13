@extends('layouts.app')

@section('title', 'Edit Ruang')
@section('header', 'Edit Ruang')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Edit Ruang</h1>
        <div>
            <a href="{{ route('ruang.show', $ruang) }}" class="btn btn-info">
                <i class="bi bi-eye"></i> Detail
            </a>
            <a href="{{ route('ruang.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <strong>Error!</strong> Silakan perbaiki kesalahan berikut:
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('ruang.update', $ruang) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <!-- Informasi Dasar -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-info-circle"></i> Informasi Dasar</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Kode Ruang <span class="text-danger">*</span></label>
                        <input type="text" name="kode_ruang" class="form-control @error('kode_ruang') is-invalid @enderror" 
                               value="{{ old('kode_ruang', $ruang->kode_ruang) }}" required>
                        @error('kode_ruang')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-5">
                        <label class="form-label">Nama Ruang <span class="text-danger">*</span></label>
                        <input type="text" name="nama_ruang" class="form-control @error('nama_ruang') is-invalid @enderror" 
                               value="{{ old('nama_ruang', $ruang->nama_ruang) }}" required>
                        @error('nama_ruang')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Gedung</label>
                        <input type="text" name="gedung" class="form-control @error('gedung') is-invalid @enderror" 
                               value="{{ old('gedung', $ruang->gedung) }}">
                        @error('gedung')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Lantai</label>
                        <input type="text" name="lantai" class="form-control @error('lantai') is-invalid @enderror" 
                               value="{{ old('lantai', $ruang->lantai) }}">
                        @error('lantai')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Spesifikasi -->
        <div class="card mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="bi bi-sliders"></i> Spesifikasi</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Kapasitas <span class="text-danger">*</span></label>
                        <input type="number" name="kapasitas" class="form-control @error('kapasitas') is-invalid @enderror" 
                               value="{{ old('kapasitas', $ruang->kapasitas) }}" min="0" required>
                        <small class="text-muted">Jumlah orang</small>
                        @error('kapasitas')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Jenis Ruang <span class="text-danger">*</span></label>
                        <select name="jenis_ruang" class="form-select @error('jenis_ruang') is-invalid @enderror" required>
                            <option value="">Pilih Jenis</option>
                            <option value="Kelas" {{ old('jenis_ruang', $ruang->jenis_ruang) == 'Kelas' ? 'selected' : '' }}>Kelas</option>
                            <option value="Lab" {{ old('jenis_ruang', $ruang->jenis_ruang) == 'Lab' ? 'selected' : '' }}>Lab</option>
                            <option value="Perpustakaan" {{ old('jenis_ruang', $ruang->jenis_ruang) == 'Perpustakaan' ? 'selected' : '' }}>Perpustakaan</option>
                            <option value="Aula" {{ old('jenis_ruang', $ruang->jenis_ruang) == 'Aula' ? 'selected' : '' }}>Aula</option>
                            <option value="Ruang Seminar" {{ old('jenis_ruang', $ruang->jenis_ruang) == 'Ruang Seminar' ? 'selected' : '' }}>Ruang Seminar</option>
                            <option value="Ruang Rapat" {{ old('jenis_ruang', $ruang->jenis_ruang) == 'Ruang Rapat' ? 'selected' : '' }}>Ruang Rapat</option>
                            <option value="Lainnya" {{ old('jenis_ruang', $ruang->jenis_ruang) == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                        @error('jenis_ruang')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                            <option value="Aktif" {{ old('status', $ruang->status) == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="Tidak Aktif" {{ old('status', $ruang->status) == 'Tidak Aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                            <option value="Dalam Perbaikan" {{ old('status', $ruang->status) == 'Dalam Perbaikan' ? 'selected' : '' }}>Dalam Perbaikan</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Fasilitas</label>
                        <textarea name="fasilitas" class="form-control @error('fasilitas') is-invalid @enderror" 
                                  rows="2">{{ old('fasilitas', $ruang->fasilitas) }}</textarea>
                        @error('fasilitas')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Kepemilikan -->
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="bi bi-building"></i> Tingkat Kepemilikan</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Tingkat Kepemilikan <span class="text-danger">*</span></label>
                        <select name="tingkat_kepemilikan" id="tingkat_kepemilikan" 
                                class="form-select @error('tingkat_kepemilikan') is-invalid @enderror" required>
                            <option value="">Pilih Tingkat</option>
                            <option value="Universitas" {{ old('tingkat_kepemilikan', $ruang->tingkat_kepemilikan) == 'Universitas' ? 'selected' : '' }}>Universitas</option>
                            <option value="Fakultas" {{ old('tingkat_kepemilikan', $ruang->tingkat_kepemilikan) == 'Fakultas' ? 'selected' : '' }}>Fakultas</option>
                            <option value="Prodi" {{ old('tingkat_kepemilikan', $ruang->tingkat_kepemilikan) == 'Prodi' ? 'selected' : '' }}>Prodi</option>
                        </select>
                        <small class="text-muted">
                            <i class="bi bi-info-circle"></i> 
                            <strong>Universitas:</strong> Semua bisa gunakan<br>
                            <strong>Fakultas:</strong> Semua prodi di fakultas bisa gunakan<br>
                            <strong>Prodi:</strong> Hanya prodi tersebut yang bisa gunakan
                        </small>
                        @error('tingkat_kepemilikan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4" id="fakultas_field">
                        <label class="form-label">Fakultas <span class="text-danger" id="fakultas_required">*</span></label>
                        <select name="fakultas_id" id="fakultas_id" class="form-select @error('fakultas_id') is-invalid @enderror">
                            <option value="">Pilih Fakultas</option>
                            @foreach($fakultas as $f)
                                <option value="{{ $f->id }}" {{ old('fakultas_id', $ruang->fakultas_id) == $f->id ? 'selected' : '' }}>
                                    {{ $f->nama_fakultas }}
                                </option>
                            @endforeach
                        </select>
                        @error('fakultas_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4" id="prodi_field">
                        <label class="form-label">Program Studi <span class="text-danger" id="prodi_required">*</span></label>
                        <select name="program_studi_id" id="program_studi_id" class="form-select @error('program_studi_id') is-invalid @enderror">
                            <option value="">Pilih Program Studi</option>
                            @foreach($programStudi as $prodi)
                                <option value="{{ $prodi->id }}" data-fakultas="{{ $prodi->fakultas_id }}" 
                                    {{ old('program_studi_id', $ruang->program_studi_id) == $prodi->id ? 'selected' : '' }}>
                                    {{ $prodi->fakultas->nama_fakultas }} - {{ $prodi->nama_prodi }}
                                </option>
                            @endforeach
                        </select>
                        @error('program_studi_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Keterangan -->
        <div class="card mb-4">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0"><i class="bi bi-file-text"></i> Keterangan</h5>
            </div>
            <div class="card-body">
                <textarea name="keterangan" class="form-control @error('keterangan') is-invalid @enderror" 
                          rows="3">{{ old('keterangan', $ruang->keterangan) }}</textarea>
                @error('keterangan')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- File Lampiran -->
        <div class="card mb-4" id="fileUploadSection">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0"><i class="bi bi-paperclip"></i> File Lampiran</h5>
            </div>
            <div class="card-body">
                <!-- Existing Files -->
                @if($ruang->files && $ruang->files->count() > 0)
                    <div class="mb-4">
                        <h6 class="mb-3">File yang Sudah Diupload:</h6>
                        <div class="list-group">
                            @foreach($ruang->files as $file)
                                <div class="list-group-item">
                                    <div class="row align-items-center">
                                        <div class="col-md-1 text-center">
                                            <i class="{{ $file->icon_class }} fs-3"></i>
                                        </div>
                                        <div class="col-md-6">
                                            <strong>{{ $file->file_name }}</strong><br>
                                            <small class="text-muted">
                                                {{ $file->formatted_size }}
                                                @if($file->description)
                                                    | {{ $file->description }}
                                                @endif
                                            </small>
                                        </div>
                                        <div class="col-md-3">
                                            <a href="{{ Storage::url($file->file_path) }}" target="_blank" 
                                               class="btn btn-sm btn-info">
                                                <i class="bi bi-download"></i> Unduh
                                            </a>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" 
                                                       name="delete_files[]" value="{{ $file->id }}" 
                                                       id="delete_file_{{ $file->id }}">
                                                <label class="form-check-label text-danger" for="delete_file_{{ $file->id }}">
                                                    <small>Hapus</small>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="alert alert-info mb-4">
                        <i class="bi bi-info-circle"></i> Belum ada file yang diupload.
                    </div>
                @endif

                <!-- Upload New Files -->
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> 
                    Anda dapat mengunggah file baru (PDF, DOC, DOCX, XLS, XLSX, JPG, PNG, GIF). Maksimal 10 MB per file.
                </div>
                
                <div id="file-upload-container">
                    <div class="file-upload-item mb-3">
                        <div class="row g-2">
                            <div class="col-md-6">
                                <label class="form-label">Pilih File</label>
                                <input type="file" name="files[]" class="form-control @error('files.*') is-invalid @enderror" 
                                       accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.gif">
                                @error('files.*')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Deskripsi File (Opsional)</label>
                                <input type="text" name="file_descriptions[]" class="form-control" 
                                       placeholder="Contoh: Denah ruangan">
                            </div>
                        </div>
                    </div>
                </div>

                <button type="button" class="btn btn-sm btn-outline-primary" id="add-file-btn">
                    <i class="bi bi-plus-circle"></i> Tambah File Lain
                </button>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="bi bi-save"></i> Update
                </button>
                <a href="{{ route('ruang.show', $ruang) }}" class="btn btn-secondary btn-lg">
                    <i class="bi bi-x-circle"></i> Batal
                </a>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    function updateKepemilikanFields() {
        var value = $('#tingkat_kepemilikan').val();
        
        $('#fakultas_field').hide();
        $('#prodi_field').hide();
        $('#fakultas_id').prop('required', false);
        $('#program_studi_id').prop('required', false);
        
        if (value === 'Fakultas') {
            $('#fakultas_field').show();
            $('#fakultas_id').prop('required', true);
        } else if (value === 'Prodi') {
            $('#fakultas_field').show();
            $('#prodi_field').show();
            $('#fakultas_id').prop('required', true);
            $('#program_studi_id').prop('required', true);
        }
    }

    $('#tingkat_kepemilikan').on('change', updateKepemilikanFields);

    // Filter prodi by fakultas
    $('#fakultas_id').on('change', function() {
        var fakultasId = $(this).val();
        var $prodiSelect = $('#program_studi_id');
        
        if (fakultasId) {
            $prodiSelect.find('option').each(function() {
                var $option = $(this);
                if ($option.val() === '') {
                    $option.show();
                } else if ($option.data('fakultas') == fakultasId) {
                    $option.show();
                } else {
                    $option.hide();
                }
            });
        } else {
            $prodiSelect.find('option').show();
        }
    });

    // Init on page load
    updateKepemilikanFields();

    // Add more file upload fields
    $('#add-file-btn').on('click', function() {
        var fileItem = `
            <div class="file-upload-item mb-3">
                <div class="row g-2">
                    <div class="col-md-6">
                        <label class="form-label">Pilih File</label>
                        <input type="file" name="files[]" class="form-control" 
                               accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.gif">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Deskripsi File (Opsional)</label>
                        <div class="input-group">
                            <input type="text" name="file_descriptions[]" class="form-control" 
                                   placeholder="Contoh: Denah ruangan">
                            <button type="button" class="btn btn-outline-danger remove-file-btn">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        $('#file-upload-container').append(fileItem);
    });

    // Remove file upload field
    $(document).on('click', '.remove-file-btn', function() {
        $(this).closest('.file-upload-item').remove();
    });
});
</script>
@endpush
