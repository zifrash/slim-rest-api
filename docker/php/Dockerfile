FROM php:8.3-fpm-alpine

ARG TIMEZONE

RUN apk add --no-cache git libpq-dev autoconf build-base linux-headers libzip-dev && \
    docker-php-ext-install sockets zip pdo_pgsql && \
    pecl install xdebug && docker-php-ext-enable xdebug

RUN echo date.timezone = ${TIMEZONE} > /usr/local/etc/php/conf.d/custom.timezone.ini

RUN adduser -D appuser wheel
USER appuser
WORKDIR /var/www/html/

COPY --chown=appuser:appuser --from=composer:latest /usr/bin/composer /usr/bin/composer
COPY --chown=appuser:appuser ./docker/php/composer/*.pub /home/appuser/.composer/
