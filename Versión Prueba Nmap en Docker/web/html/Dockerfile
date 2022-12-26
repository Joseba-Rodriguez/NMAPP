FROM php:8-apache

RUN apt-get update

RUN apt-get install -y libpq-dev \ 
  && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
  && docker-php-ext-install pdo pdo_pgsql pgsql