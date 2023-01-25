import pandas as pd
from fpdf import FPDF
import psycopg2
import smtplib

# Establecer conexión a la base de datos
conn = psycopg2.connect(
     host="db",
     database="nmap",
     user="root",
     password="root"
     )

# Crear un cursor
cursor = conn.cursor()

# Ejecutar una consulta SQL
cursor.execute("SELECT * FROM nmapScan as n WHERE NOT EXISTS(SELECT * FROM lastAnalyze AS L WHERE n.ip = L.ip)")

# Recoger los resultados de la consulta
results = cursor.fetchall()

# Convertir los resultados en un DataFrame de Pandas
df = pd.DataFrame(results, columns=["ip", "hostname", "port", "protocol", "service", "version", "vuln"])

# Crear una instancia de la clase FPDF
pdf = FPDF()

# Añadir una página
pdf.add_page()

# Establecer el tipo de letra y tamaño
pdf.set_font("Arial", size=12)

# Iterar sobre las filas del DataFrame
for i, row in df.iterrows():
    # Añadir el contenido de cada columna a la página PDF
    pdf.cell(200, 10, txt=row["ip"], ln=i, align="C")
    pdf.cell(200, 10, txt=row["hostname"], ln=i, align="C")
    pdf.cell(200, 10, txt=row["port"], ln=i, align="C")
    pdf.cell(200, 10, txt=row["protocol"], ln=i, align="C")
    pdf.cell(200, 10, txt=row["service"], ln=i, align="C")
    pdf.cell(200, 10, txt=row["version"], ln=i, align="C")
    pdf.cell(200, 10, txt=row["vuln"], ln=i, align="C")

# Guardar el archivo PDF
pdf.output("./app/resources/data.pdf")

# Cerrar la conexión a la base de datos
conn.close()

from email.mime.application import MIMEApplication
from email.mime.multipart import MIMEMultipart
from email.mime.text import MIMEText

# Crear el mensaje de correo electrónico
msg = MIMEMultipart()
msg['From'] = "nmappreporte@gmail.com"
msg['To'] = "joseba.rodriguez01@gmail.com"
msg['Subject'] = "NMAPP - Reporte de nuevos puertos encontrados"

# Agregar el cuerpo del mensaje de correo electrónico
msg.attach(MIMEText("Se adjunta pdf con los puertos encontrados por la aplicación NMAPP - Joseba Rodríguez"))

# Abrir el archivo PDF y agregarlo como un adjunto al mensaje de correo electrónico
with open("./app/resources/data.pdf", "rb") as f:
    pdf = MIMEApplication(f.read(), _subtype="pdf")
    pdf.add_header('content-disposition', 'attachment', filename=".NMAPP.pdf")
    msg.attach(pdf)

# Autenticarse en el servidor de correo de Gmail
server = smtplib.SMTP('smtp.gmail.com', 587)
server.starttls()
server.login("nmappreporte@gmail.com", "nynyncvglafjihct")

# Enviar el correo electrónico
server.sendmail(msg['From'], msg['To'], msg.as_string())

# Cerrar la conexión con el servidor de correo
server.quit()