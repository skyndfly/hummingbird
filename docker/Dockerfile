FROM php:8.4-fpm

RUN apt-get update && apt-get install -y \
    zip \
    unzip \
    git \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql


COPY --from=composer/composer:2-bin /composer /usr/bin/composer


WORKDIR /var/www/html