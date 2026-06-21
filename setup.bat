@echo off
echo ============================================
echo   Shipping Company - Auto Setup
echo ============================================
echo.

echo [0/6] Checking for wkhtmltopdf...
set WKHTMLTOPDF_EXE=C:\Program Files\wkhtmltopdf\bin\wkhtmltopdf.exe
if exist "%WKHTMLTOPDF_EXE%" (
    echo   Already installed. OK.
    goto :wkhtml_done
)
where wkhtmltopdf >nul 2>&1
if %errorlevel% equ 0 (
    echo   Found in PATH. OK.
    goto :wkhtml_done
)
echo   Not found. Attempting download...
set WKHTML_URL=https://github.com/wkhtmltopdf/packaging/releases/download/0.12.6.1-3/wkhtmltox-0.12.6.1-3.msvc2019-win64.exe
set WKHTML_TMP=%TEMP%\wkhtmltox.exe

powershell -Command "& { [Net.ServicePointManager]::SecurityProtocol = [Net.SecurityProtocolType]::Tls12; Invoke-WebRequest -Uri '%WKHTML_URL%' -OutFile '%WKHTML_TMP%' -UseBasicParsing }" 2>nul

if not exist "%WKHTML_TMP%" (
    certutil -urlcache -split -f "%WKHTML_URL%" "%WKHTML_TMP%" >nul 2>&1
)

if not exist "%WKHTML_TMP%" (
    bitsadmin /transfer wkhtmltopdf "%WKHTML_URL%" "%WKHTML_TMP%" >nul 2>&1
)

if exist "%WKHTML_TMP%" (
    echo   Installing silently...
    "%WKHTML_TMP%" /S
    echo   wkhtmltopdf installed. OK.
) else (
    echo.
    echo   *** Auto-download failed. Install manually: ***
    echo   1. Open Chrome/Edge and go to: https://wkhtmltopdf.org/downloads.html
    echo   2. Download Windows 64-bit installer and install it
    echo   3. Re-run setup.bat
    echo.
    echo   Continuing setup - PDF download will not work until wkhtmltopdf is installed.
    echo.
)

:wkhtml_done

echo [1/6] Copying environment file...
copy .env.laragon .env >nul
echo Done.

echo [2/6] Installing PHP dependencies...
composer install --ignore-platform-req=php
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
