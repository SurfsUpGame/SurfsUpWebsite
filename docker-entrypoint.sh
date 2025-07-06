#!/bin/bash

# Wait for database to be ready
echo "Waiting for database..."
until php artisan migrate:status > /dev/null 2>&1; do
  echo "Database is unavailable - sleeping"
  sleep 5
done

echo "Database is up - running migrations"
php artisan migrate --force

# Create log directory if it doesn't exist
mkdir -p /var/www/html/storage/logs
mkdir -p /var/log/supervisor
touch /var/www/html/storage/logs/queue-worker.log
chown -R www-data:www-data /var/www/html/storage/logs

# Start supervisor (this will manage both PHP-FPM and queue worker)
echo "Starting supervisor"
exec /usr/bin/supervisord -n -c /etc/supervisor/supervisord.conf