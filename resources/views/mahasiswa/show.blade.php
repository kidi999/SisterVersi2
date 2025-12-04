@extends('layouts.app')

@section('title', 'Detail Mahasiswa')
@section('header', 'Detail Mahasiswa')

@section('content')

<!-- Alert untuk kredensial yang baru di-generate -->
@if(session('show_credentials'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <h5 class="alert-heading">
        <i class="bi bi-check-circle"></i> Akun User Berhasil Dibuat!
    </h5>
    <hr>
    <p><strong>Kredensial Login Mahasiswa:</strong></p>
    <div class="bg-white p-3 rounded border border-success mb-3">
        <table class="table table-sm table-borderless mb-0">
            <tr>
                <td width="150"><strong>Email/Username:</strong></td>
                <td>
                    <code class="text-primary">{{ session('generated_email') }}</code>
                    <button class="btn btn-sm btn-outline-primary ms-2" 
                            onclick="copyToClipboard('{{ session('generated_email') }}')"
                            title="Copy email">
                        <i class="bi bi-clipboard"></i>
                    </button>
                </td>
            </tr>
            <tr>
                <td><strong>Password:</strong></td>
                <td>
                    <code class="text-danger">{{ session('generated_password') }}</code>
                    <button class="btn btn-sm btn-outline-danger ms-2" 
                            onclick="copyToClipboard('{{ session('generated_password') }}')"
                            title="Copy password">
                        <i class="bi bi-clipboard"></i>
                    </button>
                </td>
            </tr>
        </table>
    </div>
    <p class="mb-0">
        <i class="bi bi-exclamation-triangle text-warning"></i> 
        <strong>PENTING:</strong> Salin dan simpan kredensial ini sekarang. 
        Password tidak akan ditampilkan lagi setelah halaman ini ditutup.
    </p>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if(session('success') && !session('show_credentials'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="bi bi-check-circle"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="bi bi-exclamation-triangle"></i> {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="row">
    <!-- Informasi Mahasiswa -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-person-badge"></i> Informasi Mahasiswa</h5>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <div class="avatar-circle bg-primary text-white mx-auto" style="width: 100px; height: 100px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 48px;">
                        {{ strtoupper(substr($mahasiswa->nama_mahasiswa, 0, 1)) }}
                    </div>
                    <h5 class="mt-3 mb-1">{{ $mahasiswa->nama_mahasiswa }}</h5>
                    <p class="text-muted mb-2">{{ $mahasiswa->nim }}</p>
                    <span class="badge 
                        {{ $mahasiswa->status == 'Aktif' ? 'bg-success' : '' }}
                        {{ $mahasiswa->status == 'Cuti' ? 'bg-warning' : '' }}
                        {{ $mahasiswa->status == 'Lulus' ? 'bg-info' : '' }}
                        {{ $mahasiswa->status == 'DO' ? 'bg-danger' : '' }}
                        {{ $mahasiswa->status == 'Mengundurkan Diri' ? 'bg-secondary' : '' }}">
                        {{ $mahasiswa->status }}
                    </span>
                </div>

                <hr>

                <table class="table table-sm">
                    <tr>
                        <td width="40%"><strong>Program Studi</strong></td>
                        <td>{{ $mahasiswa->programStudi->nama_prodi }}</td>
                    </tr>
                    <tr>
                        <td><strong>Fakultas</strong></td>
                        <td>{{ $mahasiswa->programStudi->fakultas->nama_fakultas }}</td>
                    </tr>
                    <tr>
                        <td><strong>Jenjang</strong></td>
                        <td>{{ $mahasiswa->programStudi->jenjang }}</td>
                    </tr>
                    <tr>
                        <td><strong>Tahun Masuk</strong></td>
                        <td>{{ $mahasiswa->tahun_masuk }}</td>
                    </tr>
                    <tr>
                        <td><strong>Semester</strong></td>
                        <td>{{ $mahasiswa->semester ?? 1 }}</td>
                    </tr>
                    <tr>
                        <td><strong>IPK</strong></td>
                        <td><strong class="text-primary">{{ number_format($mahasiswa->ipk ?? 0, 2) }}</strong></td>
                    </tr>
                </table>

                <div class="d-grid gap-2 mt-3">
                    <a href="{{ route('mahasiswa.edit', $mahasiswa) }}" class="btn btn-warning">
                        <i class="bi bi-pencil"></i> Edit Data
                    </a>
                    <a href="{{ route('mahasiswa.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
        </div>

        <!-- Informasi Akun User -->
        <div class="card mt-3">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="bi bi-person-circle"></i> Akun User</h5>
            </div>
            <div class="card-body">
                @if($mahasiswa->user)
                    <div class="alert alert-success mb-3">
                        <i class="bi bi-check-circle"></i> Mahasiswa ini sudah memiliki akun user
                    </div>
                    <table class="table table-sm">
                        <tr>
                            <td width="40%"><strong>Email Login</strong></td>
                            <td>
                                <code>{{ $mahasiswa->user->email }}</code>
                                <button class="btn btn-sm btn-outline-secondary ms-2" 
                                        onclick="copyToClipboard('{{ $mahasiswa->user->email }}')"
                                        title="Copy email">
                                    <i class="bi bi-clipboard"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Nama User</strong></td>
                            <td>{{ $mahasiswa->user->name }}</td>
                        </tr>
                        <tr>
                            <td><strong>Role</strong></td>
                            <td>
                                <span class="badge bg-primary">{{ $mahasiswa->user->role->display_name }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Status Akun</strong></td>
                            <td>
                                @if($mahasiswa->user->is_active)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-danger">Non-Aktif</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Password Hash</strong></td>
                            <td>
                                <div class="input-group input-group-sm">
                                    <input type="text" class="form-control font-monospace" 
                                           id="passwordHash" 
                                           value="{{ $mahasiswa->user->password }}" 
                                           readonly 
                                           style="font-size: 10px;">
                                    <button class="btn btn-outline-secondary" 
                                            onclick="copyToClipboard('{{ $mahasiswa->user->password }}')"
                                            title="Copy password hash">
                                        <i class="bi bi-clipboard"></i>
                                    </button>
                                </div>
                                <small class="text-muted d-block mt-1">
                                    <i class="bi bi-info-circle"></i> Password terenkripsi (bcrypt)
                                </small>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Last Login</strong></td>
                            <td>
                                @if($mahasiswa->user->last_login_at ?? false)
                                    {{ \Carbon\Carbon::parse($mahasiswa->user->last_login_at)->format('d/m/Y H:i:s') }}
                                    <small class="text-muted">({{ \Carbon\Carbon::parse($mahasiswa->user->last_login_at)->diffForHumans() }})</small>
                                @else
                                    <span class="text-muted">Belum pernah login</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Dibuat</strong></td>
                            <td>{{ $mahasiswa->user->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        @if($mahasiswa->user->updated_at != $mahasiswa->user->created_at)
                        <tr>
                            <td><strong>Terakhir Diubah</strong></td>
                            <td>{{ $mahasiswa->user->updated_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        @endif
                    </table>
                    <div class="d-grid">
                        <a href="{{ route('users.show', $mahasiswa->user->id) }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-eye"></i> Lihat Detail User
                        </a>
                    </div>
                @else
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i> Mahasiswa ini belum memiliki akun user untuk login ke sistem.
                    </div>
                    
                    <div class="card border-primary mb-3">
                        <div class="card-header bg-primary text-white">
                            <i class="bi bi-magic"></i> Generate Akun Otomatis
                        </div>
                        <div class="card-body">
                            <p class="card-text mb-3">
                                Sistem akan membuat akun dengan kredensial berikut:
                            </p>
                            <ul class="list-unstyled mb-3">
                                <li><strong>Username/Email:</strong> <code>{{ $mahasiswa->email }}</code></li>
                                <li><strong>Password Default:</strong> <code>Mhs{{ $mahasiswa->nim }}</code></li>
                                <li><strong>Role:</strong> <span class="badge bg-primary">Mahasiswa</span></li>
                            </ul>
                            <form action="{{ route('mahasiswa.generate-user', $mahasiswa->id) }}" method="POST" 
                                  onsubmit="return confirm('Generate akun user untuk mahasiswa ini?\n\nEmail: {{ $mahasiswa->email }}\nPassword: Mhs{{ $mahasiswa->nim }}')">
                                @csrf
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-key"></i> Generate Akun User
                                </button>
                            </form>
                        </div>
                    </div>
                    
                    <div class="alert alert-info mb-0">
                        <small>
                            <i class="bi bi-info-circle"></i> 
                            <strong>Alternatif:</strong> Anda juga dapat membuat akun manual di menu 
                            <a href="{{ route('users.create') }}" class="alert-link">Pengguna & Role</a> 
                            dan hubungkan dengan mahasiswa ini.
                        </small>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Data Pribadi & Kontak -->
    <div class="col-lg-8">
        <div class="card mb-3">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="bi bi-person-lines-fill"></i> Data Pribadi</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td width="40%"><strong>Jenis Kelamin</strong></td>
                                <td>{{ $mahasiswa->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Tempat Lahir</strong></td>
                                <td>{{ $mahasiswa->tempat_lahir ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Tanggal Lahir</strong></td>
                                <td>{{ $mahasiswa->tanggal_lahir ? $mahasiswa->tanggal_lahir->format('d/m/Y') : '-' }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td width="40%"><strong>Email</strong></td>
                                <td>{{ $mahasiswa->email }}</td>
                            </tr>
                            <tr>
                                <td><strong>Telepon</strong></td>
                                <td>{{ $mahasiswa->telepon ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Alamat</strong></td>
                                <td>{{ $mahasiswa->alamat ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Wali -->
        @if($mahasiswa->nama_wali)
        <div class="card mb-3">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="bi bi-people"></i> Data Wali/Orang Tua</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td width="40%"><strong>Nama Wali</strong></td>
                                <td>{{ $mahasiswa->nama_wali }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td width="40%"><strong>Telepon Wali</strong></td>
                                <td>{{ $mahasiswa->telepon_wali ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Riwayat KRS -->
        <div class="card">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0"><i class="bi bi-journal-text"></i> Riwayat KRS</h5>
            </div>
            <div class="card-body">
                @if($mahasiswa->krs->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>Semester</th>
                                    <th>Mata Kuliah</th>
                                    <th>SKS</th>
                                    <th>Nilai</th>
                                    <th>Grade</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($mahasiswa->krs->groupBy('kelas.semester') as $semester => $krsItems)
                                    @foreach($krsItems as $krs)
                                        <tr>
                                            @if($loop->first)
                                                <td rowspan="{{ $krsItems->count() }}" class="align-middle">
                                                    <strong>{{ $semester }}</strong>
                                                </td>
                                            @endif
                                            <td>{{ $krs->kelas->mataKuliah->nama_mata_kuliah }}</td>
                                            <td>{{ $krs->kelas->mataKuliah->sks }}</td>
                                            <td>{{ $krs->nilai->nilai_angka ?? '-' }}</td>
                                            <td>
                                                @if($krs->nilai)
                                                    <span class="badge 
                                                        {{ $krs->nilai->grade == 'A' ? 'bg-success' : '' }}
                                                        {{ $krs->nilai->grade == 'B' ? 'bg-primary' : '' }}
                                                        {{ $krs->nilai->grade == 'C' ? 'bg-warning' : '' }}
                                                        {{ $krs->nilai->grade == 'D' ? 'bg-danger' : '' }}
                                                        {{ $krs->nilai->grade == 'E' ? 'bg-dark' : '' }}">
                                                        {{ $krs->nilai->grade }}
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info mb-0">
                        <i class="bi bi-info-circle"></i> Belum ada data KRS
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function copyToClipboard(text) {
    // Create temporary textarea
    const textarea = document.createElement('textarea');
    textarea.value = text;
    textarea.style.position = 'fixed';
    textarea.style.opacity = '0';
    document.body.appendChild(textarea);
    
    // Select and copy
    textarea.select();
    textarea.setSelectionRange(0, 99999); // For mobile devices
    
    try {
        document.execCommand('copy');
        
        // Show success message
        const toast = document.createElement('div');
        toast.className = 'position-fixed top-0 end-0 p-3';
        toast.style.zIndex = '9999';
        toast.innerHTML = `
            <div class="toast show" role="alert">
                <div class="toast-header bg-success text-white">
                    <i class="bi bi-check-circle me-2"></i>
                    <strong class="me-auto">Berhasil</strong>
                    <button type="button" class="btn-close btn-close-white" onclick="this.closest('.position-fixed').remove()"></button>
                </div>
                <div class="toast-body">
                    Teks berhasil disalin ke clipboard
                </div>
            </div>
        `;
        document.body.appendChild(toast);
        
        // Auto remove after 3 seconds
        setTimeout(() => {
            toast.remove();
        }, 3000);
        
    } catch (err) {
        alert('Gagal menyalin: ' + err);
    }
    
    // Remove temporary textarea
    document.body.removeChild(textarea);
}
</script>
@endpush

@endsection
