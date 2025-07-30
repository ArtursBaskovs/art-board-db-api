FROM php:8.2-apache

RUN a2enmod rewrite

RUN apt-get update && apt-get install -y \
    libzip-dev \
    libcurl4-openssl-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    && docker-php-ext-install pdo pdo_mysql mbstring curl tokenizer xml zip

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY . /var/www/html/

WORKDIR /var/www/html
RUN composer install --no-dev --optimize-autoloader

RUN echo '<Directory /var/www/html/>\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' >> /etc/apache2/apache2.conf

RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 755 /var/www/html

EXPOSE 80
