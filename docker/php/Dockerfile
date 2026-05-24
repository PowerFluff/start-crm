FROM php:8.5-fpm-alpine

RUN apk add --no-cache \
    icu-dev \
    postgresql-dev \
    unzip \
    git \
    curl \
    && docker-php-ext-install pdo pdo_pgsql pgsql intl

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html