# SISTER - Deployment Guide

## Metode Deployment Otomatis

Ada beberapa cara untuk mengupload aplikasi ke server secara otomatis:

---

## 🚀 Metode 1: GitHub Actions (RECOMMENDED)

Deployment otomatis setiap kali push ke GitHub.

### Setup GitHub Actions:

1. **Push project ke GitHub:**
   ```bash
   git init
   git add .
   git commit -m "Initial commit"
   git branch -M main
   git remote add origin https://github.com/username/sister.git
   git push -u origin main
   ```

2. **Setup Secrets di GitHub:**
   - Buka repository → Settings → Secrets and variables → Actions
   - Tambahkan secrets berikut:

   **Untuk FTP Deployment:**
   - `FTP_SERVER`: ftp.your-domain.com
   - `FTP_USERNAME`: username FTP Anda
   - `FTP_PASSWORD`: password FTP Anda

   **Untuk SSH Deployment (lebih aman):**
   - `SSH_HOST`: IP atau domain server
   - `SSH_USERNAME`: username SSH
   - `SSH_PRIVATE_KEY`: Private key SSH
   - `SSH_PORT`: 22 (atau port custom)
   - `DEPLOY_PATH`: /var/www/html/sister

3. **File konfigurasi sudah dibuat:**
   - `.github/workflows/deploy.yml`

4. **Cara kerja:**
   - Setiap push ke branch `main` atau `master`
   - GitHub Actions otomatis deploy ke server
   - Tidak perlu manual upload lagi!

---

## 🖥️ Metode 2: Script Deployment Manual

### Untuk Windows:

1. **Edit file `deploy.bat`:**
   - Buka `deploy.bat`
   - Ubah variabel berikut:
     ```batch
     set SERVER_USER=username_server_anda
     set SERVER_HOST=domain_atau_ip_server.com
     set SERVER_PATH=/var/www/html/sister
     ```

2. **Jalankan deployment:**
   ```bash
   deploy.bat
   ```

### Untuk Linux/Mac:

1. **Edit file `deploy.sh`:**
   ```bash
   nano deploy.sh
   ```
   - Ubah variabel konfigurasi di bagian atas

2. **Beri permission executable:**
   ```bash
   chmod +x deploy.sh
   ```

3. **Jalankan deployment:**
   ```bash
   ./deploy.sh
   ```

---

## 📁 Metode 3: FTP Auto-Sync (FileZilla)

Untuk sync otomatis via FTP menggunakan FileZilla:

1. **Install FileZilla Client**

2. **Setup Site:**
   - File → Site Manager → New Site
   - Protocol: SFTP atau FTP
   - Host: server Anda
   - Port: 21 (FTP) atau 22 (SFTP)
   - Logon Type: Normal
   - User & Password

3. **Enable Auto-Upload:**
   - Setelah connect
   - Klik kanan folder lokal → Select for Comparison
   - Klik kanan folder remote → Compare
   - Upload semua yang berbeda

4. **Automatic Synchronization:**
   - Di FileZilla, pilih folder lokal dan remote
   - Menu: Transfer → "Keep remote directory up to date"
   - Centang semua file types
   - Klik OK

---

## ☁️ Metode 4: Git Auto-Deploy di Server

Setup Git hooks untuk auto-deploy ketika push:

### Di Server:

1. **Clone repository:**
   ```bash
   cd /var/www/html
   git clone https://github.com/username/sister.git
   cd sister
   ```

2. **Buat deploy script:**
   ```bash
   nano deploy-server.sh
   ```
   
   Isi dengan:
   ```bash
   #!/bin/bash
   cd /var/www/html/sister
   git pull origin main
   composer install --no-dev --optimize-autoloader
   php artisan config:clear
   php artisan cache:clear
   php artisan migrate --force
   php artisan optimize
   chmod -R 775 storage bootstrap/cache
   chown -R www-data:www-data storage bootstrap/cache
   ```

3. **Beri permission:**
   ```bash
   chmod +x deploy-server.sh
   ```

4. **Setup Webhook (GitHub):**
   - Repository → Settings → Webhooks → Add webhook
   - Payload URL: https://your-domain.com/deploy-webhook.php
   - Content type: application/json
   - Secret: buat token rahasia
   - Events: Just the push event

5. **Buat webhook handler di server:**
   ```bash
   nano /var/www/html/deploy-webhook.php
   ```
   
   Isi dengan:
   ```php
   <?php
   $secret = "your-webhook-secret";
   $payload = file_get_contents('php://input');
   $signature = 'sha256=' . hash_hmac('sha256', $payload, $secret);
   
   if (hash_equals($signature, $_SERVER['HTTP_X_HUB_SIGNATURE_256'])) {
       shell_exec('cd /var/www/html/sister && ./deploy-server.sh > /dev/null 2>&1 &');
       http_response_code(200);
       echo "Deployment triggered";
   } else {
       http_response_code(403);
       echo "Invalid signature";
   }
   ```

