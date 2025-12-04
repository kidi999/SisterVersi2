@extends('layouts.app')

@section('title', 'Detail Pendaftaran Mahasiswa')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Detail Pendaftaran Mahasiswa</h1>
        <div>
            <a href="{{ route('pendaftaran-mahasiswa.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
            @if($pendaftaranMahasiswa->status !== 'Dieksport')
                <a href="{{ route('pendaftaran-mahasiswa.edit', $pendaftaranMahasiswa) }}" class="btn btn-warning">
                    <i class="bi bi-pencil"></i> Edit
                </a>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <!-- Info Pendaftaran -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Informasi Pendaftaran</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th width="200">No. Pendaftaran</th>
                            <td><strong class="text-primary">{{ $pendaftaranMahasiswa->no_pendaftaran }}</strong></td>
                        </tr>
                        <tr>
                            <th>Tanggal Daftar</th>
                            <td>{{ $pendaftaranMahasiswa->tanggal_daftar->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Tahun Akademik</th>
                            <td>{{ $pendaftaranMahasiswa->tahun_akademik }}</td>
                        </tr>
                        <tr>
                            <th>Jalur Masuk</th>
                            <td><span class="badge bg-{{ $pendaftaranMahasiswa->jalur_badge }}">{{ $pendaftaranMahasiswa->jalur_masuk }}</span></td>
                        </tr>
                        <tr>
                            <th>Program Studi</th>
                            <td>
                                <strong>{{ $pendaftaranMahasiswa->programStudi->nama_prodi }}</strong><br>
                                <small class="text-muted">{{ $pendaftaranMahasiswa->programStudi->fakultas->nama_fakultas }}</small>
                            </td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td><span class="badge bg-{{ $pendaftaranMahasiswa->status_badge }} fs-6">{{ $pendaftaranMahasiswa->status }}</span></td>
                        </tr>
                        @if($pendaftaranMahasiswa->tanggal_verifikasi)
                        <tr>
                            <th>Tanggal Verifikasi</th>
                            <td>{{ $pendaftaranMahasiswa->tanggal_verifikasi->format('d/m/Y H:i') }}
                                @if($pendaftaranMahasiswa->verifikator)
                                    <br><small class="text-muted">oleh: {{ $pendaftaranMahasiswa->verifikator->name }}</small>
                                @endif
                            </td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>

            <!-- Data Pribadi -->
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Data Pribadi</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th width="200">Nama Lengkap</th>
                            <td><strong>{{ $pendaftaranMahasiswa->nama_lengkap }}</strong></td>
                        </tr>
                        <tr>
                            <th>NIK</th>
                            <td>{{ $pendaftaranMahasiswa->nik ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Jenis Kelamin</th>
                            <td>{{ $pendaftaranMahasiswa->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                        </tr>
                        <tr>
                            <th>TTL</th>
                            <td>{{ $pendaftaranMahasiswa->tempat_lahir ?? '-' }}, 
                                {{ $pendaftaranMahasiswa->tanggal_lahir ? $pendaftaranMahasiswa->tanggal_lahir->format('d/m/Y') : '-' }}</td>
                        </tr>
                        <tr>
                            <th>Agama</th>
                            <td>{{ $pendaftaranMahasiswa->agama ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Status Perkawinan</th>
                            <td>{{ $pendaftaranMahasiswa->status_perkawinan }}</td>
                        </tr>
                        <tr>
                            <th>Kewarganegaraan</th>
                            <td>{{ $pendaftaranMahasiswa->kewarganegaraan }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Alamat & Kontak -->
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Alamat & Kontak</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th width="200">Alamat</th>
                            <td>{{ $pendaftaranMahasiswa->alamat ?? '-' }}</td>
                        </tr>
                        @if($pendaftaranMahasiswa->village)
                        <tr>
                            <th>Desa/Kelurahan</th>
                            <td>
                                {{ $pendaftaranMahasiswa->village->name }}, 
                                {{ $pendaftaranMahasiswa->village->subRegency->name }}, 
                                {{ $pendaftaranMahasiswa->village->subRegency->regency->name }}, 
                                {{ $pendaftaranMahasiswa->village->subRegency->regency->province->name }}
                            </td>
                        </tr>
                        @endif
                        <tr>
                            <th>Kode Pos</th>
                            <td>{{ $pendaftaranMahasiswa->kode_pos ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Telepon</th>
                            <td>{{ $pendaftaranMahasiswa->telepon ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <td>{{ $pendaftaranMahasiswa->email }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Pendidikan -->
            <div class="card mb-4">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">Pendidikan Terakhir</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th width="200">Asal Sekolah</th>
                            <td>{{ $pendaftaranMahasiswa->asal_sekolah ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Jurusan</th>
                            <td>{{ $pendaftaranMahasiswa->jurusan_sekolah ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Tahun Lulus</th>
                            <td>{{ $pendaftaranMahasiswa->tahun_lulus ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Nilai Rata-rata</th>
                            <td>{{ $pendaftaranMahasiswa->nilai_rata_rata ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Data Orang Tua -->
            <div class="card mb-4">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">Data Orang Tua / Wali</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th width="200">Nama Ayah</th>
                            <td>{{ $pendaftaranMahasiswa->nama_ayah ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Pekerjaan Ayah</th>
                            <td>{{ $pendaftaranMahasiswa->pekerjaan_ayah ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Nama Ibu</th>
                            <td>{{ $pendaftaranMahasiswa->nama_ibu ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Pekerjaan Ibu</th>
                            <td>{{ $pendaftaranMahasiswa->pekerjaan_ibu ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Nama Wali</th>
                            <td>{{ $pendaftaranMahasiswa->nama_wali ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Telepon Wali</th>
                            <td>{{ $pendaftaranMahasiswa->telepon_wali ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Alamat Wali</th>
                            <td>{{ $pendaftaranMahasiswa->alamat_wali ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Lampiran -->
            @if($pendaftaranMahasiswa->files->count() > 0)
            <div class="card mb-4">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Dokumen Lampiran</h5>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        @foreach($pendaftaranMahasiswa->files as $file)
                            <a href="{{ Storage::url($file->file_path) }}" target="_blank" class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">
                                        <i class="bi bi-file-earmark"></i> {{ $file->original_name }}
                                    </h6>
                                    <small>{{ number_format($file->file_size / 1024, 2) }} KB</small>
                                </div>
                                <small class="text-muted">{{ $file->mime_type }}</small>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>

        <div class="col-lg-4">
            <!-- Verifikasi -->
            @if($pendaftaranMahasiswa->status !== 'Dieksport')
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Verifikasi</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('pendaftaran-mahasiswa.verifikasi', $pendaftaranMahasiswa) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select" required>
                                <option value="">Pilih Status</option>
                                <option value="Diverifikasi" {{ $pendaftaranMahasiswa->status == 'Diverifikasi' ? 'selected' : '' }}>Diverifikasi</option>
                                <option value="Diterima" {{ $pendaftaranMahasiswa->status == 'Diterima' ? 'selected' : '' }}>Diterima</option>
                                <option value="Ditolak" {{ $pendaftaranMahasiswa->status == 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Catatan</label>
                            <textarea name="catatan" class="form-control" rows="3">{{ $pendaftaranMahasiswa->catatan }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-check-circle"></i> Update Status
                        </button>
                    </form>
                </div>
            </div>
            @endif

            <!-- Export ke Mahasiswa -->
            @if($pendaftaranMahasiswa->status === 'Diterima')
            <div class="card border-success">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Export ke Mahasiswa</h5>
                </div>
                <div class="card-body">
                    <p>Pendaftar telah <strong>DITERIMA</strong>. Export data ke tabel mahasiswa untuk mengaktifkan sebagai mahasiswa resmi.</p>
                    <form action="{{ route('pendaftaran-mahasiswa.export', $pendaftaranMahasiswa) }}" method="POST" onsubmit="return confirm('Yakin export ke mahasiswa? Data tidak dapat diubah setelah dieksport.')">
                        @csrf
                        <button type="submit" class="btn btn-success w-100">
                            <i class="bi bi-upload"></i> Export ke Mahasiswa
                        </button>
                    </form>
                </div>
            </div>
            @endif

            <!-- Catatan Admin -->
            @if($pendaftaranMahasiswa->catatan)
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">Catatan</h5>
                </div>
                <div class="card-body">
                    <p class="mb-0" style="white-space: pre-line;">{{ $pendaftaranMahasiswa->catatan }}</p>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
