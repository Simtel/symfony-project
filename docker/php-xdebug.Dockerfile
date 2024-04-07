FROM php:8.3-fpm

RUN apt-get update \
    && apt-get install -y libwebp-dev libjpeg62-turbo-dev libpng-dev libxpm-dev libfreetype6-dev cron \
    && docker-php-ext-install mysqli pdo_mysql \
    && docker-php-ext-enable xdebug \
    && apt-get clean; rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/* \
    && rm -rf /var/lib/apt/lists/* \
    && apt update \
    && apt-get -y install librabbitmq-dev \
    && pecl install amqp \
    && docker-php-ext-enable amqp

RUN docker-php-ext-install gd

RUN docker-php-ext-configure gd --with-jpeg --with-freetype

RUN apt-get update && apt-get install -y \
    zlib1g-dev \
    libzip-dev
RUN docker-php-ext-install zip
RUN docker-php-ext-install opcache
RUN apt update && apt install -y libicu-dev && rm -rf /var/lib/apt/lists/*
RUN docker-php-ext-configure intl
RUN docker-php-ext-install intl

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin/ --filename=composer

RUN yes | pecl install xdebug \
    && echo "zend_extension=$(find /usr/local/lib/php/extensions/ -name xdebug.so)" > /usr/local/etc/php/conf.d/xdebug.ini

RUN usermod -u 1000 www-data

WORKDIR /var/www

USER www-data
