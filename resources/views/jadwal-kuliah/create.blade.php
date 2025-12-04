@extends('layouts.app')

@section('title', 'Tambah Jadwal Kuliah')
@section('header', 'Tambah Jadwal Kuliah')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Tambah Jadwal Kuliah</h1>
        <a href="{{ route('jadwal-kuliah.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('jadwal-kuliah.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="kelas_id" class="form-label">Kelas <span class="text-danger">*</span></label>
                            <select name="kelas_id" id="kelas_id" class="form-select @error('kelas_id') is-invalid @enderror" required>
                                <option value="">Pilih Kelas</option>
                                @foreach($kelas as $k)
                                    <option value="{{ $k->id }}" data-prodi-id="{{ $k->mataKuliah->program_studi_id }}" data-fakultas-id="{{ $k->mataKuliah->programStudi->fakultas_id ?? '' }}" {{ old('kelas_id') == $k->id ? 'selected' : '' }}>
                                        {{ $k->nama_kelas }} - {{ $k->mataKuliah->nama_mk }} ({{ $k->dosen->nama_dosen ?? 'Belum ada dosen' }})
                                    </option>
                                @endforeach
                            </select>
                            @error('kelas_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="tahun_akademik_id" class="form-label">Tahun Akademik <span class="text-danger">*</span></label>
                                <select name="tahun_akademik_id" id="tahun_akademik_id" class="form-select @error('tahun_akademik_id') is-invalid @enderror" required>
                                    <option value="">Pilih Tahun Akademik</option>
                                    @foreach($tahunAkademik as $ta)
                                        <option value="{{ $ta->id }}" {{ old('tahun_akademik_id') == $ta->id ? 'selected' : '' }}>
                                            {{ $ta->nama }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('tahun_akademik_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="semester_id" class="form-label">Semester <span class="text-danger">*</span></label>
                                <select name="semester_id" id="semester_id" class="form-select @error('semester_id') is-invalid @enderror" required>
                                    <option value="">Pilih Semester</option>
                                    @foreach($semesters as $sem)
                                        <option value="{{ $sem->id }}" {{ old('semester_id') == $sem->id ? 'selected' : '' }}>
                                            {{ $sem->nama_semester }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('semester_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="hari" class="form-label">Hari <span class="text-danger">*</span></label>
                                <select name="hari" id="hari" class="form-select @error('hari') is-invalid @enderror" required>
                                    <option value="">Pilih Hari</option>
                                    <option value="Senin" {{ old('hari') == 'Senin' ? 'selected' : '' }}>Senin</option>
                                    <option value="Selasa" {{ old('hari') == 'Selasa' ? 'selected' : '' }}>Selasa</option>
                                    <option value="Rabu" {{ old('hari') == 'Rabu' ? 'selected' : '' }}>Rabu</option>
                                    <option value="Kamis" {{ old('hari') == 'Kamis' ? 'selected' : '' }}>Kamis</option>
                                    <option value="Jumat" {{ old('hari') == 'Jumat' ? 'selected' : '' }}>Jumat</option>
                                    <option value="Sabtu" {{ old('hari') == 'Sabtu' ? 'selected' : '' }}>Sabtu</option>
                                </select>
                                @error('hari')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="jam_mulai" class="form-label">Jam Mulai <span class="text-danger">*</span></label>
                                <input type="time" name="jam_mulai" id="jam_mulai" class="form-control @error('jam_mulai') is-invalid @enderror" value="{{ old('jam_mulai') }}" required>
                                @error('jam_mulai')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="jam_selesai" class="form-label">Jam Selesai <span class="text-danger">*</span></label>
                                <input type="time" name="jam_selesai" id="jam_selesai" class="form-control @error('jam_selesai') is-invalid @enderror" value="{{ old('jam_selesai') }}" required>
                                @error('jam_selesai')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="ruang_id" class="form-label">Ruangan <span class="text-danger">*</span></label>
                            <select name="ruang_id" id="ruang_id" class="form-select @error('ruang_id') is-invalid @enderror" required>
                                <option value="">Pilih waktu dan kelas terlebih dahulu</option>
                            </select>
                            @error('ruang_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Hanya ruangan yang tersedia pada waktu tersebut yang akan ditampilkan</small>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Simpan
                            </button>
                            <a href="{{ route('jadwal-kuliah.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card bg-light">
                <div class="card-body">
                    <h5 class="card-title"><i class="bi bi-info-circle"></i> Informasi</h5>
                    <ul class="mb-0">
                        <li>Sistem akan otomatis mengecek konflik ruangan dan dosen</li>
                        <li>Pilih kelas terlebih dahulu untuk melihat ruangan yang tersedia</li>
                        <li>Ruangan yang ditampilkan sesuai dengan kepemilikan prodi/fakultas</li>
                        <li>Jam selesai harus lebih besar dari jam mulai</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Function to load available ruang
    function loadAvailableRuang() {
        const kelasId = $('#kelas_id').val();
        const hari = $('#hari').val();
        const jamMulai = $('#jam_mulai').val();
        const jamSelesai = $('#jam_selesai').val();

        if (kelasId && hari && jamMulai && jamSelesai) {
            $.ajax({
                url: '{{ route("api.available-ruang") }}',
                method: 'GET',
                data: {
                    kelas_id: kelasId,
                    hari: hari,
                    jam_mulai: jamMulai,
                    jam_selesai: jamSelesai
                },
                success: function(response) {
                    const ruangSelect = $('#ruang_id');
                    ruangSelect.empty();
                    
                    if (response.length > 0) {
                        ruangSelect.append('<option value="">Pilih Ruangan</option>');
                        response.forEach(function(ruang) {
                            ruangSelect.append(
                                `<option value="${ruang.id}">${ruang.kode_ruang} - ${ruang.nama_ruang} (Kapasitas: ${ruang.kapasitas})</option>`
                            );
                        });
                    } else {
                        ruangSelect.append('<option value="">Tidak ada ruangan tersedia</option>');
                    }
                },
                error: function() {
                    alert('Gagal memuat ruangan tersedia');
                }
            });
        } else {
            $('#ruang_id').html('<option value="">Lengkapi data terlebih dahulu</option>');
        }
    }

    // Trigger on change
    $('#kelas_id, #hari, #jam_mulai, #jam_selesai').on('change', function() {
        loadAvailableRuang();
    });

    // Validate jam selesai
    $('#jam_selesai').on('change', function() {
        const jamMulai = $('#jam_mulai').val();
        const jamSelesai = $(this).val();
        
        if (jamMulai && jamSelesai && jamSelesai <= jamMulai) {
            alert('Jam selesai harus lebih besar dari jam mulai');
            $(this).val('');
        }
    });
});
</script>
@endpush
@endsection
