# Stage 1: Builder - Instala dependências
FROM composer:2 AS builder
WORKDIR /app
COPY composer.json .
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Stage 2: Runtime - Configura o ambiente PHP
FROM php:8.2-fpm-alpine

WORKDIR /var/www

# Dependências do sistema e extensões PHP
RUN apk update && apk add \
    build-base \
    libzip-dev \
    zip \
    unzip \
    mysql-client \
    && docker-php-ext-install pdo pdo_mysql zip

# Copia apenas as dependências instaladas (do estágio builder)
COPY --from=builder /app/vendor /var/www/vendor

COPY --from=builder /app/composer.lock /var/www/composer.lock

# Copia o restante do código
COPY . .

# Configura usuário não-root (segurança)
RUN addgroup -g 1000 -S www && \
    adduser -u 1000 -S www -G www && \
    chown -R www:www /var/www

USER www

EXPOSE 9000