# 🔒 Security Incident Response - SEO Poisoning

## Incident Details
- **Date Detected**: December 8, 2025
- **Type**: SEO Poisoning / Website Compromise
- **Indicator**: Spam content indexed in Google (สล็อต ฟรี - Thai gambling spam)
- **Severity**: HIGH
- **Status**: Under Investigation & Remediation

## Immediate Actions Taken

### 1. Security Audit Commands
```bash
# Find recently modified files
find /home/unic -name "*.php" -type f -mtime -7 -ls

# Search for spam keywords
grep -r "สล็อต" /home/unic --include="*.php"
grep -r "slot" /home/unic --include="*.php" --include="*.html"

# Check suspicious files
find /home/unic -name "index.php" -type f -ls
find /home/unic -name ".htaccess" -type f -ls
```

### 2. Database Scan
```sql
-- Check for injected spam in database
USE sister_db;
SELECT * FROM users WHERE name LIKE '%slot%';
SELECT * FROM pendaftaran_mahasiswa WHERE nama_lengkap LIKE '%slot%';
```

### 3. Malware Scan
```bash
# Install and run ClamAV
yum install clamav clamav-update -y
freshclam
clamscan -r /home/unic/sister/ --infected --remove
```

### 4. Clean Cache & Temporary Files
```bash
cd /home/unic/sister
rm -rf storage/framework/cache/*
rm -rf storage/framework/sessions/*
rm -rf storage/framework/views/*
rm -rf bootstrap/cache/*.php
```

### 5. Reinstall Dependencies
```bash
rm -rf vendor/
/opt/alt/php-fpm82/usr/bin/php /usr/local/bin/composer install --no-dev
```

## Security Enhancements Implemented

### 1. Security Headers Middleware
- Created `SecurityHeaders` middleware
- Implements:
  - X-Content-Type-Options: nosniff
  - X-Frame-Options: SAMEORIGIN
  - X-XSS-Protection: 1; mode=block
  - Referrer-Policy: strict-origin-when-cross-origin
  - Content-Security-Policy
  - Permissions-Policy

### 2. Hardened .htaccess
```apache
# Block SQL injection attempts
RewriteCond %{QUERY_STRING} (<|%3C).*script.*(>|%3E) [NC,OR]
RewriteCond %{QUERY_STRING} GLOBALS(=|\[|\%[0-9A-Z]{0,2}) [OR]
RewriteCond %{QUERY_STRING} _REQUEST(=|\[|\%[0-9A-Z]{0,2})
RewriteRule ^(.*)$ index.php [F,L]

# Prevent directory listing
Options -Indexes

# Block sensitive files
<FilesMatch "^\.">
    Deny from all
</FilesMatch>
```

### 3. Secure robots.txt
```
User-agent: *
Disallow: /admin
Disallow: /storage
Disallow: /vendor
Allow: /

Sitemap: https://sister.unic.ac.id/sitemap.xml
```

## Password Changes Required

### 1. Database Password
```bash
mysql -u root -p
ALTER USER 'root'@'localhost' IDENTIFIED BY 'NEW_STRONG_PASSWORD';
ALTER USER 'sister_user'@'localhost' IDENTIFIED BY 'NEW_STRONG_PASSWORD';
FLUSH PRIVILEGES;
```

### 2. Update .env
```env
DB_PASSWORD=new_strong_password_here
```

### 3. Server Passwords
```bash
# Root password
passwd root

# Application user
passwd unic
```

### 4. Admin User Passwords
- Reset all admin passwords in Laravel: https://sister.unic.ac.id/admin/users
- Force password reset for all users

## Google Search Console Actions

### 1. Request Removal
1. Go to: https://search.google.com/search-console
2. Add property: sister.unic.ac.id
3. Navigate to: Removals → New Request
4. Enter infected URLs
5. Select: "Temporarily hide from Google"

### 2. Submit Clean URLs
1. URL Inspection Tool
2. Enter: https://sister.unic.ac.id
3. Click: "Request Indexing"

### 3. Disavow Bad Backlinks
1. Go to: Removals → Disavow Links
2. Upload list of spam backlinks
3. Submit for review

## Monitoring & Prevention

