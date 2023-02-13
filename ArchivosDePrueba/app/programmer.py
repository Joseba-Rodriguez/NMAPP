import psycopg2
import schedule
import time
import os

def get_last_selection():
    # Conectar a la base de datos
    conn = psycopg2.connect(host="db",database="nmap", user="root", password="root")
    cursor = conn.cursor()

    # Obtener la última selección del botón
    cursor.execute("SELECT selection FROM buttons ORDER BY id DESC LIMIT 1")
    last_selection = cursor.fetchone()[0]

    # Cerrar la conexión a la base de datos
    cursor.close()
    conn.close()

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