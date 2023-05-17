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

# Connect to the database
conn = psycopg2.connect(host=hostdb, database=database,
                        user=user, password=password)

# Create a cursor
cursor = conn.cursor()

# Execute the first SQL query
cursor.execute(
    "SELECT ip, hostname, port, protocol, service, version, cve_str, ts FROM nmapIndividual as n WHERE NOT EXISTS(SELECT * FROM lastAnalyze AS L WHERE n.ip = L.ip)")

# Collect the results of the query
results = cursor.fetchall()

# Convert the results to a Pandas DataFrame
df1 = pd.DataFrame(results, columns=[
    "ip", "hostname", "port", "protocol", "service", "version", "cve_str", "ts"])

# Save the DataFrame to the first sheet of an Excel file
with pd.ExcelWriter("./app/resources/data.xlsx") as writer:
    df1.to_excel(writer, index=False, sheet_name="appear")

    # Execute the second SQL query
    cursor.execute(
        "SELECT ip, hostname, port, protocol, service, version, cve_str, ts FROM lastanalyze as n WHERE NOT EXISTS(SELECT * FROM nmapIndividual AS L WHERE n.ip = L.ip)")

    # Collect the results of the query
    results = cursor.fetchall()

    # Convert the results to a Pandas DataFrame
    df2 = pd.DataFrame(results, columns=[
        "ip", "hostname", "port", "protocol", "service", "version", "cve_str", "ts"])

    # Save the DataFrame to the second sheet of the Excel file
    df2.to_excel(writer, index=False, sheet_name="lost")

    # Execute the third SQL query
    cursor.execute(
        "SELECT n.ip, n.hostname, n.port, n.protocol, n.service, n.version, n.cve_str, l.ts FROM nmapIndividual n JOIN lastAnalyze l ON n.ip = l.ip AND n.port = l.port")

    # Collect the results of the query
    results = cursor.fetchall()

    # Convert the results to a Pandas DataFrame
    df3 = pd.DataFrame(results, columns=[
        "ip", "hostname", "port", "protocol", "service", "version","cve_str", "ts" ])
    # Save the DataFrame to the third sheet of the Excel file
    df3.to_excel(writer, index=False, sheet_name="Stay")

# Close the connection to the database
conn.close()

# Create the e-mail message
msg = MIMEMultipart()
msg['From'] = "bashadowyt@gmail.com"
msg['To'] = "joseba.rodriguez01@gmail.com"
msg['Subject'] = "NMAPP - Reporte de nuevos puertos encontrados"

# Add the body of the e-mail message
msg.attach(MIMEText(
    "Se adjunta Excel con los puertos encontrados por la aplicación NMAPP - Joseba Rodríguez"))

# Open the Excel file and add it as an attachment to the email message
with open("./app/resources/data.xlsx", "rb") as f:
    excel = MIMEApplication(f.read(), _subtype="xlsx")
    excel.add_header('content-disposition', 'attachment',
                     filename=".NMAPP" + fecha_actual + ".xlsx")
    msg.attach(excel)

# Authenticate to the Gmail mail server
server = smtplib.SMTP('smtp.gmail.com', 587)
server.starttls()
server.login("bshadowyt@gmail.com", "lixrvxfxpbwbuiou")

# Send the email
server.sendmail(msg['From'], msg['To'], msg.as_string())

# Close the connection with the server
server.quit()