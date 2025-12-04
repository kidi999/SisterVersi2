@echo off
chcp 65001 >nul
setlocal enabledelayedexpansion

echo.
echo ========================================
echo    SISTER - Upload Simple via FTP
echo ========================================
echo.

REM Check if PowerShell script exists
if not exist "%~dp0upload-ftp.ps1" (
    echo [ERROR] File upload-ftp.ps1 tidak ditemukan!
    pause
    exit /b 1
)

REM Run PowerShell script
powershell -ExecutionPolicy Bypass -File "%~dp0upload-ftp.ps1"

if errorlevel 1 (
    echo.
    echo ========================================
    echo    UPLOAD MANUAL DENGAN FILEZILLA
    echo ========================================
    echo.
    echo File ZIP sudah dibuat: sister_deploy.zip
    echo.
    echo Langkah upload manual:
    echo 1. Install FileZilla jika belum ada
    echo    Download: https://filezilla-project.org
    echo.
    echo 2. Buka FileZilla dan connect:
    echo    Host: 103.241.192.78
    echo    Username: unic
    echo    Password: qwert12345
    echo    Port: 21
    echo.
    echo 3. Upload file sister_deploy.zip ke folder:
    echo    /home/unic/sister
    echo.
    echo 4. Login SSH dan extract:
    echo    ssh unic@103.241.192.78
    echo    cd /home/unic/sister
    echo    unzip -o sister_deploy.zip
    echo    php artisan migrate --force
    echo    php artisan optimize
    echo.
)

echo.
pause