### 1. File Integrity Monitoring (AIDE)
```bash
yum install aide -y
aide --init
mv /var/lib/aide/aide.db.new.gz /var/lib/aide/aide.db.gz

# Daily check cron
echo "0 2 * * * /usr/sbin/aide --check | mail -s 'AIDE Report' admin@unic.ac.id" | crontab -
```

### 2. Fail2Ban for Brute Force Protection
```bash
yum install fail2ban -y
systemctl enable fail2ban
systemctl start fail2ban

# Configure for SSH, HTTP
cat > /etc/fail2ban/jail.local << EOF
[sshd]
enabled = true
port = ssh
logpath = /var/log/secure
maxretry = 3
bantime = 3600

[apache]
enabled = true
port = http,https
logpath = /var/log/httpd/error_log
maxretry = 5
bantime = 3600
EOF

systemctl restart fail2ban
```

### 3. Daily Backup
```bash
# Backup script
cat > /root/backup-sister.sh << 'EOF'
#!/bin/bash
BACKUP_DIR="/backup/sister"
DATE=$(date +%Y%m%d_%H%M%S)

# Create backup directory
mkdir -p $BACKUP_DIR

# Backup files
tar -czf $BACKUP_DIR/sister_files_$DATE.tar.gz /home/unic/sister

# Backup database
mysqldump -u root -p'password' sister_db | gzip > $BACKUP_DIR/sister_db_$DATE.sql.gz

# Keep only last 7 days
find $BACKUP_DIR -name "sister_*" -mtime +7 -delete
EOF

chmod +x /root/backup-sister.sh

# Add to cron (daily at 3 AM)
echo "0 3 * * * /root/backup-sister.sh" | crontab -
```

### 4. Security Checklist (Weekly)
- [ ] Check server logs for suspicious activity
- [ ] Review fail2ban banned IPs
- [ ] Check AIDE file integrity reports
- [ ] Verify all admin accounts are legitimate
- [ ] Update composer packages: `composer update`
- [ ] Check for Laravel security updates
- [ ] Review Google Search Console for spam
- [ ] Verify robots.txt and sitemap
- [ ] Test backup restoration

## Prevention Best Practices

### 1. Keep Software Updated
```bash
# Update server
yum update -y

# Update Laravel
/opt/alt/php-fpm82/usr/bin/php /usr/local/bin/composer update

# Update Composer itself
/opt/alt/php-fpm82/usr/bin/php /usr/local/bin/composer self-update
```

### 2. File Permissions
```bash
# Correct permissions
find /home/unic/sister -type d -exec chmod 755 {} \;
find /home/unic/sister -type f -exec chmod 644 {} \;
chmod -R 775 storage bootstrap/cache
chmod 600 /home/unic/sister/.env
```

### 3. Disable Unnecessary Services
```bash
# List running services
systemctl list-units --type=service --state=running

# Disable unused services
systemctl disable telnet
systemctl disable vsftpd  # if not using FTP
```

### 4. Enable Firewall
```bash
# Configure firewall
firewall-cmd --permanent --add-service=http
firewall-cmd --permanent --add-service=https
firewall-cmd --permanent --add-service=ssh
firewall-cmd --reload
```

### 5. Laravel Security Config
Update `.env`:
```env
APP_DEBUG=false
APP_ENV=production
LOG_LEVEL=warning

# Session security
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=strict
```

## Contact Information

**IT Security Team:**
- Email: admin@unic.ac.id
- Phone: [Your phone]

**Hosting Provider:**
- Support: support@25webhosting.com

**Emergency Response:**
- Google Safe Browsing: https://safebrowsing.google.com/
- Google Search Console: https://search.google.com/search-console

## Post-Incident Review

### Lessons Learned
1. File permissions were too permissive
2. No file integrity monitoring in place
3. Weak password policies
4. Lack of security headers
5. No regular security audits

### Action Items
- [x] Implement SecurityHeaders middleware
- [x] Harden .htaccess configuration
- [ ] Complete malware scan and cleanup
- [ ] Change all passwords
- [ ] Setup monitoring (AIDE, Fail2Ban)
- [ ] Configure daily backups
- [ ] Submit clean URLs to Google
- [ ] Train staff on security best practices

---

**Document Version**: 1.0  
**Last Updated**: December 8, 2025  
**Next Review Date**: December 15, 2025
