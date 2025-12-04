@echo off
chcp 65001 >nul
setlocal enabledelayedexpansion

echo.
echo ╔════════════════════════════════════════════╗
echo ║   SISTER - Auto Upload via FTP           ║
echo ╚════════════════════════════════════════════╝
echo.

echo [INFO] Memulai upload otomatis ke server...
echo.

powershell -ExecutionPolicy Bypass -File "%~dp0upload-auto.ps1"

if errorlevel 1 (
    echo.
    echo ╔════════════════════════════════════════════╗
    echo ║   UPLOAD GAGAL - GUNAKAN FILEZILLA       ║
    echo ╚════════════════════════════════════════════╝
    echo.
    echo Download FileZilla: https://filezilla-project.org
    echo.
    echo Connect Info:
    echo   Host: 103.241.192.78
    echo   Username: unic
    echo   Password: qwert12345
    echo   Port: 21
    echo.
    echo Upload semua file KECUALI:
    echo   - .git folder
    echo   - node_modules folder
    echo   - vendor folder
    echo   - .env file
    echo.
)

echo.
pause
