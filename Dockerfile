FROM richarvey/nginx-php-fpm:3.1.6

WORKDIR /var/www/html

COPY . .

RUN apk add --no-cache nodejs npm

RUN composer install --no-dev --optimize-autoloader --ignore-platform-req=ext-calendar

RUN npm install
RUN npm run build

RUN chmod -R 775 storage bootstrap/cache

ENV WEBROOT=/var/www/html/public

CMD sh -c "php artisan migrate --force || true; php artisan db:seed --force || true; php artisan storage:link || true; /start.sh"

EXPOSE 80