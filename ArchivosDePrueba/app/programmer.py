import psycopg2
import schedule
import time
import os


#Abrir el fichero con los datos para conectarse a la base de datos
with open('./app/postgresConfiguration.txt') as f:
    for line in f:
        name, value = line.strip().split('=')
        os.environ[name] = value

user = os.environ['DB_USER']
password = os.environ['DB_PASS']
hostdb = os.environ['DB_HOST']
database = os.environ['DB_DB']


def get_last_selection():
    # Conectar a la base de datos
    conexion = psycopg2.connect(host=hostdb,database=database, user=user, password=password)
    cursor = conexion.cursor()

    # Obtener la última selección del botón
    cursor.execute("SELECT selection FROM buttons ORDER BY id DESC LIMIT 1")
    last_selection = cursor.fetchone()[0]

    # Cerrar la conexión a la base de datos
    cursor.close()
    conexion.close()

    return last_selection

def main_task():
    # Ejecutar la tarea principal aquí
     os.system("python3 ./app/Analyzer.py")

while True:
    # Verificar la última selección del botón cada 2 segundos
    last_selection = get_last_selection()

    # Programar la tarea principal en función de la última selección del botón
    if last_selection == "daily":
        schedule.every().day.at("00:00").do(main_task)
    elif last_selection == "monthly":
        schedule.every(30).days.at("00:00").do(main_task)
    elif last_selection == "minutely":
        schedule.every(1).minute.do(main_task)

    schedule.run_pending()
    time.sleep(2)