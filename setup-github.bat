@echo off
REM Setup GitHub untuk SISTER Project
REM Script ini akan membantu setup GitHub repository pertama kali

echo ========================================
echo   SISTER GitHub Setup
echo ========================================
echo.
echo Script ini akan membantu Anda:
echo 1. Initialize Git repository
echo 2. Membuat .gitignore
echo 3. Commit initial code
echo 4. Push ke GitHub
echo.

pause

REM Check if git is installed
where git >nul 2>nul
if %errorlevel% neq 0 (
    echo [ERROR] Git tidak terinstall!
    echo.
    echo Silakan download dan install Git dari:
    echo https://git-scm.com/download/win
    echo.
    pause
    exit /b 1
)

echo [SUCCESS] Git terdeteksi
echo.

REM Initialize git if not exists
if not exist ".git" (
    echo [INFO] Initializing Git repository...
    git init
    git branch -M main
    echo [SUCCESS] Git repository initialized
) else (
    echo [INFO] Git repository sudah ada
)

echo.
echo ========================================
echo   Setup GitHub Repository
echo ========================================
echo.
echo Langkah-langkah:
echo 1. Buka https://github.com/new
echo 2. Buat repository baru (misal: sister)
echo 3. JANGAN centang "Initialize with README"
echo 4. Copy URL repository (akan seperti: https://github.com/username/sister.git)
echo.

pause

echo.
set /p REPO_URL="Paste URL repository GitHub Anda: "

REM Add remote
git remote remove origin 2>nul
git remote add origin %REPO_URL%

echo [SUCCESS] Remote origin added: %REPO_URL%
echo.

REM Configure user
echo ========================================
echo   Git Configuration
echo ========================================
echo.
set /p GIT_NAME="Masukkan nama Anda (untuk Git): "
set /p GIT_EMAIL="Masukkan email Anda (untuk Git): "

git config user.name "%GIT_NAME%"
git config user.email "%GIT_EMAIL%"

echo [SUCCESS] Git configured
echo.

REM Initial commit
echo [INFO] Membuat initial commit...
git add .
git commit -m "Initial commit: SISTER - Sistem Informasi Akademik"

if %errorlevel% neq 0 (
    echo [ERROR] Commit gagal. Mungkin tidak ada perubahan?
) else (
    echo [SUCCESS] Initial commit created
)

echo.
echo [INFO] Pushing ke GitHub...
echo [INFO] Anda mungkin diminta memasukkan username dan password GitHub
echo.

git push -u origin main

if %errorlevel% equ 0 (
    echo.
    echo ========================================
    echo   Setup Selesai!
    echo ========================================
    echo.
    echo [SUCCESS] Kode berhasil di-upload ke GitHub!
    echo.
    echo Repository Anda: %REPO_URL%
    echo.
    echo Langkah selanjutnya:
    echo 1. Untuk upload perubahan selanjutnya, gunakan: quick-push.bat
    echo 2. Untuk auto-deploy ke server, setup GitHub Actions (lihat DEPLOYMENT.md)
    echo 3. Lihat QUICK-DEPLOY.md untuk panduan deployment
    echo.
) else (
    echo.
    echo [ERROR] Push gagal!
    echo.
    echo Kemungkinan penyebab:
    echo 1. Kredensial GitHub salah
    echo 2. Tidak ada akses internet
    echo 3. Repository URL salah
    echo.
    echo Solusi:
    echo - Pastikan Anda login ke GitHub
    echo - Cek koneksi internet
    echo - Verifikasi URL repository
    echo.
    echo Coba push manual: git push -u origin main
    echo.
)

echo.
pause
