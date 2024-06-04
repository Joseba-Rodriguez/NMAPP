import os
import psycopg2
import csv


def download_data():
    # Datos de conexión
    db_config = {
        'user': 'admin',
        'password': '#TFGD3J0s3b4#',
        'host': 'db',
        'port': '5432',
        'database': 'nmap'
    }

    # Nombre de la tabla
    table_name = 'nmapIndividual'  # Cambia 'tu_tabla' por el nombre de tu tabla

    # Conexión a la base de datos
    try:
        connection = psycopg2.connect(**db_config)
        cursor = connection.cursor()

        # Consulta SQL
        query = f"SELECT * FROM {table_name}"

        # Ejecutar la consulta
        cursor.execute(query)

        # Obtener los datos
        data = cursor.fetchall()

        # Nombre del archivo CSV
        filename = f"./app/grafo/{table_name}_Scan.csv"

        # Escribir los datos en un archivo CSV
        with open(filename, 'w', newline='') as file:
            writer = csv.writer(file)
            # Escribir el encabezado
            writer.writerow([desc[0] for desc in cursor.description])
            # Escribir los datos
            writer.writerows(data)

        print(f"Archivo CSV {filename} creado correctamente.")

    except (Exception, psycopg2.Error) as error:
        print("Error al conectarse a la base de datos:", error)

    finally:
        # Cerrar la conexión
        if connection:
            cursor.close()
            connection.close()
            print("Conexión a la base de datos cerrada.")

if __name__ == "__main__":
    download_data()