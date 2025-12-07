# 🚀 Quick Deployment Commands

**SISTER v2.0** - Deployment ke Production Server

---

## 📦 Update yang Tersedia

- ✅ Google OAuth SSO Authentication
- ✅ Enhanced Profile dengan informasi akademik
- ✅ Dokumentasi lengkap sistem
- ✅ Bug fixes dan improvements

**Latest Commit**: `b0a818f5` - "Add server update script for easy deployment"

---

## 🖥️ DEPLOYMENT DI SERVER PRODUCTION

### Method 1: Automatic Script (Recommended)

```bash
# 1. SSH ke server
ssh user@your-server.com

# 2. Masuk ke directory aplikasi
cd /var/www/html/sister

# 3. Run update script
bash server-update.sh
```

Script akan otomatis:
- Pull latest code
- Install dependencies
- Run migration
- Clear & cache
- Restart services

---

### Method 2: Manual Step-by-Step

```bash
# 1. Backup Database (IMPORTANT!)
mysqldump -u root -p sister_db > backup_$(date +%Y%m%d).sql

# 2. Pull latest code
git pull origin main

# 3. Install dependencies
composer install --no-dev --optimize-autoloader

# 4. Run migration (akan tambah google_id & avatar ke tabel users)
php artisan migrate --force

# 5. Update .env dengan Google OAuth credentials
nano .env
# Tambahkan:
# GOOGLE_CLIENT_ID=your-client-id
# GOOGLE_CLIENT_SECRET=your-client-secret
# GOOGLE_REDIRECT_URI="${APP_URL}/auth/google/callback"

# 6. Clear cache
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# 7. Cache untuk production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 8. Fix permissions
sudo chown -R www-data:www-data .
sudo chmod -R 775 storage bootstrap/cache

# 9. Restart services
sudo systemctl restart php8.2-fpm
sudo systemctl restart nginx
```

---

## 🔧 Setup Google OAuth

1. Buka [Google Cloud Console](https://console.cloud.google.com/)
2. Pilih/Buat project "SISTER"
3. **APIs & Services** → **Credentials**
4. Create/Edit **OAuth 2.0 Client ID**
5. Add **Authorized redirect URIs**:
   ```
   https://your-domain.com/auth/google/callback
   ```
6. Salin **Client ID** dan **Client Secret** ke `.env`

**IMPORTANT**: 
- ⚠️ Harus pakai HTTPS (bukan HTTP)
- ⚠️ `APP_URL` di `.env` harus sesuai domain production

---

## ✅ Post-Deployment Checklist

Setelah deployment, test:

- [ ] Website bisa diakses: `https://your-domain.com`
- [ ] Login dengan email/password
- [ ] Login dengan Google SSO
- [ ] Profile mahasiswa → data akademik muncul
- [ ] KRS masih berfungsi
- [ ] Nilai masih berfungsi
- [ ] Jadwal kuliah masih berfungsi

---

## 🔍 Verifikasi Database

```sql
-- Check kolom baru di users
DESCRIBE users;

-- Harus ada: google_id, avatar

-- Check migration status
SELECT * FROM migrations WHERE migration LIKE '%google%';
```

---

## 📊 Check Logs

```bash
# Laravel log
tail -f storage/logs/laravel.log

# Nginx error log
tail -f /var/log/nginx/error.log

# PHP-FPM log
tail -f /var/log/php8.2-fpm.log
```

---

## 🐛 Common Issues

### Issue: "Class 'Socialite' not found"
```bash
composer require laravel/socialite
composer dump-autoload
php artisan config:clear
```

### Issue: "redirect_uri_mismatch" dari Google
- Check `APP_URL` di `.env`
- Check Google Console → Authorized redirect URIs
- Pastikan menggunakan HTTPS
- Run: `php artisan config:clear`

### Issue: Profile mahasiswa tidak tampil data
- Check `users.mahasiswa_id` terisi?
- Check ada semester aktif? `semester.is_active = 1`
- Check mahasiswa punya KRS yang disetujui?

---

## 📚 Dokumentasi Lengkap

Baca dokumentasi detail:

- **DEPLOYMENT_GUIDE.md** - Panduan deployment lengkap
- **DOKUMENTASI_FITUR_LENGKAP.md** - Dokumentasi fitur sistem
- **GOOGLE_OAUTH_SETUP.md** - Setup Google OAuth detail
- **PROFILE_ENHANCEMENT.md** - Dokumentasi profile enhancement

---

## 🔄 Rollback (Jika Ada Masalah)

```bash
# Rollback code
git checkout 65b9f643

# Rollback database
mysql -u root -p sister_db < backup_YYYYMMDD.sql

# Rollback migration
php artisan migrate:rollback --step=1
```

---

## 📞 Support

Jika ada masalah:
1. Check Laravel log
2. Check web server log
3. Enable debug: `APP_DEBUG=true` (sementara)
4. Contact development team

---

**Status**: ✅ Ready for Production Deployment

**Last Updated**: 7 Desember 2025
