# 📚 DOKUMENTASI LENGKAP FITUR SISTEM SISTER

**SISTER - Sistem Informasi Akademik Terintegrasi**  
Version: 2.0  
Last Updated: 7 Desember 2025

---

## 📋 Daftar Isi

1. [Tentang Sistem](#tentang-sistem)
2. [Roles & Permissions](#roles--permissions)
3. [Fitur per Role](#fitur-per-role)
4. [Modul yang Tersedia](#modul-yang-tersedia)
5. [Fitur yang Direncanakan](#fitur-yang-direncanakan)
6. [Teknologi Stack](#teknologi-stack)

---

## 🎯 Tentang Sistem

SISTER adalah sistem informasi akademik terintegrasi berbasis web yang dikembangkan menggunakan Laravel 12. Sistem ini dirancang untuk mengelola seluruh proses akademik dari tingkat universitas hingga mahasiswa individual.

### Tujuan Sistem
- Digitalisasi proses akademik
- Integrasi data lintas unit
- Kemudahan akses informasi akademik
- Transparansi dan akuntabilitas
- Efisiensi administrasi

---

## 👥 Roles & Permissions

Sistem SISTER memiliki **6 role user** dengan level akses berbeda:

### 1. 🔴 Super Admin
**Deskripsi**: Administrator sistem dengan akses penuh  
**Scope**: Seluruh sistem (universitas, fakultas, prodi)  
**Kewenangan**:
- Full CRUD semua modul
- Manajemen user dan role
- Konfigurasi sistem
- Akses semua data
- Manajemen wilayah Indonesia
- Setup tahun akademik dan semester

**Default Login**:
- Email: `superadmin@sister.ac.id`
- Password: `password`

---

### 2. 🟠 Admin Universitas
**Deskripsi**: Administrator tingkat universitas  
**Scope**: Seluruh data universitas  
**Kewenangan**:
- Manajemen data fakultas
- Manajemen data prodi
- Manajemen data mahasiswa (semua fakultas)
- Manajemen data dosen (semua fakultas)
- Manajemen mata kuliah universitas
- Manajemen kelas dan jadwal
- Monitoring KRS dan nilai (semua)
- Manajemen pembayaran mahasiswa
- Manajemen PMB (Penerimaan Mahasiswa Baru)
- Manajemen akreditasi universitas
- Manajemen tagihan mahasiswa
- User management

**Default Login**:
- Email: `admin@sister.ac.id`
- Password: `password`

**Tidak Bisa Akses**:
- Konfigurasi wilayah Indonesia
- Tahun akademik dan semester

---

### 3. 🟡 Admin Fakultas
**Deskripsi**: Administrator tingkat fakultas  
**Scope**: Data fakultas tertentu  
**Kewenangan**:
- Manajemen fakultas sendiri
- Manajemen prodi di fakultasnya
- Manajemen mahasiswa di fakultasnya
- Manajemen dosen di fakultasnya
- Manajemen mata kuliah fakultas
- Manajemen kelas dan jadwal (fakultas)
- Monitoring KRS dan nilai (fakultas)
- Manajemen pembayaran mahasiswa (fakultas)
- Manajemen akreditasi fakultas
- Manajemen tagihan mahasiswa (fakultas)

**Default Login**:
- Email: `admin.ft@sister.ac.id`
- Password: `password`

**Tidak Bisa Akses**:
- Data fakultas lain
- Manajemen user
- Konfigurasi sistem
- Tahun akademik dan semester

---

### 4. 🟢 Admin Program Studi
**Deskripsi**: Administrator tingkat program studi  
**Scope**: Data program studi tertentu  
**Kewenangan**:
- Manajemen prodi sendiri
- Manajemen mahasiswa di prodinya
- Manajemen dosen di prodinya
- Manajemen mata kuliah prodi
- Manajemen kelas dan jadwal (prodi)
- Approval KRS mahasiswa
- Input dan monitoring nilai (prodi)
- Manajemen pembayaran mahasiswa (prodi)
- Manajemen akreditasi program studi
- Manajemen tagihan mahasiswa (prodi)
- Manajemen hari libur
- Manajemen pertemuan kuliah
- Monitoring absensi

**Default Login**:
- Email: `admin.ti@sister.ac.id`
- Password: `password`

**Tidak Bisa Akses**:
- Data prodi lain
- Data fakultas lain
- Manajemen user
- Konfigurasi sistem

---

### 5. 🔵 Dosen
**Deskripsi**: Dosen pengajar  
**Scope**: Mata kuliah yang diampu  
**Kewenangan**:
- Melihat jadwal mengajar sendiri
- Melihat daftar mahasiswa di kelas yang diampu
- Input nilai mahasiswa (batch/individual)
- Monitoring KRS mahasiswa di kelasnya
- Lihat data mata kuliah
- Melihat profil mahasiswa di kelasnya
- Manajemen pertemuan kuliah (kelas sendiri)
- Generate QR code absensi
- Verifikasi kehadiran mahasiswa
- Reschedule pertemuan kuliah
- Export laporan kehadiran

**Generate User**:
- Dibuat oleh admin melalui menu Dosen
- Email: sesuai data dosen
- Password default: `Dsn{NIDN}`

**Tidak Bisa Akses**:
- Data mahasiswa di luar kelasnya
- CRUD master data
- Approval KRS
- Pembayaran
- Data keuangan

---

### 6. 🟣 Mahasiswa
**Deskripsi**: Mahasiswa aktif  
**Scope**: Data pribadi dan akademik sendiri  
**Kewenangan**:
- Dashboard mahasiswa (IPK, SKS, status)
- Melihat profil pribadi
- Edit profil (biodata, kontak, alamat)
- Ganti password
- Melihat jadwal kuliah sendiri
- Input KRS (Kartu Rencana Studi)
- Melihat KRS yang sudah disetujui
- Print KRS
- Melihat nilai sendiri
- Melihat KHS (Kartu Hasil Studi)
- Melihat transkrip nilai
- Melihat tagihan pembayaran
- Upload bukti pembayaran
- Melihat status pembayaran
- Scan QR code untuk absensi
- Melihat rekap kehadiran sendiri

**Generate User**:
- Dibuat otomatis/manual melalui menu Mahasiswa
- Email: sesuai data mahasiswa
- Password default: `Mhs{NIM}`

**Tidak Bisa Akses**:
- Data mahasiswa lain
- Data dosen
- Master data akademik
- Approval KRS (hanya input)
- Data keuangan institusi

---

## 🎓 Fitur per Role

### A. Fitur Super Admin

#### 📊 Dashboard
- Statistik universitas (total fakultas, prodi, mahasiswa, dosen)
- Grafik pertumbuhan mahasiswa
- Quick actions

#### 🏛️ Manajemen Akademik
1. **Fakultas** ✅
   - CRUD fakultas
   - Soft delete & restore
   - Data: kode, nama, singkatan, dekan, kontak

2. **Program Studi** ✅
   - CRUD program studi
   - Soft delete & restore
   - Data: kode, nama, jenjang (D3/S1/S2/S3), fakultas

3. **Mahasiswa** ✅
   - CRUD mahasiswa lengkap
   - Biodata, alamat, kontak
   - Orang tua/wali
   - Generate akun user otomatis
   - Upload foto

4. **Dosen** ✅
   - CRUD dosen
   - Soft delete & restore
   - Data: NIDN, nama, gelar, kontak
   - Upload foto

5. **Mata Kuliah** ✅
   - CRUD mata kuliah
   - Tipe: Universitas/Fakultas/Prodi
   - SKS, semester
   - Soft delete & restore

6. **Ruang** ✅
   - CRUD ruang kuliah
   - Kapasitas, lokasi
   - Soft delete & restore

7. **Kelas** ✅
   - CRUD kelas
   - Assign dosen pengampu
   - Set kapasitas
   - Soft delete & restore

8. **Jadwal Kuliah** ✅
   - CRUD jadwal
   - Validasi bentrok ruang
   - Validasi bentrok dosen
   - Multi-hari
   - Soft delete & restore

9. **Hari Libur** ✅
   - CRUD hari libur
   - Libur nasional/institusi
   - Blokir jadwal di hari libur

#### 👨‍🎓 Manajemen Mahasiswa
10. **KRS (Kartu Rencana Studi)** ✅
    - Lihat semua KRS
    - Filter by status (Draft/Disetujui/Ditolak)
    - Approval KRS
    - Reject KRS dengan alasan
    - Print KRS

11. **Nilai** ✅
    - Lihat semua nilai
    - Input nilai batch per kelas
    - Input nilai individual
    - Generate KHS
    - Generate transkrip
    - Monitoring IPK/IPS

#### 💰 Manajemen Keuangan
12. **Tagihan Mahasiswa** ✅
    - Create tagihan individual
    - Create tagihan batch (per prodi/semester)
    - Set jatuh tempo
    - Tracking status pembayaran

13. **Pembayaran Mahasiswa** ✅
    - Verifikasi pembayaran
    - Reject pembayaran
    - Lihat bukti transfer
    - Rekap pembayaran

#### 🎯 Penerimaan Mahasiswa Baru (PMB)
14. **Pendaftaran Mahasiswa** ✅
    - Lihat semua pendaftar
    - Verifikasi pendaftaran
    - Export ke data mahasiswa
    - Soft delete & restore

15. **Portal PMB Publik** ✅
    - Form pendaftaran online
    - Upload dokumen
    - Cek status pendaftaran
    - Halaman sukses registrasi

#### 🏅 Manajemen Akreditasi
16. **Akreditasi Universitas** ✅
    - CRUD data akreditasi universitas
    - Status akreditasi
    - Masa berlaku
    - Soft delete & restore

17. **Akreditasi Fakultas** ✅
    - CRUD data akreditasi fakultas
    - Per fakultas
    - Soft delete & restore

18. **Akreditasi Program Studi** ✅
    - CRUD data akreditasi prodi
    - Per program studi
    - Soft delete & restore

#### 🌍 Data Wilayah Indonesia
19. **Provinsi** ✅
    - CRUD provinsi
    - Soft delete & restore

20. **Kabupaten/Kota** ✅
    - CRUD kabupaten/kota
    - Relasi ke provinsi
    - Soft delete & restore

21. **Kecamatan** ✅
    - CRUD kecamatan
    - Relasi ke kabupaten
    - Soft delete & restore

22. **Desa/Kelurahan** ✅
    - CRUD desa/kelurahan
    - Relasi ke kecamatan
    - Soft delete & restore

#### ⚙️ Konfigurasi Sistem
23. **Tahun Akademik** ✅
    - CRUD tahun akademik
    - Toggle aktif/nonaktif
    - Soft delete & restore

24. **Semester** ✅
    - CRUD semester
    - Toggle aktif/nonaktif
    - Soft delete & restore

25. **University Profile** ✅
    - CRUD data universitas
    - Logo, visi, misi
    - Kontak, alamat

26. **User Management** ✅
    - CRUD user
    - Assign role
    - Link user ke mahasiswa/dosen
    - Toggle active/inactive
    - Soft delete & restore

#### 📅 Manajemen Pertemuan & Absensi
27. **Pertemuan Kuliah** ✅
    - CRUD pertemuan kuliah
    - Generate QR code absensi
    - Reschedule pertemuan
    - Set durasi pertemuan

28. **Absensi Mahasiswa** ✅
    - Monitoring absensi per kelas
    - Verifikasi kehadiran
    - Export laporan absensi
    - Statistik kehadiran

#### 🔐 Autentikasi
29. **Login dengan Email/Password** ✅
30. **Login dengan Google (SSO)** ✅
31. **Logout** ✅

#### 👤 Profile Management
32. **Edit Profile** ✅
    - Update nama, email
    - Upload foto profil

33. **Ganti Password** ✅

---

### B. Fitur Admin Universitas

Semua fitur Super Admin **KECUALI**:
- ❌ Data Wilayah Indonesia (Provinsi, Kabupaten, Kecamatan, Desa)
- ❌ Tahun Akademik
- ❌ Semester

**Tambahan**:
- ✅ Semua akses terbatas pada data universitas sendiri
- ✅ User management (dapat membuat user admin fakultas, admin prodi, dosen, mahasiswa)

---

### C. Fitur Admin Fakultas

#### Dapat Akses:
1. **Fakultas** ✅ (hanya fakultas sendiri)
2. **Program Studi** ✅ (hanya prodi di fakultasnya)
3. **Mahasiswa** ✅ (hanya mahasiswa di fakultasnya)
4. **Dosen** ✅ (hanya dosen di fakultasnya)
5. **Mata Kuliah** ✅ (universitas + fakultas sendiri)
6. **Ruang** ✅ (hanya ruang di fakultasnya)
7. **Kelas** ✅ (hanya kelas di fakultasnya)
8. **Jadwal Kuliah** ✅ (hanya jadwal di fakultasnya)
9. **Hari Libur** ✅
10. **KRS** ✅ (mahasiswa di fakultasnya)
11. **Nilai** ✅ (mahasiswa di fakultasnya)
12. **Tagihan Mahasiswa** ✅ (mahasiswa di fakultasnya)
13. **Pembayaran Mahasiswa** ✅ (mahasiswa di fakultasnya)
14. **Pendaftaran Mahasiswa** ✅ (pendaftar ke fakultasnya)
15. **Akreditasi Fakultas** ✅ (fakultas sendiri)
16. **Akreditasi Program Studi** ✅ (prodi di fakultasnya)
17. **Pertemuan Kuliah** ✅ (kelas di fakultasnya)
18. **Absensi Mahasiswa** ✅ (mahasiswa di fakultasnya)
19. **Profile Management** ✅

#### Tidak Dapat Akses:
- ❌ Fakultas lain
- ❌ User Management
- ❌ Akreditasi Universitas
- ❌ Wilayah Indonesia
- ❌ Tahun Akademik & Semester
- ❌ University Profile

---

### D. Fitur Admin Program Studi

#### Dapat Akses:
1. **Program Studi** ✅ (hanya prodi sendiri)
2. **Mahasiswa** ✅ (hanya mahasiswa di prodinya)
3. **Dosen** ✅ (hanya dosen di prodinya)
4. **Mata Kuliah** ✅ (universitas + fakultas + prodi sendiri)
5. **Ruang** ✅ (hanya ruang di prodinya)
6. **Kelas** ✅ (hanya kelas di prodinya)
7. **Jadwal Kuliah** ✅ (hanya jadwal di prodinya)
8. **Hari Libur** ✅
9. **KRS** ✅ (mahasiswa di prodinya)
   - Approval KRS
   - Reject KRS
10. **Nilai** ✅ (mahasiswa di prodinya)
    - Monitoring nilai
11. **Tagihan Mahasiswa** ✅ (mahasiswa di prodinya)
12. **Pembayaran Mahasiswa** ✅ (mahasiswa di prodinya)
13. **Pendaftaran Mahasiswa** ✅ (pendaftar ke prodinya)
14. **Akreditasi Program Studi** ✅ (prodi sendiri)
15. **Pertemuan Kuliah** ✅ (kelas di prodinya)
16. **Absensi Mahasiswa** ✅ (mahasiswa di prodinya)
17. **Profile Management** ✅

#### Tidak Dapat Akses:
- ❌ Prodi lain
- ❌ Fakultas lain
- ❌ User Management
- ❌ Akreditasi Universitas & Fakultas
- ❌ Wilayah Indonesia
- ❌ Tahun Akademik & Semester

---

### E. Fitur Dosen

#### Dashboard Dosen
- Jadwal mengajar hari ini
- Kelas yang diampu semester ini
- Total mahasiswa di kelas
- Quick access ke input nilai

#### Dapat Akses:
1. **Mata Kuliah** ✅ (view only)
   - Lihat semua mata kuliah

2. **Jadwal Kuliah** ✅ (view only)
   - Lihat jadwal mengajar sendiri
   - Filter by semester

3. **Mahasiswa** ✅ (view only)
   - Lihat mahasiswa di kelas yang diampu
   - Lihat profil mahasiswa

4. **KRS** ✅ (view only)
   - Lihat KRS mahasiswa di kelasnya
   - Tidak bisa approval

5. **Nilai** ✅ (CRUD)
   - **Input nilai batch** per kelas
   - **Input nilai individual**
   - Edit nilai
   - Lihat rekap nilai per kelas
   - Generate KHS mahasiswa

6. **Pertemuan Kuliah** ✅ (CRUD)
   - Create pertemuan kuliah
   - Generate QR code absensi
   - Reschedule pertemuan
   - Set durasi pertemuan
   - Lihat list pertemuan per kelas

7. **Absensi Mahasiswa** ✅
   - Monitoring kehadiran mahasiswa per kelas
   - Verifikasi kehadiran manual
   - Export laporan kehadiran
   - Lihat statistik kehadiran per mahasiswa

8. **Profile Management** ✅
   - Edit profil
   - Ganti password
   - Upload foto

#### Tidak Dapat Akses:
- ❌ CRUD master data (Fakultas, Prodi, Dosen, dll)
- ❌ Approval KRS
- ❌ Pembayaran
- ❌ PMB
- ❌ Akreditasi
- ❌ User Management
- ❌ Konfigurasi sistem

---

### F. Fitur Mahasiswa

#### Dashboard Mahasiswa ✅
- **Informasi Akademik**:
  - Nama, NIM
  - Program Studi
  - Semester saat ini
  - Status mahasiswa (Aktif/Cuti/Lulus)
  
- **Statistik Akademik**:
  - IPK (Indeks Prestasi Kumulatif)
  - Total SKS Lulus
  - SKS semester ini
  
- **Jadwal Hari Ini**:
  - List mata kuliah hari ini
  - Ruang, waktu, dosen
  
- **Quick Actions**:
  - Button ke KRS
  - Button ke nilai
  - Button ke jadwal lengkap
  - Button ke profil

#### 1. Profil Mahasiswa ✅
**Halaman**: `/profil-mahasiswa`

##### a. Lihat Profil
- **Data Pribadi**:
  - Foto profil
  - Nama lengkap
  - NIM
  - Jenis kelamin
  - Tempat, tanggal lahir
  - Agama
  - Status perkawinan
  
- **Data Kontak**:
  - Email
  - No. HP
  - No. Telepon
  
- **Data Alamat**:
  - Alamat lengkap
  - RT/RW
  - Provinsi
  - Kabupaten/Kota
  - Kecamatan
  - Desa/Kelurahan
  - Kode pos
  
- **Data Akademik**:
  - Program Studi
  - Fakultas
  - Tahun masuk
  - Status mahasiswa
  - Dosen wali
  
- **Data Orang Tua/Wali**:
  - Nama ayah, pekerjaan, no. HP
  - Nama ibu, pekerjaan, no. HP
  - Nama wali, pekerjaan, no. HP (jika ada)

##### b. Edit Profil ✅
**Halaman**: `/profil-mahasiswa/edit`

**Yang Dapat Diedit**:
- ✅ Foto profil
- ✅ No. HP
- ✅ No. Telepon
- ✅ Alamat lengkap
- ✅ RT/RW
- ✅ Provinsi, Kabupaten, Kecamatan, Desa (dropdown)
- ✅ Kode pos
- ✅ Agama
- ✅ Status perkawinan
- ✅ Data orang tua/wali (nama, pekerjaan, no. HP)

**Yang Tidak Dapat Diedit**:
- ❌ NIM
- ❌ Nama mahasiswa
- ❌ Email
- ❌ Program Studi
- ❌ Tahun masuk
- ❌ Status mahasiswa

**Fitur Khusus**:
- Upload foto profil
- Validasi format foto (JPG, PNG, max 2MB)
- Dropdown bertingkat wilayah Indonesia (AJAX)
- Auto-fill berdasarkan data lama

##### c. Ganti Password ✅
**Halaman**: `/profil-mahasiswa/change-password`

**Form**:
- Password lama
- Password baru (min 8 karakter)
- Konfirmasi password baru

**Validasi**:
- Password lama harus benar
- Password baru min 8 karakter
- Konfirmasi harus match

#### 2. Jadwal Kuliah ✅
**Halaman**: `/jadwal-kuliah`

**Fitur**:
- Lihat jadwal kuliah semester aktif
- Filter by hari
- Informasi: Mata kuliah, SKS, Hari, Jam, Ruang, Dosen
- View detail jadwal
- Tidak bisa edit/delete

#### 3. KRS (Kartu Rencana Studi) ✅
**Halaman**: `/krs`

##### a. List KRS
- Lihat semua KRS yang pernah diinput
- Filter by tahun ajaran & semester
- Status: Draft/Disetujui/Ditolak
- Total SKS per semester

##### b. Input KRS ✅
**Halaman**: `/krs/create`

**Proses**:
1. Pilih tahun ajaran dan semester
2. Pilih mata kuliah yang tersedia
3. Sistem validasi:
   - SKS max per semester (24 SKS)
   - Prasyarat mata kuliah
   - Kuota kelas
4. Simpan sebagai draft
5. Submit untuk approval

**Validasi**:
- Tidak bisa input KRS yang sudah ada
- Cek batas maksimal SKS
- Cek jadwal bentrok
- Cek mata kuliah sudah lulus

##### c. Print KRS ✅
**Halaman**: `/krs/print/{mahasiswaId}/{tahunAjaran}/{semester}`

**Output**: PDF berisi:
- Header universitas
- Data mahasiswa
- Daftar mata kuliah (kode, nama, SKS, kelas, dosen)
- Total SKS
- Status approval
- Tanda tangan digital

#### 4. Nilai Mahasiswa ✅
**Halaman**: `/nilai`

##### a. Lihat Nilai
- List semua nilai
- Filter by tahun ajaran & semester
- Info: Mata Kuliah, SKS, Nilai Huruf, Nilai Angka, Semester

##### b. KHS (Kartu Hasil Studi) ✅
**Halaman**: `/nilai/khs/{mahasiswaId}/{tahunAjaran}/{semester}`

**Output**: PDF berisi:
- Data mahasiswa
- Daftar nilai semester tersebut
- IPS (Indeks Prestasi Semester)
- IPK (Indeks Prestasi Kumulatif)
- Total SKS semester
- Total SKS kumulatif

##### c. Transkrip Nilai ✅
**Halaman**: `/nilai/transkrip/{mahasiswaId}`

**Output**: PDF berisi:
- Data mahasiswa
- Seluruh nilai dari semester 1 sampai terakhir
- IPK akhir
- Total SKS lulus
- Predikat kelulusan

#### 5. Tagihan & Pembayaran ✅
**Halaman**: `/my-payments`

##### a. Lihat Tagihan
- List semua tagihan
- Info: Jenis tagihan, nominal, jatuh tempo, status
- Status: Belum Bayar/Menunggu Verifikasi/Lunas/Ditolak

##### b. Upload Bukti Pembayaran ✅
**Proses**:
1. Pilih tagihan yang belum bayar
2. Upload bukti transfer (JPG, PNG, PDF max 2MB)
3. Input tanggal bayar
4. Submit
5. Status jadi "Menunggu Verifikasi"

##### c. Tracking Status
- Lihat status verifikasi
- Jika ditolak, bisa upload ulang
- Jika diverifikasi, status "Lunas"

#### 6. Absensi Kehadiran ✅
**Halaman**: `/kehadiran-saya`

##### a. Scan QR Code Absensi ✅
**Proses**:
1. Dosen generate QR code di awal pertemuan
2. Mahasiswa scan QR code via smartphone/laptop
3. Sistem validasi:
   - Mahasiswa terdaftar di kelas
   - Waktu scan dalam durasi yang ditentukan
   - Belum absen sebelumnya
4. Status kehadiran: Hadir/Terlambat/Izin/Sakit/Alfa

##### b. Rekap Kehadiran ✅
- Lihat rekap kehadiran per mata kuliah
- Persentase kehadiran
- Total: Hadir/Terlambat/Izin/Sakit/Alfa
- Warning jika kehadiran < 75%

#### 7. Profile Management ✅
- Edit profile (nama, email)
- Ganti password
- Upload foto profile

#### 8. Logout ✅

#### Tidak Dapat Akses:
- ❌ Data mahasiswa lain
- ❌ Data dosen
- ❌ Master data akademik
- ❌ Input nilai
- ❌ Approval KRS
- ❌ Pembayaran institusi
- ❌ PMB
- ❌ User management

---

## 📦 Modul yang Tersedia

### 1. Modul Akademik

#### A. Master Data ✅
- Fakultas
- Program Studi
- Mata Kuliah
- Ruang
- Tahun Akademik
- Semester
- Hari Libur

#### B. Data Personil ✅
- Mahasiswa
- Dosen

#### C. Perkuliahan ✅
- Kelas
- Jadwal Kuliah
- Pertemuan Kuliah
- Absensi Mahasiswa

#### D. Penilaian ✅
- KRS (Kartu Rencana Studi)
- Nilai
- KHS (Kartu Hasil Studi)
- Transkrip

### 2. Modul Keuangan ✅
- Tagihan Mahasiswa
- Pembayaran Mahasiswa
- Verifikasi Pembayaran
- Tracking Pembayaran

### 3. Modul PMB (Penerimaan Mahasiswa Baru) ✅
- Portal Pendaftaran Online
- Formulir Pendaftaran
- Upload Dokumen
- Verifikasi Pendaftaran
- Export ke Data Mahasiswa
- Cek Status Pendaftaran

### 4. Modul Akreditasi ✅
- Akreditasi Universitas
- Akreditasi Fakultas
- Akreditasi Program Studi
- Tracking Masa Berlaku

### 5. Modul User Management ✅
- User CRUD
- Role Assignment
- Link User to Mahasiswa/Dosen
- Active/Inactive User
- Soft Delete

### 6. Modul Autentikasi ✅
- Login Email/Password
- Login Google SSO
- Logout
- Session Management

### 7. Modul Profile ✅
- View Profile
- Edit Profile
- Change Password
- Upload Photo

### 8. Modul Dashboard ✅
- Dashboard Super Admin
- Dashboard Admin Universitas
- Dashboard Admin Fakultas
- Dashboard Admin Prodi
- Dashboard Dosen
- Dashboard Mahasiswa (khusus)

### 9. Modul Wilayah Indonesia ✅
- Provinsi
- Kabupaten/Kota
- Kecamatan
- Desa/Kelurahan
- AJAX Dropdown Bertingkat

### 10. Modul File Upload ✅
- Upload File (Polymorphic)
- Download File
- Delete File
- Support: PDF, JPG, PNG, DOCX
- Max size: 2MB

---

## 🚀 Fitur yang Direncanakan

### 1. Modul E-Learning 📚
**Status**: ❌ Belum Ada

**Fitur**:
- Upload materi kuliah (PDF, PPT, Video)
- Forum diskusi per mata kuliah
- Assignment submission online
- Quiz online dengan auto-grading
- Video conference integration (Zoom/Google Meet)
- Attendance tracking via LMS
- Progress tracking per mahasiswa

**Role yang Dapat Akses**:
- Dosen: Upload materi, buat assignment, buat quiz
- Mahasiswa: Lihat materi, submit assignment, ikut quiz

---

### 2. Modul Bimbingan Akademik 👨‍🏫
**Status**: ❌ Belum Ada

**Fitur**:
- Assign dosen wali ke mahasiswa
- Jadwal konsultasi online
- Catatan bimbingan
- Tracking masalah akademik mahasiswa
- Alert jika IPK < 2.5
- Riwayat konsultasi
- Approval cuti/resign mahasiswa

**Role yang Dapat Akses**:
- Admin Prodi: Assign dosen wali
- Dosen: Input catatan bimbingan, jadwal konsultasi
- Mahasiswa: Request konsultasi, lihat catatan bimbingan

---

### 3. Modul Perpustakaan 📖
**Status**: ❌ Belum Ada

**Fitur**:
- Katalog buku digital
- Peminjaman buku online
- E-book repository
- Tracking jatuh tempo pengembalian
- Denda keterlambatan
- Search buku by kategori/pengarang
- Reservasi buku
- Statistik peminjaman

**Role yang Dapat Akses**:
- Admin Perpustakaan (new role): Manajemen buku, approval peminjaman
- Mahasiswa & Dosen: Pinjam buku, lihat katalog

---

### 4. Modul Surat Menyurat 📝
**Status**: ❌ Belum Ada

**Fitur**:
- Template surat (Surat Keterangan Mahasiswa Aktif, Surat Pengantar, dll)
- Request surat online
- Approval workflow
- Tanda tangan digital
- Tracking status surat
- Download surat PDF
- Notifikasi surat ready

**Role yang Dapat Akses**:
- Mahasiswa: Request surat
- Admin Prodi: Approval dan generate surat

---

### 5. Modul Wisuda 🎓
**Status**: ❌ Belum Ada

**Fitur**:
- Pendaftaran wisuda online
- Syarat wisuda checklist (IPK, SKS, tagihan lunas)
- Upload dokumen wisuda
- Cetak undangan wisuda
- Absensi gladi bersih
- Cetak sertifikat wisuda
- Alumni data export

**Role yang Dapat Akses**:
- Mahasiswa: Daftar wisuda, upload dokumen
- Admin Universitas: Verifikasi, generate sertifikat

---

### 6. Modul Beasiswa 💰
**Status**: ❌ Belum Ada

**Fitur**:
- List beasiswa available (internal/eksternal)
- Pendaftaran beasiswa online
- Upload dokumen persyaratan
- Approval workflow
- Tracking status beasiswa
- Pencairan beasiswa
- Laporan penerima beasiswa

**Role yang Dapat Akses**:
- Mahasiswa: Daftar beasiswa, upload dokumen
- Admin Universitas: Post beasiswa, verifikasi, approval

---

### 7. Modul Magang/PKL 💼
**Status**: ❌ Belum Ada

**Fitur**:
- Pendaftaran magang
- Data perusahaan mitra
- Assign dosen pembimbing
- Logbook magang online
- Upload laporan magang
- Penilaian magang
- Sertifikat magang
- Job posting dari perusahaan

**Role yang Dapat Akses**:
- Mahasiswa: Daftar magang, input logbook, upload laporan
- Dosen: Bimbingan, penilaian magang
- Admin Prodi: Assign pembimbing, verifikasi

---

### 8. Modul Alumni 👥
**Status**: ❌ Belum Ada

**Fitur**:
- Database alumni
- Tracer study (tracking karir alumni)
- Alumni network
- Job posting for alumni
- Alumni card digital
- Alumni event
- Donation tracking

**Role yang Dapat Akses**:
- Alumni (new role): Update data, lihat job posting
- Admin Universitas: Manajemen data alumni, tracer study

---

### 9. Modul Penelitian 🔬
**Status**: ❌ Belum Ada

**Fitur**:
- Proposal penelitian
- Laporan penelitian
- Publikasi ilmiah
- Hibah penelitian
- Kolaborasi penelitian
- Repository publikasi
- Citation tracking

**Role yang Dapat Akses**:
- Dosen: Submit proposal, laporan, publikasi
- Admin Universitas: Approval, monitoring

---

### 10. Modul Skripsi/TA 📄
**Status**: ❌ Belum Ada

**Fitur**:
- Pendaftaran skripsi/TA
- Pengajuan judul
- Assign dosen pembimbing
- Assign dosen penguji
- Logbook bimbingan online
- Jadwal seminar proposal
- Jadwal sidang akhir
- Upload draft skripsi
- Plagiarism checker
- Repository skripsi
- Cetak lembar pengesahan

**Role yang Dapat Akses**:
- Mahasiswa: Daftar, upload draft, input logbook
- Dosen: Bimbingan, approval judul, penilaian
- Admin Prodi: Assign pembimbing/penguji, jadwal sidang

---

### 11. Modul Notifikasi 🔔
**Status**: ❌ Belum Ada

**Fitur**:
- Push notification in-app
- Email notification
- SMS notification (optional)
- WhatsApp notification (optional)
- Notification center
- Mark as read/unread
- Notification settings

**Notifikasi untuk**:
- KRS disetujui/ditolak
- Nilai sudah diinput
- Jadwal kuliah berubah
- Tagihan jatuh tempo
- Pembayaran diverifikasi
- Surat sudah ready

---

### 12. Modul Laporan & Analytics 📊
**Status**: ❌ Belum Ada

**Fitur**:
- Dashboard analytics lengkap
- Report builder
- Export to Excel/PDF
- Grafik interaktif
- Custom filter & date range

**Laporan**:
- Laporan akademik (KRS, nilai, IPK)
- Laporan keuangan (tagihan, pembayaran)
- Laporan kehadiran
- Laporan PMB
- Laporan per fakultas/prodi
- Laporan per semester/tahun ajaran

---

### 13. Modul Kuisioner & Survey 📋
**Status**: ❌ Belum Ada

**Fitur**:
- Create kuisioner/survey
- Form builder (multiple choice, essay, rating)
- Anonymous/named response
- Auto-close survey
- Result analytics
- Export result

**Jenis Survey**:
- Evaluasi dosen by mahasiswa (EDOM)
- Kepuasan mahasiswa
- Tracer study alumni
- Survey internal

**Role yang Dapat Akses**:
- Admin: Create survey
- Mahasiswa/Alumni: Fill survey
- Admin: Lihat result analytics

---

### 14. Modul Mobile App 📱
**Status**: ❌ Belum Ada

**Platform**:
- iOS (Swift/Flutter)
- Android (Kotlin/Flutter)

**Fitur Mobile**:
- Login dengan Face ID/Fingerprint
- Push notification
- Scan QR absensi (lebih mudah)
- Lihat jadwal kuliah
- Lihat nilai
- Upload pembayaran via camera
- Chat/messaging (jika ada)
- Offline mode (cached data)

---

### 15. Enhancement Existing Features 🔧

#### A. KRS Enhancement
- **Auto-scheduling**: AI recommend jadwal without bentrok
- **Priority registration**: by IPK/semester
- **Waiting list**: jika kelas penuh
- **Swap class**: mahasiswa tukar kelas

#### B. Nilai Enhancement
- **Grade distribution chart**: per mata kuliah
- **Compare IPK**: dengan angkatan/prodi
- **Transcript verification**: QR code untuk verifikasi transkrip
- **Grade appeal**: mahasiswa banding nilai

#### C. Absensi Enhancement
- **Face recognition**: absensi pakai wajah
- **Geolocation**: cek lokasi mahasiswa saat absensi
- **Auto-attendance**: integrase dengan WiFi kampus
- **Attendance prediction**: AI predict kehadiran

#### D. Jadwal Enhancement
- **Room optimization**: AI allocate room by kapasitas
- **Conflict detection**: auto-detect bentrok before save
- **Schedule visualization**: Gantt chart per ruang/dosen
- **Calendar integration**: export to Google Calendar

#### E. Pembayaran Enhancement
- **Payment gateway**: integrasi dengan Midtrans/Xendit
- **Virtual account**: auto-generate VA per mahasiswa
- **Cicilan pembayaran**: bisa cicil tagihan
- **Payment reminder**: auto-remind via email/WA

---

### 16. Modul Keamanan & Audit 🔒
**Status**: ⚠️ Partial (AuditableTrait ada)

**Enhancement Needed**:
- **Activity log**: log semua aktivitas user
- **Login history**: track login location & device
- **Data encryption**: encrypt sensitive data
- **Two-factor authentication (2FA)**: via SMS/Email/Authenticator
- **Session timeout**: auto-logout after idle
- **IP whitelist**: restrict akses by IP
- **Backup & restore**: auto-backup database
- **GDPR compliance**: data privacy

---

### 17. Modul Integrasi 🔗
**Status**: ❌ Belum Ada

**Integrasi dengan**:
- **PDDIKTI (Pangkalan Data Pendidikan Tinggi)**: sync data mahasiswa & dosen
- **SINTA (Science and Technology Index)**: track publikasi dosen
- **Google Workspace**: sync email, calendar, drive
- **Microsoft Teams**: untuk virtual class
- **Payment Gateway**: Midtrans, Xendit, dll
- **SMS Gateway**: untuk notifikasi SMS
- **Email Service**: SendGrid, Mailgun
- **WhatsApp Business API**: untuk notifikasi WA

---

## 💻 Teknologi Stack

### Backend
- **Framework**: Laravel 12.x
- **PHP**: 8.2+
- **Database**: MySQL 8.0 / MariaDB 10.4+
- **ORM**: Eloquent
- **Authentication**: Laravel Sanctum + Laravel Socialite (Google OAuth)
- **Middleware**: Custom role-based middleware
- **File Storage**: Local storage + Polymorphic relationships

### Frontend
- **Template Engine**: Blade
- **CSS Framework**: Bootstrap 5.3
- **Icons**: Bootstrap Icons
- **JavaScript**: Vanilla JS + jQuery (minimal)
- **AJAX**: untuk dropdown wilayah, file upload

### Libraries & Packages
- **Laravel Socialite**: Google SSO
- **Intervention Image**: Image processing
- **DomPDF / TCPDF**: Generate PDF
- **SimpleSoftwareIO/simple-qrcode**: Generate QR code absensi

### Development Tools
- **Server**: XAMPP (Apache + MySQL)
- **Version Control**: Git
- **Deployment**: Manual deploy via FTP/SSH

### Server Requirements
- PHP >= 8.2
- MySQL >= 8.0 atau MariaDB >= 10.4
- Apache/Nginx web server
- Composer 2.x
- Node.js & NPM (untuk asset compilation)

---

## 📂 Struktur Role & Relasi Database

### Tabel: roles
```sql
id | name              | display_name           | description
1  | super_admin       | Super Admin            | Akses penuh ke seluruh sistem
2  | admin_universitas | Admin Universitas      | Administrator tingkat universitas
3  | admin_fakultas    | Admin Fakultas         | Administrator tingkat fakultas
4  | admin_prodi       | Admin Program Studi    | Administrator tingkat program studi
5  | dosen             | Dosen                  | Dosen pengajar
6  | mahasiswa         | Mahasiswa              | Mahasiswa aktif
```

### Tabel: users
```sql
id
name
email
password
role_id              → roles(id)
fakultas_id          → fakultas(id)
program_studi_id     → program_studi(id)
dosen_id             → dosen(id)
mahasiswa_id         → mahasiswa(id)
google_id            → Google SSO ID
avatar               → Profile photo URL
is_active            → Boolean
email_verified_at
remember_token
created_at
updated_at
deleted_at
```

### Relasi User dengan Entity
- User **belongsTo** Role
- User **belongsTo** Fakultas (nullable)
- User **belongsTo** ProgramStudi (nullable)
- User **belongsTo** Dosen (nullable, jika user adalah dosen)
- User **belongsTo** Mahasiswa (nullable, jika user adalah mahasiswa)

**Catatan Penting**:
- Foreign key `dosen_id` dan `mahasiswa_id` ada di tabel `users`, **BUKAN** sebaliknya
- Ini desain yang benar: User dapat link ke Mahasiswa/Dosen, tapi tidak semua Mahasiswa/Dosen punya User

---

## 🎯 Summary Fitur per Role

| Modul/Fitur | Super Admin | Admin Univ | Admin Fak | Admin Prodi | Dosen | Mahasiswa |
|-------------|:-----------:|:----------:|:---------:|:-----------:|:-----:|:---------:|
| **Dashboard** | ✅ Full | ✅ Full | ✅ Fakultas | ✅ Prodi | ✅ Mengajar | ✅ Personal |
| **Fakultas** | ✅ CRUD | ✅ CRUD | ✅ View Own | ❌ | ❌ | ❌ |
| **Program Studi** | ✅ CRUD | ✅ CRUD | ✅ CRUD (Fak) | ✅ View Own | ❌ | ❌ |
| **Mahasiswa** | ✅ CRUD | ✅ CRUD | ✅ CRUD (Fak) | ✅ CRUD (Prodi) | ✅ View (Kelas) | ✅ View Own |
| **Dosen** | ✅ CRUD | ✅ CRUD | ✅ CRUD (Fak) | ✅ CRUD (Prodi) | ❌ | ❌ |
| **Mata Kuliah** | ✅ CRUD | ✅ CRUD | ✅ CRUD (Fak) | ✅ CRUD (Prodi) | ✅ View | ❌ |
| **Ruang** | ✅ CRUD | ✅ CRUD | ✅ CRUD (Fak) | ✅ CRUD (Prodi) | ❌ | ❌ |
| **Kelas** | ✅ CRUD | ✅ CRUD | ✅ CRUD (Fak) | ✅ CRUD (Prodi) | ❌ | ❌ |
| **Jadwal Kuliah** | ✅ CRUD | ✅ CRUD | ✅ CRUD (Fak) | ✅ CRUD (Prodi) | ✅ View | ✅ View Own |
| **Hari Libur** | ✅ CRUD | ✅ CRUD | ✅ CRUD | ✅ CRUD | ❌ | ❌ |
| **Pertemuan Kuliah** | ✅ CRUD | ✅ CRUD | ✅ CRUD (Fak) | ✅ CRUD (Prodi) | ✅ CRUD (Kelas) | ❌ |
| **KRS** | ✅ View All | ✅ View All | ✅ View (Fak) | ✅ Approval (Prodi) | ✅ View (Kelas) | ✅ CRUD Own |
| **Nilai** | ✅ View All | ✅ View All | ✅ View (Fak) | ✅ View (Prodi) | ✅ Input (Kelas) | ✅ View Own |
| **Tagihan** | ✅ CRUD | ✅ CRUD | ✅ CRUD (Fak) | ✅ CRUD (Prodi) | ❌ | ✅ View Own |
| **Pembayaran** | ✅ Verify | ✅ Verify | ✅ Verify (Fak) | ✅ Verify (Prodi) | ❌ | ✅ Upload |
| **Absensi** | ✅ View All | ✅ View All | ✅ View (Fak) | ✅ View (Prodi) | ✅ Verify (Kelas) | ✅ Scan QR |
| **PMB** | ✅ Verify | ✅ Verify | ✅ Verify (Fak) | ✅ Verify (Prodi) | ❌ | ❌ |
| **Akreditasi Univ** | ✅ CRUD | ✅ CRUD | ❌ | ❌ | ❌ | ❌ |
| **Akreditasi Fak** | ✅ CRUD | ✅ CRUD | ✅ CRUD (Fak) | ❌ | ❌ | ❌ |
| **Akreditasi Prodi** | ✅ CRUD | ✅ CRUD | ✅ CRUD (Fak) | ✅ CRUD (Prodi) | ❌ | ❌ |
| **Wilayah Indonesia** | ✅ CRUD | ❌ | ❌ | ❌ | ❌ | ❌ |
| **Tahun Akademik** | ✅ CRUD | ❌ | ❌ | ❌ | ❌ | ❌ |
| **Semester** | ✅ CRUD | ❌ | ❌ | ❌ | ❌ | ❌ |
| **University Profile** | ✅ CRUD | ✅ CRUD | ❌ | ❌ | ❌ | ❌ |
| **User Management** | ✅ CRUD | ✅ CRUD | ❌ | ❌ | ❌ | ❌ |
| **Profile** | ✅ Edit | ✅ Edit | ✅ Edit | ✅ Edit | ✅ Edit | ✅ Edit |
| **Login Google SSO** | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |

**Legend**:
- ✅ **CRUD**: Full akses (Create, Read, Update, Delete)
- ✅ **View**: Hanya lihat
- ✅ **Own**: Hanya data sendiri
- ✅ **(Fak)**: Data di fakultas sendiri
- ✅ **(Prodi)**: Data di prodi sendiri
- ✅ **(Kelas)**: Data kelas yang diampu
- ❌ **Tidak ada akses**

---

## 📞 Informasi Kontak & Support

### Login Credentials (Development)

#### Super Admin
- Email: `superadmin@sister.ac.id`
- Password: `password`

#### Admin Universitas
- Email: `admin@sister.ac.id`
- Password: `password`

#### Admin Fakultas
- Email: `admin.ft@sister.ac.id`
- Password: `password`

#### Admin Prodi
- Email: `admin.ti@sister.ac.id`
- Password: `password`

### Catatan Keamanan
⚠️ **PENTING**: Password default harus diganti saat deployment ke production!

---

## 📝 Changelog

### Version 2.0 (7 Desember 2025)
- ✅ Added: Google SSO Authentication
- ✅ Added: Dashboard Mahasiswa dengan statistik
- ✅ Added: Modul Pertemuan Kuliah
- ✅ Added: Modul Absensi dengan QR Code
- ✅ Added: Profile Management mahasiswa lengkap
- ✅ Fixed: Relasi User-Mahasiswa-Dosen
- ✅ Fixed: KRS dan Nilai filtering by role
- ✅ Enhanced: UI/UX improvements
- ✅ Enhanced: Dokumentasi lengkap

### Version 1.0 (November 2024)
- ✅ Initial release
- ✅ Basic CRUD modules
- ✅ Role-based access control
- ✅ PMB module
- ✅ Payment module
- ✅ KRS & Nilai module

---

## 🚦 Status Implementasi

### ✅ Completed (100%)
- Authentication & Authorization
- Master Data Akademik
- Manajemen Mahasiswa & Dosen
- KRS & Nilai
- Jadwal Kuliah
- Pembayaran
- PMB
- Akreditasi
- User Management
- Profile Management
- Dashboard (all roles)
- Pertemuan Kuliah & Absensi
- Google SSO

### ⚠️ Partial (50-99%)
- Laporan & Export (ada tapi belum lengkap)
- File Upload (ada tapi perlu enhancement)

### ❌ Not Started (0%)
- E-Learning
- Bimbingan Akademik
- Perpustakaan
- Surat Menyurat
- Wisuda
- Beasiswa
- Magang/PKL
- Alumni
- Penelitian
- Skripsi/TA
- Notifikasi Push
- Mobile App
- Integrasi External

---

## 📖 Dokumentasi Tambahan

File dokumentasi lain yang tersedia:

1. **README_SISTER.md** - Penjelasan umum sistem
2. **DATABASE_RELATIONS.md** - Diagram relasi database
3. **PERBAIKAN_RELASI_USER_MAHASISWA.md** - Fix user-mahasiswa relation
4. **JADWAL_KULIAH_MODULE.md** - Dokumentasi modul jadwal
5. **DASHBOARD_MAHASISWA.md** - Dokumentasi dashboard mahasiswa
6. **GOOGLE_OAUTH_SETUP.md** - Setup Google OAuth
7. **FILE_UPLOAD_IMPLEMENTATION.md** - Dokumentasi file upload
8. **DEPLOYMENT.md** - Panduan deployment
9. **SERVER-GUIDE.md** - Setup server guide

---

## 🎉 Kesimpulan

Sistem SISTER versi 2.0 sudah memiliki fitur dasar yang lengkap untuk mengelola sistem informasi akademik. Dengan 6 role berbeda, sistem dapat mengakomodasi kebutuhan dari berbagai tingkat pengguna.

**Kelebihan Sistem Saat Ini**:
- ✅ Role-based access control yang jelas
- ✅ Modular dan scalable
- ✅ UI/UX yang user-friendly
- ✅ Google SSO untuk kemudahan login
- ✅ Dashboard statistik informatif
- ✅ Absensi modern dengan QR code
- ✅ Soft delete untuk data recovery
- ✅ Audit trail (created_by, updated_by, deleted_by)

**Area untuk Improvement**:
- 📌 Tambahkan modul E-Learning
- 📌 Implementasi notifikasi push
- 📌 Mobile app development
- 📌 Payment gateway integration
- 📌 Enhanced reporting & analytics
- 📌 Integrasi dengan PDDIKTI

---

**Developed with ❤️ by SISTER Development Team**  
© 2025 SISTER - Sistem Informasi Akademik Terintegrasi
