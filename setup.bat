@echo off
echo ============================================
echo   Shipping Company - Auto Setup
echo ============================================
echo.

echo [1/5] Installing PHP dependencies...
composer install --ignore-platform-req=php
echo Done.

echo [2/5] Copying environment file...
copy .env.laragon .env >nul
echo Done.

echo [3/5] Creating database...
mysql -u root -e "CREATE DATABASE IF NOT EXISTS shippingcompany;"
echo Done.

echo [4/5] Running migrations...
php artisan migrate --force
echo Done.

echo [5/5] Seeding data...
php artisan db:seed --class=ShipmentSeeder --force
php artisan db:seed --class=CoaRecordSeeder --force
echo Done.

echo.
echo ============================================
echo   Setup complete!
echo   Open: http://shippingcompany.test
echo ============================================
pause
