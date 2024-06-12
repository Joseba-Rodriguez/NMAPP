FROM python:3

RUN apt-get -y update && apt-get -y upgrade \
    && apt-get install -y nmap apache2 npm \
    && npm install -g npm@latest \
    && npm cache clean --force

RUN npm install -g compromise

RUN pip install psycopg2 schedule pandas FPDF secure-smtplib mime openpyxl dash geoip2 folium

WORKDIR /usr/src/app

COPY ./app/programmer.py .
COPY ./app/storer.py .

EXPOSE 587  
EXPOSE 8050

CMD ["python", "./app/programmer.py"]
