# DOKUMENTASI AKHIR — SISTER (Sistem Informasi Akademik Terintegrasi)

Tanggal: 13 Desember 2025

Dokumen ini merangkum status akhir pengembangan SISTER: fitur yang sudah selesai, fitur yang akan dibuat (backlog), cara menjalankan aplikasi, deployment checklist ringkas, dan lokasi test otomatis.

---

## 1) Ringkasan Sistem

SISTER adalah sistem informasi akademik berbasis web untuk mengelola data akademik universitas (fakultas, prodi, dosen, mahasiswa, perkuliahan, nilai, KRS, jadwal, pembayaran, PMB, dan lainnya).

**Teknologi**
- Backend: Laravel 12 (PHP 8.2)
- Database: MySQL
- UI: Blade + Bootstrap 5.3 + Bootstrap Icons
- PDF: DomPDF (barryvdh/laravel-dompdf)
- Auth: Email/Password + Google OAuth (SSO)

---

## 2) Status Pengembangan (Ringkas)

### A. Sudah Selesai Dibuat ✅

**Modul Akademik**
- Master Data: Fakultas, Program Studi, Mata Kuliah, Ruang, Tahun Akademik, Semester, Hari Libur
- Data Personil: Mahasiswa, Dosen
- Perkuliahan: Kelas, Jadwal Kuliah, Pertemuan Kuliah, Absensi Mahasiswa
- Penilaian: KRS, Nilai, KHS, Transkrip

**Modul Keuangan**
- Tagihan Mahasiswa
- Pembayaran Mahasiswa + verifikasi/monitoring

**Modul PMB (Penerimaan Mahasiswa Baru)**
- Portal pendaftaran + upload dokumen + verifikasi + export ke data mahasiswa + cek status

**Modul Akreditasi**
- Akreditasi Universitas, Fakultas, Program Studi (tracking masa berlaku)

**Modul User Management & Auth**
- User CRUD, role assignment, active/inactive, soft delete
- Login email/password, Google SSO, logout, session

**Modul Profile**
- View/Edit profile, ubah password, upload foto

**Modul Dashboard**
- Dashboard per role (Super Admin, Admin Univ, Admin Fakultas, Admin Prodi, Dosen, Mahasiswa)

**Modul Wilayah Indonesia (PENTING – telah ditingkatkan)**
- Provinsi, Kabupaten/Kota, Kecamatan, Desa/Kelurahan
- CRUD lengkap + soft delete/trash/restore/force delete
- Filter/pencarian bertingkat: Provinsi → Kabupaten/Kota → Kecamatan → Desa
- Paginasi (khususnya untuk data besar agar tidak memakan memori)
- Export CSV & PDF untuk:
  - Provinsi
  - Kabupaten/Kota
  - Kecamatan
  - Desa/Kelurahan

**University Profile**
- Halaman profil universitas: /university-profile
- Menampilkan universitas aktif (status = "Aktif")
- Disediakan seeder untuk memastikan data universitas aktif tersedia

**Modul File Upload**
- Upload/Download/Delete file (polymorphic) untuk entitas yang mendukung

---

### B. Akan Dibuat (Backlog / Belum Ada) ❌

Mengacu pada dokumentasi rencana:
- Modul E-Learning
- Modul Bimbingan Akademik
- Modul Perpustakaan
- Modul Surat Menyurat
- Modul Wisuda

---

## 3) Catatan Implementasi Terbaru (Wilayah & University Profile)

### A. Wilayah Indonesia

Peningkatan yang sudah diterapkan:
- Query index sudah dibatasi dengan paginasi untuk mencegah error memory di server.
- Fitur export CSV/PDF tersedia melalui tombol pada halaman index.
- Filter provinsi/kabupaten/kecamatan berjalan dengan dropdown bertingkat.

### B. University Profile

Perilaku:
- Jika tidak ada universitas berstatus "Aktif", halaman akan menampilkan 404.

Solusi produksi:
- Jalankan seeder universitas aktif, atau buat data universitas aktif lewat modul universitas.

---

## 4) Test Otomatis

Feature test yang tersedia:
- tests/Feature/ProvinceExportTest.php
- tests/Feature/ProvinceExportCsvTest.php
- tests/Feature/SubRegencyExportTest.php
- tests/Feature/VillageExportTest.php
- tests/Feature/UniversityProfileTest.php

Menjalankan test:
- Lokal/Windows: php artisan test
- Server (custom PHP path): /opt/alt/php-fpm82/usr/bin/php artisan test

Catatan:
- Test export CSV menggunakan response stream (buffered) agar stabil.

---

## 5) Deployment Checklist (Ringkas)

1. Pull update
- git pull origin main

2. Install dependency
- composer install --no-dev --optimize-autoloader

3. Migrasi
- php artisan migrate

4. Seeder penting (opsional tapi direkomendasikan untuk /university-profile)
- php artisan db:seed --class=UniversitySeeder

5. Clear cache
- php artisan config:clear
- php artisan cache:clear
- php artisan route:clear
- php artisan view:clear

6. Permission (Linux) — wajib agar tidak error “Permission denied” pada compiled view
- Pastikan storage dan bootstrap/cache writable oleh user webserver.

---

## 6) Lokasi Dokumen Lain (Referensi)

- Dokumentasi lengkap fitur: DOKUMENTASI_FITUR_LENGKAP.md
- Panduan deployment: DEPLOYMENT_GUIDE.md, DEPLOYMENT.md
- Panduan OAuth: GOOGLE_OAUTH_SETUP.md

---

## 7) Definisi Selesai (Definition of Done)

Fitur dianggap selesai bila:
- CRUD berjalan (create/read/update/delete) + validasi
- Tidak ada error 500 pada akses normal
- Tidak menambah beban memori berlebihan (gunakan pagination untuk listing besar)
- Export CSV/PDF berjalan tanpa memblokir halaman
- Test otomatis minimal untuk fitur kritikal (export/paginasi/halaman utama) lulus

---

Dokumen ini adalah ringkasan status akhir implementasi hingga 13 Desember 2025.
