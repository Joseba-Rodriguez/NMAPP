import pandas as pd
import matplotlib.pyplot as plt
from reportlab.lib.pagesizes import letter
from reportlab.pdfgen import canvas

# Cargar los datos desde el archivo CSV
df = pd.read_csv('nmapIndividual_Scan.csv')

# Procesar la columna 'cve_str' para extraer las CVE individuales
cves = df['cve_str'].str.split(';', expand=True).stack().str.split(':', expand=True)
cve_counts = cves[1].value_counts()

# Tomar las primeras 10 CVEs más comunes
top_10_cves = cve_counts.head(10)

# Crear una figura y ejes para el dashboard
fig, axs = plt.subplots(4, 2, figsize=(16, 20))

# Graficar distribución de servicios por host
services_by_host = df.groupby(['ip', 'service']).size().unstack(fill_value=0)
services_by_host.plot(kind='bar', stacked=True, ax=axs[0, 0])
axs[0, 0].set_title('Distribución de Servicios por Host')
axs[0, 0].set_xlabel('Host')
axs[0, 0].set_ylabel('Cantidad de Servicios')

# Graficar las 10 CVEs más comunes
top_10_cves.plot(kind='bar', ax=axs[0, 1], color='skyblue')
axs[0, 1].set_title('Top 10 CVEs')
axs[0, 1].set_xlabel('CVE')
axs[0, 1].set_ylabel('Frecuencia')
axs[0, 1].tick_params(axis='x', rotation=45)

# Graficar distribución de protocolos
protocol_counts = df['protocol'].value_counts()
protocol_counts.plot(kind='pie', ax=axs[1, 0], autopct='%1.1f%%')
axs[1, 0].set_title('Distribución de Protocolos')

# Convertir la columna 'ts' a datos de fecha y hora
df['ts'] = pd.to_datetime(df['ts'])
# Graficar mapa de calor de escaneos por hora y día
scan_heatmap = df.groupby([df['ts'].dt.hour, df['ts'].dt.dayofweek]).size().unstack(fill_value=0)
axs[1, 1].imshow(scan_heatmap, cmap='YlGn', aspect='auto')
axs[1, 1].set_title('Mapa de Calor de Escaneos')
axs[1, 1].set_xlabel('Día de la Semana')
axs[1, 1].set_ylabel('Hora del Día')
axs[1, 1].set_xticks(range(7))
axs[1, 1].set_yticks(range(24))

# Graficar distribución de versiones de servicios
service_version_counts = df['version'].value_counts().head(10)
service_version_counts.plot(kind='bar', ax=axs[2, 0], color='skyblue')
axs[2, 0].set_title('Top 10 Versiones de Servicios')
axs[2, 0].set_xlabel('Versión')
axs[2, 0].set_ylabel('Frecuencia')
axs[2, 0].tick_params(axis='x', rotation=45)

# Eliminar el gráfico de distribución de puertos
fig.delaxes(axs[3, 0])
fig.delaxes(axs[3, 1])

# Ajustar el diseño del dashboard
plt.tight_layout()

# Guardar el dashboard en un PDF
pdf = canvas.Canvas("dashboard.pdf", pagesize=letter)
pdf.drawString(72, 750, "Dashboard de Análisis de Seguridad")
fig.savefig("temp.png", format="png")
pdf.drawImage("temp.png", 72, 50, width=500, height=700)
pdf.showPage()
pdf.save()
