@echo off
REM ========================================
REM   Setup Server Production Pertama Kali
REM   sister.unic.ac.id
REM ========================================

echo.
echo ========================================
echo   SISTER - First Time Server Setup
echo ========================================
echo.
echo Script ini akan membantu setup server production pertama kali
echo.
echo Server: sister.unic.ac.id (103.241.192.78)
echo Path: /home/unic/sister
echo.

pause

echo.
echo ========================================
echo   INSTRUKSI SETUP SERVER
echo ========================================
echo.
echo LANGKAH 1: Login ke SSH/Terminal cPanel
echo   - Buka Terminal di cPanel
echo   - Atau gunakan SSH: ssh unic@103.241.192.78
echo.
echo LANGKAH 2: Buat direktori project (jika belum ada)
echo.
echo   mkdir -p /home/unic/sister
echo   cd /home/unic/sister
echo.
echo LANGKAH 3: Upload file .env.production ke server
echo   - Buka FileZilla atau FTP client
echo   - Connect ke: 103.241.192.78
echo   - Username: unic
echo   - Password: qwert12345
echo   - Upload file: .env.production
echo   - Rename menjadi: .env
echo.

pause

echo.
echo LANGKAH 4: Edit file .env di server
echo.
echo   nano /home/unic/sister/.env
echo.
echo   Pastikan isi nya:
echo   - APP_ENV=production
echo   - APP_DEBUG=false
echo   - APP_URL=https://sister.unic.ac.id
echo   - DB_DATABASE=unic_sister
echo   - DB_USERNAME=unic_sister
echo   - DB_PASSWORD=qwert12345
echo.
echo   Save dengan: Ctrl+O, Enter, Ctrl+X
echo.

pause

echo.
echo LANGKAH 5: Upload semua file project
echo.
echo   Gunakan FileZilla atau jalankan:
echo   deploy-direct.bat
echo.
echo   File/Folder yang TIDAK perlu di-upload:
echo   - .env (sudah ada)
echo   - vendor/ (akan di-generate)
echo   - node_modules/
echo   - storage/logs/
echo   - .git/
echo.

pause

echo.
echo LANGKAH 6: Jalankan perintah di Terminal Server
echo.
echo cd /home/unic/sister
echo.
echo # Generate APP_KEY
echo php artisan key:generate --force
echo.
echo # Install dependencies
echo composer install --no-dev --optimize-autoloader
echo.
echo # Setup database
echo php artisan migrate --force
echo.
echo # Seed data (jika diperlukan)
echo php artisan db:seed --force
echo.
echo # Set permissions
echo chmod -R 775 storage bootstrap/cache
echo find storage -type f -exec chmod 664 {} \;
echo find storage -type d -exec chmod 775 {} \;
echo.
echo # Clear dan optimize
echo php artisan config:clear
echo php artisan cache:clear
echo php artisan view:clear
echo php artisan route:clear
echo php artisan optimize
echo.

pause

echo.
echo ========================================
echo   KONFIGURASI WEB SERVER (cPanel)
echo ========================================
echo.
echo LANGKAH 7: Setup Domain di cPanel
echo.
echo 1. Login ke cPanel (biasanya: https://103.241.192.78:2083)
echo 2. Cari "Domains" atau "Addon Domains"
echo 3. Tambah domain: sister.unic.ac.id
echo 4. Document Root: /home/unic/sister/public
echo.
echo PENTING: Document root harus ke folder PUBLIC!
echo.

pause

echo.
echo LANGKAH 8: Setup SSL (HTTPS)
echo.
echo 1. Di cPanel, cari "SSL/TLS" atau "Let's Encrypt"
echo 2. Install SSL untuk domain: sister.unic.ac.id
echo 3. Enable Force HTTPS
echo.

pause

echo.
echo LANGKAH 9: Test Website
echo.
echo Buka browser dan akses:
echo https://sister.unic.ac.id
echo.
echo Jika muncul error 500:
echo - Cek file .env sudah benar
echo - Cek permissions storage/ dan bootstrap/cache/
echo - Cek error log: tail -f storage/logs/laravel.log
echo.
echo Jika muncul "No input file specified":
echo - Document root salah, harus ke /public
echo.

pause

echo.
echo ========================================
echo   Setup Selesai!
echo ========================================
echo.
echo Untuk update aplikasi selanjutnya:
echo 1. Jalankan: deploy-direct.bat
echo 2. Login ke server terminal
echo 3. Jalankan: php artisan migrate --force
echo 4. Jalankan: php artisan optimize
echo.
echo Simpan file ini untuk referensi!
echo.

pause
