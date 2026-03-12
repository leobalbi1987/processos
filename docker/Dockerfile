# ---------- COMPOSER ----------
FROM composer:2 AS composer

WORKDIR /app

COPY composer.json composer.lock ./

RUN composer install --no-interaction --no-plugins --no-scripts --prefer-dist


# ---------- NODE ----------
FROM node:18-alpine AS npm

WORKDIR /app

COPY package*.json ./
COPY vite.config.js ./
COPY postcss.config.js ./
COPY tailwind.config.js ./
COPY resources ./resources

RUN npm install && npm run build


# ---------- PHP ----------
FROM php:8.2-fpm-alpine

WORKDIR /var/www/html

ARG UID=1000
ARG GID=1000

RUN addgroup -g ${GID} laravel \
    && adduser -G laravel -s /bin/sh -D -u ${UID} laravel

RUN apk add --no-cache \
    mysql-client \
    libzip-dev \
    libpng-dev \
    libxml2-dev \
    zip \
    unzip \
    curl

RUN docker-php-ext-install pdo pdo_mysql zip gd bcmath

# copiar dependências
COPY --from=composer /app/vendor /var/www/html/vendor
COPY --from=npm /app/public /var/www/html/public

# copiar projeto
COPY . .

RUN chown -R laravel:laravel /var/www/html \
    && chmod -R 775 storage bootstrap/cache

USER laravel

EXPOSE 9000

CMD ["php-fpm"]
