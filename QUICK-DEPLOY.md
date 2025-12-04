# 🚀 Quick Deployment Guide

## Pilih Metode Deployment:

### ⚡ Option 1: GitHub Actions (OTOMATIS - RECOMMENDED)

**Kelebihan:** 
- Deploy otomatis setiap push
- Tidak perlu script manual
- Terintegrasi dengan GitHub

**Langkah:**
1. Push ke GitHub
2. Setup secrets di GitHub (lihat DEPLOYMENT.md)
3. Selesai! Setiap push akan otomatis deploy

---

### 💻 Option 2: Script Manual (SIMPLE)

**Windows:**
```bash
# Edit deploy.bat (ubah SERVER_USER, SERVER_HOST, SERVER_PATH)
deploy.bat
```

**Linux/Mac:**
```bash
chmod +x deploy.sh
# Edit deploy.sh (ubah konfigurasi)
./deploy.sh
```

---

### 📁 Option 3: FTP Manual

**Tools:** FileZilla, WinSCP, Cyberduck

1. Connect ke server FTP
2. Upload semua file KECUALI:
   - `.env` (buat baru di server)
   - `vendor/` (run composer install di server)
   - `node_modules/`
   - `storage/` (set permission dulu)
3. Di server:
   ```bash
   composer install --no-dev
   php artisan key:generate
   php artisan migrate --force
   php artisan optimize
   ```

---

## 📋 Checklist Sebelum Deploy:

- [ ] Database sudah dibuat di server
- [ ] File `.env` sudah dikonfigurasi di server
- [ ] PHP 8.2+ terinstall
- [ ] Composer terinstall
- [ ] Web server (Nginx/Apache) sudah dikonfigurasi
- [ ] Domain sudah pointing ke server

---

## 🔗 Links Penting:

- **Dokumentasi Lengkap:** [DEPLOYMENT.md](DEPLOYMENT.md)
- **Laravel Docs:** https://laravel.com/docs/deployment
- **Server Setup:** Lihat section "Setup di Server Production" di DEPLOYMENT.md

---

## ⚙️ Konfigurasi Server (First Time Setup)

```bash
# Clone project
cd /var/www/html
git clone https://github.com/username/sister.git
cd sister

# Install dependencies
composer install --no-dev --optimize-autoloader

# Setup environment
cp .env.example .env
nano .env  # Edit dengan kredensial production

# Generate key
php artisan key:generate

# Run migrations
php artisan migrate --force

# Set permissions
sudo chown -R www-data:www-data .
sudo chmod -R 775 storage bootstrap/cache

# Optimize
php artisan optimize
```

---

## 🛠️ Update Aplikasi (Setelah Deploy)

Jika menggunakan script manual, jalankan saja:
- Windows: `deploy.bat`
- Linux/Mac: `./deploy.sh`

Atau di server:
```bash
cd /var/www/html/sister
git pull
composer install --no-dev
php artisan migrate --force
php artisan optimize
```

---

## 📞 Butuh Bantuan?

Cek file **DEPLOYMENT.md** untuk:
- Panduan lengkap semua metode
- Troubleshooting
- Security checklist
- Konfigurasi server detail
