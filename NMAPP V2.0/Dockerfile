FROM python:3

RUN apt-get -y update && apt-get -y upgrade

RUN apt-get install -y nmap
RUN apt-get install -y apache2

RUN pip install psycopg2
RUN pip install schedule
RUN pip install pandas
RUN pip install FPDF
RUN pip install secure-smtplib
RUN pip install mime
RUN pip install openpyxl
RUN pip install dash
RUN pip install geoip2
RUN pip install folium

WORKDIR /usr/src/app

COPY ./app/programmer.py .
COPY ./app/storer.py .

EXPOSE 587
EXPOSE 8050

CMD [ "python", "./app/programmer.py" ]

 