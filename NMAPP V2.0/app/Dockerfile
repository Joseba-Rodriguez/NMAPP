FROM php:8-apache
RUN apt-get -y update && apt-get -y upgrade
COPY ./vat/www/html/
RUN apt-get install -y libpq-dev \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \ 
    && docker-php-ext-install pdo pdo_pgsql pgsql
RUN apt-get install -y python3-pip
RUN pip install --upgrade pip
RUN apt-get install python3
RUN apt-get install -y nmap
RUN apt-get install -y apache2
RUN pip install psycopg2
RUN pip install schedule

RUN a2enmod ssl
COPY default-ssl.conf /etc/apache2/sites-available/default-ssl.conf
RUN a2ensite default-ssl.conf
