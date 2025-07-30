FROM php:8.2-apache

RUN a2enmod rewrite

RUN docker-php-ext-install pdo pdo_mysql

COPY . /var/www/html/

RUN echo '<Directory /var/www/html/>\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' >> /etc/apache2/apache2.conf

RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 755 /var/www/html

EXPOSE 80
