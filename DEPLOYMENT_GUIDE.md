# 🚀 DEPLOYMENT GUIDE - SISTER v2.0

**Tanggal**: 7 Desember 2025  
**Version**: 2.0  
**Commit**: f19ab7d3

---

## 📋 Ringkasan Update

Update ini mencakup:
- ✅ Google OAuth SSO Authentication
- ✅ Enhanced Profile dengan informasi akademik mahasiswa
- ✅ Dokumentasi lengkap fitur sistem
- ✅ Profile enhancement untuk semua role
- ✅ Bug fixes dan improvements

---

## 🔧 LANGKAH DEPLOYMENT DI SERVER

### Step 1: Backup Database (PENTING!)

```bash
# Login ke server
ssh user@your-server.com

# Backup database
mysqldump -u root -p sister_db > backup_sister_$(date +%Y%m%d_%H%M%S).sql

# Atau via phpMyAdmin: Export database sister_db
```

### Step 2: Backup Files Lama

```bash
# Masuk ke directory aplikasi
cd /path/to/sister

# Backup files lama
cp -r . ../sister_backup_$(date +%Y%m%d_%H%M%S)
```

### Step 3: Pull Update dari GitHub

```bash
# Pull latest changes
git pull origin main

# Atau jika ada conflict, force pull:
git fetch origin
git reset --hard origin/main
```

**Output yang diharapkan**:
```
From https://github.com/kidi999/SisterVersi2
   65b9f643..f19ab7d3  main -> main
Updating 65b9f643..f19ab7d3
Fast-forward
 17 files changed, 3343 insertions(+), 42 deletions(-)
 create mode 100644 DOKUMENTASI_FITUR_LENGKAP.md
 create mode 100644 GOOGLE_OAUTH_SETUP.md
 create mode 100644 PROFILE_ENHANCEMENT.md
 create mode 100644 app/Http/Controllers/Auth/GoogleController.php
 create mode 100644 app/Http/Controllers/ProfileController.php
 create mode 100644 database/migrations/2025_12_07_053920_add_google_id_to_users_table.php
 ...
```

### Step 4: Install/Update Dependencies

```bash
# Update composer dependencies
composer install --no-dev --optimize-autoloader

# Jika Laravel Socialite belum terinstall, akan otomatis terinstall
```

**Packages yang akan diinstall**:
- `laravel/socialite` v5.23.2
- Dependencies: firebase/php-jwt, league/oauth1-client, phpseclib/phpseclib

### Step 5: Update Environment Variables

Edit file `.env`:

```bash
nano .env
# atau
vim .env
```

**Tambahkan konfigurasi Google OAuth**:

```env
# Google OAuth Configuration
GOOGLE_CLIENT_ID=your-google-client-id-here.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=your-google-client-secret-here
GOOGLE_REDIRECT_URI="${APP_URL}/auth/google/callback"
```

**PENTING**: Pastikan `APP_URL` sudah sesuai dengan domain production:
```env
APP_URL=https://your-domain.com
```

**Jangan gunakan**:
```env
# ❌ JANGAN untuk production
APP_URL=http://127.0.0.1:8000
APP_URL=http://sister.test
```

### Step 6: Setup Google Cloud Console untuk Production

