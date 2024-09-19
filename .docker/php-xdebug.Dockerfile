FROM php:8.3-fpm-alpine

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
    icu-dev \
    rabbitmq-c-dev \
    linux-headers

RUN docker-php-ext-install \
    bcmath \
    gd \
    pdo \
    mysqli \
    pdo_mysql \
    zip \
    intl


# Установка расширения AMQP
RUN pecl install amqp && \
    docker-php-ext-enable amqp

# Установка Xdebug
RUN pecl install xdebug && \
    docker-php-ext-enable xdebug

# Установка Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /var/cache/*
RUN usermod -u 1000 www-data
RUN chown -R www-data:www-data /var/www/html

# Установка рабочей директории
WORKDIR /var/www

# Переход на пользователя www-data
USER www-data

