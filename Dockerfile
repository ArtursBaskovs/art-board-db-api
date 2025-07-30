FROM php8.2-apache

RUN a2enmod rewrite

RUN docker-php-ext-install pdo pdo_mysql

COPY . varwwwhtml

RUN chown -R www-datawww-data varwwwhtml

RUN chmod -R 755 varwwwhtml

RUN echo Directory varwwwhtmln
    AllowOverride Alln
    Require all grantedn
Directory  etcapache2apache2.conf

EXPOSE 80
