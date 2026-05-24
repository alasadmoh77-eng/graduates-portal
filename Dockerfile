FROM richarvey/nginx-php-fpm:3.1.6

WORKDIR /var/www/html

COPY . .

RUN apk add --no-cache nodejs npm \
    && npm install && npm run build \
    && composer install --no-dev --optimize-autoloader --ignore-platform-req=ext-calendar \
    && php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache \
    && chmod -R 775 storage bootstrap/cache public

ENV WEBROOT=/var/www/html/public

CMD sh -c "php artisan migrate --force || true; php artisan db:seed --force || true; /start.sh"

EXPOSE 80