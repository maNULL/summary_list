FROM composer:latest AS composer

FROM php:8.1.8-fpm-alpine3.15

LABEL maintainer="John Komarov <komarov.j@gmail.com>"

COPY --from=composer /usr/bin/composer /usr/bin/composer

RUN apk add --no-cache --virtual .phpize-deps $PHPIZE_DEPS \
    && apk add --no-cache libnsl libaio-dev icu-dev postgresql-dev \
    && ln -s /usr/lib/libnsl.so.2 /usr/lib/libnsl.so.1 \
    \
    && pecl install xdebug-3.1.5 \
    && pecl install apcu-5.1.21 \
    \
    && docker-php-ext-configure opcache --enable-opcache \
    \
    && docker-php-ext-install -j$(nproc) \
        opcache \
        intl \
        pdo \
        pdo_pgsql \
    \
    && docker-php-ext-enable xdebug apcu pdo_pgsql \
    \
    && apk del .phpize-deps