FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    git curl zip unzip \
    libzip-dev \
    libicu-dev \
    && docker-php-ext-install intl zip pdo pdo_mysql

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
COPY --from=caddy:2 /usr/bin/caddy /usr/bin/caddy

WORKDIR /var/www
COPY . .

RUN composer install --optimize-autoloader --no-interaction
RUN chmod -R 775 storage bootstrap/cache

COPY Caddyfile /etc/caddy/Caddyfile

CMD php artisan migrate --force && php-fpm & caddy run --config /etc/caddy/Caddyfile --adapter caddyfile