#We use the official container php verion 8 of apache 
FROM php:8-apache

#Firstly we update the OS so that we can not get a firmware error
RUN apt-get -y update && apt-get -y upgrade
#Copy all the path to the apache main folder
COPY . /var/www/html/

#We install all the things that we need 
#Postgresql libraries should be installed
RUN apt-get install -y libpq-dev \ 
  && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
  && docker-php-ext-install pdo pdo_pgsql pgsql 
#This python3-pip needs to be installed in order to install any library in python 
RUN apt-get install -y python3-pip
RUN pip install --upgrade pip
#We install the main language
RUN apt-get install python3
#For the execution we need nmap
RUN apt-get install -y nmap
#Also we need the apache version, just to launch the web interface
RUN apt-get install -y apache2
#psycog2 is for the connection in python with the database postgresql 
RUN pip install psycopg2
#To schedule the PDF export we need this python library
RUN pip install schedule
