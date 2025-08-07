FROM php:8.4-fpm-alpine

RUN apk --no-cache add shadow sudo

RUN apk update && apk add --no-cache \
    $PHPIZE_DEPS \
    bash \
    git \
    libmcrypt-dev \
    libpng-dev \
    libwebp-dev \
    libzip-dev \
    nodejs \
    npm \
    openssl \
    unzip \
    vim \
    wget \
    zip \
    rabbitmq-c-dev \
    icu-dev

RUN docker-php-ext-install \
    bcmath \
    gd \
    pdo \
    mysqli \
    pdo_mysql \
    zip \
    intl

# Установка расширения AMQP через PECL
RUN pecl install amqp && \
    docker-php-ext-enable amqp

# Установка Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin/ --filename=composer

RUN rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /var/cache/*
RUN usermod -u 1000 www-data
RUN chown -R www-data:www-data /var/www/html

# Установим рабочую директорию
WORKDIR /var/www

# Переключаемся на пользователя www-data
USER www-data