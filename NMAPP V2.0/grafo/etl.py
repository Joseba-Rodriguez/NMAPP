import json
import pandas as pd

# Cargar el archivo CSV
df = pd.read_csv("NMAPP V2.0\IPsNMAPP.csv")

# Agrupar los datos por IP y Subred
subnets = df.groupby('ip').apply(lambda x: {
    "id": "Subnet_" + str(x.name),
    "color": "#1f77b4",
    "nodes": x.apply(lambda row: {
        "ip": row['ip'],
        "port": int(row['port']),
        "service": row['service']
    }, axis=1).tolist()
}).tolist()

# Eliminar duplicados de nodos
unique_nodes = df.drop_duplicates(subset=['ip', 'port', 'service'])

# Crear la lista de enlaces (links)
links = []
for i in range(len(unique_nodes)):
    for j in range(i+1, len(unique_nodes)):
        ip1, ip2 = unique_nodes.iloc[i]['ip'], unique_nodes.iloc[j]['ip']
        if ip1.split('.')[0:3] == ip2.split('.')[0:3]:
            links.append({"source": ip1, "target": ip2})

# Crear el diccionario final
data = {
    "subnets": subnets,
    "links": links
}

# Guardar los datos en un archivo JSON
with open('data.json', 'w') as f:
    json.dump(data, f)

print("Los datos se han guardado correctamente en 'data.json'.")
