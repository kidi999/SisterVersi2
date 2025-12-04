# Modul Jadwal Kuliah - Dokumentasi

## Deskripsi
Modul Jadwal Kuliah adalah sistem manajemen penjadwalan perkuliahan dengan fitur:
- Deteksi konflik ruangan otomatis
- Deteksi konflik dosen otomatis  
- Kontrol akses berdasarkan level mata kuliah (Universitas/Fakultas/Prodi)
- Validasi kepemilikan ruangan
- Multi-filter dan pencarian
- Soft delete dengan recycle bin

## Struktur Database

### Tabel: jadwal_kuliah
Kolom utama:
- `kelas_id` - Foreign key ke tabel kelas
- `tahun_akademik_id` - Foreign key ke tahun akademik
- `semester_id` - Foreign key ke semester
- `hari` - Enum: Senin, Selasa, Rabu, Kamis, Jumat, Sabtu
- `jam_mulai` - Format HH:MM
- `jam_selesai` - Format HH:MM
- `ruang_id` - Foreign key ke tabel ruang
- `created_by`, `updated_by`, `deleted_by` - Audit trail
- `created_at`, `updated_at`, `deleted_at` - Timestamps

## Fitur Utama

### 1. Deteksi Konflik Ruangan
Sistem secara otomatis mengecek apakah ruangan sudah digunakan pada hari dan waktu yang sama:
```php
JadwalKuliah::checkRuangConflict($ruangId, $hari, $jamMulai, $jamSelesai, $excludeId);
```

Algoritma:
- Cek jadwal lain di ruangan yang sama
- Pada hari yang sama
- Dengan waktu yang overlap:
  - Jam mulai baru berada di antara jadwal existing
  - Jam selesai baru berada di antara jadwal existing
  - Jadwal baru mencakup seluruh jadwal existing

### 2. Deteksi Konflik Dosen
Sistem mengecek apakah dosen sudah mengajar di kelas lain pada waktu yang sama:
```php
JadwalKuliah::checkDosenConflict($kelasId, $hari, $jamMulai, $jamSelesai, $excludeId);
```

### 3. Kontrol Akses Berdasarkan Level Mata Kuliah

**Mata Kuliah Universitas:**
- Dapat diambil oleh semua prodi di semua fakultas
- Ruangan yang digunakan harus tingkat Universitas

**Mata Kuliah Fakultas:**
- Hanya dapat diambil oleh prodi dalam fakultas yang sama
- Ruangan yang digunakan: tingkat Universitas atau Fakultas yang sama

**Mata Kuliah Prodi:**
- Hanya dapat diambil oleh prodi yang bersangkutan
- Ruangan yang digunakan: tingkat Universitas, Fakultas yang sama, atau Prodi yang sama

### 4. Validasi Kepemilikan Ruangan
```php
$ruang->canBeUsedBy($prodiId, $fakultasId)
```

Validasi:
- Ruang tingkat Universitas → Dapat digunakan semua prodi
- Ruang tingkat Fakultas → Hanya prodi dalam fakultas yang sama
- Ruang tingkat Prodi → Hanya prodi yang bersangkutan

## API Endpoint

### GET /api/available-ruang
Mendapatkan daftar ruangan yang tersedia untuk jadwal tertentu.

**Parameters:**
- `kelas_id` (required) - ID kelas
- `hari` (required) - Hari (Senin-Sabtu)
- `jam_mulai` (required) - Format HH:MM
- `jam_selesai` (required) - Format HH:MM
- `exclude_id` (optional) - ID jadwal yang dikecualikan (untuk edit)

**Response:**
```json
[
  {
    "id": 1,
    "kode_ruang": "A-101",
    "nama_ruang": "Ruang Kuliah A-101",
    "kapasitas": 40
  }
]
```

## Penggunaan

### 1. Persiapan Data

Sebelum membuat jadwal, pastikan data berikut sudah tersedia:
1. **Tahun Akademik** - Buat di menu Master Data > Tahun Akademik
2. **Semester** - Buat di menu Master Data > Semester
3. **Fakultas** - Buat di menu Data Universitas > Fakultas
4. **Program Studi** - Buat di menu Data Universitas > Program Studi
5. **Dosen** - Buat di menu Data Akademik > Dosen
6. **Mata Kuliah** - Buat dengan level yang sesuai (Universitas/Fakultas/Prodi)
7. **Ruang** - Buat dengan kepemilikan yang sesuai
8. **Kelas** - Buat kelas untuk mata kuliah dengan menentukan dosen pengampu

### 2. Membuat Jadwal Baru

1. Masuk ke menu **Data Akademik > Jadwal Kuliah**
2. Klik tombol **Tambah Jadwal**
3. Isi form:
   - **Kelas** - Pilih kelas (kombinasi mata kuliah + dosen)
   - **Tahun Akademik** - Pilih tahun akademik aktif
   - **Semester** - Pilih semester aktif
   - **Hari** - Pilih hari perkuliahan
   - **Jam Mulai** - Tentukan jam mulai (format 24 jam)
   - **Jam Selesai** - Tentukan jam selesai (harus lebih besar dari jam mulai)
   - **Ruangan** - Dropdown akan otomatis menampilkan ruangan yang tersedia

