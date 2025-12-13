@extends('layouts.app')

@section('title', 'Input Pembayaran Mahasiswa')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Input Pembayaran Mahasiswa</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('pembayaran-mahasiswa.index') }}">Pembayaran Mahasiswa</a></li>
                    <li class="breadcrumb-item active">Input Pembayaran</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('pembayaran-mahasiswa.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Periksa kembali input Anda:</strong>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            @if(!$tagihan && $tagihanOptions->isEmpty())
                <div class="alert alert-warning mb-0">
                    <i class="bi bi-exclamation-circle me-2"></i>
                    Tidak ada tagihan dengan sisa tagihan &gt; 0 yang tersedia untuk diinput pembayarannya.
                </div>
            @else
                <form action="{{ route('pembayaran-mahasiswa.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label">Tagihan <span class="text-danger">*</span></label>

                            @if($tagihan)
                                <input type="hidden" name="tagihan_mahasiswa_id" value="{{ $tagihan->id }}">
                                <div class="p-3 border rounded">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div><small class="text-muted">No. Tagihan</small></div>
                                            <div class="fw-semibold font-monospace">{{ $tagihan->nomor_tagihan }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div><small class="text-muted">Jenis Pembayaran</small></div>
                                            <div class="fw-semibold">{{ $tagihan->jenisPembayaran->nama ?? '-' }}</div>
                                        </div>
                                        <div class="col-md-6 mt-2">
                                            <div><small class="text-muted">Mahasiswa</small></div>
                                            <div class="fw-semibold">{{ $tagihan->mahasiswa->nama_mahasiswa ?? '-' }} <span class="text-muted">({{ $tagihan->mahasiswa->nim ?? '-' }})</span></div>
                                        </div>
                                        <div class="col-md-6 mt-2">
                                            <div><small class="text-muted">Sisa Tagihan</small></div>
                                            <div class="fw-semibold text-danger">Rp {{ number_format($tagihan->sisa_tagihan ?? 0, 0, ',', '.') }}</div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <select class="form-select" name="tagihan_mahasiswa_id" required>
                                    <option value="">-- Pilih Tagihan --</option>
                                    @foreach($tagihanOptions as $t)
                                        <option value="{{ $t->id }}" {{ old('tagihan_mahasiswa_id') == $t->id ? 'selected' : '' }}>
                                            {{ $t->nomor_tagihan }} - {{ $t->mahasiswa->nim ?? '-' }} / {{ $t->mahasiswa->nama_mahasiswa ?? '-' }} - {{ $t->jenisPembayaran->nama ?? '-' }} (Sisa: Rp {{ number_format($t->sisa_tagihan ?? 0, 0, ',', '.') }})
                                        </option>
                                    @endforeach
                                </select>
                                <div class="form-text">Menampilkan maksimal 200 tagihan terbaru yang masih memiliki sisa tagihan.</div>
                            @endif
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Jumlah Bayar <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0" class="form-control" name="jumlah_bayar" value="{{ old('jumlah_bayar') }}" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Tanggal Bayar <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="tanggal_bayar" value="{{ old('tanggal_bayar', now()->toDateString()) }}" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Waktu Bayar</label>
                            <input type="time" class="form-control" name="waktu_bayar" value="{{ old('waktu_bayar') }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Metode Pembayaran <span class="text-danger">*</span></label>
                            <select class="form-select" name="metode_pembayaran" required>
                                @php
                                    $methods = ['Transfer Bank','Tunai','Virtual Account','E-Wallet','Kartu Kredit/Debit','Lainnya'];
                                @endphp
                                <option value="">-- Pilih Metode --</option>
                                @foreach($methods as $m)
                                    <option value="{{ $m }}" {{ old('metode_pembayaran') == $m ? 'selected' : '' }}>{{ $m }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Nomor Referensi</label>
                            <input type="text" class="form-control" name="nomor_referensi" value="{{ old('nomor_referensi') }}" maxlength="100">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Nama Bank</label>
                            <input type="text" class="form-control" name="nama_bank" value="{{ old('nama_bank') }}" maxlength="100">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Nomor Rekening</label>
                            <input type="text" class="form-control" name="nomor_rekening" value="{{ old('nomor_rekening') }}" maxlength="50">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Nama Pemilik Rekening</label>
                            <input type="text" class="form-control" name="nama_pemilik_rekening" value="{{ old('nama_pemilik_rekening') }}" maxlength="100">
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Bukti Pembayaran</label>
                            <input type="file" class="form-control" name="bukti_pembayaran" accept="image/*,.pdf">
                            <div class="form-text">Format: JPG/JPEG/PNG/PDF, maks 2MB.</div>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Keterangan</label>
                            <textarea class="form-control" name="keterangan" rows="3">{{ old('keterangan') }}</textarea>
                        </div>

                        @include('components.file-upload', [
                            'existingFiles' => collect(),
                            'fileableType' => \App\Models\PembayaranMahasiswa::class,
                            'fileableId' => 0,
                            'maxFiles' => 10,
                        ])

                        <div class="col-12 mt-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Simpan Pembayaran
                            </button>
                        </div>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection
