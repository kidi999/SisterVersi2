@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Input Nilai</h1>
        <a href="{{ route('nilai.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-8">
            <!-- Filter Kelas -->
            <div class="card mb-3">
                <div class="card-body">
                    <form method="GET" action="{{ route('nilai.create') }}" class="row g-3">
                        <div class="col-md-8">
                            <select name="kelas_id" class="form-select" onchange="this.form.submit()">
                                <option value="">-- Semua Kelas --</option>
                                @foreach($kelasList as $kelas)
                                <option value="{{ $kelas->id }}" {{ request('kelas_id') == $kelas->id ? 'selected' : '' }}>
                                    {{ $kelas->kode_kelas }} - {{ $kelas->mataKuliah->nama }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('nilai.create') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-clockwise"></i> Reset Filter
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Pilih Mahasiswa untuk Input Nilai</h5>
                </div>
                <div class="card-body">
                    @if($krsList->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>NIM</th>
                                    <th>Nama Mahasiswa</th>
                                    <th>Mata Kuliah</th>
                                    <th>Kelas</th>
                                    <th>SKS</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($krsList as $krs)
                                <tr>
                                    <td>{{ $krs->mahasiswa->nim }}</td>
                                    <td>{{ $krs->mahasiswa->nama }}</td>
                                    <td>
                                        <strong>{{ $krs->kelas->mataKuliah->nama }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $krs->kelas->mataKuliah->kode }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $krs->kelas->kode_kelas }}</span>
                                    </td>
                                    <td>{{ $krs->kelas->mataKuliah->sks }}</td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-primary" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#nilaiModal"
                                                data-krs-id="{{ $krs->id }}"
                                                data-nim="{{ $krs->mahasiswa->nim }}"
                                                data-nama="{{ $krs->mahasiswa->nama }}"
                                                data-matkul="{{ $krs->kelas->mataKuliah->nama }}">
                                            <i class="bi bi-pencil"></i> Input Nilai
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-5">
                        <i class="bi bi-inbox" style="font-size: 2rem; color: #ccc;"></i>
                        <p class="mt-2 text-muted">Tidak ada mahasiswa yang perlu dinilai</p>
                        <small class="text-muted">
                            Mahasiswa harus memiliki KRS yang disetujui dan belum memiliki nilai
                        </small>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-info-circle"></i> Informasi
                    </h5>
                </div>
                <div class="card-body">
                    <h6>Komponen Nilai:</h6>
                    <ul class="small">
                        <li>Tugas: 30%</li>
                        <li>UTS: 30%</li>
                        <li>UAS: 40%</li>
                    </ul>
                    
                    <hr>
                    
                    <h6>Konversi Nilai:</h6>
                    <table class="table table-sm small">
                        <tr><td>≥ 85</td><td>A (4.00)</td></tr>
                        <tr><td>80-84</td><td>A- (3.75)</td></tr>
                        <tr><td>75-79</td><td>B+ (3.50)</td></tr>
                        <tr><td>70-74</td><td>B (3.00)</td></tr>
                        <tr><td>65-69</td><td>B- (2.75)</td></tr>
                        <tr><td>60-64</td><td>C+ (2.50)</td></tr>
                        <tr><td>55-59</td><td>C (2.00)</td></tr>
                        <tr><td>50-54</td><td>D (1.00)</td></tr>
                        <tr><td>&lt; 50</td><td>E (0.00)</td></tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Input Nilai -->
<div class="modal fade" id="nilaiModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('nilai.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Input Nilai</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="krs_id" id="modal_krs_id">
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">NIM</label>
                        <p id="modal_nim" class="form-control-plaintext">-</p>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama</label>
                        <p id="modal_nama" class="form-control-plaintext">-</p>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Mata Kuliah</label>
                        <p id="modal_matkul" class="form-control-plaintext">-</p>
                    </div>
                    
                    <hr>
                    
                    <div class="mb-3">
                        <label for="nilai_tugas" class="form-label">Nilai Tugas (30%)</label>
                        <input type="number" step="0.01" min="0" max="100" 
                               class="form-control @error('nilai_tugas') is-invalid @enderror" 
                               id="nilai_tugas" name="nilai_tugas" required>
                        @error('nilai_tugas')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="nilai_uts" class="form-label">Nilai UTS (30%)</label>
                        <input type="number" step="0.01" min="0" max="100" 
                               class="form-control @error('nilai_uts') is-invalid @enderror" 
                               id="nilai_uts" name="nilai_uts" required>
                        @error('nilai_uts')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="nilai_uas" class="form-label">Nilai UAS (40%)</label>
                        <input type="number" step="0.01" min="0" max="100" 
                               class="form-control @error('nilai_uas') is-invalid @enderror" 
                               id="nilai_uas" name="nilai_uas" required>
                        @error('nilai_uas')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    @include('components.file-upload', [
                        'existingFiles' => [],
                        'fileableType' => \App\Models\Nilai::class,
                        'fileableId' => 0,
                    ])
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Simpan Nilai
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const nilaiModal = document.getElementById('nilaiModal');
        
        nilaiModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const krsId = button.getAttribute('data-krs-id');
            const nim = button.getAttribute('data-nim');
            const nama = button.getAttribute('data-nama');
            const matkul = button.getAttribute('data-matkul');
            
            document.getElementById('modal_krs_id').value = krsId;
            document.getElementById('modal_nim').textContent = nim;
            document.getElementById('modal_nama').textContent = nama;
            document.getElementById('modal_matkul').textContent = matkul;
        });
    });
</script>
@endpush
@endsection
