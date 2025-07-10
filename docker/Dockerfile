FROM php:8.2-fpm

# Instala dependências
RUN apt-get update && apt-get install -y \
    git unzip libzip-dev libpng-dev libonig-dev libxml2-dev \
    && docker-php-ext-install pdo_mysql zip gd mbstring exif pcntl bcmath

# Configura PHP
COPY php.ini /usr/local/etc/php/conf.d/custom.ini

WORKDIR /var/www