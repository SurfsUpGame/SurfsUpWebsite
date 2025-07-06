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
    supervisor \
    && docker-php-ext-install pdo pdo_mysql zip intl

# Set working directory
WORKDIR /var/www/html

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy supervisor configuration files first
COPY docker/supervisor/supervisord.conf /etc/supervisor/supervisord.conf

# Copy app source
COPY . .

# Set git safe directory
RUN git config --global --add safe.directory /var/www/html

# Install dependencies
RUN composer install --no-dev --optimize-autoloader
RUN npm install && npm run build

# Create necessary directories and set permissions
RUN mkdir -p /var/www/html/storage/logs \
    && mkdir -p /var/www/html/bootstrap/cache \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Copy and set permissions for entrypoint script
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Set file ownership
RUN chown -R www-data:www-data /var/www/html

# Set entrypoint
ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
