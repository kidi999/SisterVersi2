# Manual Upload Guide - SISTER to Production
# ============================================

Write-Host "`n========================================" -ForegroundColor Cyan
Write-Host "   PANDUAN UPLOAD MANUAL KE SERVER" -ForegroundColor Cyan
Write-Host "========================================`n" -ForegroundColor Cyan

Write-Host "SERVER INFO:" -ForegroundColor Yellow
Write-Host "Host: 103.241.192.78" -ForegroundColor White
Write-Host "FTP User: unic" -ForegroundColor White
Write-Host "FTP Pass: qwert12345" -ForegroundColor White
Write-Host "Path: /home/unic/sister" -ForegroundColor White
Write-Host ""

Write-Host "========================================" -ForegroundColor Green
Write-Host "   CARA 1: UPLOAD VIA FILEZILLA (RECOMMENDED)" -ForegroundColor Green
Write-Host "========================================`n" -ForegroundColor Green

Write-Host "1. Download FileZilla Client:" -ForegroundColor Yellow
Write-Host "   https://filezilla-project.org/download.php?type=client`n" -ForegroundColor Cyan

Write-Host "2. Install dan buka FileZilla`n" -ForegroundColor Yellow

Write-Host "3. Connect ke server:" -ForegroundColor Yellow
Write-Host "   - Klik 'File' > 'Site Manager'" -ForegroundColor White
Write-Host "   - Klik 'New Site'" -ForegroundColor White
Write-Host "   - Isi detail:" -ForegroundColor White
Write-Host "     Protocol: FTP" -ForegroundColor Cyan
Write-Host "     Host: 103.241.192.78" -ForegroundColor Cyan
Write-Host "     Port: 21" -ForegroundColor Cyan
Write-Host "     Encryption: Use explicit FTP over TLS if available" -ForegroundColor Cyan
Write-Host "     Logon Type: Normal" -ForegroundColor Cyan
Write-Host "     User: unic" -ForegroundColor Cyan
Write-Host "     Password: qwert12345" -ForegroundColor Cyan
Write-Host "   - Klik 'Connect'`n" -ForegroundColor White

Write-Host "4. Di panel kanan FileZilla, navigasi ke:" -ForegroundColor Yellow
Write-Host "   /home/unic/sister`n" -ForegroundColor Cyan

Write-Host "5. Di panel kiri FileZilla, navigasi ke folder project ini:" -ForegroundColor Yellow
Write-Host "   $PSScriptRoot`n" -ForegroundColor Cyan

Write-Host "6. SELECT SEMUA file dan folder KECUALI:" -ForegroundColor Yellow
Write-Host "   ❌ .git (folder)" -ForegroundColor Red
Write-Host "   ❌ .env (file - nanti dibuat manual di server)" -ForegroundColor Red
Write-Host "   ❌ node_modules (folder)" -ForegroundColor Red
Write-Host "   ❌ vendor (folder)" -ForegroundColor Red
Write-Host "   ❌ storage/logs (isi folder)" -ForegroundColor Red
Write-Host "   ❌ *.log (file)" -ForegroundColor Red
Write-Host "   ❌ *.bat (file)" -ForegroundColor Red
Write-Host "   ❌ *.md (file)" -ForegroundColor Red
Write-Host "   ❌ *.zip (file)" -ForegroundColor Red
Write-Host ""
Write-Host "   ✅ Upload semua yang lain!`n" -ForegroundColor Green

Write-Host "7. Klik kanan > Upload" -ForegroundColor Yellow
Write-Host "   (Tunggu sampai selesai - mungkin 5-10 menit)`n" -ForegroundColor White

Write-Host "========================================" -ForegroundColor Green
Write-Host "   CARA 2: UPLOAD VIA CPANEL FILE MANAGER" -ForegroundColor Green
Write-Host "========================================`n" -ForegroundColor Green

Write-Host "1. Login cPanel:" -ForegroundColor Yellow
Write-Host "   https://103.241.192.78:2083" -ForegroundColor Cyan
Write-Host "   User: unic" -ForegroundColor White
Write-Host "   Pass: qwert12345`n" -ForegroundColor White

Write-Host "2. Buka 'File Manager'`n" -ForegroundColor Yellow

Write-Host "3. Navigasi ke folder: /home/unic/sister`n" -ForegroundColor Yellow

Write-Host "4. Klik 'Upload' di toolbar atas`n" -ForegroundColor Yellow

Write-Host "5. Buat ZIP file dulu di komputer:" -ForegroundColor Yellow
Write-Host "   - Buka folder project: $PSScriptRoot" -ForegroundColor White
Write-Host "   - Select semua KECUALI .git, node_modules, vendor, .env" -ForegroundColor White
Write-Host "   - Klik kanan > Send to > Compressed (zipped) folder" -ForegroundColor White
Write-Host "   - Nama: sister_upload.zip`n" -ForegroundColor Cyan

