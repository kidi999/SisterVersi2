<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SISTER - Sistem Informasi Akademik')</title>
    <!-- Open Graph Meta Tags untuk WhatsApp/Facebook Preview -->
    <meta property="og:title" content="SISTER - Sistem Informasi Akademik Terintegrasi">
    <meta property="og:description" content="Sistem manajemen akademik terpadu untuk universitas.">
    <meta property="og:image" content="https://unicimi.ac.id/wp-content/uploads/2022/09/cropped-logo-unicimi.png">
    <meta property="og:url" content="https://sister.unic.ac.id">
    <meta name="twitter:card" content="summary_large_image">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        body {
            overflow-x: hidden;
            padding-top: 56px;
        }
        
        /* Top Navbar Styles */
        .navbar.fixed-top {
            height: 56px;
        }
        
        .user-avatar-small {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 0.9rem;
        }
        
        .user-avatar-dropdown {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 1.1rem;
        }
        
        .dropdown-header {
            padding: 12px 16px;
        }
        
        .dropdown-item {
            padding: 10px 20px;
            transition: all 0.2s;
        }
        
        .dropdown-item:hover {
            background-color: #f8f9fa;
            padding-left: 25px;
        }
        
        .sidebar {
            min-height: calc(100vh - 56px);
            max-height: calc(100vh - 56px);
            overflow-y: auto;
            background: linear-gradient(180deg, #2c3e50 0%, #34495e 100%);
            position: fixed;
            width: 250px;
            left: 0;
            top: 0;
            transition: left 0.3s ease;
            z-index: 1000;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }
        .sidebar.hidden {
            left: -250px;
        }
        .sidebar::-webkit-scrollbar {
            width: 6px;
        }
        .sidebar::-webkit-scrollbar-track {
            background: #2c3e50;
        }
        .sidebar::-webkit-scrollbar-thumb {
            background: #34495e;
            border-radius: 3px;
        }
        .sidebar .nav-link {
            color: #ecf0f1;
            padding: 10px 20px;
            margin: 2px 0;
            font-size: 0.9rem;
            transition: all 0.2s;
            border-left: 3px solid transparent;
        }
        .sidebar .nav-link:hover {
            background-color: rgba(52, 73, 94, 0.5);
            color: #fff;
            border-left-color: #3498db;
            padding-left: 25px;
        }
        .sidebar .nav-link.active {
            background-color: #34495e;
            color: #fff;
            border-left-color: #3498db;
        }
        .sidebar h6 {
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.5px;
            margin-top: 15px;
        }
        .menu-section {
            margin-bottom: 5px;
        }
        .menu-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 20px;
            color: #ecf0f1;
            background-color: rgba(0,0,0,0.2);
            cursor: pointer;
            transition: all 0.2s;
            border-left: 3px solid transparent;
        }
        .menu-header:hover {
            background-color: rgba(52, 73, 94, 0.5);
            border-left-color: #3498db;
        }
        .menu-header.collapsed .menu-icon {
            transform: rotate(-90deg);
        }
        .menu-icon {
            transition: transform 0.2s;
            font-size: 0.8rem;
        }
        .menu-title {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .menu-collapse {
            padding-left: 10px;
        }
        .menu-collapse .nav-link {
            font-size: 0.85rem;
            padding: 8px 20px;
        }
        .sidebar-header {
            padding: 20px;
            background-color: rgba(0,0,0,0.2);
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .sidebar-footer {
            position: sticky;
            bottom: 0;
            background: linear-gradient(180deg, transparent 0%, #2c3e50 20%);
            padding: 15px;
            border-top: 1px solid rgba(255,255,255,0.1);
        }
        .user-profile {
            display: flex;
            align-items: center;
            padding: 10px;
            background-color: rgba(0,0,0,0.2);
            border-radius: 8px;
            margin-bottom: 10px;
        }
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            margin-right: 10px;
        }
        .user-info {
            flex: 1;
            overflow: hidden;
        }
        .user-name {
            color: #fff;
            font-size: 0.9rem;
            font-weight: 600;
            margin: 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .user-role {
            color: #bdc3c7;
            font-size: 0.75rem;
            margin: 0;
        }
        .main-content {
            padding: 20px;
            margin-left: 250px;
            margin-top: 56px;
            transition: margin-left 0.3s ease;
            min-height: calc(100vh - 56px);
            background-color: #f8f9fa;
        }
        .main-content.expanded {
            margin-left: 0;
        }
        
        .navbar.expanded {
            margin-left: 0 !important;
        }
        .navbar-brand {
            font-weight: bold;
            color: #fff !important;
        }
        .sidebar-toggle {
            position: fixed;
            left: 260px;
            top: 20px;
            width: 40px;
            height: 40px;
            background: #3498db;
            border: none;
            border-radius: 50%;
            color: white;
            cursor: pointer;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            transition: all 0.3s ease;
            z-index: 1001;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .sidebar-toggle:hover {
            background: #2980b9;
            transform: scale(1.1);
        }
        .sidebar-toggle.moved {
            left: 10px;
        }
        .content-header {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        @media (max-width: 768px) {
            .sidebar {
                left: -250px;
            }
            .sidebar.show {
                left: 0;
            }
            .main-content {
                margin-left: 0;
            }
            .sidebar-toggle {
                left: 10px;
            }
        }

        /* File Upload Component Styles */
        .file-item {
            position: relative;
            padding: 15px;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            background: #f8f9fa;
            transition: all 0.3s;
        }
        .file-item:hover {
            background: #e9ecef;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .file-item .btn-delete-file {
            position: absolute;
            top: 8px;
            right: 8px;
            padding: 4px 8px;
            font-size: 12px;
        }
        .file-item .file-icon {
            font-size: 2rem;
            margin-bottom: 8px;
        }
        .file-item .file-name {
            font-size: 0.875rem;
            font-weight: 500;
            margin-bottom: 4px;
            word-break: break-all;
        }
        .file-item .file-size {
            font-size: 0.75rem;
            color: #6c757d;
        }
        .file-item img.file-preview {
            width: 100%;
            height: 120px;
            object-fit: cover;
            border-radius: 4px;
            margin-bottom: 8px;
        }
        .alert-sm {
            padding: 8px 12px;
            font-size: 0.875rem;
        }
        #fileList .col {
            margin-bottom: 15px;
        }
    </style>
    
    @yield('styles')
</head>
<body>
    <!-- Top Navbar -->
    <nav class="navbar navbar-expand navbar-dark bg-primary fixed-top shadow-sm" style="margin-left: 250px; transition: margin-left 0.3s ease; z-index: 999;">
        <div class="container-fluid">
            <button class="btn btn-link text-white" id="sidebarToggleTop">
                <i class="bi bi-list fs-4"></i>
            </button>
            
            <div class="d-flex align-items-center ms-auto">
                <!-- Notifications -->
                <div class="dropdown me-3">
                    <button class="btn btn-link text-white position-relative" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-bell fs-5"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem;">
                            3
                        </span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" style="min-width: 300px;">
                        <li><h6 class="dropdown-header">Notifikasi</h6></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item small" href="#">Tidak ada notifikasi baru</a></li>
                    </ul>
                </div>

                <!-- User Profile Dropdown -->
                <div class="dropdown">
                    <button class="btn btn-link text-white text-decoration-none d-flex align-items-center" type="button" data-bs-toggle="dropdown">
                        <div class="user-avatar-small me-2">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                        <div class="text-start me-2 d-none d-md-block">
                            <div class="fw-semibold small">{{ Auth::user()->name }}</div>
                            <div class="text-white-50" style="font-size: 0.7rem;">{{ Auth::user()->getRoleNames()->first() }}</div>
                        </div>
                        <i class="bi bi-chevron-down"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <div class="dropdown-header d-flex align-items-center">
                                <div class="user-avatar-dropdown me-2">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="fw-semibold">{{ Auth::user()->name }}</div>
                                    <div class="small text-muted">{{ Auth::user()->email }}</div>
                                </div>
                            </div>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                <i class="bi bi-person me-2"></i>Profil Saya
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('profile.edit-password') }}">
                                <i class="bi bi-key me-2"></i>Ganti Password
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="bi bi-box-arrow-right me-2"></i>Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- Sidebar -->
    <nav class="sidebar" id="sidebar" style="top: 56px;">
        <!-- Sidebar Header -->
        <div class="sidebar-header">
            <div class="text-center">
                <h4 class="text-white mb-1">SISTER</h4>
                <small class="text-white-50">Sistem Informasi Akademik</small>
            </div>
        </div>

        <!-- Menu Navigation -->
        <div class="py-2">
            <!-- Dashboard -->
            <ul class="nav flex-column px-2">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                </li>
            </ul>

            @if(Auth::user()->hasRole(['super_admin']))
            <!-- MASTER DATA Section -->
            <div class="menu-section">
                <div class="menu-header collapsed" data-bs-toggle="collapse" data-bs-target="#menuMasterData">
                    <div class="menu-title">
                        <i class="bi bi-database"></i>
                        <span>Master Data</span>
                    </div>
                    <i class="bi bi-chevron-down menu-icon"></i>
                </div>
                <div class="collapse" id="menuMasterData">
                    <ul class="nav flex-column menu-collapse">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('users.*') && !request()->routeIs('users.trash') ? 'active' : '' }}" href="{{ route('users.index') }}">
                                <i class="bi bi-people"></i> Pengguna & Role
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('tahun-akademik.*') && !request()->routeIs('tahun-akademik.trash') ? 'active' : '' }}" href="{{ route('tahun-akademik.index') }}">
                                <i class="bi bi-calendar-range"></i> Tahun Akademik
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('semester.*') && !request()->routeIs('semester.trash') ? 'active' : '' }}" href="{{ route('semester.index') }}">
                                <i class="bi bi-calendar2-week"></i> Semester
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            @endif

            @if(Auth::user()->hasRole(['super_admin']))
            <!-- WILAYAH Section -->
            <div class="menu-section">
                <div class="menu-header collapsed" data-bs-toggle="collapse" data-bs-target="#menuWilayah">
                    <div class="menu-title">
                        <i class="bi bi-map"></i>
                        <span>Wilayah</span>
                    </div>
                    <i class="bi bi-chevron-down menu-icon"></i>
                </div>
                <div class="collapse" id="menuWilayah">
                    <ul class="nav flex-column menu-collapse">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('provinsi.*') ? 'active' : '' }}" href="{{ route('provinsi.index') }}">
                                <i class="bi bi-map"></i> Provinsi
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('regency.*') ? 'active' : '' }}" href="{{ route('regency.index') }}">
                                <i class="bi bi-pin-map"></i> Kabupaten/Kota
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('sub-regency.*') ? 'active' : '' }}" href="{{ route('sub-regency.index') }}">
                                <i class="bi bi-signpost"></i> Kecamatan
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('village.*') ? 'active' : '' }}" href="{{ route('village.index') }}">
                                <i class="bi bi-geo-alt"></i> Desa/Kelurahan
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            @endif

            @if(!Auth::user()->hasRole(['mahasiswa']))
            <!-- DATA UNIVERSITAS Section -->
            <div class="menu-section">
                <div class="menu-header collapsed" data-bs-toggle="collapse" data-bs-target="#menuUniversitas">
                    <div class="menu-title">
                        <i class="bi bi-bank"></i>
                        <span>Data Universitas</span>
                    </div>
                    <i class="bi bi-chevron-down menu-icon"></i>
                </div>
                <div class="collapse" id="menuUniversitas">
                    <ul class="nav flex-column menu-collapse">
                        @if(Auth::user()->hasRole(['super_admin', 'admin_universitas']))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('universities.*') && !request()->routeIs('universities.trash') ? 'active' : '' }}" href="{{ route('universities.index') }}">
                                <i class="bi bi-bank"></i> Data Universitas
                            </a>
                        </li>
                        @endif
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('university.profile') ? 'active' : '' }}" href="{{ route('university.profile') }}">
                                <i class="bi bi-info-circle"></i> Profil Universitas
                            </a>
                        </li>
                        @if(Auth::user()->hasRole(['super_admin', 'admin_universitas', 'admin_fakultas']))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('fakultas.*') && !request()->routeIs('fakultas.trash') ? 'active' : '' }}" href="{{ route('fakultas.index') }}">
                                <i class="bi bi-building"></i> Fakultas
                            </a>
                        </li>
                        @endif
                        @if(Auth::user()->hasRole(['super_admin', 'admin_universitas', 'admin_fakultas', 'admin_prodi']))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('program-studi.*') && !request()->routeIs('program-studi.trash') ? 'active' : '' }}" href="{{ route('program-studi.index') }}">
                                <i class="bi bi-bookshelf"></i> Program Studi
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('dosen.*') && !request()->routeIs('dosen.trash') ? 'active' : '' }}" href="{{ route('dosen.index') }}">
                                <i class="bi bi-person-badge"></i> Data Dosen
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('mata-kuliah.*') && !request()->routeIs('mata-kuliah.trash') ? 'active' : '' }}" href="{{ route('mata-kuliah.index') }}">
                                <i class="bi bi-book"></i> Mata Kuliah
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('ruang.*') && !request()->routeIs('ruang.trash') ? 'active' : '' }}" href="{{ route('ruang.index') }}">
                                <i class="bi bi-door-open"></i> Ruang
                            </a>
                        </li>
                        @endif
                    </ul>
                </div>
            </div>
            @endif

            <!-- AKREDITASI Section -->
            @if(Auth::user()->hasRole(['super_admin', 'admin_universitas', 'admin_fakultas', 'admin_prodi']))
            <div class="menu-section">
                <div class="menu-header collapsed" data-bs-toggle="collapse" data-bs-target="#menuAkreditasi">
                    <div class="menu-title">
                        <i class="bi bi-award"></i>
                        <span>Akreditasi</span>
                    </div>
                    <i class="bi bi-chevron-down menu-icon"></i>
                </div>
                <div class="collapse" id="menuAkreditasi">
                    <ul class="nav flex-column menu-collapse">
                        @if(Auth::user()->hasRole(['super_admin', 'admin_universitas']))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('akreditasi-universitas.*') && !request()->routeIs('akreditasi-universitas.trash') ? 'active' : '' }}" href="{{ route('akreditasi-universitas.index') }}">
                                <i class="bi bi-award-fill"></i> Akreditasi Universitas
                            </a>
                        </li>
                        @endif
                        @if(Auth::user()->hasRole(['super_admin', 'admin_universitas', 'admin_fakultas']))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('akreditasi-fakultas.*') && !request()->routeIs('akreditasi-fakultas.trash') ? 'active' : '' }}" href="{{ route('akreditasi-fakultas.index') }}">
                                <i class="bi bi-patch-check"></i> Akreditasi Fakultas
                            </a>
                        </li>
                        @endif
                        @if(Auth::user()->hasRole(['super_admin', 'admin_universitas', 'admin_fakultas', 'admin_prodi']))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('akreditasi-program-studi.*') && !request()->routeIs('akreditasi-program-studi.trash') ? 'active' : '' }}" href="{{ route('akreditasi-program-studi.index') }}">
                                <i class="bi bi-trophy"></i> Akreditasi Program Studi
                            </a>
                        </li>
                        @endif
                    </ul>
                </div>
            </div>
            @endif

            <!-- DATA AKADEMIK Section -->
            @if(Auth::user()->hasRole(['mahasiswa']))
            <!-- Menu untuk Mahasiswa -->
            <div class="menu-section">
                <div class="menu-header collapsed" data-bs-toggle="collapse" data-bs-target="#menuAkademikMahasiswa">
                    <div class="menu-title">
                        <i class="bi bi-book"></i>
                        <span>Akademik Saya</span>
                    </div>
                    <i class="bi bi-chevron-down menu-icon"></i>
                </div>
                <div class="collapse" id="menuAkademikMahasiswa">
                    <ul class="nav flex-column menu-collapse">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('krs.*') ? 'active' : '' }}" href="{{ route('krs.index') }}">
                                <i class="bi bi-card-checklist"></i> Kartu Rencana Studi (KRS)
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('nilai.*') ? 'active' : '' }}" href="{{ route('nilai.index') }}">
                                <i class="bi bi-award"></i> Nilai & Transkrip
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('jadwal-kuliah.*') ? 'active' : '' }}" href="{{ route('jadwal-kuliah.index') }}">
                                <i class="bi bi-calendar3"></i> Jadwal Kuliah
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('absensi-mahasiswa.kehadiran-saya') ? 'active' : '' }}" href="{{ route('absensi-mahasiswa.kehadiran-saya') }}">
                                <i class="bi bi-person-check"></i> Kehadiran Saya
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('my-payments.*') ? 'active' : '' }}" href="{{ route('my-payments.index') }}">
                                <i class="bi bi-credit-card"></i> Tagihan & Pembayaran
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            @else
            <!-- Menu untuk Admin/Dosen -->
            <div class="menu-section">
                <div class="menu-header collapsed" data-bs-toggle="collapse" data-bs-target="#menuAkademik">
                    <div class="menu-title">
                        <i class="bi bi-book"></i>
                        <span>Data Akademik</span>
                    </div>
                    <i class="bi bi-chevron-down menu-icon"></i>
                </div>
                <div class="collapse" id="menuAkademik">
                    <ul class="nav flex-column menu-collapse">
                        @if(Auth::user()->hasRole(['super_admin', 'admin_universitas', 'admin_fakultas', 'admin_prodi']))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('pendaftaran-mahasiswa.*') && !request()->routeIs('pendaftaran-mahasiswa-trash') ? 'active' : '' }}" href="{{ route('pendaftaran-mahasiswa.index') }}">
                                <i class="bi bi-clipboard-check"></i> Pendaftaran Mahasiswa
                            </a>
                        </li>
                        @endif
                        @if(Auth::user()->hasRole(['super_admin', 'admin_universitas', 'admin_fakultas', 'admin_prodi', 'dosen']))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('mahasiswa.*') && !request()->routeIs('mahasiswa.trash') ? 'active' : '' }}" href="{{ route('mahasiswa.index') }}">
                                <i class="bi bi-person-badge"></i> Mahasiswa
                            </a>
                        </li>
                        @endif
                        @if(Auth::user()->hasRole(['super_admin', 'admin_universitas', 'admin_fakultas', 'admin_prodi']))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('dosen.*') && !request()->routeIs('dosen.trash') ? 'active' : '' }}" href="{{ route('dosen.index') }}">
                                <i class="bi bi-person-workspace"></i> Dosen
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('mata-kuliah.*') && !request()->routeIs('mata-kuliah.trash') ? 'active' : '' }}" href="{{ route('mata-kuliah.index') }}">
                                <i class="bi bi-book"></i> Mata Kuliah
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('ruang.*') && !request()->routeIs('ruang.trash') ? 'active' : '' }}" href="{{ route('ruang.index') }}">
                                <i class="bi bi-door-open"></i> Ruang
                            </a>
                        </li>
                        @endif
                        @if(Auth::user()->hasRole(['super_admin', 'admin_universitas', 'admin_fakultas', 'admin_prodi', 'dosen']))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('jadwal-kuliah.*') && !request()->routeIs('jadwal-kuliah.trash') ? 'active' : '' }}" href="{{ route('jadwal-kuliah.index') }}">
                                <i class="bi bi-calendar3"></i> Jadwal Kuliah
                            </a>
                        </li>
                        @endif



                        @if(Auth::user()->hasRole(['super_admin', 'admin_universitas', 'admin_fakultas', 'admin_prodi']))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('kelas.*') && !request()->routeIs('kelas.trash') ? 'active' : '' }}" href="{{ route('kelas.index') }}">
                                <i class="bi bi-grid-3x3"></i> Kelas
                            </a>
                        </li>
                        @endif
                        @if(Auth::user()->hasRole(['admin_universitas', 'admin_fakultas', 'admin_prodi', 'dosen']))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('krs.*') ? 'active' : '' }}" href="{{ route('krs.index') }}">
                                <i class="bi bi-card-checklist"></i> KRS
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('nilai.*') ? 'active' : '' }}" href="{{ route('nilai.index') }}">
                                <i class="bi bi-award"></i> Nilai
                            </a>
                        </li>
                        @endif
                    </ul>
                </div>
            </div>
            @endif

            <!-- PEMBAYARAN Section -->
            @if(!Auth::user()->hasRole(['mahasiswa']))
            <div class="menu-section">
                <div class="menu-header collapsed" data-bs-toggle="collapse" data-bs-target="#menuPembayaran">
                    <div class="menu-title">
                        <i class="bi bi-cash-coin"></i>
                        <span>Pembayaran</span>
                    </div>
                    <i class="bi bi-chevron-down menu-icon"></i>
                </div>
                <div class="collapse" id="menuPembayaran">
                    <ul class="nav flex-column menu-collapse">
                        @if(Auth::user()->hasRole(['super_admin', 'admin_universitas', 'admin_fakultas', 'admin_prodi']))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('tagihan-mahasiswa.*') ? 'active' : '' }}" href="{{ route('tagihan-mahasiswa.index') }}">
                                <i class="bi bi-receipt"></i> Tagihan Mahasiswa
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('pembayaran-mahasiswa.*') ? 'active' : '' }}" href="{{ route('pembayaran-mahasiswa.index') }}">
                                <i class="bi bi-credit-card-2-front"></i> Pembayaran Mahasiswa
                            </a>
                        </li>
                        @endif
                    </ul>
                </div>
            </div>
            @endif

            <!-- RENCANA KERJA TAHUNAN Section -->
            @if(Auth::user()->hasRole(['super_admin', 'admin_universitas', 'admin_fakultas', 'admin_prodi']))
            <div class="menu-section">
                <div class="menu-header collapsed" data-bs-toggle="collapse" data-bs-target="#menuRKT">
                    <div class="menu-title">
                        <i class="bi bi-clipboard-data"></i>
                        <span>Rencana Kerja Tahunan</span>
                    </div>
                    <i class="bi bi-chevron-down menu-icon"></i>
                </div>
                <div class="collapse" id="menuRKT">
                    <ul class="nav flex-column menu-collapse">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('rencana-kerja-tahunan.*') ? 'active' : '' }}" href="{{ route('rencana-kerja-tahunan.index') }}">
                                <i class="bi bi-journal-text"></i> Daftar RKT
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('rencana-kerja-tahunan.create') ? 'active' : '' }}" href="{{ route('rencana-kerja-tahunan.create') }}">
                                <i class="bi bi-plus-circle"></i> Tambah RKT
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            @endif

            @if(Auth::user()->hasRole(['super_admin']))
            <!-- RECYCLE BIN Section -->
            <div class="menu-section">
                <div class="menu-header collapsed" data-bs-toggle="collapse" data-bs-target="#menuTrash">
                    <div class="menu-title">
                        <i class="bi bi-trash"></i>
                        <span>Recycle Bin</span>
                    </div>
                    <i class="bi bi-chevron-down menu-icon"></i>
                </div>
                <div class="collapse" id="menuTrash">
                    <ul class="nav flex-column menu-collapse">
                        @if(Auth::user()->hasRole(['super_admin', 'admin_universitas']))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('universities.trash') ? 'active' : '' }}" href="{{ route('universities.trash') }}">
                                <i class="bi bi-bank"></i> Universitas
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('akreditasi-universitas.trash') ? 'active' : '' }}" href="{{ route('akreditasi-universitas.trash') }}">
                                <i class="bi bi-award"></i> Akreditasi Universitas
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('akreditasi-fakultas.trash') ? 'active' : '' }}" href="{{ route('akreditasi-fakultas.trash') }}">
                                <i class="bi bi-patch-check"></i> Akreditasi Fakultas
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('akreditasi-program-studi.trash') ? 'active' : '' }}" href="{{ route('akreditasi-program-studi.trash') }}">
                                <i class="bi bi-trophy"></i> Akreditasi Program Studi
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('users.trash') ? 'active' : '' }}" href="{{ route('users.trash') }}">
                                <i class="bi bi-people"></i> User
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('tahun-akademik.trash') ? 'active' : '' }}" href="{{ route('tahun-akademik.trash') }}">
                                <i class="bi bi-calendar-range"></i> Tahun Akademik
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('semester.trash') ? 'active' : '' }}" href="{{ route('semester.trash') }}">
                                <i class="bi bi-calendar2-week"></i> Semester
                            </a>
                        </li>
                        @endif
                        @if(Auth::user()->hasRole(['super_admin']))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('provinsi.trash') || request()->routeIs('provinsi.restore') || request()->routeIs('provinsi.force-delete') ? 'active' : '' }}" href="{{ route('provinsi.trash') }}">
                                <i class="bi bi-geo-alt"></i> Provinsi
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('regency.trash') || request()->routeIs('regency.restore') || request()->routeIs('regency.force-delete') ? 'active' : '' }}" href="{{ route('regency.trash') }}">
                                <i class="bi bi-geo"></i> Kabupaten/Kota
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('sub-regency.trash') || request()->routeIs('sub-regency.restore') || request()->routeIs('sub-regency.force-delete') ? 'active' : '' }}" href="{{ route('sub-regency.trash') }}">
                                <i class="bi bi-signpost"></i> Kecamatan
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('village.trash') || request()->routeIs('village.restore') || request()->routeIs('village.force-delete') ? 'active' : '' }}" href="{{ route('village.trash') }}">
                                <i class="bi bi-geo-alt"></i> Desa/Kelurahan
                            </a>
                        </li>
                        @endif
                        @if(Auth::user()->hasRole(['super_admin', 'admin_universitas', 'admin_fakultas']))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('fakultas.trash') ? 'active' : '' }}" href="{{ route('fakultas.trash') }}">
                                <i class="bi bi-building"></i> Fakultas
                            </a>
                        </li>
                        @endif
                        @if(Auth::user()->hasRole(['super_admin', 'admin_universitas', 'admin_fakultas', 'admin_prodi']))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('program-studi.trash') ? 'active' : '' }}" href="{{ route('program-studi.trash') }}">
                                <i class="bi bi-bookshelf"></i> Program Studi
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('dosen.trash') ? 'active' : '' }}" href="{{ route('dosen.trash') }}">
                                <i class="bi bi-person-badge"></i> Dosen
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('mata-kuliah.trash') ? 'active' : '' }}" href="{{ route('mata-kuliah.trash') }}">
                                <i class="bi bi-book"></i> Mata Kuliah
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('ruang.trash') ? 'active' : '' }}" href="{{ route('ruang.trash') }}">
                                <i class="bi bi-door-open"></i> Ruang
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('jadwal-kuliah.trash') ? 'active' : '' }}" href="{{ route('jadwal-kuliah.trash') }}">
                                <i class="bi bi-calendar3"></i> Jadwal Kuliah
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('kelas.trash') ? 'active' : '' }}" href="{{ route('kelas.trash') }}">
                                <i class="bi bi-grid-3x3"></i> Kelas
                            </a>
                        </li>
                        @endif
                    </ul>
                </div>
            </div>
            @endif

            @if(Auth::user()->hasRole(['super_admin', 'admin_universitas', 'admin_fakultas', 'admin_prodi']))
            <!-- LAPORAN Section -->
            <div class="menu-section">
                <div class="menu-header collapsed" data-bs-toggle="collapse" data-bs-target="#menuLaporan">
                    <div class="menu-title">
                        <i class="bi bi-file-earmark-bar-graph"></i>
                        <span>Laporan</span>
                    </div>
                    <i class="bi bi-chevron-down menu-icon"></i>
                </div>
                <div class="collapse" id="menuLaporan">
                    <ul class="nav flex-column menu-collapse">
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="bi bi-file-earmark-bar-graph"></i> Laporan
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            @endif

            @if(Auth::user()->hasRole(['mahasiswa']))
            <!-- PROFIL MAHASISWA Section -->
            <div class="menu-section">
                <div class="menu-header collapsed" data-bs-toggle="collapse" data-bs-target="#menuProfilMahasiswa">
                    <div class="menu-title">
                        <i class="bi bi-person-circle"></i>
                        <span>Profil Saya</span>
                    </div>
                    <i class="bi bi-chevron-down menu-icon"></i>
                </div>
                <div class="collapse" id="menuProfilMahasiswa">
                    <ul class="nav flex-column menu-collapse">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('profil-mahasiswa.index') || request()->routeIs('profil-mahasiswa.edit') ? 'active' : '' }}" href="{{ route('profil-mahasiswa.index') }}">
                                <i class="bi bi-person-lines-fill"></i> Biodata
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('profil-mahasiswa.edit-password') ? 'active' : '' }}" href="{{ route('profil-mahasiswa.edit-password') }}">
                                <i class="bi bi-lock"></i> Ubah Password
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            @endif

            @if(Auth::user()->hasRole(['super_admin']))
            <!-- PENGATURAN Section -->
            <div class="menu-section">
                <div class="menu-header collapsed" data-bs-toggle="collapse" data-bs-target="#menuPengaturan">
                    <div class="menu-title">
                        <i class="bi bi-gear"></i>
                        <span>Pengaturan</span>
                    </div>
                    <i class="bi bi-chevron-down menu-icon"></i>
                </div>
                <div class="collapse" id="menuPengaturan">
                    <ul class="nav flex-column menu-collapse">
                        @if(Auth::user()->hasRole(['super_admin', 'admin_universitas']))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('users.*') && !request()->routeIs('users.trash') ? 'active' : '' }}" href="{{ route('users.index') }}">
                                <i class="bi bi-people"></i> Pengguna
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="bi bi-shield-check"></i> Role & Permission
                            </a>
                        </li>
                        @endif
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="bi bi-gear"></i> Pengaturan Sistem
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="bi bi-clock-history"></i> Activity Log
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="bi bi-cloud-download"></i> Backup & Restore
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar Footer -->
        <div class="sidebar-footer text-center">
            <small class="text-white-50">© 2025 SISTER v1.0</small>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content" id="mainContent">
        <!-- Content Header -->
        <div class="content-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">@yield('header', 'Dashboard')</h1>
                    <small class="text-muted">@yield('breadcrumb', '')</small>
                </div>
                <div class="d-flex align-items-center">
                    <span class="badge bg-primary me-2">
                        <i class="bi bi-calendar-event"></i> {{ now()->format('d M Y') }}
                    </span>
                    <span class="badge bg-info">
                        <i class="bi bi-clock"></i> <span id="currentTime"></span>
                    </span>
                </div>
            </div>
        </div>

                <!-- Alert Messages -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

        <!-- Content -->
        @yield('content')
    </main>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Setup CSRF token for all AJAX requests -->
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>
    
    <script>
        $(document).ready(function() {
            // Sidebar Toggle
            function toggleSidebar() {
                $('#sidebar').toggleClass('hidden');
                $('#mainContent').toggleClass('expanded');
                $('.navbar.fixed-top').toggleClass('expanded');
                $('#sidebarToggle').toggleClass('moved');
                
                // Change icon for both buttons
                const isHidden = $('#sidebar').hasClass('hidden');
                const iconClass = isHidden ? 'bi-layout-sidebar-inset' : 'bi-list';
                const removeIconClass = isHidden ? 'bi-list' : 'bi-layout-sidebar-inset';
                
                $('#sidebarToggle i, #sidebarToggleTop i').removeClass(removeIconClass).addClass(iconClass);
            }
            
            $('#sidebarToggle, #sidebarToggleTop').click(function() {
                toggleSidebar();
            });

            // Update clock every second
            function updateClock() {
                const now = new Date();
                const hours = String(now.getHours()).padStart(2, '0');
                const minutes = String(now.getMinutes()).padStart(2, '0');
                const seconds = String(now.getSeconds()).padStart(2, '0');
                $('#currentTime').text(`${hours}:${minutes}:${seconds}`);
            }
            updateClock();
            setInterval(updateClock, 1000);

            // Mobile responsive - auto hide sidebar on small screens
            function checkWidth() {
                if ($(window).width() < 768) {
                    $('#sidebar').addClass('hidden');
                    $('#mainContent').addClass('expanded');
                    $('#sidebarToggle').addClass('moved');
                } else {
                    $('#sidebar').removeClass('hidden');
                    $('#mainContent').removeClass('expanded');
                    $('#sidebarToggle').removeClass('moved');
                }
            }
            
            checkWidth();
            $(window).resize(checkWidth);

            // Close sidebar when clicking outside on mobile
            $(document).click(function(event) {
                if ($(window).width() < 768) {
                    if (!$(event.target).closest('#sidebar, #sidebarToggle').length) {
                        if (!$('#sidebar').hasClass('hidden')) {
                            $('#sidebar').addClass('hidden');
                            $('#mainContent').addClass('expanded');
                            $('#sidebarToggle').addClass('moved');
                            $('#sidebarToggle i').removeClass('bi-layout-sidebar-inset').addClass('bi-list');
                        }
                    }
                }
            });
        });
    </script>
    
    @stack('scripts')
    @yield('scripts')
</body>
</html>
