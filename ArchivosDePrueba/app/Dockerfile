FROM php:8-apache

RUN apt-get update
COPY /web/ /var/www/html/

RUN apt-get install -y libpq-dev \ 
  && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
  && docker-php-ext-install pdo pdo_pgsql pgsql \ 
  && apt-get install -y python3 \ 
  && apt-get install -y nmap \

EXPOSE 80