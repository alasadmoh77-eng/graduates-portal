FROM richarvey/nginx-php-fpm:3.1.6

WORKDIR /var/www/html

COPY . .

RUN composer install --no-dev --optimize-autoloader --ignore-platform-req=ext-calendar \
    && php artisan optimize:clear \
    && chmod -R 775 storage bootstrap/cache

ENV WEBROOT=/var/www/html/public

CMD php artisan migrate --force && php artisan db:seed --force && php artisan storage:link || true && /start.sh

EXPOSE 80