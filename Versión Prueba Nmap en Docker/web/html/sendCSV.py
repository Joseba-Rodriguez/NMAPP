import pandas as pd 
import psycopg2  

datos = pd.read_csv('./web/html/datos.csv')

lista_valores  = datos.values.tolist()
print(lista_valores)

conexion = psycopg2.connect(host="db", database= "nmap", user= "root", password= "root")


cur =conexion.cursor()

cur.executemany("INSERT INTO nmap VALUES(?,?,?,?,?,?,?)", lista_valores)

conexion.commit()
conexion.close()