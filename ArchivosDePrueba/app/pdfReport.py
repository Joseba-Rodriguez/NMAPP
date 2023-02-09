import pandas as pd
from fpdf import FPDF
import psycopg2
import smtplib

# Connect to the database
conn = psycopg2.connect(
     host="db",
     database="nmap",
     user="root",
     password="root"
     )

# Create a cursor
cursor = conn.cursor()

# Execute a SQL query
cursor.execute("SELECT * FROM nmapIndividual as n WHERE NOT EXISTS(SELECT * FROM lastAnalyze AS L WHERE n.ip = L.ip)")

# Collect the results of the consultation
results = cursor.fetchall()

# Convert the results to a Pandas DataFrame
df = pd.DataFrame(results, columns=["ip", "hostname", "port", "protocol", "service", "version", "vuln"])

# Create an instance of the FPDF class
pdf = FPDF()

# Add a page
pdf.add_page()

# Set font and size
pdf.set_font("Arial", size=12)

# Iterate over DataFrame rows
for i, row in df.iterrows():
    #  Add the contents of each column to the PDF page
    pdf.cell(200, 10, txt=row["ip"], ln=i, align="C")
    pdf.cell(200, 10, txt=row["hostname"], ln=i, align="C")
    pdf.cell(200, 10, txt=row["port"], ln=i, align="C")
    pdf.cell(200, 10, txt=row["protocol"], ln=i, align="C")
    pdf.cell(200, 10, txt=row["service"], ln=i, align="C")
    pdf.cell(200, 10, txt=row["version"], ln=i, align="C")
    pdf.cell(200, 10, txt=row["vuln"], ln=i, align="C")

# Save the PDF file
pdf.output("./app/resources/data.pdf")

# Close the connection to the database
conn.close()

from email.mime.application import MIMEApplication
from email.mime.multipart import MIMEMultipart
from email.mime.text import MIMEText

# Create the e-mail message
msg = MIMEMultipart()
msg['From'] = "nmappreporte@gmail.com"
msg['To'] = "joseba.rodriguez01@gmail.com"
msg['Subject'] = "NMAPP - Reporte de nuevos puertos encontrados"

# Add the body of the e-mail message
msg.attach(MIMEText("Se adjunta pdf con los puertos encontrados por la aplicación NMAPP - Joseba Rodríguez"))

# Open the PDF file and add it as an attachment to the email message
with open("./app/resources/data.pdf", "rb") as f:
    pdf = MIMEApplication(f.read(), _subtype="pdf")
    pdf.add_header('content-disposition', 'attachment', filename=".NMAPP.pdf")
    msg.attach(pdf)

# Authenticate to the Gmail mail server
server = smtplib.SMTP('smtp.gmail.com', 587)
server.starttls()
server.login("nmappreporte@gmail.com", "nynyncvglafjihct")

# Send the email 
server.sendmail(msg['From'], msg['To'], msg.as_string())

# Close the connection with the server
server.quit()
