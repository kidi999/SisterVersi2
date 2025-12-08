@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Detail Nilai</h1>
        <a href="{{ route('nilai.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Informasi Nilai</h5>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th width="200">NIM</th>
                            <td>{{ $nilai->krs->mahasiswa->nim }}</td>
                        </tr>
                        <tr>
                            <th>Nama Mahasiswa</th>
                            <td>{{ $nilai->krs->mahasiswa->nama }}</td>
                        </tr>
                        <tr>
                            <th>Program Studi</th>
                            <td>{{ $nilai->krs->mahasiswa->programStudi->nama }}</td>
                        </tr>
                        <tr>
                            <th>Mata Kuliah</th>
                            <td>
                                <strong>{{ $nilai->krs->kelas->mataKuliah->nama }}</strong>
                                <br>
                                <small class="text-muted">{{ $nilai->krs->kelas->mataKuliah->kode }}</small>
                            </td>
                        </tr>
                        <tr>
                            <th>Kelas</th>
                            <td>
                                <span class="badge bg-secondary">{{ $nilai->krs->kelas->kode_kelas }}</span>
                            </td>
                        </tr>
                        <tr>
                            <th>SKS</th>
                            <td>{{ $nilai->krs->kelas->mataKuliah->sks }}</td>
                        </tr>
                        <tr>
                            <th>Dosen Pengampu</th>
                            <td>
                                {{ $nilai->krs->kelas->dosen->nama ?? '-' }}
                                @if($nilai->krs->kelas->dosen)
                                <br>
                                <small class="text-muted">NIDN: {{ $nilai->krs->kelas->dosen->nidn }}</small>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Tahun Ajaran</th>
                            <td>{{ $nilai->krs->tahun_ajaran }}</td>
                        </tr>
                        <tr>
                            <th>Semester</th>
                            <td>
                                <span class="badge {{ $nilai->krs->semester == 'Ganjil' ? 'bg-info' : 'bg-warning' }}">
                                    {{ $nilai->krs->semester }}
                                </span>
                            </td>
                        </tr>
                    </table>

                    <h6 class="mt-4">Rincian Nilai</h6>
                    <table class="table table-bordered">
                        <tr>
                            <th width="200">Nilai Tugas (30%)</th>
                            <td>{{ number_format($nilai->nilai_tugas, 2) }}</td>
                        </tr>
                        <tr>
                            <th>Nilai UTS (30%)</th>
                            <td>{{ number_format($nilai->nilai_uts, 2) }}</td>
                        </tr>
                        <tr>
                            <th>Nilai UAS (40%)</th>
                            <td>{{ number_format($nilai->nilai_uas, 2) }}</td>
                        </tr>
                        <tr class="table-primary">
                            <th>Nilai Akhir</th>
                            <td><strong>{{ number_format($nilai->nilai_akhir, 2) }}</strong></td>
                        </tr>
                        <tr>
                            <th>Nilai Huruf</th>
                            <td>
                                <span class="badge 
                                    @if(in_array($nilai->nilai_huruf, ['A', 'A-'])) bg-success
                                    @elseif(in_array($nilai->nilai_huruf, ['B+', 'B', 'B-'])) bg-primary
                                    @elseif(in_array($nilai->nilai_huruf, ['C+', 'C'])) bg-warning
                                    @else bg-danger
                                    @endif fs-5">
                                    {{ $nilai->nilai_huruf }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Bobot</th>
                            <td><strong>{{ number_format($nilai->bobot, 2) }}</strong></td>
                        </tr>
                    </table>
                </div>
            </div>

            @if(in_array(Auth::user()->role->name, ['dosen', 'admin_prodi', 'admin_fakultas', 'admin_universitas', 'super_admin']))
            <div class="card">
                <div class="card-header bg-warning">
                    <h5 class="card-title mb-0">Aksi</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex gap-2">
                        <a href="{{ route('nilai.edit', $nilai->id) }}" class="btn btn-warning">
                            <i class="bi bi-pencil"></i> Edit Nilai
                        </a>
                        
                        <form action="{{ route('nilai.destroy', $nilai->id) }}" 
                              method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" 
                                    onclick="return confirm('Hapus nilai ini?')">
                                <i class="bi bi-trash"></i> Hapus Nilai
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <div class="col-md-4">
            <!-- Audit Trail -->
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-clock-history"></i> Audit Trail
                    </h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <td><strong>Dibuat oleh</strong></td>
                            <td>{{ $nilai->created_by ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Dibuat pada</strong></td>
                            <td>
                                @if($nilai->created_at)
                                {{ \Carbon\Carbon::parse($nilai->created_at)->format('d/m/Y H:i') }}
                                @else
                                -
                                @endif
                            </td>
                        </tr>
                        @if($nilai->updated_by)
                        <tr>
                            <td><strong>Diubah oleh</strong></td>
                            <td>{{ $nilai->updated_by }}</td>
                        </tr>
                        <tr>
                            <td><strong>Diubah pada</strong></td>
                            <td>{{ \Carbon\Carbon::parse($nilai->updated_at)->format('d/m/Y H:i') }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-info-circle"></i> Konversi Nilai
                    </h5>
                </div>
                <div class="card-body">
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
@endsection
