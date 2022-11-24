FROM python:3

RUN apt-get update -y && apt-get -y upgrade
RUN apt-get install -y python3-pip
RUN pip install --upgrade pip
RUN apt-get install python3
RUN pip install python-nmap 
RUN apt-get install -y nmap

WORKDIR /usr/src/app
COPY python-nmap.py .
COPY /datos/ips.txt .

CMD [ "python", "./python-nmap.py" ]
