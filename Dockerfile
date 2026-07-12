# ==============================
# Build frontend assets with Vite
# ==============================
FROM node:22-alpine AS frontend

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci

COPY . .
RUN npm run build


# ==============================
# Laravel + Nginx + PHP-FPM
# ==============================
FROM richarvey/nginx-php-fpm:3.1.6

WORKDIR /var/www/html

COPY . .

COPY --from=frontend /app/public/build /var/www/html/public/build

RUN composer install \
    --no-dev \
    --prefer-dist \
    --no-interaction \
    --optimize-autoloader \
    --ignore-platform-req=ext-calendar \
    && mkdir -p \
        storage/app/private \
        storage/app/public \
        storage/fonts \
        storage/framework/cache \
        storage/framework/sessions \
        storage/framework/views \
        storage/logs \
        bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache public \
    && sed -i 's/try_files $uri $uri\/ =404;/try_files $uri $uri\/ \/index.php?$query_string;/g' \
        /etc/nginx/sites-available/default.conf || true

ENV WEBROOT=/var/www/html/public
ENV SKIP_COMPOSER=1
ENV PHP_ERRORS_STDERR=1
ENV LOG_CHANNEL=stderr

CMD ["sh", "-c", "php artisan optimize:clear && php artisan migrate --force && php artisan db:seed --class=MajorSeeder --force && php artisan db:seed --class=DocumentTypeSeeder --force && php artisan db:seed --class=ProductionAdminSeeder --force && (php artisan storage:link || true) && php artisan config:cache && exec /start.sh"]

EXPOSE 80