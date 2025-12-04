# Dashboard dan Navigasi Role Mahasiswa

## Perubahan yang Dilakukan

### 1. DashboardController.php
**File**: `app/Http/Controllers/DashboardController.php`

**Perubahan**:
- Menambahkan logika untuk mendeteksi role user yang login
- Jika role = mahasiswa, tampilkan dashboard khusus mahasiswa dengan data:
  - Data profil mahasiswa (nama, NIM, prodi, fakultas)
  - IPK (Indeks Prestasi Kumulatif)
  - Total SKS yang sudah lulus
  - Mata kuliah semester aktif dari KRS
  - Total SKS semester ini
  - Daftar jadwal kuliah semester aktif
  - Status mahasiswa (Aktif/Tidak Aktif)
  - Semester keberapa mahasiswa

**Import yang ditambahkan**:
```php
use Illuminate\Support\Facades\Auth;
use App\Models\Mahasiswa;
use App\Models\Krs;
use App\Models\Nilai;
use App\Models\Semester;
```

### 2. dashboard-mahasiswa.blade.php (BARU)
**File**: `resources/views/dashboard-mahasiswa.blade.php`

**Fitur**:
- Welcome card dengan foto profil dan info dasar (NIM, Prodi, Fakultas, Semester Aktif)
- 4 Stats cards:
  1. IPK dengan progress bar
  2. Total SKS Lulus dengan target progress
  3. Mata Kuliah Semester Ini dengan total SKS
  4. Status Mahasiswa dengan badge aktif/non-aktif
- Tabel jadwal kuliah semester ini (dari KRS)
- Empty state jika belum ada KRS
- Quick Actions menu dengan 4 tombol:
  - KRS (Kartu Rencana Studi)
  - Nilai (Nilai & Transkrip)
  - Jadwal Kuliah
  - Profil (Edit Profil Mahasiswa)

### 3. layouts/app.blade.php - Sidebar Navigation
**File**: `resources/views/layouts/app.blade.php`

**Perubahan Navigasi untuk Mahasiswa**:

#### Menu yang DISEMBUNYIKAN untuk mahasiswa:
- ❌ Master Data (Super Admin only)
- ❌ Wilayah (Super Admin only)
- ❌ Data Universitas (Admin only)
- ❌ Akreditasi (Admin only)
- ❌ Recycle Bin (Admin only)
- ❌ Laporan (Admin only)
- ❌ Pengaturan (Super Admin only)

#### Menu yang DITAMPILKAN untuk mahasiswa:
- ✅ Dashboard (halaman utama)
- ✅ **Akademik Saya** (section khusus):
  - Kartu Rencana Studi (KRS)
  - Nilai & Transkrip
  - Jadwal Kuliah
- ✅ **Profil Saya** (section baru):
  - Biodata
  - Ubah Password

### 4. Struktur Navigasi Berdasarkan Role

#### Mahasiswa:
```
📊 Dashboard
📚 Akademik Saya
   ├── 📋 Kartu Rencana Studi (KRS)
   ├── 🏆 Nilai & Transkrip
   └── 📅 Jadwal Kuliah
👤 Profil Saya
   ├── 📝 Biodata
   └── 🔒 Ubah Password
🚪 Logout
```

#### Admin/Dosen (tetap seperti semula):
```
📊 Dashboard
🗄️ Master Data
🗺️ Wilayah
🏛️ Data Universitas
🏅 Akreditasi
📚 Data Akademik
🗑️ Recycle Bin
📊 Laporan
⚙️ Pengaturan
🚪 Logout
```

## Keamanan & Validasi

1. **Role-based Access Control**: Setiap menu dibungkus dengan `@if(Auth::user()->hasRole([...]))`
2. **Data Validation**: Dashboard mahasiswa cek keberadaan data mahasiswa, jika tidak ada akan redirect logout
3. **Relasi Database**: Menggunakan eager loading untuk efisiensi query
4. **Session Management**: Data mahasiswa diambil berdasarkan `mahasiswa_id` dari user yang login

## Perhitungan IPK

Formula yang digunakan:
```php
IPK = Total(Nilai Bobot × SKS) / Total SKS
```

Grade Points:
- A  = 4.0
- A- = 3.7
- B+ = 3.3
- B  = 3.0
- B- = 2.7
- C+ = 2.3
- C  = 2.0
- D  = 1.0
- E  = 0.0

## Testing

### Login sebagai Mahasiswa:
1. Login dengan akun yang memiliki role "mahasiswa"
2. Pastikan `mahasiswa_id` terisi di tabel users
3. Dashboard akan menampilkan informasi:
   - Nama, NIM, Prodi, Fakultas
   - IPK
   - Total SKS Lulus
   - Jadwal kuliah semester aktif
   - Status mahasiswa

### Navigasi yang Terlihat:
- Dashboard
- Akademik Saya (KRS, Nilai, Jadwal)
- Profil Saya (Biodata, Ubah Password)

### Menu yang TIDAK terlihat untuk mahasiswa:
- Master Data, Wilayah, Data Universitas, Akreditasi
- Dosen, Mata Kuliah, Ruang, Kelas (menu manajemen)
- Pendaftaran Mahasiswa, Data Mahasiswa
- Recycle Bin, Laporan, Pengaturan

## Deployment

### File yang Diubah:
1. `app/Http/Controllers/DashboardController.php`
2. `resources/views/layouts/app.blade.php`

### File yang Dibuat:
1. `resources/views/dashboard-mahasiswa.blade.php`

### Command yang Perlu Dijalankan:
```bash
php artisan optimize:clear
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

## Notes

- Dashboard mahasiswa menggunakan data real-time dari database
- Jika tidak ada semester aktif, akan ditampilkan pesan "Tidak ada semester aktif"
- Jika belum ada KRS, akan menampilkan empty state dengan tombol "Isi KRS Sekarang"
- Semester keberapa dihitung otomatis berdasarkan tahun masuk dan bulan sekarang
- Quick Actions memudahkan mahasiswa mengakses fitur utama

## Future Improvements

1. **Profil Mahasiswa**: Buat halaman edit profil mahasiswa
2. **Ubah Password**: Implementasi fitur ubah password
3. **KHS (Kartu Hasil Studi)**: Tampilkan KHS per semester
4. **Transkrip**: Cetak transkrip nilai
5. **Notifikasi**: Notifikasi untuk pengumuman, jadwal, dll
6. **Kalender Akademik**: Tampilkan kalender akademik
7. **Pembayaran**: Integrasi dengan sistem pembayaran
