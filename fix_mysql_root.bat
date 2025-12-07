@echo off
echo Stopping MySQL...
taskkill /F /IM mysqld.exe 2>nul
timeout /t 3 >nul

echo Starting MySQL in safe mode (skip-grant-tables)...
start "" "C:\xampp\mysql\bin\mysqld.exe" --skip-grant-tables --console

echo Waiting for MySQL to start...
timeout /t 5 >nul

echo Fixing root user permissions...
"C:\xampp\mysql\bin\mysql.exe" -u root -e "FLUSH PRIVILEGES; ALTER USER 'root'@'localhost' IDENTIFIED BY ''; GRANT ALL PRIVILEGES ON *.* TO 'root'@'localhost' WITH GRANT OPTION; CREATE USER IF NOT EXISTS 'root'@'127.0.0.1' IDENTIFIED BY ''; GRANT ALL PRIVILEGES ON *.* TO 'root'@'127.0.0.1' WITH GRANT OPTION; FLUSH PRIVILEGES;"

if %ERRORLEVEL% EQU 0 (
    echo Successfully fixed root user permissions!
) else (
    echo Failed to fix permissions. Trying alternative method...
    "C:\xampp\mysql\bin\mysql.exe" -u root mysql -e "UPDATE user SET plugin='', authentication_string='' WHERE User='root' AND Host='localhost'; FLUSH PRIVILEGES;"
)

echo Stopping MySQL...
taskkill /F /IM mysqld.exe
timeout /t 3 >nul

echo Starting MySQL normally...
start "" "C:\xampp\mysql\bin\mysqld.exe" --console

echo.
echo Fix completed! You can now access phpMyAdmin and the application.
echo Press any key to close this window...
pause >nul
