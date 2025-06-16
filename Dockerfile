FROM php:8.2-fpm

# Install dependencies
RUN apt-get update && apt-get install -y \
    libzip-dev \
    unzip \
    curl \
    git \
    mariadb-client \
    && docker-php-ext-install pdo pdo_mysql zip

# Set working directory
WORKDIR /var/www/html

# Copy source
COPY . .

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Optional: install dependencies now
# RUN composer install

# Set permissions
RUN chown -R www-data:www-data /var/www/html