Write-Host "6. Upload file sister_upload.zip ke cPanel File Manager`n" -ForegroundColor Yellow

Write-Host "7. Setelah upload selesai:" -ForegroundColor Yellow
Write-Host "   - Klik kanan file sister_upload.zip" -ForegroundColor White
Write-Host "   - Pilih 'Extract'" -ForegroundColor White
Write-Host "   - Extract ke: /home/unic/sister" -ForegroundColor White
Write-Host "   - Klik 'Extract Files'`n" -ForegroundColor White

Write-Host "========================================" -ForegroundColor Green
Write-Host "   SETELAH FILE TERUPLOAD" -ForegroundColor Green
Write-Host "========================================`n" -ForegroundColor Green

Write-Host "1. Buka Terminal di cPanel atau SSH:" -ForegroundColor Yellow
Write-Host "   ssh unic@103.241.192.78`n" -ForegroundColor Cyan

Write-Host "2. Cek apakah file sudah ada:" -ForegroundColor Yellow
Write-Host @"
   cd /home/unic/sister
   ls -la
   
   # Harus terlihat file:
   # - artisan
   # - composer.json
   # - package.json
   # - folder: app, bootstrap, config, database, public, resources, routes, storage, tests
"@ -ForegroundColor White
Write-Host ""

Write-Host "3. Buat file .env:" -ForegroundColor Yellow
Write-Host @"
   cd /home/unic/sister
   nano .env
   
   # Copy paste isi dari .env.production yang ada di folder project
   # Atau ketik manual:
"@ -ForegroundColor White
Write-Host ""

Write-Host @"
APP_NAME="SISTER UNIC"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://sister.unic.ac.id

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=unic_sister
DB_USERNAME=unic_sister
DB_PASSWORD=qwert12345

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_DOMAIN=sister.unic.ac.id
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax

CACHE_STORE=database
QUEUE_CONNECTION=database
"@ -ForegroundColor Cyan

Write-Host "`n   Save: Ctrl+O, Enter, Ctrl+X`n" -ForegroundColor White

Write-Host "4. Generate APP_KEY:" -ForegroundColor Yellow
Write-Host "   php artisan key:generate --force`n" -ForegroundColor White

Write-Host "5. Install Composer dependencies:" -ForegroundColor Yellow
Write-Host "   composer install --no-dev --optimize-autoloader`n" -ForegroundColor White

Write-Host "6. Setup database:" -ForegroundColor Yellow
Write-Host @"
   php artisan migrate --force
   php artisan db:seed --force
"@ -ForegroundColor White
Write-Host ""

Write-Host "7. Set permissions:" -ForegroundColor Yellow
Write-Host @"
   chmod -R 775 storage bootstrap/cache
   find storage -type f -exec chmod 664 {} \;
   find storage -type d -exec chmod 775 {} \;
"@ -ForegroundColor White
Write-Host ""

Write-Host "8. Optimize aplikasi:" -ForegroundColor Yellow
Write-Host @"
   php artisan config:clear
   php artisan cache:clear
   php artisan view:clear
   php artisan route:clear
   php artisan optimize
"@ -ForegroundColor White
Write-Host ""

Write-Host "9. Test website:" -ForegroundColor Yellow
Write-Host "   https://sister.unic.ac.id`n" -ForegroundColor Cyan

Write-Host "========================================" -ForegroundColor Green
Write-Host "   TROUBLESHOOTING" -ForegroundColor Green
Write-Host "========================================`n" -ForegroundColor Green

Write-Host "Jika artisan tidak ditemukan:" -ForegroundColor Yellow
Write-Host @"
   # Cek lokasi saat ini
   pwd
   
   # Harus di: /home/unic/sister
   # Jika tidak, pindah dulu:
   cd /home/unic/sister
   
   # Cek isi folder
   ls -la
   
   # Cek apakah artisan ada dan executable
   ls -la artisan
   chmod +x artisan
"@ -ForegroundColor White
Write-Host ""

Write-Host "Jika upload FileZilla gagal:" -ForegroundColor Yellow
Write-Host "   - Pastikan firewall tidak block port 21" -ForegroundColor White
Write-Host "   - Coba ganti mode transfer: Auto -> Binary" -ForegroundColor White
Write-Host "   - Coba upload sedikit-sedikit per folder" -ForegroundColor White
Write-Host "   - Atau gunakan cara 2 (cPanel File Manager)`n" -ForegroundColor White

Write-Host "========================================`n" -ForegroundColor Green

Write-Host "Tekan Enter untuk keluar..." -ForegroundColor Gray
Read-Host
