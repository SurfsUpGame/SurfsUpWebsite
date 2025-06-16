FROM php:8.2-fpm

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    libzip-dev \
    unzip \
    git \
    curl \
    libicu-dev \
    mariadb-client \
    && docker-php-ext-install pdo pdo_mysql zip intl

# Set working directory
WORKDIR /var/www/html

# Copy app source
COPY . .

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Optional: install dependencies
# RUN composer install

# Set permissions
RUN chown -R www-data:www-data /var/www/html
