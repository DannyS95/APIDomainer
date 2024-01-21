FROM php:8.3.2RC1-fpm-alpine3.18

WORKDIR /app

RUN docker-php-ext-install bcmath && \
 apk add libzip-dev && \
 docker-php-ext-install pdo pdo_mysql

COPY . /app/
 
COPY --from=composer:2.6.6 /usr/bin/composer /usr/bin/composer

RUN composer --version && \
    composer install