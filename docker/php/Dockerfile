FROM php:8.4-fpm-alpine3.21

# PHP EXTENSIONS
RUN apk add --no-cache \
    bash \
    shadow \
    libzip-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    libwebp-dev \
    libxml2-dev \
    oniguruma-dev \
    icu-dev \
    zlib-dev \
    libmcrypt-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring zip exif pcntl intl xml

# COMPOSER
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# XDEBUG ONLY IN DEVELOPMENT ENVIRONMENT VIA CONDITIONAL
ARG INSTALL_XDEBUG=false
RUN if [ "$INSTALL_XDEBUG" = "true" ]; then \
        apk add --no-cache $PHPIZE_DEPS linux-headers && \
        pecl install xdebug && \
        docker-php-ext-enable xdebug ; \
    fi

# CUSTOM PHP CONFIGURATION
COPY custom.ini /usr/local/etc/php/conf.d/

WORKDIR /var/www/lab

CMD ["php-fpm"]
