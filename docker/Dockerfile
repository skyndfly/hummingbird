FROM php:8.4-fpm

RUN apt-get update && apt-get install -y \
    zip \
    unzip \
    git \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql


COPY --from=composer/composer:2-bin /composer /usr/bin/composer

# Создаем пользователя www-data если нет и устанавливаем правильные права
RUN groupmod -o -g 1001 www-data && \
    usermod -o -u 1001 www-data && \
    mkdir -p /var/www/html/runtime /var/www/html/web/assets && \
    chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html && \
    chmod -R 775 /var/www/html/runtime /var/www/html/web/assets

RUN git config --global --add safe.directory /var/www/html
RUN echo "memory_limit=512M" > /usr/local/etc/php/conf.d/memory-limit.ini
WORKDIR /var/www/html

# Меняем пользователя на www-data
USER www-data