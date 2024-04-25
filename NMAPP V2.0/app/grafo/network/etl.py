import json
import os
import pandas as pd

# Cargar el archivo CSV
df = pd.read_csv("/usr/src/app/app/grafo/nmapIndividual_Scan.csv")

# Agrupar los datos por IP y recopilar puertos y servicios
ip_data = df.groupby('ip').apply(lambda x: {
    "ip": x.name,
    "ports": x.apply(lambda row: {
        "port": int(row['port']),
        "service": row['service']
    }, axis=1).tolist()
}).tolist()

# Crear la lista de nodos
nodes = []
for item in ip_data:
    nodes.append({
        "ip": item['ip'],
        "ports": item['ports']
    })

# Crear la lista de enlaces (links)
links = []
for i in range(len(df)):
    for j in range(i+1, len(df)):
        ip1, ip2 = df.iloc[i]['ip'], df.iloc[j]['ip']
        if ip1.split('.')[0:3] == ip2.split('.')[0:3]:
            links.append({"source": ip1, "target": ip2})

# Crear el diccionario final
data = {
    "nodes": nodes,
    "links": links
}

# Guardar los datos en un archivo JSON sobrescribiendo el archivo existente
with open('/usr/src/app/app/grafo/network/graph_data_main.json', 'w') as f:
    json.dump(data, f)

print("Los datos se han guardado correctamente en 'data.json'.")