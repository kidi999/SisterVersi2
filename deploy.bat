@echo off
REM SISTER Deployment Script for Windows
REM Script untuk deploy otomatis ke server production

echo ========================================
echo   SISTER Auto Deployment Script
echo ========================================
echo.

REM Configuration - Edit sesuai dengan server Anda
set SERVER_USER=username
set SERVER_HOST=your-server.com
set SERVER_PATH=/var/www/html/sister
set SSH_PORT=22

echo [INFO] Checking for uncommitted changes...
git status --short > temp_status.txt
set /p HAS_CHANGES=<temp_status.txt
del temp_status.txt

if not "%HAS_CHANGES%"=="" (
    echo [WARNING] You have uncommitted changes!
    echo.
    set /p COMMIT_CHOICE="Do you want to commit them now? (y/n): "
    
    if /i "%COMMIT_CHOICE%"=="y" (
        set /p COMMIT_MSG="Enter commit message: "
        git add .
        git commit -m "%COMMIT_MSG%"
        echo [SUCCESS] Changes committed
    ) else (
        echo [ERROR] Deployment cancelled. Please commit your changes first.
        pause
        exit /b 1
    )
)

echo.
echo [INFO] Pushing to repository...
git push origin main
if %errorlevel% neq 0 (
    git push origin master
)

if %errorlevel% equ 0 (
    echo [SUCCESS] Code pushed to repository
) else (
    echo [ERROR] Failed to push to repository
    pause
    exit /b 1
)

echo.
echo [INFO] Deploying to server %SERVER_HOST%...
echo.

REM Deploy via SSH (requires plink.exe from PuTTY or OpenSSH)
ssh %SERVER_USER%@%SERVER_HOST% -p %SSH_PORT% "cd %SERVER_PATH% && git pull origin main && composer install --no-dev --optimize-autoloader && php artisan config:clear && php artisan cache:clear && php artisan view:clear && php artisan route:clear && php artisan migrate --force && php artisan optimize && chmod -R 775 storage bootstrap/cache"

if %errorlevel% equ 0 (
    echo.
    echo [SUCCESS] Deployment completed successfully!
    echo [INFO] Your application is now live at https://%SERVER_HOST%
) else (
    echo.
    echo [ERROR] Deployment failed. Please check the error messages above.
    pause
    exit /b 1
)

echo.
pause
