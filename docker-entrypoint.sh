#!/bin/bash

# Wait for database to be ready
echo "Waiting for database..."
until php artisan migrate:status > /dev/null 2>&1; do
  echo "Database is unavailable - sleeping"
  sleep 5
done

echo "Database is up - running migrations"
php artisan migrate --force

# Clear and optimize caches for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Create necessary directories and set permissions
mkdir -p /var/www/html/storage/logs
mkdir -p /var/log/supervisor
mkdir -p /var/run
touch /var/www/html/storage/logs/queue-worker.log
touch /var/www/html/storage/logs/laravel.log
touch /var/www/html/storage/logs/scheduler.log

# Ensure proper permissions
chown -R www-data:www-data /var/www/html/storage
chown -R www-data:www-data /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage
chmod -R 775 /var/www/html/bootstrap/cache

# Start supervisor (this will manage PHP-FPM, queue worker, and scheduler)
echo "Starting supervisor..."
exec /usr/bin/supervisord -n -c /etc/supervisor/supervisord.conf