#!/bin/sh

# Start PHP-FPM in the background
php-fpm -D

# Check if APP_KEY is set, if not, warn (Laravel requires it)
if [ -z "$APP_KEY" ]; then
    echo "WARNING: APP_KEY is not set. You should set this in the Render dashboard."
fi

# Clear config cache just in case
echo "Clearing config..."
php artisan config:clear

# Debug Environment (Safe check)
echo "Checking Environment..."
echo "APP_ENV: $APP_ENV"
echo "DB_CONNECTION: $DB_CONNECTION"
if [ -n "$DB_URL" ]; then echo "DB_URL is set"; else echo "DB_URL is NOT set"; fi

echo "Checking database connection..."
php artisan db:monitor || echo "Database connection failed, but proceeding anyway..."

echo "Running migrations..."
php artisan migrate --force --no-interaction

echo "Creating storage link..."
php artisan storage:link --force || echo "Storage link already exists."

# Start NGINX in the foreground
echo "Starting NGINX..."
nginx -g "daemon off;"
