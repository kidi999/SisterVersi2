# 🚀 Panduan Deployment SISTER ke sister.unic.ac.id

## 📋 Informasi Server

```
Domain      : sister.unic.ac.id
IP Server   : 103.241.192.78
OS          : CentOS + cPanel
Path        : /home/unic/sister
FTP User    : unic
FTP Pass    : qwert12345
DB Name     : unic_sister
DB User     : unic_sister
DB Pass     : qwert12345
```

---

## 🎯 Setup Awal (Pertama Kali)

### 1. Jalankan Setup Script

```bash
setup-server.bat
```

Script ini akan memandu Anda step-by-step untuk:
- Setup direktori server
- Konfigurasi .env
- Upload file
- Install dependencies
- Setup database
- Konfigurasi domain di cPanel

### 2. Manual Setup (Alternatif)

#### A. Upload File .env

1. Buka FileZilla atau FTP client
2. Connect ke:
   - Host: `103.241.192.78`
   - User: `unic`
   - Pass: `qwert12345`
3. Upload file `.env.production` → rename jadi `.env`
4. Edit di server, pastikan isinya:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://sister.unic.ac.id
DB_DATABASE=unic_sister
DB_USERNAME=unic_sister
DB_PASSWORD=qwert12345
```

#### B. Upload Semua File

Gunakan salah satu cara:

**Cara 1 - Script Otomatis:**
```bash
upload.bat
```

**Cara 2 - FTP Manual:**
Upload semua file KECUALI:
- `.env` (sudah dibuat di server)
- `vendor/` (akan di-generate)
- `node_modules/`
- `storage/logs/`
- `.git/`
- `*.log`

#### C. Setup di Server Terminal

Login ke Terminal cPanel atau SSH:
```bash
ssh unic@103.241.192.78
```

Jalankan perintah:
```bash
cd /home/unic/sister

# Generate APP_KEY
php artisan key:generate --force

# Install Composer dependencies
composer install --no-dev --optimize-autoloader

# Setup database
php artisan migrate --force

# Seed data admin (jika ada)
php artisan db:seed --force

# Set permissions
chmod -R 775 storage bootstrap/cache
find storage -type f -exec chmod 664 {} \;
find storage -type d -exec chmod 775 {} \;

# Optimize aplikasi
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
php artisan optimize
```

#### D. Setup Domain di cPanel

1. Login cPanel: `https://103.241.192.78:2083`
2. Username: `unic`
3. Password: `qwert12345`
4. Cari menu **"Domains"** atau **"Addon Domains"**
5. Tambah domain:
   - Domain: `sister.unic.ac.id`
   - Document Root: `/home/unic/sister/public` ⚠️ **HARUS ke folder PUBLIC!**
6. Klik **"Add Domain"**

#### E. Setup SSL (HTTPS)

1. Di cPanel, cari **"SSL/TLS Status"** atau **"Let's Encrypt"**
2. Install SSL untuk domain `sister.unic.ac.id`
3. Enable **"Force HTTPS Redirect"**

#### F. Test Website

Buka browser:
```
https://sister.unic.ac.id
```

---

## 🔄 Update Aplikasi (Setelah Setup Awal)

Setiap kali ada perubahan code:

### Cara Paling Mudah:

```bash
upload.bat
```

Script akan:
1. ✅ Package semua perubahan
2. ✅ Upload ke server via FTP
3. ✅ Memberikan instruksi perintah server

Setelah upload selesai, jalankan di server terminal:
```bash
cd /home/unic/sister
php artisan migrate --force
php artisan optimize
```

### Cara Manual (FTP):

1. Buka FileZilla
2. Connect ke server (info di atas)
3. Upload file yang berubah
4. Jalankan di terminal server:
   ```bash
   cd /home/unic/sister
   php artisan config:clear
   php artisan cache:clear
   php artisan migrate --force
   php artisan optimize
   ```

---

## 📁 Struktur File di Server

```
/home/unic/sister/
├── app/
├── bootstrap/
├── config/
├── database/
├── public/          ← Document root domain di-set ke sini!
│   ├── index.php
│   ├── css/
│   └── js/
├── resources/
├── routes/
├── storage/
│   ├── app/
│   ├── framework/
│   └── logs/        ← Cek error di laravel.log
├── vendor/          ← Di-generate oleh composer
├── .env             ← File konfigurasi (JANGAN di-upload dari local!)
├── artisan
└── composer.json
```

---

## 🔧 Perintah Server yang Sering Digunakan

