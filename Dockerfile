FROM richarvey/nginx-php-fpm:3.1.6

WORKDIR /var/www/html

COPY . .

RUN apk add --no-cache curl bash \
    && curl -fsSL https://unofficial-builds.nodejs.org/download/release/v22.12.0/node-v22.12.0-linux-x64-musl.tar.xz -o node.tar.xz \
    && mkdir -p /usr/local/node \
    && tar -xJf node.tar.xz -C /usr/local/node --strip-components=1 \
    && ln -sf /usr/local/node/bin/node /usr/local/bin/node \
    && ln -sf /usr/local/node/bin/npm /usr/local/bin/npm \
    && ln -sf /usr/local/node/bin/npx /usr/local/bin/npx \
    && rm node.tar.xz

RUN composer install --no-dev --optimize-autoloader --ignore-platform-req=ext-calendar

RUN npm install
RUN npm run build

RUN chmod -R 775 storage bootstrap/cache

ENV WEBROOT=/var/www/html/public

CMD sh -c "php artisan migrate --force || true; php artisan db:seed --force || true; php artisan storage:link || true; /start.sh"

EXPOSE 80