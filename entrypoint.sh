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

# APP_KEY Validation
if [ -n "$APP_KEY" ]; then
    echo "APP_KEY check:"
    echo "  - Raw Length: ${#APP_KEY}"
    case "$APP_KEY" in
        "base64:"*) echo "  - Starts with base64: Yes" ;;
        *) echo "  - Starts with base64: NO" ;;
    esac
else
    echo "APP_KEY is NOT set!"
fi

echo "Checking database connection..."
# Try to connect up to 5 times
NEXT_WAIT_TIME=0
until php artisan db:monitor || [ $NEXT_WAIT_TIME -eq 5 ]; do
   echo "Waiting for database connection... ($NEXT_WAIT_TIME/5)"
   sleep 2
   NEXT_WAIT_TIME=$((NEXT_WAIT_TIME+1))
done

echo "Running fresh migrations..."
# We use migrate:fresh --force because the first attempt was interrupted and left stale tables
php artisan migrate:fresh --force --no-interaction || echo "Migration failed! Check your DB settings."

echo "Migration Status:"
php artisan migrate:status

echo "Ensuring storage permissions..."
# Ensure www-data can write to storage despite container quirks
chmod -R 777 /var/www/html/storage /var/www/html/bootstrap/cache

echo "Creating storage link..."
php artisan storage:link --force || echo "Storage link already exists."

# Start NGINX in the foreground
echo "Starting NGINX..."
nginx -g "daemon off;"