### Cek Error Log:
```bash
tail -f /home/unic/sister/storage/logs/laravel.log
```

### Clear All Cache:
```bash
cd /home/unic/sister
php artisan optimize:clear
```

### Run Migration:
```bash
php artisan migrate --force
```

### Rollback Migration:
```bash
php artisan migrate:rollback --force
```

### Fresh Database (HATI-HATI - Hapus semua data!):
```bash
php artisan migrate:fresh --seed --force
```

### Fix Permissions:
```bash
chmod -R 775 /home/unic/sister/storage
chmod -R 775 /home/unic/sister/bootstrap/cache
```

### Update Composer Dependencies:
```bash
cd /home/unic/sister
composer update --no-dev
```

---

## 🐛 Troubleshooting

### Error: "500 Internal Server Error"

**Cek 1 - File .env:**
```bash
cat /home/unic/sister/.env
```
Pastikan APP_KEY sudah terisi

**Cek 2 - Permissions:**
```bash
chmod -R 775 /home/unic/sister/storage
chmod -R 775 /home/unic/sister/bootstrap/cache
```

**Cek 3 - Error Log:**
```bash
tail -50 /home/unic/sister/storage/logs/laravel.log
```

### Error: "No input file specified"

**Penyebab:** Document root tidak mengarah ke folder `public`

**Solusi:**
1. Login cPanel
2. Domains → Manage Domain
3. Ubah Document Root ke: `/home/unic/sister/public`

### Error: "SQLSTATE[HY000] [1045] Access denied"

**Penyebab:** Koneksi database salah

**Solusi:** Cek file `.env` di server:
```bash
nano /home/unic/sister/.env
```

Pastikan:
```env
DB_HOST=localhost
DB_DATABASE=unic_sister
DB_USERNAME=unic_sister
DB_PASSWORD=qwert12345
```

### Error: "Class not found"

**Penyebab:** Autoload outdated

**Solusi:**
```bash
cd /home/unic/sister
composer dump-autoload
php artisan optimize
```

### Error: "Session store not set"

**Solusi:**
```bash
php artisan session:table
php artisan migrate
```

### Website Lambat / Cache Issue

```bash
cd /home/unic/sister
php artisan optimize:clear
php artisan optimize
```

---

## 🔐 Keamanan

### File Permissions yang Benar:

```bash
# Folders
chmod 755 /home/unic/sister
chmod 755 /home/unic/sister/public
chmod 775 /home/unic/sister/storage
chmod 775 /home/unic/sister/bootstrap/cache

# Files
find /home/unic/sister -type f -exec chmod 644 {} \;
find /home/unic/sister/storage -type f -exec chmod 664 {} \;
```

### File yang TIDAK Boleh Accessible dari Web:

File `.env` HARUS di luar document root atau di-protect.

Di cPanel, cek `.htaccess` di `/home/unic/sister/public/`:
```apache
<FilesMatch "^\.env">
    Order allow,deny
    Deny from all
</FilesMatch>
```

### Backup Database Rutin:

**Via cPanel:**
1. cPanel → Backup → Download Database Backup
2. Pilih database: `unic_sister`

**Via Command Line:**
```bash
mysqldump -u unic_sister -p unic_sister > backup_$(date +%Y%m%d).sql
```

---

## 📞 Quick Commands Reference

```bash
# Login SSH
ssh unic@103.241.192.78

# Masuk ke folder project
cd /home/unic/sister

# Update aplikasi
php artisan migrate --force
php artisan optimize

# Cek status
php artisan about

# Lihat error log
tail -f storage/logs/laravel.log

# Clear cache
php artisan optimize:clear
```

---

## 📝 Workflow Harian

**Ketika ada perubahan code:**

1. **Di komputer lokal:**
   ```bash
   upload.bat
   ```

2. **Di server terminal:**
   ```bash
   cd /home/unic/sister
   php artisan migrate --force
   php artisan optimize
   ```

3. **Test website:**
   ```
   https://sister.unic.ac.id
   ```

**Selesai!** 🎉

---

## 🆘 Support

Jika ada masalah:

1. **Cek error log:** `tail -f storage/logs/laravel.log`
2. **Cek web server log:** Di cPanel → Error Logs
3. **Test koneksi database:** `php artisan tinker` lalu `DB::connection()->getPdo()`
4. **Verify permissions:** `ls -la storage/`

---

**Server siap digunakan! Gunakan `upload.bat` untuk update cepat setiap ada perubahan.** 🚀
