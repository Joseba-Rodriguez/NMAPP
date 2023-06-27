from email.mime.text import MIMEText
from email.mime.multipart import MIMEMultipart
from email.mime.application import MIMEApplication
import pandas as pd
import psycopg2
import smtplib
import os
import datetime

with open('./app/postgresConfiguration.txt', encoding='utf-8') as f:
    for line in f:
        name, value = line.strip().split('=')
        os.environ[name] = value

user = os.environ['DB_USER']
password = os.environ['DB_PASS']
hostdb = os.environ['DB_HOST']
database = os.environ['DB_DB']

fecha_actual = datetime.datetime.now().strftime("%Y-%m-%d")

conn = psycopg2.connect(host=hostdb, database=database,
                        user=user, password=password)

cursor = conn.cursor()

cursor.execute(
    "SELECT ip, hostname, port, protocol, service, version, cve_str, ts FROM nmapIndividual as n WHERE NOT EXISTS(SELECT * FROM lastAnalyze AS L WHERE n.ip = L.ip)")

results = cursor.fetchall()

df1 = pd.DataFrame(results, columns=[
    "ip", "hostname", "port", "protocol", "service", "version", "cve_str", "ts"])

with pd.ExcelWriter("./app/resources/data.xlsx") as writer:
    df1.to_excel(writer, index=False, sheet_name="appear")

    cursor.execute(
        "SELECT ip, hostname, port, protocol, service, version, cve_str, ts FROM lastanalyze as n WHERE NOT EXISTS(SELECT * FROM nmapIndividual AS L WHERE n.ip = L.ip)")

    results = cursor.fetchall()

    df2 = pd.DataFrame(results, columns=[
        "ip", "hostname", "port", "protocol", "service", "version", "cve_str", "ts"])

    df2.to_excel(writer, index=False, sheet_name="lost")

    cursor.execute(
        "SELECT n.ip, n.hostname, n.port, n.protocol, n.service, n.version, n.cve_str, l.ts FROM nmapIndividual n JOIN lastAnalyze l ON n.ip = l.ip AND n.port = l.port")

    results = cursor.fetchall()

    df3 = pd.DataFrame(results, columns=[
        "ip", "hostname", "port", "protocol", "service", "version","cve_str", "ts" ])
    df3.to_excel(writer, index=False, sheet_name="Stay")

conn.close()

msg = MIMEMultipart()

#Enter the email "From" and "To" you want to send the report
msg['From'] = ""
msg['To'] = ""
msg['Subject'] = "NMAPP - Reporte de nuevos puertos encontrados"

msg.attach(MIMEText(
    "Se adjunta Excel con los puertos encontrados por la aplicación NMAPP - Joseba Rodríguez"))

with open("./app/resources/data.xlsx", "rb") as f:
    excel = MIMEApplication(f.read(), _subtype="xlsx")
    excel.add_header('content-disposition', 'attachment',
                     filename=".NMAPP" + fecha_actual + ".xlsx")
    msg.attach(excel)

# Authenticate to the Gmail mail server
server = smtplib.SMTP('smtp.gmail.com', 587)
server.starttls()
#Introduce the credentials
server.login("", "")

server.sendmail(msg['From'], msg['To'], msg.as_string())

server.quit()
