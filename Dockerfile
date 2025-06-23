# docker compose -f docker-compose-prod.yml up -d --build
FROM php:8.3-fpm

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    libzip-dev \
    unzip \
    git \
    curl \
    mariadb-client \
    libicu-dev \
    nodejs \
    npm \
    && docker-php-ext-install pdo pdo_mysql zip intl

# Set working directory
WORKDIR /var/www/html

# Copy app source
COPY . .

RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
 && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN git config --global --add safe.directory /var/www/html
RUN composer install
RUN npm install && npm run build
RUN php artisan migrate

# Set file ownership
RUN chown -R www-data:www-data /var/www/html
