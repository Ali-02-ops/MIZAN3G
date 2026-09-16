FROM composer:2 AS vendor

WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --no-scripts
COPY . .
RUN composer dump-autoload --no-dev --optimize --no-scripts

FROM node:22-bookworm-slim AS frontend

WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY resources ./resources
COPY public ./public
COPY vite.config.js ./
RUN npm run build

FROM php:8.3-cli-bookworm

WORKDIR /var/www/html
RUN apt-get update && apt-get install -y --no-install-recommends libpng-dev libpq-dev libsqlite3-dev libzip-dev \
    && docker-php-ext-install gd mbstring pdo_pgsql pdo_sqlite zip \
    && rm -rf /var/lib/apt/lists/*
COPY --from=vendor /app ./
COPY --from=frontend /app/public/build ./public/build
COPY docker/entrypoint.sh /usr/local/bin/mizan3g-entrypoint
RUN chmod +x /usr/local/bin/mizan3g-entrypoint \
    && chown -R www-data:www-data storage bootstrap/cache

EXPOSE 10000
CMD ["mizan3g-entrypoint"]
