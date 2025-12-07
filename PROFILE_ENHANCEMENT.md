# Profile Enhancement - Mahasiswa Information

## Tanggal: 7 Desember 2025

## Perubahan yang Dilakukan

### 1. ProfileController.php
**File**: `app/Http/Controllers/ProfileController.php`

**Perubahan**:
- ✅ Menambahkan logika untuk mendeteksi role user (Mahasiswa/Dosen/Admin)
- ✅ Jika user adalah mahasiswa, tampilkan:
  - Informasi akademik (NIM, Nama, Prodi, Fakultas, Semester, IPK, Status, Tahun Masuk)
  - Statistik semester aktif (Total MK, Total SKS, Semester & Tahun Ajaran aktif)
  - Mata kuliah yang diambil semester aktif dengan nilai
  - Jadwal kuliah semester aktif
- ✅ Jika user adalah dosen, tampilkan:
  - Informasi dosen (NIDN, Nama, Gelar, Fakultas, Email, Telepon)
- ✅ Jika user adalah admin, tampilkan info role saja

**Query yang Digunakan**:
```php
// Get semester aktif
$semesterAktif = Semester::where('is_active', true)
    ->where(function($q) use ($mahasiswa) {
        $q->whereNull('program_studi_id')
          ->orWhere('program_studi_id', $mahasiswa->program_studi_id);
    })
    ->first();

// Get KRS semester aktif dengan nilai
$krsList = Krs::where('mahasiswa_id', $mahasiswa->id)
    ->where('tahun_ajaran', $semesterAktif->tahunAkademik->tahun_ajaran)
    ->where('semester', $semesterAktif->nama_semester)
    ->where('status', 'Disetujui')
    ->with(['kelas.mataKuliah', 'kelas.dosen', 'nilai'])
    ->get();

// Get jadwal kuliah semester aktif
$jadwalData = JadwalKuliah::whereIn('kelas_id', $kelasIds)
    ->where('semester_id', $semesterAktif->id)
    ->with(['kelas.mataKuliah', 'kelas.dosen', 'ruang'])
    ->orderBy('hari')
    ->orderBy('jam_mulai')
    ->get();
```

### 2. resources/views/profile/edit.blade.php
**File**: `resources/views/profile/edit.blade.php`

**Perubahan**:
- ✅ Refactor layout menjadi 2 kolom:
  - **Left Column (col-lg-4)**: User Profile & Form Edit
  - **Right Column (col-lg-8)**: Academic Information
- ✅ Tampilkan informasi mahasiswa:
  - Card "Informasi Akademik" (NIM, Prodi, Fakultas, Semester, IPK, Status)
  - Card statistik semester aktif (3 card kecil: Total MK, Total SKS, Semester Aktif)
  - Tabel "Mata Kuliah & Nilai Semester Aktif" dengan badge warna sesuai grade
  - Tabel "Jadwal Kuliah Semester Aktif" sorted by hari
- ✅ Tampilkan informasi dosen jika user adalah dosen
- ✅ Tampilkan info umum jika user adalah admin
- ✅ Responsive design dengan Bootstrap 5

**Fitur UI**:
- Avatar user (foto atau initial)
- Badge role dengan warna
- Tabel mata kuliah dengan badge nilai berwarna:
  - A/A- = Green (bg-success)
  - B+/B/B- = Blue (bg-primary)
  - C+/C = Yellow (bg-warning)
  - D = Red (bg-danger)
  - E/Belum Ada = Gray (bg-secondary)
- Jadwal kuliah sorted by hari (Senin - Minggu)
- Button "Ganti Password" dan "Kembali ke Dashboard"

### 3. app/Models/User.php
**File**: `app/Models/User.php`

**Perubahan**:
- ✅ Update method `getRoleNames()` untuk menampilkan `display_name` instead of `name`
- Sebelum: `return collect([$this->role ? $this->role->name : 'User']);`
- Sesudah: `return collect([$this->role ? $this->role->display_name : 'User']);`
- Ini membuat tampilan role lebih user-friendly (e.g., "Mahasiswa" instead of "mahasiswa")

## Relasi Database yang Digunakan

### User Model
- `$user->mahasiswa_id` → Foreign key to mahasiswa table
- `$user->mahasiswa` → BelongsTo Mahasiswa
- `$user->dosen_id` → Foreign key to dosen table
- `$user->dosen` → BelongsTo Dosen
- `$user->role` → BelongsTo Role

### Mahasiswa Model
- `$mahasiswa->programStudi` → BelongsTo ProgramStudi
- `$mahasiswa->programStudi->fakultas` → Through ProgramStudi to Fakultas
- `$mahasiswa->krs` → HasMany Krs

### Krs Model
- `$krs->mahasiswa` → BelongsTo Mahasiswa
- `$krs->kelas` → BelongsTo Kelas
- `$krs->kelas->mataKuliah` → Through Kelas to MataKuliah
- `$krs->kelas->dosen` → Through Kelas to Dosen
- `$krs->nilai` → HasOne Nilai

### JadwalKuliah Model
- `$jadwal->kelas` → BelongsTo Kelas
- `$jadwal->semester` → BelongsTo Semester
- `$jadwal->tahunAkademik` → BelongsTo TahunAkademik
- `$jadwal->ruang` → BelongsTo Ruang

## Testing Checklist

