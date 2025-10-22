FROM php:8.2-apache

COPY ./ /var/www/html/

RUN useradd -u 1800 horseisle
RUN a2enmod rewrite

RUN docker-php-ext-install mysqli
RUN chown -R www-data:www-data /var/www/html

ENV APP_ENV production
ENV APACHE_DOCUMENT_ROOT /var/www/html/

EXPOSE 80/tcp

# shitty hack to make the hisp folder still accessible to apache.
# (surely, theres a better way to do this?)
ENTRYPOINT bash -c 'chown www-data:www-data -R /etc/hisp;apache2-foreground'
