# ── Stage 1: Composer dependencies ──────────────────────────────────
FROM composer:2 AS composer_stage
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --prefer-dist \
    --optimize-autoloader \
    --no-interaction \
    --no-scripts

# ── Stage 2: Final PHP-FPM image ─────────────────────────────────────
FROM php:8.2-fpm-alpine

# Install system dependencies & PHP extensions
RUN apk add --no-cache \
        bash \
        libpng-dev \
        libjpeg-turbo-dev \
        libwebp-dev \
        freetype-dev \
        libzip-dev \
        zip \
        unzip \
        postgresql-dev \
        ghostscript \
    && docker-php-ext-configure gd \
        --with-freetype \
        --with-jpeg \
        --with-webp \
    && docker-php-ext-install -j$(nproc) \
        pdo \
        pdo_pgsql \
        pgsql \
        gd \
        zip \
        exif \
        pcntl \
        bcmath \
    && pecl install redis \
    && docker-php-ext-enable redis

# Copy composer artifacts
COPY --from=composer_stage /app/vendor /var/www/html/vendor

# Copy application source
WORKDIR /var/www/html
COPY . .

# Set correct permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

EXPOSE 9000
CMD ["php-fpm"]
