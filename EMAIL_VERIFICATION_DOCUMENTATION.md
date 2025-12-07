# Dokumentasi Email Verification untuk Pendaftaran PMB

## 📋 Ringkasan Fitur

Sistem verifikasi email untuk pendaftaran mahasiswa baru (PMB) yang memastikan calon mahasiswa menggunakan alamat email yang valid dan aktif.

## 🎯 Fitur Utama

1. **Validasi Email Wajib**: Form pendaftaran memvalidasi format email
2. **Kirim Email Verifikasi Otomatis**: Setelah daftar, sistem mengirim email dengan link verifikasi
3. **Link Verifikasi Unik**: Token SHA-256 yang aman untuk setiap pendaftaran
4. **Expired Token (24 jam)**: Link verifikasi hanya berlaku 24 jam
5. **Resend Email**: Pendaftar bisa request kirim ulang email verifikasi
6. **Status Verifikasi**: Tampilan status email terverifikasi atau belum
7. **Notifikasi Email**: Email template profesional dengan informasi lengkap

## 📂 File yang Dibuat/Diubah

### 1. Migration
**File**: `database/migrations/2025_12_07_090000_add_email_verification_to_pendaftaran_mahasiswa.php`

Menambahkan kolom:
- `email_verification_token` (string, 64 char, nullable, indexed)
- `email_verified_at` (timestamp, nullable)

### 2. Model
**File**: `app/Models/PendaftaranMahasiswa.php`

Update:
- Tambah `email_verification_token` dan `email_verified_at` ke `$fillable`
- Cast `email_verified_at` sebagai datetime

### 3. Notification Class
**File**: `app/Notifications/PendaftaranEmailVerification.php`

Email notification dengan:
- Subject: "Verifikasi Email Pendaftaran"
- Konten: Informasi pendaftaran + tombol verifikasi
- Link verifikasi dengan token unik

### 4. Controller
**File**: `app/Http/Controllers/PmbController.php`

**Method Baru:**

#### `store()` - Modified
- Generate token SHA-256 saat pendaftaran
- Simpan token ke database
- Kirim email verifikasi otomatis
- Redirect ke halaman success dengan pesan "cek email"

#### `verifyEmail($token)` - NEW
```php
Route: GET /pmb/verify-email/{token}
```
- Validasi token
- Cek apakah sudah diverifikasi
- Cek expired (24 jam)
- Update `email_verified_at` jika valid
- Hapus token setelah digunakan
- Tampilkan hasil verifikasi

#### `resendVerification(Request $request)` - NEW
```php
Route: POST /pmb/resend-verification
Parameters: no_pendaftaran, email
```
- Validasi data pendaftaran
- Cek apakah sudah diverifikasi
- Generate token baru
- Kirim email verifikasi baru

### 5. Routes
**File**: `routes/web.php`

Routes tambahan:
```php
Route::get('/pmb/verify-email/{token}', [PmbController::class, 'verifyEmail'])
    ->name('pmb.verify-email');
    
Route::post('/pmb/resend-verification', [PmbController::class, 'resendVerification'])
    ->name('pmb.resend-verification');
```

### 6. Views

#### `resources/views/pmb/verify-result.blade.php` - NEW
Halaman hasil verifikasi dengan 3 kondisi:
- **Success**: Email berhasil diverifikasi
- **Already Verified**: Email sudah diverifikasi sebelumnya
- **Failed/Expired**: Token invalid atau kadaluarsa (dengan form resend)

#### `resources/views/pmb/success.blade.php` - MODIFIED
Update alert informasi:
- Instruksi cek email untuk verifikasi
- Link verifikasi berlaku 24 jam
- Button "Kirim Ulang Email Verifikasi"

#### `resources/views/pmb/status.blade.php` - MODIFIED
Update tampilan email dengan badge:
- ✅ Badge hijau "Terverifikasi" jika `email_verified_at` ada
- ⚠️ Badge kuning "Belum Diverifikasi" jika null
- Button "Kirim Ulang Email Verifikasi" jika belum diverifikasi

#### `resources/views/pmb/form.blade.php` - MODIFIED
Update field email:
- Small text warning: "Penting: Masukkan email yang valid. Link verifikasi akan dikirim ke email ini."

## 🔄 Flow Proses

### 1. Pendaftaran
```
Calon Mahasiswa → Isi Form → Submit
↓
Controller Generate Token SHA-256
↓
Simpan ke DB (status: Pending, email_verified_at: NULL)
↓
Kirim Email Verifikasi Otomatis
↓
Redirect ke Halaman Success
```

### 2. Verifikasi Email
```
Calon Mahasiswa → Cek Email → Klik Link Verifikasi
↓
Controller Validasi Token
↓
Cek Expired (< 24 jam?)
↓
Update email_verified_at = NOW()
↓
Hapus token (set NULL)
↓
Tampilkan Success Page
```

### 3. Resend Email
```
Calon Mahasiswa → Cek Status atau Success Page → Klik "Kirim Ulang"
↓
Input No. Pendaftaran + Email
↓
Controller Validasi Data
↓
Generate Token Baru
↓
Kirim Email Baru
↓
Success Message
```

