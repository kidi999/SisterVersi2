@extends('layouts.app')

@section('title', 'Edit Akreditasi Fakultas')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Akreditasi Fakultas</h1>
        <a href="{{ route('akreditasi-fakultas.index') }}" class="btn btn-secondary btn-icon-split">
            <span class="icon text-white-50">
                <i class="fas fa-arrow-left"></i>
            </span>
            <span class="text">Kembali</span>
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Form Edit Akreditasi Fakultas</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('akreditasi-fakultas.update', $akreditasiFakulta->id) }}" method="POST" id="akreditasiForm">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="fakultas_id">Fakultas <span class="text-danger">*</span></label>
                            <select class="form-control @error('fakultas_id') is-invalid @enderror" 
                                    id="fakultas_id" name="fakultas_id" required>
                                <option value="">-- Pilih Fakultas --</option>
                                @foreach($fakultasList as $fak)
                                    <option value="{{ $fak->id }}" {{ old('fakultas_id', $akreditasiFakulta->fakultas_id) == $fak->id ? 'selected' : '' }}>
                                        {{ $fak->nama_fakultas }}
                                    </option>
                                @endforeach
                            </select>
                            @error('fakultas_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="lembaga_akreditasi">Lembaga Akreditasi <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('lembaga_akreditasi') is-invalid @enderror" 
                                   id="lembaga_akreditasi" name="lembaga_akreditasi" 
                                   value="{{ old('lembaga_akreditasi', $akreditasiFakulta->lembaga_akreditasi) }}" 
                                   placeholder="Contoh: BAN-PT, LAMDIK, dll" required>
                            @error('lembaga_akreditasi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="nomor_sk">Nomor SK <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nomor_sk') is-invalid @enderror" 
                                   id="nomor_sk" name="nomor_sk" 
                                   value="{{ old('nomor_sk', $akreditasiFakulta->nomor_sk) }}" required>
                            @error('nomor_sk')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="tanggal_sk">Tanggal SK <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('tanggal_sk') is-invalid @enderror" 
                                   id="tanggal_sk" name="tanggal_sk" 
                                   value="{{ old('tanggal_sk', $akreditasiFakulta->tanggal_sk->format('Y-m-d')) }}" required>
                            @error('tanggal_sk')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="tanggal_berakhir">Tanggal Berakhir</label>
                            <input type="date" class="form-control @error('tanggal_berakhir') is-invalid @enderror" 
                                   id="tanggal_berakhir" name="tanggal_berakhir" 
                                   value="{{ old('tanggal_berakhir', $akreditasiFakulta->tanggal_berakhir ? $akreditasiFakulta->tanggal_berakhir->format('Y-m-d') : '') }}">
                            @error('tanggal_berakhir')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="peringkat">Peringkat Akreditasi <span class="text-danger">*</span></label>
                            <select class="form-control @error('peringkat') is-invalid @enderror" 
                                    id="peringkat" name="peringkat" required>
                                <option value="">-- Pilih Peringkat --</option>
                                <option value="Unggul" {{ old('peringkat', $akreditasiFakulta->peringkat) == 'Unggul' ? 'selected' : '' }}>Unggul</option>
                                <option value="A" {{ old('peringkat', $akreditasiFakulta->peringkat) == 'A' ? 'selected' : '' }}>A</option>
                                <option value="Baik Sekali" {{ old('peringkat', $akreditasiFakulta->peringkat) == 'Baik Sekali' ? 'selected' : '' }}>Baik Sekali</option>
                                <option value="B" {{ old('peringkat', $akreditasiFakulta->peringkat) == 'B' ? 'selected' : '' }}>B</option>
                                <option value="Baik" {{ old('peringkat', $akreditasiFakulta->peringkat) == 'Baik' ? 'selected' : '' }}>Baik</option>
                                <option value="C" {{ old('peringkat', $akreditasiFakulta->peringkat) == 'C' ? 'selected' : '' }}>C</option>
                                <option value="Belum Terakreditasi" {{ old('peringkat', $akreditasiFakulta->peringkat) == 'Belum Terakreditasi' ? 'selected' : '' }}>Belum Terakreditasi</option>
                            </select>
                            @error('peringkat')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="tahun_akreditasi">Tahun Akreditasi <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('tahun_akreditasi') is-invalid @enderror" 
                                   id="tahun_akreditasi" name="tahun_akreditasi" 
                                   value="{{ old('tahun_akreditasi', $akreditasiFakulta->tahun_akreditasi) }}" 
                                   min="1900" max="{{ date('Y') + 1 }}" required>
                            @error('tahun_akreditasi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="status">Status <span class="text-danger">*</span></label>
                            <select class="form-control @error('status') is-invalid @enderror" 
                                    id="status" name="status" required>
                                <option value="">-- Pilih Status --</option>
                                <option value="Aktif" {{ old('status', $akreditasiFakulta->status) == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="Kadaluarsa" {{ old('status', $akreditasiFakulta->status) == 'Kadaluarsa' ? 'selected' : '' }}>Kadaluarsa</option>
                                <option value="Dalam Proses" {{ old('status', $akreditasiFakulta->status) == 'Dalam Proses' ? 'selected' : '' }}>Dalam Proses</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="catatan">Catatan</label>
                            <textarea class="form-control @error('catatan') is-invalid @enderror" 
                                      id="catatan" name="catatan" rows="3">{{ old('catatan', $akreditasiFakulta->catatan) }}</textarea>
                            @error('catatan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <!-- File Upload Component -->
                @include('components.file-upload', [
                    'fileableType' => 'App\\Models\\AkreditasiFakultas',
                    'fileableId' => $akreditasiFakulta->id,
                    'existingFiles' => $akreditasiFakulta->files
                ])

                <hr class="my-4">

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update
                    </button>
                    <a href="{{ route('akreditasi-fakultas.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
