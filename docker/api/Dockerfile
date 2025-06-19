FROM php:8.4-apache

RUN apt-get update
RUN docker-php-ext-install pdo_mysql
RUN a2enmod rewrite

ENTRYPOINT ["apache2-foreground"]

EXPOSE 80

WORKDIR /var/www/html