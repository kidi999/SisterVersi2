# Script untuk reset MySQL XAMPP
Write-Host "=== XAMPP MySQL Reset Script ===" -ForegroundColor Cyan

# Stop MySQL
Write-Host "Stopping MySQL..." -ForegroundColor Yellow
Stop-Process -Name "mysqld" -Force -ErrorAction SilentlyContinue
Start-Sleep -Seconds 3

# Backup my.ini
$myIniPath = "C:\xampp\mysql\bin\my.ini"
$backupPath = "C:\xampp\mysql\bin\my.ini.backup"

if (Test-Path $myIniPath) {
    Write-Host "Backing up my.ini..." -ForegroundColor Yellow
    Copy-Item $myIniPath $backupPath -Force
    
    # Tambahkan skip-grant-tables
    $content = Get-Content $myIniPath
    if ($content -notcontains "skip-grant-tables") {
        $content = $content -replace '\[mysqld\]', "[mysqld]`nskip-grant-tables"
        $content | Set-Content $myIniPath
        Write-Host "Added skip-grant-tables to my.ini" -ForegroundColor Green
    }
}

# Start MySQL dengan skip-grant-tables
Write-Host "Starting MySQL in safe mode..." -ForegroundColor Yellow
Start-Process "C:\xampp\mysql\bin\mysqld.exe" -ArgumentList "--skip-grant-tables" -WindowStyle Hidden
Start-Sleep -Seconds 5

# Fix root permissions
Write-Host "Fixing root user permissions..." -ForegroundColor Yellow
$fixScript = @"
FLUSH PRIVILEGES;
ALTER USER 'root'@'localhost' IDENTIFIED BY '';
GRANT ALL PRIVILEGES ON *.* TO 'root'@'localhost' WITH GRANT OPTION;
CREATE USER IF NOT EXISTS 'root'@'127.0.0.1' IDENTIFIED BY '';
GRANT ALL PRIVILEGES ON *.* TO 'root'@'127.0.0.1' WITH GRANT OPTION;
FLUSH PRIVILEGES;
"@

$fixScript | & "C:\xampp\mysql\bin\mysql.exe" -u root

if ($LASTEXITCODE -eq 0) {
    Write-Host "Root permissions fixed successfully!" -ForegroundColor Green
} else {
    Write-Host "Failed to fix permissions" -ForegroundColor Red
}

# Stop MySQL
Write-Host "Stopping MySQL..." -ForegroundColor Yellow
Stop-Process -Name "mysqld" -Force -ErrorAction SilentlyContinue
Start-Sleep -Seconds 3

# Restore my.ini
if (Test-Path $backupPath) {
    Write-Host "Restoring my.ini..." -ForegroundColor Yellow
    Copy-Item $backupPath $myIniPath -Force
    Remove-Item $backupPath -Force
}

# Start MySQL normally
Write-Host "Starting MySQL normally..." -ForegroundColor Yellow
Start-Process "C:\xampp\mysql\bin\mysqld.exe" -WindowStyle Hidden

Write-Host ""
Write-Host "=== COMPLETED ===" -ForegroundColor Green
Write-Host "Please wait 10 seconds and try accessing phpMyAdmin again" -ForegroundColor Cyan
Write-Host "URL: http://localhost/phpmyadmin" -ForegroundColor Cyan
