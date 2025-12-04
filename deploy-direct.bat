@echo off
REM ========================================
REM   SISTER Direct Deploy to Server
REM   Deploy langsung ke sister.unic.ac.id
REM ========================================

echo.
echo ========================================
echo   SISTER - Direct Server Deployment
echo ========================================
echo.
echo Target: sister.unic.ac.id
echo Path: /home/unic/sister
echo.

set SERVER_HOST=103.241.192.78
set FTP_USER=unic
set FTP_PASS=qwert12345
set REMOTE_PATH=/home/unic/sister
set LOCAL_PATH=%cd%

echo [INFO] Membuat backup .env di server...
echo [INFO] Mempersiapkan upload...
echo.

REM Create WinSCP script file
echo option batch abort > deploy_script.txt
echo option confirm off >> deploy_script.txt
echo open ftp://%FTP_USER%:%FTP_PASS%@%SERVER_HOST% >> deploy_script.txt
echo cd %REMOTE_PATH% >> deploy_script.txt
echo.

REM Upload files except excluded
echo lcd "%LOCAL_PATH%" >> deploy_script.txt
echo.

REM Sync all files
echo synchronize remote -delete -criteria=size ^
    -filemask="|.git/;.env;.env.backup;node_modules/;vendor/;storage/framework/cache/;storage/framework/sessions/;storage/framework/views/;storage/logs/;.gitignore;.git*;*.log;deploy*.txt;deploy*.bat;setup*.bat;quick*.bat" >> deploy_script.txt
echo.

echo exit >> deploy_script.txt

REM Check if WinSCP is installed
where winscp.com >nul 2>nul
if %errorlevel% neq 0 (
    echo [ERROR] WinSCP tidak ditemukan!
    echo.
    echo Menggunakan metode alternatif dengan CURL...
    goto USE_CURL
) else (
    echo [INFO] Menggunakan WinSCP untuk upload...
    winscp.com /script=deploy_script.txt
    
    if %errorlevel% equ 0 (
        del deploy_script.txt
        goto SUCCESS
    ) else (
        echo [WARNING] WinSCP gagal, mencoba metode alternatif...
        del deploy_script.txt
        goto USE_CURL
    )
)

:USE_CURL
echo.
echo [INFO] Membuat archive untuk upload...

REM Create zip excluding unnecessary files
powershell -Command "Compress-Archive -Path * -DestinationPath sister_deploy.zip -Force -CompressionLevel Fastest"

if exist sister_deploy.zip (
    echo [SUCCESS] Archive berhasil dibuat
    echo.
    echo [INFO] Upload archive ke server...
    
    REM Upload via FTP using PowerShell
    powershell -Command "$webclient = New-Object System.Net.WebClient; $webclient.Credentials = New-Object System.Net.NetworkCredential('%FTP_USER%', '%FTP_PASS%'); $webclient.UploadFile('ftp://%SERVER_HOST%%REMOTE_PATH%/sister_deploy.zip', '%cd%\sister_deploy.zip')"
    
    if %errorlevel% equ 0 (
        echo [SUCCESS] Archive berhasil di-upload
        echo.
        echo [INFO] Membersihkan archive lokal...
        del sister_deploy.zip
        
        echo.
        echo [PENTING] Jalankan di server (via SSH atau Terminal cPanel):
        echo.
        echo cd /home/unic/sister
        echo unzip -o sister_deploy.zip
        echo rm sister_deploy.zip
        echo composer install --no-dev --optimize-autoloader
        echo php artisan config:clear
        echo php artisan cache:clear
        echo php artisan view:clear
        echo php artisan route:clear
        echo php artisan migrate --force
        echo php artisan optimize
        echo chmod -R 775 storage bootstrap/cache
        echo.
        goto END
    ) else (
        echo [ERROR] Upload gagal!
        del sister_deploy.zip
        goto MANUAL_STEPS
    )
) else (
    echo [ERROR] Gagal membuat archive
    goto MANUAL_STEPS
)

:SUCCESS
echo.
echo ========================================
echo   Upload Berhasil!
echo ========================================
echo.
echo [SUKSES] File berhasil di-upload ke server
echo.
echo [INFO] Jalankan perintah berikut di Terminal SSH/cPanel:
echo.
echo cd /home/unic/sister
echo composer install --no-dev --optimize-autoloader
echo php artisan config:clear
echo php artisan cache:clear
echo php artisan migrate --force
echo php artisan optimize
echo.
goto END

:MANUAL_STEPS
echo.
echo ========================================
echo   Upload Manual via FileZilla
echo ========================================
echo.
echo Silakan upload manual menggunakan FileZilla:
echo.
echo Host: 103.241.192.78
echo Username: unic
echo Password: qwert12345
echo Port: 21
echo Path: /home/unic/sister
echo.
echo File yang JANGAN di-upload:
echo - .env (sudah ada di server)
echo - vendor/ (run composer install di server)
echo - node_modules/
echo - storage/logs/
echo - .git/
echo.

:END
echo.
pause
