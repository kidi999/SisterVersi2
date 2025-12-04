<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>SISTER - Sistem Informasi Akademik Terintegrasi</title>

        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

        <style>
            body {
                min-height: 100vh;
                display: flex;
                flex-direction: column;
            }
            .hero-section {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                padding: 100px 0;
            }
            .feature-card {
                transition: transform 0.3s;
                height: 100%;
            }
            .feature-card:hover {
                transform: translateY(-5px);
            }
            .role-badge {
                font-size: 0.875rem;
                padding: 0.5rem 1rem;
            }
        </style>
    </head>
    <body>
        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container">
                <a class="navbar-brand fw-bold" href="{{ url('/') }}">
                    <i class="bi bi-mortarboard-fill me-2"></i>SISTER
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        @auth
                            <li class="nav-item">
                                <a class="nav-link" href="{{ url('/dashboard') }}">
                                    <i class="bi bi-speedometer2"></i> Dashboard
                                </a>
                            </li>
                        @else
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">
                                    <i class="bi bi-box-arrow-in-right"></i> Login
                                </a>
                            </li>
                        @endauth
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <section class="hero-section text-center">
            <div class="container">
                <h1 class="display-4 fw-bold mb-4">Sistem Informasi Akademik Terintegrasi</h1>
                <p class="lead mb-4">Sistem manajemen akademik terpadu untuk mengelola data mahasiswa, dosen, mata kuliah, dan nilai secara efisien</p>
                @auth
                    <a href="{{ url('/dashboard') }}" class="btn btn-light btn-lg">
                        <i class="bi bi-speedometer2 me-2"></i>Ke Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-light btn-lg">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Login Sekarang
                    </a>
                @endauth
            </div>
        </section>

        <!-- Features Section -->
        <section class="py-5 bg-light">
            <div class="container">
                <h2 class="text-center mb-5">Fitur Utama</h2>
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="card feature-card shadow-sm">
                            <div class="card-body text-center">
                                <i class="bi bi-people-fill text-primary" style="font-size: 3rem;"></i>
                                <h5 class="card-title mt-3">Manajemen Mahasiswa</h5>
                                <p class="card-text">Kelola data mahasiswa, KRS, dan nilai secara komprehensif</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card feature-card shadow-sm">
                            <div class="card-body text-center">
                                <i class="bi bi-person-badge-fill text-success" style="font-size: 3rem;"></i>
                                <h5 class="card-title mt-3">Manajemen Dosen</h5>
                                <p class="card-text">Atur jadwal mengajar dan data dosen dengan mudah</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card feature-card shadow-sm">
                            <div class="card-body text-center">
                                <i class="bi bi-book-fill text-warning" style="font-size: 3rem;"></i>
                                <h5 class="card-title mt-3">Manajemen Mata Kuliah</h5>
                                <p class="card-text">Kelola mata kuliah, kelas, dan jadwal perkuliahan</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card feature-card shadow-sm">
                            <div class="card-body text-center">
                                <i class="bi bi-building text-info" style="font-size: 3rem;"></i>
                                <h5 class="card-title mt-3">Struktur Organisasi</h5>
                                <p class="card-text">Manajemen fakultas dan program studi</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card feature-card shadow-sm">
                            <div class="card-body text-center">
                                <i class="bi bi-graph-up text-danger" style="font-size: 3rem;"></i>
                                <h5 class="card-title mt-3">Sistem Penilaian</h5>
                                <p class="card-text">Input dan monitoring nilai mahasiswa real-time</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card feature-card shadow-sm">
                            <div class="card-body text-center">
                                <i class="bi bi-shield-check text-secondary" style="font-size: 3rem;"></i>
                                <h5 class="card-title mt-3">Role-Based Access</h5>
                                <p class="card-text">Hak akses berbasis peran untuk keamanan data</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Roles Section -->
        <section class="py-5">
            <div class="container">
                <h2 class="text-center mb-5">Hak Akses Pengguna</h2>
                <div class="row g-3 justify-content-center">
                    <div class="col-md-4">
                        <div class="card border-danger">
                            <div class="card-body">
                                <span class="badge bg-danger role-badge w-100 mb-3">
                                    <i class="bi bi-star-fill me-2"></i>Super Admin
                                </span>
                                <p class="card-text small">Akses penuh ke seluruh sistem, manajemen pengguna, dan konfigurasi sistem</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-warning">
                            <div class="card-body">
                                <span class="badge bg-warning text-dark role-badge w-100 mb-3">
                                    <i class="bi bi-building me-2"></i>Admin Universitas
                                </span>
                                <p class="card-text small">Manajemen data universitas, fakultas, dan program studi</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-info">
                            <div class="card-body">
                                <span class="badge bg-info role-badge w-100 mb-3">
                                    <i class="bi bi-diagram-3 me-2"></i>Admin Fakultas
                                </span>
                                <p class="card-text small">Manajemen data fakultas dan program studi di bawahnya</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-success">
                            <div class="card-body">
                                <span class="badge bg-success role-badge w-100 mb-3">
                                    <i class="bi bi-mortarboard me-2"></i>Admin Prodi
                                </span>
                                <p class="card-text small">Manajemen data program studi, mahasiswa, dan dosen</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-primary">
                            <div class="card-body">
                                <span class="badge bg-primary role-badge w-100 mb-3">
                                    <i class="bi bi-person-badge me-2"></i>Dosen
                                </span>
                                <p class="card-text small">Akses ke jadwal mengajar, input nilai, dan data mahasiswa</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-secondary">
                            <div class="card-body">
                                <span class="badge bg-secondary role-badge w-100 mb-3">
                                    <i class="bi bi-person me-2"></i>Mahasiswa
                                </span>
                                <p class="card-text small">Akses ke KRS, jadwal kuliah, dan nilai pribadi</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-dark text-white py-4 mt-auto">
            <div class="container text-center">
                <p class="mb-0">&copy; {{ date('Y') }} SISTER - Sistem Informasi Akademik Terintegrasi</p>
                <p class="small text-muted mb-0">Powered by Laravel {{ app()->version() }}</p>
            </div>
        </footer>

        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>
