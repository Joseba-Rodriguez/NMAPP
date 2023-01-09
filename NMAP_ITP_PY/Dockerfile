FROM python:3

RUN apt-get -y update && apt-get -y upgrade
RUN apt-get install -y python3-pip
RUN pip install --upgrade pip
RUN apt-get install python3
RUN apt-get install -y nmap
RUN apt-get install -y apache2
RUN pip install psycopg2
RUN pip install flask
RUN pip install flask-login
RUN pip install flask-sqlalchemy

WORKDIR /usr/src/app
COPY python-nmap.py .
COPY ./web/Storer.py .
COPY ips.txt .

CMD [ "python", "./python-nmap.py" ]

