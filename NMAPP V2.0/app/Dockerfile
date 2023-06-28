FROM php:8-apache

RUN apt-get -y update
RUN apt-get -y upgrade
RUN apt-get install -y libpq-dev 
RUN apt-get install apache2 
RUN apt-get install -y nmap

RUN docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql

RUN docker-php-ext-install pdo pdo_pgsql pgsql

COPY . /var/www/html/

RUN a2enmod ssl

COPY default-ssl.conf /etc/apache2/sites-available/default-ssl.conf

RUN a2ensite default-ssl.conf
