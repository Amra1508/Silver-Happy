FROM php:8.2-apache AS builder

RUN docker-php-ext-install mysqli pdo_mysql

RUN a2enmod rewrite

RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

FROM builder AS production

EXPOSE 80
COPY . /var/www/html/