## 📧 Email Template

### Subject
`Verifikasi Email Pendaftaran - [APP_NAME]`

### Konten
```
Halo [Nama Lengkap],

Terima kasih telah mendaftar di [APP_NAME].

Nomor Pendaftaran Anda: [NO_PENDAFTARAN]
Program Studi: [NAMA_PRODI]

Silakan klik tombol di bawah untuk memverifikasi alamat email Anda:

[Tombol: Verifikasi Email]

Link verifikasi ini akan kadaluarsa dalam 24 jam.
Jika Anda tidak mendaftar, abaikan email ini.

Hormat kami,
Tim [APP_NAME]
```

## 🔐 Keamanan

1. **Token Generation**: 
   - SHA-256 hash
   - Random 60 karakter + email + timestamp
   - Stored sebagai 64 character string

2. **Token Expiry**: 
   - Berlaku 24 jam sejak pendaftaran
   - Validasi di controller

3. **One-Time Use**: 
   - Token dihapus setelah digunakan
   - Tidak bisa digunakan ulang

4. **Email Validation**:
   - Laravel validation `required|email|max:100`
   - Format email valid

## 🧪 Testing Checklist

### Test Pendaftaran
- [ ] Submit form dengan email valid
- [ ] Email verifikasi terkirim
- [ ] Email masuk ke inbox (cek spam juga)
- [ ] Link verifikasi ada di email

### Test Verifikasi
- [ ] Klik link verifikasi dari email
- [ ] Halaman success muncul
- [ ] Status `email_verified_at` terisi di database
- [ ] Token dihapus dari database
- [ ] Klik link kedua kali: "Sudah diverifikasi"

### Test Expired Token
- [ ] Ubah `created_at` pendaftaran menjadi > 24 jam yang lalu
- [ ] Klik link verifikasi
- [ ] Muncul pesan "Token kadaluarsa"
- [ ] Form resend muncul

### Test Resend Email
- [ ] Klik "Kirim Ulang Email Verifikasi"
- [ ] Input no. pendaftaran + email
- [ ] Email baru terkirim
- [ ] Token baru di-generate
- [ ] Link baru berfungsi

### Test Status Page
- [ ] Email belum diverifikasi: badge kuning muncul
- [ ] Button "Kirim Ulang" tersedia
- [ ] Setelah verifikasi: badge hijau muncul
- [ ] Tanggal verifikasi tampil

## 📊 Database Schema

### Tabel: `pendaftaran_mahasiswa`

```sql
-- Kolom baru
email_verification_token VARCHAR(64) NULL,
email_verified_at TIMESTAMP NULL,

-- Index
INDEX idx_email_verification_token (email_verification_token)
```

## ⚙️ Konfigurasi Email

Pastikan `.env` sudah dikonfigurasi:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="${APP_NAME}"
```

**Catatan**: Untuk Gmail, gunakan App Password, bukan password akun.

## 🚀 Deployment Steps

1. **Run Migration**:
```bash
php artisan migrate
```

2. **Clear Cache**:
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

3. **Test Email Configuration**:
```bash
php artisan tinker
>>> Notification::route('mail', 'test@example.com')->notify(new \App\Notifications\PendaftaranEmailVerification(\App\Models\PendaftaranMahasiswa::first(), 'https://test.com'));
```

4. **Setup Queue (Optional)**:
Notification menggunakan `ShouldQueue`, jadi perlu queue worker:
```bash
php artisan queue:work
```

Atau ubah di `PendaftaranEmailVerification.php` hapus `implements ShouldQueue` jika tidak pakai queue.

## 🐛 Troubleshooting

### Email tidak terkirim
- Cek konfigurasi MAIL_* di `.env`
- Cek log: `storage/logs/laravel.log`
- Test koneksi SMTP
- Cek quota email provider

### Link verifikasi 404
- Cek route sudah terdaftar: `php artisan route:list | grep verify`
- Clear route cache: `php artisan route:clear`

### Token selalu expired
- Cek timezone server vs aplikasi
- Cek `config/app.php` timezone setting
- Cek `created_at` di database

### Email masuk spam
- Tambahkan SPF record di DNS domain
- Setup DKIM authentication
- Gunakan domain email yang sama dengan website

## 📝 Future Enhancements

1. **Multi-language Support**: Email dalam bahasa Indonesia dan Inggris
2. **Email Queue Dashboard**: Monitor email terkirim/gagal
3. **Reminder Email**: Auto-send reminder jika belum verifikasi dalam 12 jam
4. **Phone Verification**: Tambahkan OTP SMS sebagai alternatif
5. **Webhook Integration**: Notifikasi ke external system saat verifikasi
6. **Analytics**: Track verification rate, expired tokens, resend count

## 👥 Support

Jika ada masalah dengan email verification:
1. Cek documentation ini
2. Lihat log file: `storage/logs/laravel.log`
3. Test email configuration
4. Hubungi developer

---

**Created**: 2025-12-07
**Version**: 1.0
**Author**: GitHub Copilot
