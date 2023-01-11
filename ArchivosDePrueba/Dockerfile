FROM python:3

RUN apt-get -y update && apt-get -y upgrade
RUN apt-get install -y python3-pip
RUN pip install --upgrade pip
RUN apt-get install python3
RUN apt-get install -y nmap
RUN apt-get install -y apache2
RUN pip install psycopg2

WORKDIR /usr/src/app
COPY ./app/python-nmap.py .
COPY ./app/web/Storer.py .
COPY ./app/web/html/ips2.txt .
COPY ./app/ips.txt .

CMD [ "python", "./app/python-nmap.py" ]

