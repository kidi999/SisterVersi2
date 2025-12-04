@echo off
REM Setup Repository Pribadi untuk SISTER Project
echo ========================================
echo   Setup Repository Git Pribadi
echo ========================================
echo.

echo [INFO] Langkah yang akan dilakukan:
echo 1. Checkout ke branch main
echo 2. Hapus remote lama (Laravel official)
echo 3. Tambah remote baru (repository Anda)
echo 4. Commit semua perubahan
echo 5. Push ke repository baru
echo.

pause

REM 1. Checkout ke branch main
echo.
echo [STEP 1] Checkout ke branch main...
git checkout -b main
if %errorlevel% neq 0 (
    echo [WARNING] Branch main sudah ada, pindah ke main...
    git checkout main
)

REM 2. Hapus remote lama
echo.
echo [STEP 2] Menghapus remote lama...
git remote remove origin
git remote remove composer
echo [SUCCESS] Remote lama dihapus

REM 3. Input repository baru
echo.
echo [STEP 3] Setup repository baru
echo.
echo Silakan buat repository baru di GitHub/GitLab terlebih dahulu!
echo Contoh: https://github.com/username/sister.git
echo.
set /p NEW_REPO="Masukkan URL repository baru Anda: "

git remote add origin %NEW_REPO%
echo [SUCCESS] Remote baru ditambahkan: %NEW_REPO%

REM 4. Add semua perubahan
echo.
echo [STEP 4] Adding semua file perubahan...
git add .
echo [SUCCESS] Semua file ditambahkan

REM 5. Commit
echo.
set /p COMMIT_MSG="Masukkan pesan commit (atau Enter untuk default): "
if "%COMMIT_MSG%"=="" (
    set COMMIT_MSG=Initial commit - SISTER Project with Payment and Attendance System
)

git commit -m "%COMMIT_MSG%"
echo [SUCCESS] Changes committed

REM 6. Push
echo.
echo [STEP 5] Pushing ke repository baru...
git push -u origin main

if %errorlevel% equ 0 (
    echo.
    echo ========================================
    echo   SUCCESS! Repository Sudah Terupload
    echo ========================================
    echo.
    echo Repository: %NEW_REPO%
    echo Branch: main
    echo.
    echo Untuk update selanjutnya, gunakan:
    echo   quick-push.bat
    echo.
) else (
    echo.
    echo [ERROR] Push gagal!
    echo Kemungkinan:
    echo 1. Repository belum dibuat di GitHub/GitLab
    echo 2. Autentikasi gagal (perlu token/SSH key)
    echo 3. URL repository salah
    echo.
)

pause
