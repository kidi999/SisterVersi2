
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FakultasController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\DosenController;
use App\Http\Controllers\RegionController;
use App\Http\Controllers\FileUploadController;
use App\Http\Controllers\ProvinceController;
use App\Http\Controllers\PendaftaranMahasiswaController;
use App\Http\Controllers\RegencyController;
use App\Http\Controllers\SubRegencyController;
use App\Http\Controllers\VillageController;
use App\Http\Controllers\UniversityController;
use App\Http\Controllers\ProgramStudiController;
use App\Http\Controllers\AkreditasiUniversitasController;
use App\Http\Controllers\AkreditasiFakultasController;
use App\Http\Controllers\AkreditasiProgramStudiController;
use App\Http\Controllers\MataKuliahController;
use App\Http\Controllers\RuangController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\JadwalKuliahController;
use App\Http\Controllers\KrsController;
use App\Http\Controllers\NilaiController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TahunAkademikController;
use App\Http\Controllers\SemesterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\PmbController;
use App\Http\Controllers\TagihanMahasiswaController;
use App\Http\Controllers\PembayaranMahasiswaController;


// Public Routes
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// PMB Public Routes (Tidak perlu login)
Route::prefix('pmb')->name('pmb.')->group(function () {
    Route::get('/', [PmbController::class, 'index'])->name('index');
    Route::get('/daftar', [PmbController::class, 'create'])->name('create');
    Route::post('/daftar', [PmbController::class, 'store'])->name('store');
    Route::get('/success/{id}', [PmbController::class, 'success'])->name('success');
    Route::match(['get', 'post'], '/cek-status', [PmbController::class, 'checkStatus'])->name('check-status');
    Route::get('/verify-email/{token}', [PmbController::class, 'verifyEmail'])->name('verify-email');
    Route::post('/resend-verification', [PmbController::class, 'resendVerification'])->name('resend-verification');
});

// Public API Routes for Region Data (for PMB form)
Route::prefix('api')->name('api.')->group(function () {
    Route::get('regencies/{province}', [RegionController::class, 'getRegencies'])->name('regencies');
    Route::get('sub-regencies/{regency}', [RegionController::class, 'getSubRegencies'])->name('subregencies');
    Route::get('villages/{subRegency}', [RegionController::class, 'getVillages'])->name('villages');
});

// Authentication Routes
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

