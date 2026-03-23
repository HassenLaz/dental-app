# Use PHP FPM 8.2
FROM php:8.2-fpm

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    git curl zip unzip libzip-dev libicu-dev \
    nodejs npm \
    && docker-php-ext-install intl zip pdo pdo_mysql \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install Caddy
COPY --from=caddy:2 /usr/bin/caddy /usr/bin/caddy

# Set working directory
WORKDIR /var/www

# Copy project files
COPY . .

# Install PHP dependencies
RUN composer install --optimize-autoloader --no-interaction --no-scripts

# Install Node dependencies (if you have frontend build)
RUN npm install

# Set permissions
RUN chmod -R 775 storage bootstrap/cache

# Copy Caddyfile
COPY Caddyfile /etc/caddy/Caddyfile

# Expose ports
EXPOSE 80 9000

# Entrypoint: run migrations, PHP-FPM, and Caddy
CMD php artisan migrate --force && php-fpm & caddy run --config /etc/caddy/Caddyfile --adapter caddyfile