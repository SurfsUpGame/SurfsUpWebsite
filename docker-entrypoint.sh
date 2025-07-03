#!/bin/bash

# Wait for database to be ready
echo "Waiting for database..."
until php artisan migrate:status > /dev/null 2>&1; do
  echo "Database is unavailable - sleeping"
  sleep 5
done

echo "Database is up - running migrations"
php artisan migrate --force

echo "Starting queue worker in background"
php artisan queue:work --daemon --tries=3 --timeout=300 &

# Start PHP-FPM
echo "Starting PHP-FPM"
php-fpm