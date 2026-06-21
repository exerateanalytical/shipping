@echo off
echo ============================================
echo   Shipping Company - Auto Setup
echo ============================================
echo.

echo [0/6] Checking for wkhtmltopdf...
set WKHTML_DEFAULT="C:\Program Files\wkhtmltopdf\bin\wkhtmltopdf.exe"
where wkhtmltopdf >nul 2>&1
if %errorlevel% equ 0 (
    echo   Found in PATH. OK.
) else if exist %WKHTML_DEFAULT% (
    echo   Found at default install path. OK.
) else (
    echo.
    echo   *** wkhtmltopdf NOT found! ***
    echo   PDF downloads require wkhtmltopdf to render the preview exactly.
    echo.
    echo   1. Download installer from:
    echo      https://wkhtmltopdf.org/downloads.html
    echo      (choose the Windows 64-bit installer)
    echo.
    echo   2. Install it (default path is fine)
    echo.
    echo   3. Re-run this setup.bat
    echo.
    pause
    exit /b 1
)

echo [1/6] Installing PHP dependencies...
composer install --ignore-platform-req=php
echo Done.

echo [2/6] Copying environment file...
copy .env.laragon .env >nul
echo Done.

echo [3/6] Creating database...
mysql -u root -e "CREATE DATABASE IF NOT EXISTS shippingcompany;"
echo Done.

echo [4/6] Running migrations...
php artisan migrate --force
echo Done.

echo [5/6] Seeding data...
php artisan db:seed --class=ShipmentSeeder --force
php artisan db:seed --class=CoaRecordSeeder --force
echo Done.

echo [6/6] Clearing config cache...
php artisan config:clear
echo Done.

echo.
echo ============================================
echo   Setup complete!
echo   Open: http://shippingcompany.test
echo ============================================
pause