---

## 🔄 Metode 5: Continuous Deployment dengan Deployer

Install Deployer untuk deployment yang lebih advanced:

1. **Install Deployer:**
   ```bash
   composer require deployer/deployer --dev
   ```

2. **Generate config:**
   ```bash
   vendor/bin/dep init
   ```

3. **Edit deploy.php:**
   ```php
   <?php
   namespace Deployer;
   
   require 'recipe/laravel.php';
   
   set('application', 'SISTER');
   set('repository', 'git@github.com:username/sister.git');
   set('ssh_multiplexing', true);
   
   host('production')
       ->set('hostname', 'your-server.com')
       ->set('remote_user', 'username')
       ->set('deploy_path', '/var/www/html/sister');
   
   task('deploy', [
       'deploy:prepare',
       'deploy:vendors',
       'artisan:migrate',
       'artisan:optimize',
       'deploy:publish',
   ]);
   
   after('deploy:failed', 'deploy:unlock');
   ```

4. **Deploy:**
   ```bash
   vendor/bin/dep deploy production
   ```

---

## 📝 Setup di Server Production

### Prerequisites di Server:

1. **Update system:**
   ```bash
   sudo apt update && sudo apt upgrade -y
   ```

2. **Install required packages:**
   ```bash
   sudo apt install -y php8.2 php8.2-cli php8.2-fpm php8.2-mysql php8.2-xml php8.2-mbstring php8.2-curl php8.2-zip php8.2-gd php8.2-bcmath
   sudo apt install -y nginx mysql-server composer git
   ```

3. **Configure Nginx:**
   ```bash
   sudo nano /etc/nginx/sites-available/sister
   ```
   
   Paste konfigurasi:
   ```nginx
   server {
       listen 80;
       server_name your-domain.com;
       root /var/www/html/sister/public;
       
       add_header X-Frame-Options "SAMEORIGIN";
       add_header X-Content-Type-Options "nosniff";
       
       index index.php;
       
       charset utf-8;
       
       location / {
           try_files $uri $uri/ /index.php?$query_string;
       }
       
       location = /favicon.ico { access_log off; log_not_found off; }
       location = /robots.txt  { access_log off; log_not_found off; }
       
       error_page 404 /index.php;
       
       location ~ \.php$ {
           fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
           fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
           include fastcgi_params;
       }
       
       location ~ /\.(?!well-known).* {
           deny all;
       }
   }
   ```

4. **Enable site:**
   ```bash
   sudo ln -s /etc/nginx/sites-available/sister /etc/nginx/sites-enabled/
   sudo nginx -t
   sudo systemctl restart nginx
   ```

5. **Set permissions:**
   ```bash
   sudo chown -R www-data:www-data /var/www/html/sister
   sudo chmod -R 775 /var/www/html/sister/storage
   sudo chmod -R 775 /var/www/html/sister/bootstrap/cache
   ```

6. **Configure .env di server:**
   ```bash
   cd /var/www/html/sister
   cp .env.example .env
   nano .env
   ```
   
   Update dengan kredensial production:
   ```
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://your-domain.com
   
   DB_HOST=localhost
   DB_DATABASE=sister_db
   DB_USERNAME=db_user
   DB_PASSWORD=secure_password
   ```

7. **Generate key & migrate:**
   ```bash
   php artisan key:generate
   php artisan migrate --force
   php artisan optimize
   ```

---

## 🔐 Security Checklist

- [ ] Set `APP_ENV=production` di server
- [ ] Set `APP_DEBUG=false` di server
- [ ] Gunakan HTTPS (install SSL certificate)
- [ ] Ganti semua password default
- [ ] Restrict file permissions (755 untuk folder, 644 untuk file)
- [ ] Enable firewall di server
- [ ] Backup database secara rutin
- [ ] Setup monitoring (Uptime Robot, New Relic, dll)

---

## 🆘 Troubleshooting

### Permission Issues:
```bash
sudo chown -R www-data:www-data /var/www/html/sister
sudo chmod -R 775 storage bootstrap/cache
```

### Clear Cache:
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
```

### Database Issues:
```bash
php artisan migrate:fresh --seed --force
```

### 500 Error:
- Check Laravel logs: `storage/logs/laravel.log`
- Check Nginx error log: `sudo tail -f /var/log/nginx/error.log`
- Check PHP-FPM log: `sudo tail -f /var/log/php8.2-fpm.log`

---

## 📞 Support

Jika ada masalah deployment, cek:
1. Laravel logs di `storage/logs/`
2. Server error logs
3. File permissions
4. Database connection
5. PHP version compatibility

---

**Happy Deploying! 🚀**