4. Sistem akan otomatis:
   - Memfilter ruangan berdasarkan kepemilikan
   - Mengecualikan ruangan yang bentrok
   - Validasi konflik dosen
   - Validasi konflik ruangan

5. Klik **Simpan**

### 3. Filter dan Pencarian

Filter tersedia:
- **Tahun Akademik** - Filter berdasarkan tahun akademik
- **Semester** - Filter berdasarkan semester
- **Hari** - Filter berdasarkan hari
- **Ruangan** - Filter berdasarkan ruangan
- **Fakultas** - Filter berdasarkan fakultas
- **Program Studi** - Filter berdasarkan prodi
- **Cari** - Pencarian berdasarkan nama MK atau dosen

### 4. Edit Jadwal

1. Klik icon pensil pada jadwal yang ingin diedit
2. Ubah data yang diperlukan
3. Sistem akan mengecualikan jadwal yang sedang diedit dari pengecekan konflik
4. Klik **Update**

### 5. Hapus dan Restore

**Soft Delete:**
- Klik icon trash pada jadwal
- Data akan dipindah ke Recycle Bin
- Dapat di-restore atau dihapus permanen

**Restore:**
- Masuk ke **Recycle Bin > Jadwal Kuliah**
- Klik tombol restore (icon panah balik)
- Data akan kembali aktif

**Hapus Permanen:**
- Masuk ke **Recycle Bin > Jadwal Kuliah**
- Klik tombol hapus permanen (icon X)
- Data tidak dapat dikembalikan

## Role dan Permission

### Super Admin & Admin Universitas
- Dapat menjadwalkan semua mata kuliah
- Akses full CRUD
- Akses recycle bin

### Admin Fakultas
- Dapat menjadwalkan:
  - Mata kuliah fakultas di fakultasnya
  - Mata kuliah universitas
- Akses full CRUD untuk jadwal di fakultasnya

### Admin Prodi
- Dapat menjadwalkan:
  - Mata kuliah prodi-nya
  - Mata kuliah fakultas (dari fakultas yang sama)
  - Mata kuliah universitas
- Akses full CRUD untuk jadwal prodi-nya

### Dosen
- **Hanya dapat melihat** jadwal
- Tidak dapat create/edit/delete

## Validasi dan Error Handling

### Error Messages

**Konflik Ruangan:**
```
Ruangan sudah digunakan pada hari [Hari] jam [Waktu] 
untuk mata kuliah [Nama MK] kelas [Nama Kelas]
```

**Konflik Dosen:**
```
Dosen [Nama Dosen] sudah mengajar mata kuliah [Nama MK] 
pada hari [Hari] jam [Waktu]
```

**Ruangan Tidak Sesuai:**
```
Ruangan ini tidak dapat digunakan untuk prodi/fakultas ini
```

## Best Practices

1. **Perencanaan Jadwal:**
   - Buat jadwal per semester
   - Hindari jadwal di hari Jumat siang (waktu ibadah)
   - Pertimbangkan kapasitas ruangan vs kuota kelas

2. **Pengelolaan Ruangan:**
   - Gunakan ruangan dengan kapasitas sesuai kuota kelas
   - Prioritaskan ruangan prodi untuk MK prodi
   - Gunakan ruangan fakultas untuk MK fakultas

3. **Koordinasi Dosen:**
   - Cek jadwal dosen sebelum membuat jadwal baru
   - Hindari jadwal mengajar berturut-turut di gedung berbeda

4. **Backup Data:**
   - Gunakan soft delete untuk keamanan data
   - Lakukan review berkala di recycle bin
   - Hapus permanen hanya jika yakin

## Troubleshooting

### Problem: Ruangan tidak muncul di dropdown
**Solusi:**
- Pastikan ruangan berstatus "Aktif"
- Cek kepemilikan ruangan vs level mata kuliah
- Pastikan tidak ada konflik waktu

### Problem: Tidak bisa simpan jadwal
**Solusi:**
- Cek validasi form (semua field required harus diisi)
- Jam selesai harus lebih besar dari jam mulai
- Cek pesan error untuk detail konflik

### Problem: Data tidak muncul di index
**Solusi:**
- Cek filter yang aktif
- Reset filter dengan klik tombol "Reset"
- Pastikan ada data tahun akademik dan semester

## File dan Lokasi

### Controller
`app/Http/Controllers/JadwalKuliahController.php`

### Model
`app/Models/JadwalKuliah.php`

### Views
- `resources/views/jadwal-kuliah/index.blade.php`
- `resources/views/jadwal-kuliah/create.blade.php`
- `resources/views/jadwal-kuliah/edit.blade.php`
- `resources/views/jadwal-kuliah/show.blade.php`
- `resources/views/jadwal-kuliah/trash.blade.php`

### Routes
`routes/web.php` (search for "jadwal-kuliah")

### Migration
`database/migrations/2025_12_02_020452_modify_jadwal_kuliah_table_add_comprehensive_fields.php`

## Update Log

### Version 1.0.0 (Desember 2025)
- Initial release
- Conflict detection untuk ruangan dan dosen
- Multi-level access control
- Dynamic available room filtering
- Soft delete dengan audit trail
- Multi-filter dan search
