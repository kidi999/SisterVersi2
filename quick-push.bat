@echo off
REM Quick Git Push - Otomatis commit dan push ke GitHub
REM Gunakan ini untuk cepat upload perubahan

echo ========================================
echo   Quick Git Push
echo ========================================
echo.

REM Check if git is initialized
if not exist ".git" (
    echo [INFO] Git repository not found. Initializing...
    git init
    git branch -M main
    set /p REPO_URL="Enter GitHub repository URL (e.g., https://github.com/username/sister.git): "
    git remote add origin %REPO_URL%
    echo [SUCCESS] Git initialized
    echo.
)

REM Get commit message
set /p COMMIT_MSG="Enter commit message (or press Enter for auto message): "

if "%COMMIT_MSG%"=="" (
    REM Auto generate commit message with timestamp
    for /f "tokens=2 delims==" %%I in ('wmic os get localdatetime /value') do set datetime=%%I
    set COMMIT_MSG=Update %datetime:~0,4%-%datetime:~4,2%-%datetime:~6,2% %datetime:~8,2%:%datetime:~10,2%
)

echo.
echo [INFO] Adding all changes...
git add .

echo [INFO] Committing changes...
git commit -m "%COMMIT_MSG%"

if %errorlevel% neq 0 (
    echo [WARNING] Nothing to commit or commit failed
    echo [INFO] Trying to push anyway...
)

echo.
echo [INFO] Pushing to GitHub...
git push origin main

if %errorlevel% neq 0 (
    echo [INFO] Trying master branch...
    git push origin master
)

if %errorlevel% neq 0 (
    echo.
    echo [INFO] First time push? Setting upstream...
    git push -u origin main
)

if %errorlevel% equ 0 (
    echo.
    echo [SUCCESS] Changes uploaded successfully!
    echo [INFO] GitHub Actions will auto-deploy if configured
) else (
    echo.
    echo [ERROR] Failed to push. Check your internet connection and GitHub credentials.
)

echo.
pause
