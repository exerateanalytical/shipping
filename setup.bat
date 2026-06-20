@echo off
echo ============================================
echo   Shipping Company - Auto Setup
echo ============================================
echo.

echo [1/4] Copying environment file...
copy .env.laragon .env >nul
echo Done.

echo [2/4] Creating database...
mysql -u root -e "CREATE DATABASE IF NOT EXISTS shippingcompany;"
echo Done.

echo [3/4] Running migrations...
php artisan migrate --force
echo Done.

echo [4/4] Seeding data...
php artisan db:seed --class=ShipmentSeeder --force
php artisan db:seed --class=CoaRecordSeeder --force
echo Done.

echo.
echo ============================================
echo   Setup complete!
echo   Open: http://shippingcompany.test
echo ============================================
pause
