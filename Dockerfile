FROM composer:2 as composer

WORKDIR /app

COPY database /app/database
COPY composer.json /app/composer.json
COPY composer.lock /app/composer.lock

RUN composer install --no-interaction --no-plugins --no-scripts --prefer-dist


FROM node:18-alpine as npm

WORKDIR /app

COPY package.json /app/package.json
COPY package-lock.json /app/package-lock.json
COPY vite.config.js /app/vite.config.js
COPY postcss.config.js /app/postcss.config.js
COPY tailwind.config.js /app/tailwind.config.js
COPY resources /app/resources

RUN npm install && npm run build


FROM php:8.2-fpm-alpine

WORKDIR /var/www/html

ARG UID=1000
ARG GID=1000

RUN addgroup -g ${GID} laravel && adduser -G laravel -s /bin/sh -D -u ${UID} laravel

RUN apk add --no-cache \
    nginx \
    supervisor \
    mysql-client \
    libzip-dev \
    libpng-dev \
    libxml2-dev \
    zip \
    unzip \
    curl

RUN docker-php-ext-install pdo pdo_mysql zip gd bcmath

COPY --from=composer /app/vendor /var/www/html/vendor
COPY --from=npm /app/public /var/www/html/public

COPY . .

RUN chown -R laravel:laravel /var/www/html && \
    chmod -R 755 /var/www/html/storage && \
    chmod -R 755 /var/www/html/bootstrap/cache

EXPOSE 80

CMD ["php-fpm"]
