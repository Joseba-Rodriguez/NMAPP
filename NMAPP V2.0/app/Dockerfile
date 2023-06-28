FROM php:8-apache

RUN apt-get update && apt-get -y upgrade && \
    apt-get install -y libpq-dev python3-pip python3 python3-venv apache2 nmap && \
    docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql && \
    docker-php-ext-install pdo pdo_pgsql pgsql

COPY . /var/www/html/

RUN python3 -m venv /venv && \
    /venv/bin/pip install --upgrade pip && \
    /venv/bin/pip install psycopg2 schedule

RUN a2enmod ssl
COPY default-ssl.conf /etc/apache2/sites-available/default-ssl.conf
RUN a2ensite default-ssl.conf
