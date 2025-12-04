@echo off
REM ========================================
REM   Quick Upload - Upload Cepat ke Server
REM   sister.unic.ac.id
REM ========================================

echo.
echo ========================================
echo   SISTER - Quick Upload
echo ========================================
echo.

REM Check for changes
echo [INFO] Memeriksa perubahan file...
echo.

REM Prompt for description
set /p DESCRIPTION="Deskripsi perubahan (opsional): "

if "%DESCRIPTION%"=="" (
    set DESCRIPTION=Update %date% %time%
)

echo.
echo [INFO] Upload perubahan: %DESCRIPTION%
echo.

REM Install WinSCP if needed
where winscp.com >nul 2>nul
if %errorlevel% neq 0 (
    echo [INFO] WinSCP tidak terinstall
    echo [INFO] Menggunakan PowerShell untuk upload...
    goto POWERSHELL_METHOD
)

REM Create WinSCP script
echo option batch abort > quick_deploy.txt
echo option confirm off >> quick_deploy.txt
echo open ftp://unic:qwert12345@103.241.192.78 >> quick_deploy.txt
echo cd /home/unic/sister >> quick_deploy.txt
echo lcd "%cd%" >> quick_deploy.txt
echo synchronize remote -delete ^
    -filemask="|.git/;.env;.env.*;node_modules/;vendor/;storage/framework/;storage/logs/;*.log;deploy*.txt;deploy*.bat;setup*.bat;quick*.bat;.gitignore" >> quick_deploy.txt
echo exit >> quick_deploy.txt

echo [INFO] Uploading files...
winscp.com /script=quick_deploy.txt

if %errorlevel% equ 0 (
    del quick_deploy.txt
    goto SUCCESS
) else (
    del quick_deploy.txt
    echo [ERROR] Upload gagal
    goto POWERSHELL_METHOD
)

:POWERSHELL_METHOD
echo.
echo [INFO] Membuat package...

REM Get current directory name
for %%I in (.) do set FOLDER_NAME=%%~nxI

REM Create zip excluding unnecessary files
powershell -Command "$ProgressPreference = 'SilentlyContinue'; Get-ChildItem -Recurse | Where-Object { $_.FullName -notmatch '\\\.git\\|\\node_modules\\|\\vendor\\|\\storage\\logs\\|\\storage\\framework\\cache\\|\\storage\\framework\\sessions\\|\\storage\\framework\\views\\|\.env$|\.log$|deploy.*\.(bat|txt)$|setup.*\.bat$|quick.*\.bat$' } | Compress-Archive -DestinationPath 'sister_update.zip' -Force"

if exist sister_update.zip (
    echo [SUCCESS] Package berhasil dibuat (sister_update.zip)
    echo.
    echo [INFO] Uploading ke server...
    
    REM Upload using PowerShell
    powershell -Command "$webclient = New-Object System.Net.WebClient; $webclient.Credentials = New-Object System.Net.NetworkCredential('unic', 'qwert12345'); $webclient.UploadFile('ftp://103.241.192.78/home/unic/sister/sister_update.zip', '%cd%\sister_update.zip')"
    
    if %errorlevel% equ 0 (
        echo [SUCCESS] File berhasil di-upload
        del sister_update.zip
        
        echo.
        echo ========================================
        echo   Jalankan di Server Terminal:
        echo ========================================
        echo.
        echo cd /home/unic/sister
        echo unzip -o sister_update.zip
        echo rm sister_update.zip
        echo php artisan config:clear
        echo php artisan cache:clear
        echo php artisan view:clear
        echo php artisan migrate --force
        echo php artisan optimize
        echo.
        goto END
    ) else (
        echo [ERROR] Upload gagal
        del sister_update.zip
        goto MANUAL
    )
) else (
    echo [ERROR] Gagal membuat package
    goto MANUAL
)

:SUCCESS
echo.
echo ========================================
echo   Upload Berhasil!
echo ========================================
echo.
echo [SUCCESS] File berhasil disinkronkan ke server
echo.
echo Jangan lupa jalankan di server (via SSH atau cPanel Terminal):
echo.
echo cd /home/unic/sister
echo php artisan migrate --force
echo php artisan optimize
echo.
goto END

:MANUAL
echo.
echo ========================================
echo   Upload Manual via FTP
echo ========================================
echo.
echo Silakan upload manual menggunakan FileZilla:
echo.
echo Host: 103.241.192.78
echo Username: unic  
echo Password: qwert12345
echo Remote Path: /home/unic/sister
echo.
echo Setelah upload, jalankan di terminal:
echo cd /home/unic/sister
echo php artisan migrate --force
echo php artisan optimize
echo.

:END
echo.
echo ========================================
echo.
pause
