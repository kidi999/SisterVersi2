@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Isi KRS</h1>
        <a href="{{ route('krs.index') }}" class="btn btn-secondary">
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
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Pilih Mata Kuliah</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('krs.store') }}" method="POST" id="krsForm">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-bold">Tahun Ajaran: {{ $tahunAkademik->tahun_ajaran ?? '-' }}</label>
                            <span class="ms-3">Semester: 
                                <span class="badge {{ ($tahunAkademik->semester ?? '') == 'Ganjil' ? 'bg-info' : 'bg-warning' }}">
                                    {{ $tahunAkademik->semester ?? '-' }}
                                </span>
                            </span>
                        </div>

                        @if($errors->has('kelas_ids'))
                            <div class="alert alert-danger">
                                {{ $errors->first('kelas_ids') }}
                            </div>
                        @endif

                        <div class="table-responsive">
                            <table class="table table-hover" id="kelasTable">
                                <thead>
                                    <tr>
                                        <th width="50">
                                            <input type="checkbox" id="checkAll" class="form-check-input">
                                        </th>
                                        <th>Kode Kelas</th>
                                        <th>Mata Kuliah</th>
                                        <th>SKS</th>
                                        <th>Dosen</th>
                                        <th>Kapasitas</th>
                                        <th>Semester</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($kelasList as $kelas)
                                    @php
                                        $isEnrolled = in_array($kelas->id, $enrolledKelasIds);
                                        $isFull = $kelas->terisi >= $kelas->kapasitas;
                                    @endphp
                                    <tr class="{{ $isEnrolled ? 'table-success' : ($isFull ? 'table-secondary' : '') }}">
                                        <td>
                                            @if($isEnrolled)
                                                <span class="badge bg-success">Terdaftar</span>
                                            @elseif($isFull)
                                                <span class="badge bg-danger">Penuh</span>
                                            @else
                                                <input type="checkbox" name="kelas_ids[]" 
                                                       value="{{ $kelas->id }}" 
                                                       class="form-check-input kelas-checkbox"
                                                       data-sks="{{ $kelas->mataKuliah->sks }}">
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $kelas->kode_kelas }}</span>
                                        </td>
                                        <td>
                                            <strong>{{ $kelas->mataKuliah->nama }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $kelas->mataKuliah->kode }}</small>
                                        </td>
                                        <td>{{ $kelas->mataKuliah->sks }}</td>
                                        <td>{{ $kelas->dosen->nama ?? '-' }}</td>
                                        <td>
                                            <span class="badge {{ $kelas->terisi >= $kelas->kapasitas ? 'bg-danger' : 'bg-success' }}">
                                                {{ $kelas->terisi }}/{{ $kelas->kapasitas }}
                                            </span>
                                        </td>
                                        <td>{{ $kelas->mataKuliah->semester }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <i class="bi bi-inbox" style="font-size: 2rem; color: #ccc;"></i>
                                            <p class="mt-2 text-muted">Tidak ada kelas yang tersedia</p>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if($kelasList->count() > 0)
                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-save"></i> Simpan KRS
                            </button>
                        </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-info-circle"></i> Informasi Mahasiswa
                    </h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <td width="120"><strong>NIM</strong></td>
                            <td>{{ $mahasiswa->nim }}</td>
                        </tr>
                        <tr>
                            <td><strong>Nama</strong></td>
                            <td>{{ $mahasiswa->nama }}</td>
                        </tr>
                        <tr>
                            <td><strong>Program Studi</strong></td>
                            <td>{{ $mahasiswa->programStudi->nama }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-calculator"></i> Total SKS Dipilih
                    </h5>
                </div>
                <div class="card-body text-center">
                    <h1 class="display-4 mb-0" id="totalSks">0</h1>
                    <p class="text-muted">SKS</p>
                    <div id="sksWarning" class="alert alert-warning d-none" role="alert">
                        <i class="bi bi-exclamation-triangle"></i> Maksimal 24 SKS per semester
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-lightbulb"></i> Petunjuk
                    </h5>
                </div>
                <div class="card-body">
                    <ul class="small mb-0">
                        <li>Pilih mata kuliah dengan centang checkbox</li>
                        <li>Maksimal beban SKS: 24 SKS</li>
                        <li>Kelas yang sudah penuh tidak dapat dipilih</li>
                        <li>Kelas yang sudah terdaftar ditandai hijau</li>
                        <li>KRS yang diajukan perlu persetujuan dari dosen wali</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const checkboxes = document.querySelectorAll('.kelas-checkbox');
        const checkAll = document.getElementById('checkAll');
        const totalSksElement = document.getElementById('totalSks');
        const sksWarning = document.getElementById('sksWarning');

        function updateTotalSks() {
            let total = 0;
            checkboxes.forEach(function(checkbox) {
                if (checkbox.checked) {
                    total += parseInt(checkbox.dataset.sks);
                }
            });
            totalSksElement.textContent = total;
            
            if (total > 24) {
                sksWarning.classList.remove('d-none');
            } else {
                sksWarning.classList.add('d-none');
            }
        }

        checkboxes.forEach(function(checkbox) {
            checkbox.addEventListener('change', updateTotalSks);
        });

        checkAll.addEventListener('change', function() {
            checkboxes.forEach(function(checkbox) {
                checkbox.checked = checkAll.checked;
            });
            updateTotalSks();
        });

        document.getElementById('krsForm').addEventListener('submit', function(e) {
            const totalSks = parseInt(totalSksElement.textContent);
            if (totalSks > 24) {
                if (!confirm('Total SKS melebihi 24. Apakah Anda yakin ingin melanjutkan?')) {
                    e.preventDefault();
                }
            }
            if (totalSks === 0) {
                e.preventDefault();
                alert('Pilih minimal 1 mata kuliah');
            }
        });
    });
</script>
@endpush
@endsection
