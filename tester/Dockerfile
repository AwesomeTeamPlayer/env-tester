FROM php:7.1-fpm

ADD . /app
WORKDIR /app

RUN apt-get update && \
    apt-get install -y --no-install-recommends git zip unzip

RUN docker-php-ext-install mbstring
RUN docker-php-ext-install mysqli
RUN docker-php-ext-install bcmath

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN composer install