### ✅ Test sebagai Mahasiswa
1. Login dengan akun mahasiswa
2. Akses `/profile` atau klik dropdown user → "Profil Saya"
3. Verifikasi tampilan:
   - ✅ Informasi Akademik (NIM, Prodi, Fakultas, Semester, IPK, Status)
   - ✅ Statistik Semester Aktif (Total MK, Total SKS, Semester Aktif)
   - ✅ Tabel Mata Kuliah & Nilai (jika sudah ada KRS yang disetujui)
   - ✅ Tabel Jadwal Kuliah (jika sudah ada jadwal)
   - ✅ Form Edit Profile (Nama, Email)
   - ✅ Button "Ganti Password", "Simpan Perubahan", "Kembali ke Dashboard"

### ✅ Test sebagai Dosen
1. Login dengan akun dosen
2. Akses `/profile`
3. Verifikasi tampilan:
   - ✅ Informasi Dosen (NIDN, Nama, Gelar, Fakultas, Email, Telepon)
   - ✅ Form Edit Profile
   - ✅ Button actions

### ✅ Test sebagai Admin
1. Login dengan akun admin (Super Admin/Admin Universitas/Admin Fakultas/Admin Prodi)
2. Akses `/profile`
3. Verifikasi tampilan:
   - ✅ Informasi Administrator (Role info)
   - ✅ Form Edit Profile
   - ✅ Button actions

### ✅ Test Edge Cases
- ✅ Mahasiswa yang belum isi KRS → Tampilkan pesan "Belum ada data mata kuliah..."
- ✅ Mahasiswa di semester yang tidak ada jadwal → Tidak error, tampilkan tabel kosong
- ✅ User tanpa mahasiswa_id atau dosen_id → Tampilkan sebagai admin
- ✅ Semester aktif tidak ada → Tidak error, skip data akademik

## Fitur yang Ditampilkan per Role

### Mahasiswa
- ✅ Informasi Akademik Lengkap
- ✅ Mata Kuliah & Nilai Semester Aktif
- ✅ Jadwal Kuliah Semester Aktif
- ✅ Statistik (Total MK, Total SKS)
- ✅ IPK
- ✅ Status Mahasiswa

### Dosen
- ✅ Informasi Dosen (NIDN, Nama, Gelar, Fakultas)
- ✅ Kontak (Email, Telepon)

### Admin (Super Admin, Admin Universitas, Admin Fakultas, Admin Prodi)
- ✅ Informasi Role
- ✅ Pesan informatif

## Perbaikan Compatibility

### Issues Fixed
1. ✅ **getRoleNames() Display Name**: Ubah dari `role->name` ke `role->display_name` untuk tampilan yang lebih user-friendly
2. ✅ **Relasi User-Mahasiswa**: Pastikan menggunakan `$user->mahasiswa_id` dan `$user->mahasiswa` (sudah benar di semua controller)
3. ✅ **Eager Loading**: Menggunakan `with()` untuk optimize query dan menghindari N+1 problem
4. ✅ **Null Safety**: Menggunakan null coalescing operator (`??`) untuk handle data yang belum ada

## Routes yang Digunakan

```php
// Profile routes (sudah ada di routes/web.php)
Route::prefix('profile')->name('profile.')->group(function () {
    Route::get('/', [ProfileController::class, 'edit'])->name('edit');
    Route::put('/', [ProfileController::class, 'update'])->name('update');
    Route::get('/password', [ProfileController::class, 'editPassword'])->name('edit-password');
    Route::put('/password', [ProfileController::class, 'updatePassword'])->name('update-password');
});
```

## Cache Cleared

```bash
php artisan config:clear
php artisan view:clear
```

## Dependencies

Tidak ada dependency baru yang ditambahkan. Menggunakan:
- Laravel 12 Eloquent ORM
- Blade Templates
- Bootstrap 5.3
- Bootstrap Icons

## Kompatibilitas dengan Modul Lain

### ✅ Tidak Mengganggu Modul Lain
- ✅ KRS Module: Menggunakan relasi yang sudah ada, tidak ada perubahan
- ✅ Nilai Module: Menggunakan relasi yang sudah ada
- ✅ Jadwal Kuliah Module: Menggunakan relasi yang sudah ada
- ✅ Dashboard Mahasiswa: Masih berfungsi normal
- ✅ Profil Mahasiswa (`/profil-mahasiswa`): Modul terpisah, tidak terpengaruh
- ✅ User Management: Tidak ada perubahan

### Data yang Ditampilkan
- **Semester Aktif**: Berdasarkan `Semester::where('is_active', true)`
- **KRS**: Hanya KRS dengan `status = 'Disetujui'`
- **Nilai**: Diambil dari relasi `krs->nilai`
- **Jadwal**: Berdasarkan `semester_id` yang aktif

## Catatan Penting

1. **Profile vs Profil Mahasiswa**:
   - `/profile`: Untuk semua role (General profile)
   - `/profil-mahasiswa`: Khusus mahasiswa (Detail lengkap dengan edit data pribadi, alamat, dll)

2. **Semester Aktif**:
   - Sistem otomatis detect semester aktif dari tabel `semester` where `is_active = true`
   - Jika tidak ada semester aktif, data akademik tidak ditampilkan

3. **Performance**:
   - Menggunakan eager loading untuk optimize query
   - Single query untuk KRS dengan relasi (mataKuliah, dosen, nilai)
   - Single query untuk jadwal dengan relasi

4. **UI/UX**:
   - Responsive layout (mobile-friendly)
   - Badge warna untuk nilai (visual feedback)
   - Sorted jadwal by hari (Senin - Minggu)
   - Clear separation antara profile form dan academic info

## Screenshot Lokasi

- **URL**: http://127.0.0.1:8000/profile
- **Menu Access**: Klik dropdown user di top-right navbar → "Profil Saya"

---

## Status: ✅ COMPLETED

Fitur profile mahasiswa berhasil diperbaiki dan dilengkapi dengan informasi akademik lengkap tanpa mengganggu modul lain yang sudah ada.
