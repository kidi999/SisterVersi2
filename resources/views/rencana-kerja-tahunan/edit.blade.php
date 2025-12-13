@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0"><i class="bi bi-pencil me-2"></i>Edit Rencana Kerja Tahunan</h4>
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

                    <form action="{{ route('rencana-kerja-tahunan.update', $rkt->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Kode RKT</label>
                                <input type="text" class="form-control" value="{{ $rkt->kode_rkt }}" readonly>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label">Tahun</label>
                                <input type="text" class="form-control" value="{{ $rkt->tahun }}" readonly>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label">Level</label>
                                <input type="text" class="form-control" value="{{ $rkt->level }}" readonly>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="judul_rkt" class="form-label">Judul RKT <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('judul_rkt') is-invalid @enderror" 
                                       id="judul_rkt" name="judul_rkt" value="{{ old('judul_rkt', $rkt->judul_rkt) }}" required>
                                @error('judul_rkt')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="deskripsi" class="form-label">Deskripsi</label>
                            <textarea class="form-control @error('deskripsi') is-invalid @enderror" 
                                      id="deskripsi" name="deskripsi" rows="4">{{ old('deskripsi', $rkt->deskripsi) }}</textarea>
                            @error('deskripsi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="tanggal_mulai" class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('tanggal_mulai') is-invalid @enderror" 
                                       id="tanggal_mulai" name="tanggal_mulai" 
                                       value="{{ old('tanggal_mulai', $rkt->tanggal_mulai?->format('Y-m-d')) }}" required>
                                @error('tanggal_mulai')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="tanggal_selesai" class="form-label">Tanggal Selesai <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('tanggal_selesai') is-invalid @enderror" 
                                       id="tanggal_selesai" name="tanggal_selesai" 
                                       value="{{ old('tanggal_selesai', $rkt->tanggal_selesai?->format('Y-m-d')) }}" required>
                                @error('tanggal_selesai')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="anggaran" class="form-label">Anggaran (Rp)</label>
                                <input type="number" class="form-control @error('anggaran') is-invalid @enderror" 
                                       id="anggaran" name="anggaran" 
                                       value="{{ old('anggaran', $rkt->anggaran) }}" step="1000" min="0">
                                @error('anggaran')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        @include('components.file-upload', [
                            'existingFiles' => $rkt->files ?? collect(),
                            'fileableType' => \App\Models\RencanaKerjaTahunan::class,
                            'fileableId' => $rkt->id,
                            'maxFiles' => 10,
                        ])

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('rencana-kerja-tahunan.show', $rkt->id) }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left me-1"></i>Kembali
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-1"></i>Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