// Google OAuth Routes
Route::get('auth/google', [App\Http\Controllers\Auth\GoogleController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('auth/google/callback', [App\Http\Controllers\Auth\GoogleController::class, 'handleGoogleCallback']);

// Protected Routes - Requires Authentication
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Routes untuk Fakultas - Admin roles only
    Route::middleware(['role:super_admin,admin_universitas,admin_fakultas'])->group(function () {
        Route::resource('fakultas', FakultasController::class)->parameters([
            'fakultas' => 'fakultas'
        ]);
        
        // Trash routes - Only for super_admin
        Route::get('fakultas-trash', [FakultasController::class, 'trash'])->name('fakultas.trash');
        Route::post('fakultas/{id}/restore', [FakultasController::class, 'restore'])->name('fakultas.restore');
        Route::delete('fakultas/{id}/force-delete', [FakultasController::class, 'forceDelete'])->name('fakultas.force-delete');
    });

    // Routes untuk Mahasiswa - Admin and Dosen can access
    Route::middleware(['role:super_admin,admin_universitas,admin_fakultas,admin_prodi,dosen'])->group(function () {
        Route::resource('mahasiswa', MahasiswaController::class);
        Route::post('mahasiswa/{mahasiswa}/generate-user', [MahasiswaController::class, 'generateUser'])->name('mahasiswa.generate-user');
    });

    // Routes untuk Dosen - Admin roles only
    Route::middleware(['role:super_admin,admin_universitas,admin_fakultas,admin_prodi'])->group(function () {
        Route::resource('dosen', DosenController::class);
        Route::get('dosen-trash', [DosenController::class, 'trash'])->name('dosen.trash');
        Route::post('dosen/{id}/restore', [DosenController::class, 'restore'])->name('dosen.restore');
        Route::delete('dosen/{id}/force-delete', [DosenController::class, 'forceDelete'])->name('dosen.force-delete');
    });

    // Routes untuk Mata Kuliah - Admin roles and Dosen can access
    Route::middleware(['role:super_admin,admin_universitas,admin_fakultas,admin_prodi,dosen'])->group(function () {
        Route::resource('mata-kuliah', MataKuliahController::class)->parameters([
            'mata-kuliah' => 'mataKuliah'
        ]);
        Route::get('mata-kuliah-trash', [MataKuliahController::class, 'trash'])->name('mata-kuliah.trash');
        Route::post('mata-kuliah/{id}/restore', [MataKuliahController::class, 'restore'])->name('mata-kuliah.restore');
        Route::delete('mata-kuliah/{id}/force-delete', [MataKuliahController::class, 'forceDelete'])->name('mata-kuliah.force-delete');
    });

    // Routes untuk Ruang - Admin roles only
    Route::middleware(['role:super_admin,admin_universitas,admin_fakultas,admin_prodi'])->group(function () {
        Route::resource('ruang', RuangController::class);
        Route::get('ruang-trash', [RuangController::class, 'trash'])->name('ruang.trash');
        Route::post('ruang/{id}/restore', [RuangController::class, 'restore'])->name('ruang.restore');
        Route::delete('ruang/{id}/force-delete', [RuangController::class, 'forceDelete'])->name('ruang.force-delete');
        Route::get('api/prodi-by-fakultas/{fakultasId}', [RuangController::class, 'getProdiByFakultas'])->name('api.prodi-by-fakultas');
    });

    // Routes untuk Kelas - Admin roles only
    Route::middleware(['role:super_admin,admin_universitas,admin_fakultas,admin_prodi'])->group(function () {
        Route::resource('kelas', KelasController::class)->parameters([
            'kelas' => 'kela'
        ]);
        Route::get('kelas-trash', [KelasController::class, 'trash'])->name('kelas.trash');
        Route::post('kelas/{id}/restore', [KelasController::class, 'restore'])->name('kelas.restore');
        Route::delete('kelas/{id}/force-delete', [KelasController::class, 'forceDelete'])->name('kelas.force-delete');
    });

    // Routes untuk KRS - All roles except super_admin
    Route::middleware(['role:admin_universitas,admin_fakultas,admin_prodi,dosen,mahasiswa'])->group(function () {
        Route::resource('krs', KrsController::class)->parameters([
            'krs' => 'kr'
        ]);
        Route::put('krs/{id}/approve', [KrsController::class, 'approve'])->name('krs.approve');
        Route::put('krs/{id}/reject', [KrsController::class, 'reject'])->name('krs.reject');
        Route::get('krs/print/{mahasiswaId}/{tahunAjaran}/{semester}', [KrsController::class, 'print'])->name('krs.print');
    });

    // Routes untuk Nilai - All roles except super_admin
    Route::middleware(['role:admin_universitas,admin_fakultas,admin_prodi,dosen,mahasiswa'])->group(function () {
        Route::resource('nilai', NilaiController::class);
        Route::get('nilai/khs/{mahasiswaId}/{tahunAjaran}/{semester}', [NilaiController::class, 'khs'])->name('nilai.khs');
        Route::get('nilai/transkrip/{mahasiswaId}', [NilaiController::class, 'transkrip'])->name('nilai.transkrip');
        Route::get('nilai/batch/{kelasId}', [NilaiController::class, 'batch'])->name('nilai.batch');
        Route::post('nilai/batch/{kelasId}', [NilaiController::class, 'storeBatch'])->name('nilai.storeBatch');
    });

    // Routes untuk Jadwal Kuliah - Admin roles, Dosen, and Mahasiswa can view
    Route::middleware(['role:super_admin,admin_universitas,admin_fakultas,admin_prodi,dosen,mahasiswa'])->group(function () {
        Route::get('jadwal-kuliah', [JadwalKuliahController::class, 'index'])->name('jadwal-kuliah.index');
        Route::get('jadwal-kuliah/{jadwalKuliah}', [JadwalKuliahController::class, 'show'])->name('jadwal-kuliah.show');
    });
    
    // Routes untuk Jadwal Kuliah - Admin roles and Dosen only (CRUD)
    Route::middleware(['role:super_admin,admin_universitas,admin_fakultas,admin_prodi,dosen'])->group(function () {
        Route::get('jadwal-kuliah/create', [JadwalKuliahController::class, 'create'])->name('jadwal-kuliah.create');
        Route::post('jadwal-kuliah', [JadwalKuliahController::class, 'store'])->name('jadwal-kuliah.store');
        Route::get('jadwal-kuliah/{jadwalKuliah}/edit', [JadwalKuliahController::class, 'edit'])->name('jadwal-kuliah.edit');
        Route::put('jadwal-kuliah/{jadwalKuliah}', [JadwalKuliahController::class, 'update'])->name('jadwal-kuliah.update');
        Route::delete('jadwal-kuliah/{jadwalKuliah}', [JadwalKuliahController::class, 'destroy'])->name('jadwal-kuliah.destroy');
        Route::get('jadwal-kuliah-trash', [JadwalKuliahController::class, 'trash'])->name('jadwal-kuliah.trash');
        Route::post('jadwal-kuliah/{id}/restore', [JadwalKuliahController::class, 'restore'])->name('jadwal-kuliah.restore');
        Route::delete('jadwal-kuliah/{id}/force-delete', [JadwalKuliahController::class, 'forceDelete'])->name('jadwal-kuliah.force-delete');
        Route::get('api/available-ruang', [JadwalKuliahController::class, 'getAvailableRuang'])->name('api.available-ruang');
    });


    // (Dihapus) Routes untuk Pertemuan Kuliah - Admin roles and Dosen





    // Routes untuk Pendaftaran Mahasiswa - Admin roles only
    Route::middleware(['role:super_admin,admin_universitas,admin_fakultas,admin_prodi'])->group(function () {
        Route::resource('pendaftaran-mahasiswa', PendaftaranMahasiswaController::class)->parameters([
            'pendaftaran-mahasiswa' => 'pendaftaranMahasiswa'
        ]);
        Route::post('pendaftaran-mahasiswa/{pendaftaranMahasiswa}/verifikasi', [PendaftaranMahasiswaController::class, 'verifikasi'])->name('pendaftaran-mahasiswa.verifikasi');
        Route::post('pendaftaran-mahasiswa/{pendaftaranMahasiswa}/export', [PendaftaranMahasiswaController::class, 'exportToMahasiswa'])->name('pendaftaran-mahasiswa.export');
        Route::get('pendaftaran-mahasiswa-trash', [PendaftaranMahasiswaController::class, 'trash'])->name('pendaftaran-mahasiswa-trash');
        Route::post('pendaftaran-mahasiswa/{id}/restore', [PendaftaranMahasiswaController::class, 'restore'])->name('pendaftaran-mahasiswa.restore');
        Route::delete('pendaftaran-mahasiswa/{id}/force-delete', [PendaftaranMahasiswaController::class, 'forceDelete'])->name('pendaftaran-mahasiswa.force-delete');
    });

    // Routes untuk Program Studi - Admin roles only
    Route::middleware(['role:super_admin,admin_universitas,admin_fakultas,admin_prodi'])->group(function () {
        Route::resource('program-studi', ProgramStudiController::class)->parameters([
            'program-studi' => 'programStudi'
        ]);
        Route::get('program-studi-trash', [ProgramStudiController::class, 'trash'])->name('program-studi.trash');
        Route::patch('program-studi/{id}/restore', [ProgramStudiController::class, 'restore'])->name('program-studi.restore');
        Route::delete('program-studi/{id}/force-delete', [ProgramStudiController::class, 'forceDelete'])->name('program-studi.force-delete');
    });

    // Routes untuk Akreditasi Universitas - Admin roles only
    Route::middleware(['role:super_admin,admin_universitas'])->group(function () {
        Route::resource('akreditasi-universitas', AkreditasiUniversitasController::class)->parameters([
            'akreditasi-universitas' => 'akreditasiUniversita'
        ]);
        Route::get('akreditasi-universitas-trash', [AkreditasiUniversitasController::class, 'trash'])->name('akreditasi-universitas.trash');
        Route::post('akreditasi-universitas/{id}/restore', [AkreditasiUniversitasController::class, 'restore'])->name('akreditasi-universitas.restore');
        Route::delete('akreditasi-universitas/{id}/force-delete', [AkreditasiUniversitasController::class, 'forceDelete'])->name('akreditasi-universitas.force-delete');
    });

    // Routes untuk Akreditasi Fakultas - Admin roles only
    Route::middleware(['role:super_admin,admin_universitas,admin_fakultas'])->group(function () {
        Route::resource('akreditasi-fakultas', AkreditasiFakultasController::class)->parameters([
            'akreditasi-fakultas' => 'akreditasiFakulta'
        ]);
        Route::get('akreditasi-fakultas-trash', [AkreditasiFakultasController::class, 'trash'])->name('akreditasi-fakultas.trash');
        Route::post('akreditasi-fakultas/{id}/restore', [AkreditasiFakultasController::class, 'restore'])->name('akreditasi-fakultas.restore');
        Route::delete('akreditasi-fakultas/{id}/force-delete', [AkreditasiFakultasController::class, 'forceDelete'])->name('akreditasi-fakultas.force-delete');
    });

    // Routes untuk Akreditasi Program Studi - Admin roles only
    Route::middleware(['role:super_admin,admin_universitas,admin_fakultas,admin_prodi'])->group(function () {
        Route::resource('akreditasi-program-studi', AkreditasiProgramStudiController::class)->parameters([
            'akreditasi-program-studi' => 'akreditasiProgramStudi'
        ]);
        Route::get('akreditasi-program-studi-trash', [AkreditasiProgramStudiController::class, 'trash'])->name('akreditasi-program-studi.trash');
        Route::post('akreditasi-program-studi/{id}/restore', [AkreditasiProgramStudiController::class, 'restore'])->name('akreditasi-program-studi.restore');
        Route::delete('akreditasi-program-studi/{id}/force-delete', [AkreditasiProgramStudiController::class, 'forceDelete'])->name('akreditasi-program-studi.force-delete');
    });

    // Routes untuk Wilayah - Super Admin only
    Route::middleware(['role:super_admin'])->group(function () {
        // Provinsi
        Route::resource('provinsi', ProvinceController::class)->parameters([
            'provinsi' => 'province'
        ]);
        Route::get('provinsi-trash', [ProvinceController::class, 'trash'])->name('provinsi.trash');
        Route::post('provinsi/{id}/restore', [ProvinceController::class, 'restore'])->name('provinsi.restore');
        Route::delete('provinsi/{id}/force-delete', [ProvinceController::class, 'forceDelete'])->name('provinsi.force-delete');

        // Kabupaten/Kota
        Route::resource('regency', RegencyController::class)->parameters([
            'regency' => 'regency'
        ]);
        Route::get('regency-trash', [RegencyController::class, 'trash'])->name('regency.trash');
        Route::post('regency/{id}/restore', [RegencyController::class, 'restore'])->name('regency.restore');
        Route::delete('regency/{id}/force-delete', [RegencyController::class, 'forceDelete'])->name('regency.force-delete');

        // Kecamatan
        Route::resource('sub-regency', SubRegencyController::class)->parameters([
            'sub_regency' => 'subRegency'
        ]);
        Route::get('sub-regency-trash', [SubRegencyController::class, 'trash'])->name('sub-regency.trash');
        Route::patch('sub-regency/{id}/restore', [SubRegencyController::class, 'restore'])->name('sub-regency.restore');
        Route::delete('sub-regency/{id}/force-delete', [SubRegencyController::class, 'forceDelete'])->name('sub-regency.force-delete');
        Route::get('regencies-by-province/{provinceId}', [SubRegencyController::class, 'getRegenciesByProvince'])->name('regencies-by-province');

        // Desa/Kelurahan
        Route::resource('village', VillageController::class)->parameters([
            'village' => 'village'
        ]);
        Route::get('village-trash', [VillageController::class, 'trash'])->name('village.trash');
        Route::patch('village/{id}/restore', [VillageController::class, 'restore'])->name('village.restore');
        Route::delete('village/{id}/force-delete', [VillageController::class, 'forceDelete'])->name('village.force-delete');
        Route::get('sub-regencies-by-regency/{regencyId}', [VillageController::class, 'getSubRegenciesByRegency'])->name('sub-regencies-by-regency');

        // Tahun Akademik
        Route::resource('tahun-akademik', TahunAkademikController::class);
        Route::get('tahun-akademik-trash', [TahunAkademikController::class, 'trash'])->name('tahun-akademik.trash');
        Route::patch('tahun-akademik/{id}/restore', [TahunAkademikController::class, 'restore'])->name('tahun-akademik.restore');
        Route::delete('tahun-akademik/{id}/force-delete', [TahunAkademikController::class, 'forceDelete'])->name('tahun-akademik.force-delete');
        Route::post('tahun-akademik/{tahunAkademik}/toggle-active', [TahunAkademikController::class, 'toggleActive'])->name('tahun-akademik.toggle-active');

        // Semester
        Route::resource('semester', SemesterController::class);
        Route::get('semester-trash', [SemesterController::class, 'trash'])->name('semester.trash');
        Route::patch('semester/{id}/restore', [SemesterController::class, 'restore'])->name('semester.restore');
        Route::delete('semester/{id}/force-delete', [SemesterController::class, 'forceDelete'])->name('semester.force-delete');
        Route::post('semester/{semester}/toggle-active', [SemesterController::class, 'toggleActive'])->name('semester.toggle-active');
    });

    // Routes untuk University - Super Admin and Admin Universitas
    Route::middleware(['role:super_admin,admin_universitas'])->group(function () {
        Route::resource('universities', UniversityController::class);
        Route::get('universities-trash', [UniversityController::class, 'trash'])->name('universities.trash');
        Route::patch('universities/{id}/restore', [UniversityController::class, 'restore'])->name('universities.restore');
        Route::delete('universities/{id}/force-delete', [UniversityController::class, 'forceDelete'])->name('universities.force-delete');
    });

    // Public University Profile - All authenticated users
    Route::get('university-profile', [UniversityController::class, 'profile'])->name('university.profile');

    // Routes untuk Tagihan Mahasiswa - Admin roles only
    Route::middleware(['role:super_admin,admin_universitas,admin_fakultas,admin_prodi'])->group(function () {
        Route::resource('tagihan-mahasiswa', TagihanMahasiswaController::class)->parameters([
            'tagihan-mahasiswa' => 'tagihanMahasiswa'
        ]);
        Route::get('tagihan-mahasiswa/batch/create', [TagihanMahasiswaController::class, 'batchCreate'])->name('tagihan-mahasiswa.batch-create');
        Route::post('tagihan-mahasiswa/batch/store', [TagihanMahasiswaController::class, 'batchStore'])->name('tagihan-mahasiswa.batch-store');
    });

    // Routes untuk Pembayaran Mahasiswa - Admin roles
    Route::middleware(['role:super_admin,admin_universitas,admin_fakultas,admin_prodi'])->group(function () {
        Route::resource('pembayaran-mahasiswa', PembayaranMahasiswaController::class)->only(['index', 'create', 'store', 'show']);
        Route::post('pembayaran-mahasiswa/{pembayaranMahasiswa}/verify', [PembayaranMahasiswaController::class, 'verify'])->name('pembayaran-mahasiswa.verify');
        Route::post('pembayaran-mahasiswa/{pembayaranMahasiswa}/reject', [PembayaranMahasiswaController::class, 'reject'])->name('pembayaran-mahasiswa.reject');
    });

    // Routes untuk Pembayaran Mahasiswa - Mahasiswa only
    Route::middleware(['role:mahasiswa'])->prefix('my-payments')->name('my-payments.')->group(function () {
        Route::get('/', [PembayaranMahasiswaController::class, 'myPayments'])->name('index');
        Route::post('/upload-bukti', [PembayaranMahasiswaController::class, 'uploadBukti'])->name('upload-bukti');
    });

    // Routes untuk Profil Mahasiswa - Mahasiswa only
    Route::middleware(['role:mahasiswa'])->prefix('profil-mahasiswa')->name('profil-mahasiswa.')->group(function () {
        Route::get('/', [\App\Http\Controllers\ProfilMahasiswaController::class, 'index'])->name('index');
        Route::get('/edit', [\App\Http\Controllers\ProfilMahasiswaController::class, 'edit'])->name('edit');
        Route::put('/update', [\App\Http\Controllers\ProfilMahasiswaController::class, 'update'])->name('update');
        Route::get('/change-password', [\App\Http\Controllers\ProfilMahasiswaController::class, 'editPassword'])->name('edit-password');
        Route::put('/change-password', [\App\Http\Controllers\ProfilMahasiswaController::class, 'updatePassword'])->name('update-password');
        
        // AJAX routes untuk wilayah
        Route::get('/regencies/{provinsi}', [\App\Http\Controllers\ProfilMahasiswaController::class, 'getRegencies'])->name('regencies');
        Route::get('/sub-regencies/{regency}', [\App\Http\Controllers\ProfilMahasiswaController::class, 'getSubRegencies'])->name('sub-regencies');
        Route::get('/villages/{subRegency}', [\App\Http\Controllers\ProfilMahasiswaController::class, 'getVillages'])->name('villages');
    });

    // Routes untuk User Management - Super Admin and Admin Universitas
    Route::middleware(['role:super_admin,admin_universitas'])->group(function () {
        Route::resource('users', UserController::class);
        Route::get('users-trash', [UserController::class, 'trash'])->name('users.trash');
        Route::patch('users/{id}/restore', [UserController::class, 'restore'])->name('users.restore');
        Route::delete('users/{id}/force-delete', [UserController::class, 'forceDelete'])->name('users.force-delete');
        Route::post('users/{user}/toggle-active', [UserController::class, 'toggleActive'])->name('users.toggle-active');
        Route::get('users/program-studi/{fakultasId}', [UserController::class, 'getProgramStudi'])->name('users.program-studi');
    });

    // Routes untuk Region (AJAX) - Removed from here, moved to public routes above for PMB form access

    // Routes untuk File Upload (AJAX) - Available to all authenticated users
    Route::post('api/file-upload', [FileUploadController::class, 'upload'])->name('api.file-upload.upload');
    Route::delete('api/file-upload/{id}', [FileUploadController::class, 'destroy'])->name('api.file-upload.destroy');
    Route::get('api/file-upload/{id}/download', [FileUploadController::class, 'download'])->name('api.file-upload.download');
    Route::get('api/file-upload/get-files', [FileUploadController::class, 'getFiles'])->name('api.file-upload.getFiles');

    // Routes untuk Rencana Kerja Tahunan - Admin roles only
    Route::middleware(['role:super_admin,admin_universitas,admin_fakultas,admin_prodi'])->group(function () {
        Route::resource('rencana-kerja-tahunan', \App\Http\Controllers\RencanaKerjaTahunanController::class);
        Route::post('rencana-kerja-tahunan/{id}/submit', [\App\Http\Controllers\RencanaKerjaTahunanController::class, 'submit'])->name('rencana-kerja-tahunan.submit');
        Route::post('rencana-kerja-tahunan/{id}/approve', [\App\Http\Controllers\RencanaKerjaTahunanController::class, 'approve'])->name('rencana-kerja-tahunan.approve');
        Route::post('rencana-kerja-tahunan/{id}/reject', [\App\Http\Controllers\RencanaKerjaTahunanController::class, 'reject'])->name('rencana-kerja-tahunan.reject');
    });

    // Routes untuk Profile - Available to all authenticated users
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('edit');
        Route::put('/', [\App\Http\Controllers\ProfileController::class, 'update'])->name('update');
        Route::get('/password', [\App\Http\Controllers\ProfileController::class, 'editPassword'])->name('edit-password');
        Route::put('/password', [\App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('update-password');
    });
});
