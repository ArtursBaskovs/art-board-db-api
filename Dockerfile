FROM php:8.2-apache

RUN apt-get update && apt-get install -y unzip git default-mysql-client libonig-dev libzip-dev libpng-dev libxml2-dev && \
    docker-php-ext-install pdo pdo_mysql mysqli && \
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN a2enmod rewrite

WORKDIR /var/www/html

RUN sed -i 's/AllowOverride None/AllowOverride All/i' /etc/apache2/apache2.conf

ENV COMPOSER_ALLOW_SUPERUSER=1

COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader

COPY . .

RUN chown -R www-data:www-data /var/www/html
