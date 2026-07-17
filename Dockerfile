# syntax=docker/dockerfile:1
#
# Production image for the Laravel API (php-fpm + nginx in one container via
# supervisor). Host-agnostic: runs on any container platform (Fly, Render,
# ECS, Kubernetes, a plain VPS). See DEPLOYMENT.md for the operational runbook.

# ---- Stage 1: PHP dependencies + optimized autoloader --------------------
# Runs in the composer image (which has both composer and, after COPY, the app
# code) so the runtime image needs no composer at all. --no-scripts skips
# package:discover (it runs at boot via the entrypoint instead).
FROM composer:2 AS vendor
WORKDIR /app
COPY . .
RUN composer install --no-dev --optimize-autoloader --no-scripts --prefer-dist --no-interaction

# ---- Stage 2: runtime -----------------------------------------------------
FROM php:8.4-fpm AS runtime

# System packages + PHP extensions needed in production.
RUN apt-get update && apt-get install -y --no-install-recommends \
        nginx supervisor \
        libpng-dev libjpeg-dev libfreetype6-dev libzip-dev libicu-dev libonig-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" pdo_mysql mbstring bcmath gd zip intl pcntl opcache \
    && pecl install redis && docker-php-ext-enable redis \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Opcache tuned for production.
COPY docker/opcache.ini /usr/local/etc/php/conf.d/opcache.ini
COPY docker/nginx.conf /etc/nginx/sites-available/default
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/entrypoint.sh /usr/local/bin/entrypoint
RUN chmod +x /usr/local/bin/entrypoint

WORKDIR /var/www/html

# App code + the vendored dependencies (with optimized autoloader) built above.
COPY --chown=www-data:www-data . .
COPY --from=vendor --chown=www-data:www-data /app/vendor ./vendor
RUN chown -R www-data:www-data storage bootstrap/cache

EXPOSE 8080
ENTRYPOINT ["entrypoint"]
CMD ["supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
