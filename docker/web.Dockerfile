FROM php:8.2-fpm

RUN apt-get update \
    && apt-get install -y libwebp-dev libjpeg62-turbo-dev libpng-dev libxpm-dev libfreetype6-dev cron \
    && docker-php-ext-install mysqli pdo_mysql \
    && pecl install xdebug-2.9.2 \
    && docker-php-ext-enable xdebug \
    && apt-get clean; rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

RUN docker-php-ext-install gd

RUN docker-php-ext-configure gd --with-jpeg --with-freetype

RUN apt-get update && apt-get install -y \
    zlib1g-dev \
    libzip-dev
RUN docker-php-ext-install zip
RUN docker-php-ext-install opcache
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin/ --filename=composer

RUN usermod -u 1000 www-data

WORKDIR /var/www

USER www-data
