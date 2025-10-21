FROM php:8.2-apache

COPY ./ /var/www/html/

RUN a2enmod rewrite

RUN docker-php-ext-install pdo pdo_mysql

EXPOSE 80