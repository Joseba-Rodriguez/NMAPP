FROM python:3

RUN apt-get -y update && apt-get -y upgrade
RUN apt-get install -y python3-pip
RUN pip install --upgrade pip
RUN apt-get install python3
RUN pip install python-nmap
RUN apt-get install -y nmap
RUN apt-get install -y xsltproc
RUN apt-get install -y  apache2

WORKDIR /usr/src/app
COPY python-nmap.py .
COPY ips.txt .

CMD [ "python", "./python-nmap.py" ]

