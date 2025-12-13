@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0"><i class="bi bi-plus-circle me-2"></i>Tambah Rencana Kerja Tahunan</h4>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('rencana-kerja-tahunan.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="judul_rkt" class="form-label">Judul RKT <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('judul_rkt') is-invalid @enderror" 
                                       id="judul_rkt" name="judul_rkt" value="{{ old('judul_rkt') }}" required>
                                @error('judul_rkt')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="tahun" class="form-label">Tahun <span class="text-danger">*</span></label>
                                <select class="form-select @error('tahun') is-invalid @enderror" id="tahun" name="tahun" required>
                                    <option value="">Pilih Tahun</option>
                                    @for($y = date('Y'); $y <= date('Y') + 5; $y++)
                                        <option value="{{ $y }}" {{ old('tahun') == $y ? 'selected' : '' }}>{{ $y }}</option>
                                    @endfor
                                </select>
                                @error('tahun')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="level" class="form-label">Level <span class="text-danger">*</span></label>
                                <select class="form-select @error('level') is-invalid @enderror" id="level" name="level" required>
                                    <option value="">Pilih Level</option>
                                    @if(Auth::user()->hasRole(['super_admin', 'admin_universitas']))
                                        <option value="Universitas" {{ old('level') == 'Universitas' ? 'selected' : '' }}>Universitas</option>
                                    @endif
                                    @if(Auth::user()->hasRole(['super_admin', 'admin_universitas', 'admin_fakultas']))
                                        <option value="Fakultas" {{ old('level') == 'Fakultas' ? 'selected' : '' }}>Fakultas</option>
                                    @endif
                                    @if(Auth::user()->hasRole(['super_admin', 'admin_universitas', 'admin_fakultas', 'admin_prodi']))
                                        <option value="Prodi" {{ old('level') == 'Prodi' ? 'selected' : '' }}>Prodi</option>
                                    @endif
                                </select>
                                @error('level')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3" id="university_field" style="display:none;">
                                <label for="university_id" class="form-label">Universitas</label>
                                <select class="form-select @error('university_id') is-invalid @enderror" id="university_id" name="university_id">
                                    <option value="">Pilih Universitas</option>
                                    @foreach($universities as $univ)
                                        <option value="{{ $univ->id }}" {{ old('university_id') == $univ->id ? 'selected' : '' }}>
                                            {{ $univ->nama }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('university_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3" id="fakultas_field" style="display:none;">
                                <label for="fakultas_id" class="form-label">Fakultas</label>
                                <select class="form-select @error('fakultas_id') is-invalid @enderror" id="fakultas_id" name="fakultas_id">
                                    <option value="">Pilih Fakultas</option>
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

                            <div class="col-md-4 mb-3" id="prodi_field" style="display:none;">
                                <label for="program_studi_id" class="form-label">Program Studi</label>
                                <select class="form-select @error('program_studi_id') is-invalid @enderror" id="program_studi_id" name="program_studi_id">
                                    <option value="">Pilih Program Studi</option>
                                    @foreach($prodi as $ps)
                                        <option value="{{ $ps->id }}" {{ old('program_studi_id') == $ps->id ? 'selected' : '' }}>
                                            {{ $ps->nama_prodi }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('program_studi_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="deskripsi" class="form-label">Deskripsi</label>
                            <textarea class="form-control @error('deskripsi') is-invalid @enderror" 
                                      id="deskripsi" name="deskripsi" rows="4">{{ old('deskripsi') }}</textarea>
                            @error('deskripsi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="tanggal_mulai" class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('tanggal_mulai') is-invalid @enderror" 
                                       id="tanggal_mulai" name="tanggal_mulai" value="{{ old('tanggal_mulai') }}" required>
                                @error('tanggal_mulai')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="tanggal_selesai" class="form-label">Tanggal Selesai <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('tanggal_selesai') is-invalid @enderror" 
                                       id="tanggal_selesai" name="tanggal_selesai" value="{{ old('tanggal_selesai') }}" required>
                                @error('tanggal_selesai')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="anggaran" class="form-label">Anggaran (Rp)</label>
                                <input type="number" class="form-control @error('anggaran') is-invalid @enderror" 
                                       id="anggaran" name="anggaran" value="{{ old('anggaran', 0) }}" step="1000" min="0">
                                @error('anggaran')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        @include('components.file-upload', [
                            'existingFiles' => collect(),
                            'fileableType' => \App\Models\RencanaKerjaTahunan::class,
                            'fileableId' => 0,
                            'maxFiles' => 10,
                        ])

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('rencana-kerja-tahunan.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left me-1"></i>Kembali
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-1"></i>Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('level').addEventListener('change', function() {
    const level = this.value;
    const univField = document.getElementById('university_field');
    const fakField = document.getElementById('fakultas_field');
    const prodiField = document.getElementById('prodi_field');
    
    univField.style.display = 'none';
    fakField.style.display = 'none';
    prodiField.style.display = 'none';
    
    if (level === 'Universitas') {
        univField.style.display = 'block';
    } else if (level === 'Fakultas') {
        fakField.style.display = 'block';
    } else if (level === 'Prodi') {
        prodiField.style.display = 'block';
    }
});

// Trigger on page load if old value exists
if (document.getElementById('level').value) {
    document.getElementById('level').dispatchEvent(new Event('change'));
}
</script>
@endpush
@endsection
