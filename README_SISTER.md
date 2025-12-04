# SISTER - Sistem Informasi Akademik Terintegrasi

Sistem Informasi Akademik berbasis web menggunakan Laravel 12, PHP 8.2, dan MySQL.

## Fitur Utama

- ✅ Manajemen Data Fakultas dan Program Studi
- ✅ Manajemen Data Mahasiswa
- ✅ Manajemen Data Dosen
- ✅ Manajemen Mata Kuliah dan Kelas
- ✅ Kartu Rencana Studi (KRS)
- ✅ Sistem Penilaian dan Transkrip
- ✅ Dashboard Statistik

## Teknologi yang Digunakan

- **Framework**: Laravel 12.x
- **PHP**: 8.2+
- **Database**: MySQL
- **Frontend**: Bootstrap 5.3, Bootstrap Icons
- **Server**: XAMPP (Apache + MySQL)

## Struktur Database

### Tabel Utama:
1. **fakultas** - Data fakultas
2. **program_studi** - Data program studi per fakultas
3. **mahasiswa** - Data mahasiswa
4. **dosen** - Data dosen
5. **mata_kuliah** - Data mata kuliah
6. **kelas** - Data kelas perkuliahan
7. **jadwal_kuliah** - Jadwal kuliah per kelas
8. **krs** - Kartu Rencana Studi mahasiswa
9. **nilai** - Nilai mahasiswa per mata kuliah

## Instalasi

Sistem sudah terinstall di `C:\xampp\htdocs\sister`

### Konfigurasi Database

1. Pastikan MySQL di XAMPP sudah running
2. Database `sister_db` sudah dibuat otomatis
3. Konfigurasi database ada di file `.env`:
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=sister_db
   DB_USERNAME=root
   DB_PASSWORD=
   ```

### Menjalankan Aplikasi

1. Buka terminal/command prompt
2. Masuk ke direktori project:
   ```bash
   cd C:\xampp\htdocs\sister
   ```

3. Jalankan development server:
   ```bash
   php artisan serve
   ```

4. Akses aplikasi di browser:
   ```
   http://localhost:8000
   ```

## Struktur Project

```
sister/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       ├── DashboardController.php
│   │       ├── FakultasController.php
│   │       ├── MahasiswaController.php
│   │       └── DosenController.php
│   └── Models/
│       ├── Fakultas.php
│       ├── ProgramStudi.php
│       ├── Mahasiswa.php
│       ├── Dosen.php
│       ├── MataKuliah.php
│       ├── Kelas.php
│       ├── JadwalKuliah.php
│       ├── Krs.php
│       └── Nilai.php
├── database/
│   ├── migrations/
│   └── seeders/
├── resources/
│   └── views/
│       ├── layouts/
│       │   └── app.blade.php
│       ├── dashboard.blade.php
│       ├── fakultas/
│       └── mahasiswa/
└── routes/
    └── web.php
```

## Endpoints API / Routes

### Dashboard
- `GET /` - Dashboard utama

### Fakultas
- `GET /fakultas` - List fakultas
- `GET /fakultas/create` - Form tambah fakultas
- `POST /fakultas` - Simpan fakultas baru
- `GET /fakultas/{id}` - Detail fakultas
- `GET /fakultas/{id}/edit` - Form edit fakultas
- `PUT /fakultas/{id}` - Update fakultas
- `DELETE /fakultas/{id}` - Hapus fakultas

### Mahasiswa
- `GET /mahasiswa` - List mahasiswa (dengan search & pagination)
- `GET /mahasiswa/create` - Form tambah mahasiswa
- `POST /mahasiswa` - Simpan mahasiswa baru
- `GET /mahasiswa/{id}` - Detail mahasiswa
- `GET /mahasiswa/{id}/edit` - Form edit mahasiswa
- `PUT /mahasiswa/{id}` - Update mahasiswa
- `DELETE /mahasiswa/{id}` - Hapus mahasiswa

### Dosen
- `GET /dosen` - List dosen
- `GET /dosen/create` - Form tambah dosen
- `POST /dosen` - Simpan dosen baru
- `GET /dosen/{id}` - Detail dosen
- `GET /dosen/{id}/edit` - Form edit dosen
- `PUT /dosen/{id}` - Update dosen
- `DELETE /dosen/{id}` - Hapus dosen

## Data Sample

Sistem sudah include data sample:
- 2 Fakultas (Teknik & Ekonomi)
- 4 Program Studi (TI, SI, Manajemen, Akuntansi)

## Fitur yang Dapat Dikembangkan

- [ ] Autentikasi & Authorization (Role: Admin, Dosen, Mahasiswa)
- [ ] Modul KRS Online
- [ ] Modul Input Nilai oleh Dosen
- [ ] Cetak KHS & Transkrip
- [ ] Jadwal Kuliah Online
- [ ] Absensi Mahasiswa
- [ ] Export Data (Excel, PDF)
- [ ] Notifikasi Email
- [ ] Dashboard Analytics lebih lengkap

## Troubleshooting

### Error: SQLSTATE[HY000] [1049] Unknown database
Pastikan database `sister_db` sudah dibuat. Jalankan:
```bash
php artisan migrate
```

### Port 8000 already in use
Gunakan port lain:
```bash
php artisan serve --port=8001
```

### Composer dependencies error
Install ulang dependencies:
```bash
composer install
```

## Lisensi

Project ini dibuat untuk keperluan pembelajaran.

## Kontak & Support

Untuk pertanyaan dan dukungan, silakan hubungi tim development.

---
© 2024 SISTER - Sistem Informasi Akademik Terintegrasi
