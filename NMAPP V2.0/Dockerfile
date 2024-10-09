FROM python:3.10

RUN apt-get -y update && apt-get -y upgrade \
    && apt-get install -y nmap apache2 

RUN pip install psycopg2-binary schedule pandas FPDF secure-smtplib mime openpyxl dash geoip2 folium

WORKDIR /usr/src/app

COPY ./app/programmer.py .
COPY ./app/storer.py .

EXPOSE 587  
EXPOSE 80

CMD ["python", "./app/programmer.py"]
