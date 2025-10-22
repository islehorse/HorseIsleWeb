FROM php:8.2-apache

COPY ./ /var/www/html/

RUN a2enmod rewrite php8.2

RUN docker-php-ext-install pdo pdo_mysql intl
RUN chown -R www-data:www-data /var/www/html
RUN chmod 777 -R /etc

ENV APP_ENV production
ENV APACHE_DOCUMENT_ROOT /var/www/html/

EXPOSE 80