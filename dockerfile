FROM php:8.2-fpm

# Install system packages and PHP extensions
RUN apt-get update && apt-get install -y \
    git curl unzip zip libzip-dev libicu-dev g++ zlib1g-dev \
    && docker-php-ext-install intl zip pdo pdo_mysql \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install Caddy
COPY --from=caddy:2 /usr/bin/caddy /usr/bin/caddy

WORKDIR /var/www

# Copy all project files
COPY . .

# Set folder permissions
RUN chmod -R 775 storage bootstrap/cache

# Install PHP dependencies (after extensions are installed)
RUN composer install --optimize-autoloader --no-interaction --no-scripts

# Expose ports
EXPOSE 80 9000

# Start PHP-FPM and Caddy
CMD php artisan migrate --force && php-fpm & caddy run --config /etc/caddy/Caddyfile --adapter caddyfile