1. Buka [Google Cloud Console](https://console.cloud.google.com/)
2. Pilih project "SISTER" atau buat baru
3. Masuk ke **APIs & Services** → **Credentials**
4. Edit OAuth 2.0 Client ID yang sudah ada atau buat baru
5. Tambahkan **Authorized redirect URIs**:
   ```
   https://your-domain.com/auth/google/callback
   ```
6. Tambahkan **Authorized JavaScript origins**:
   ```
   https://your-domain.com
   ```
7. **PENTING**: Pastikan menggunakan HTTPS, bukan HTTP!
8. Salin **Client ID** dan **Client Secret** ke `.env`

### Step 7: Run Database Migration

```bash
# Jalankan migrasi baru
php artisan migrate

# Jika ada pertanyaan, ketik: yes
```

**Migration yang akan dijalankan**:
```
2025_12_07_053920_add_google_id_to_users_table.php
```

**Perubahan database**:
- Tambah kolom `google_id` (string, nullable, unique) di tabel `users`
- Tambah kolom `avatar` (string, nullable) di tabel `users`

**Verifikasi migration berhasil**:
```bash
php artisan migrate:status
```

### Step 8: Clear All Cache

```bash
# Clear semua cache
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Optimize untuk production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Step 9: Set Permissions (Linux/Ubuntu)

```bash
# Set ownership
sudo chown -R www-data:www-data /path/to/sister

# Set directory permissions
sudo find /path/to/sister -type d -exec chmod 755 {} \;

# Set file permissions
sudo find /path/to/sister -type f -exec chmod 644 {} \;

# Set storage and cache writable
sudo chmod -R 775 /path/to/sister/storage
sudo chmod -R 775 /path/to/sister/bootstrap/cache
```

### Step 10: Restart Services

```bash
# Restart PHP-FPM
sudo systemctl restart php8.2-fpm
# atau
sudo service php8.2-fpm restart

# Restart Nginx
sudo systemctl restart nginx
# atau
sudo service nginx restart

# Restart Apache (jika pakai Apache)
sudo systemctl restart apache2
# atau
sudo service apache2 restart
```

### Step 11: Test Aplikasi

1. **Buka browser**: `https://your-domain.com`
2. **Test login biasa**: Email + Password
3. **Test Google SSO**:
   - Klik "Login dengan Google"
   - Pilih akun Google
   - Harus redirect ke dashboard
4. **Test profile mahasiswa**:
   - Login sebagai mahasiswa
   - Klik dropdown user → "Profil Saya"
   - Verifikasi data akademik muncul
5. **Test modul lain**: KRS, Nilai, Jadwal

---

## 🔍 VERIFIKASI DEPLOYMENT

### Checklist Post-Deployment

- [ ] ✅ Website bisa diakses
- [ ] ✅ Login dengan email/password berfungsi
- [ ] ✅ Login dengan Google SSO berfungsi
- [ ] ✅ Profile mahasiswa menampilkan data akademik
- [ ] ✅ KRS masih berfungsi normal
- [ ] ✅ Nilai masih berfungsi normal
- [ ] ✅ Jadwal kuliah masih berfungsi normal
- [ ] ✅ Dashboard mahasiswa masih berfungsi
- [ ] ✅ Tidak ada error di log

### Check Database Migration

```sql
-- Login ke MySQL
mysql -u root -p sister_db

-- Check kolom baru di users
DESCRIBE users;

-- Harus ada kolom:
-- google_id (varchar, nullable)
-- avatar (varchar, nullable)

-- Check apakah ada user dengan google_id
SELECT id, name, email, google_id, avatar FROM users WHERE google_id IS NOT NULL;
```

### Check Laravel Logs

```bash
# Check log untuk error
tail -f storage/logs/laravel.log

# Atau
less storage/logs/laravel.log
```

---

## 🐛 TROUBLESHOOTING

### Error 1: "Class 'Socialite' not found"

**Solusi**:
```bash
composer require laravel/socialite
composer dump-autoload
php artisan config:clear
```

### Error 2: "SQLSTATE[42S21]: Column already exists: google_id"

**Solusi**: Migrasi sudah pernah dijalankan
```bash
php artisan migrate:status
# Jika sudah ada, skip migration
```

### Error 3: "Google OAuth Error 400: redirect_uri_mismatch"

**Solusi**:
1. Check `.env` → `APP_URL` sudah benar?
2. Check Google Console → Authorized redirect URIs sudah ditambahkan?
3. Harus: `https://your-domain.com/auth/google/callback`
4. Clear config: `php artisan config:clear`

### Error 4: "Access blocked: Authorization Error" dari Google

**Solusi**:
1. OAuth consent screen masih dalam mode "Testing"
2. Tambahkan email user sebagai "Test users" di Google Console
3. Atau publish app untuk production (memerlukan verifikasi Google)

### Error 5: Profile mahasiswa tidak menampilkan data akademik

**Cek**:
1. User sudah linked ke mahasiswa? `users.mahasiswa_id` terisi?
2. Mahasiswa sudah punya KRS yang disetujui?
3. Ada semester aktif? Check `semester.is_active = 1`
4. Check log: `tail -f storage/logs/laravel.log`

### Error 6: "500 Internal Server Error"

**Solusi**:
```bash
# Enable debug mode sementara
# Edit .env
APP_DEBUG=true

# Reload page, lihat error message detail
# Setelah fix, kembalikan:
APP_DEBUG=false
```

### Error 7: Permission denied di storage/

**Solusi**:
```bash
sudo chmod -R 775 storage/
sudo chown -R www-data:www-data storage/
```

---

## 📊 FILES YANG BERUBAH

### New Files (17 files)
1. `DOKUMENTASI_FITUR_LENGKAP.md` - Dokumentasi lengkap fitur sistem
2. `GOOGLE_OAUTH_SETUP.md` - Panduan setup Google OAuth
3. `PROFILE_ENHANCEMENT.md` - Dokumentasi enhancement profile
4. `app/Http/Controllers/Auth/GoogleController.php` - OAuth controller
5. `app/Http/Controllers/ProfileController.php` - Profile controller
6. `database/migrations/2025_12_07_053920_add_google_id_to_users_table.php` - Migration
7. `resources/views/profile/edit.blade.php` - Profile view
8. `resources/views/profile/edit-password.blade.php` - Change password view

### Modified Files (9 files)
1. `app/Models/User.php` - Update getRoleNames()
2. `composer.json` - Add laravel/socialite
3. `composer.lock` - Lock dependencies
4. `config/services.php` - Google OAuth config
5. `resources/views/auth/login.blade.php` - Add Google button
6. `resources/views/layouts/app.blade.php` - Update navbar dropdown
7. `routes/web.php` - Add Google OAuth routes

---

## 🔐 KEAMANAN

### Environment Variables yang Sensitif

**JANGAN commit ke Git**:
- `GOOGLE_CLIENT_ID`
- `GOOGLE_CLIENT_SECRET`
- `DB_PASSWORD`
- `APP_KEY`

File `.env` sudah ada di `.gitignore`

### Production Security Checklist

- [ ] `APP_DEBUG=false`
- [ ] `APP_ENV=production`
- [ ] HTTPS enabled (SSL certificate installed)
- [ ] Google OAuth menggunakan HTTPS redirect URI
- [ ] Database password strong
- [ ] File permissions correct (755 for directories, 644 for files)
- [ ] Storage writable oleh web server

---

## 📞 SUPPORT

Jika ada masalah saat deployment:

1. **Check Laravel Log**: `storage/logs/laravel.log`
2. **Check Web Server Log**: 
   - Nginx: `/var/log/nginx/error.log`
   - Apache: `/var/log/apache2/error.log`
3. **Check PHP Error Log**: `/var/log/php8.2-fpm.log`
4. **Enable Debug Mode**: Set `APP_DEBUG=true` di `.env` (sementara)

---

## 📝 ROLLBACK (Jika Terjadi Masalah)

Jika deployment bermasalah dan perlu rollback:

### Rollback Database

```bash
# Restore dari backup
mysql -u root -p sister_db < backup_sister_YYYYMMDD_HHMMSS.sql
```

### Rollback Code

```bash
# Kembali ke commit sebelumnya
git log --oneline
git checkout 65b9f643

# Atau restore dari backup
rm -rf /path/to/sister/*
cp -r /path/to/sister_backup_YYYYMMDD_HHMMSS/* /path/to/sister/
```

### Rollback Migration

```bash
# Rollback 1 migration terakhir
php artisan migrate:rollback --step=1

# Akan drop kolom google_id dan avatar dari users
```

---

## ✅ KESIMPULAN

Setelah deployment selesai:

1. ✅ Aplikasi SISTER v2.0 sudah running di production
2. ✅ Google OAuth SSO aktif dan berfungsi
3. ✅ Profile mahasiswa menampilkan data akademik lengkap
4. ✅ Semua modul lama tetap berfungsi normal
5. ✅ Database sudah update dengan kolom baru

**Selamat! Deployment berhasil! 🎉**

---

**Catatan**: Dokumen ini dibuat untuk server Linux/Ubuntu dengan Nginx/Apache dan PHP 8.2. Sesuaikan command jika menggunakan OS atau web server berbeda.

**Support**: Jika ada pertanyaan, hubungi tim development.
