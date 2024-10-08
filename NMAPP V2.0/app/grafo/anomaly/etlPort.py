import pandas as pd
import json

# Leer nmapIndividual_Scan.csv
def read_nmap_file(filename):
    return pd.read_csv(filename)

# Leer service-ports.csv
def read_service_ports(filename):
    service_ports = pd.read_csv(filename)
    # Convertir a un conjunto de tuplas (Service Name, Port Number)
    return set(zip(service_ports['Service Name'], service_ports['Port Number']))

# Comprobar si un puerto y servicio es común o anómalo
def check_service(service_ports, service_name, port_number):
    return (service_name, port_number) in service_ports

# Crear el grafo en formato JSON
def create_graph_data(nmap_df, service_ports):
    nodes = {}
    links = []
    
    for _, row in nmap_df.iterrows():
        ip = row['ip']
        port = row['port']
        service = row['service']
        vulnerabilities = row.get('cve_str', None)
        
        # Nodo IP
        if ip not in nodes:
            nodes[ip] = {'id': ip, 'group': 1}
        
        # Nodo puerto y servicio
        node_id = f"{service}:{port}"
        if check_service(service_ports, service, port):
            if node_id not in nodes:
                node_data = {'id': node_id, 'group': 2}
                if pd.notna(vulnerabilities):
                    node_data['vulnerabilities'] = vulnerabilities.split(';')
                nodes[node_id] = node_data
        else:
            if node_id not in nodes:
                node_data = {'id': node_id, 'group': 3}
                if pd.notna(vulnerabilities):
                    node_data['vulnerabilities'] = vulnerabilities.split(';')
                nodes[node_id] = node_data
        
        # Enlace entre IP y puerto-servicio
        links.append({'source': ip, 'target': node_id, 'value': 1})
    
    return {'nodes': list(nodes.values()), 'links': links}

if __name__ == "__main__":
    nmap_df = read_nmap_file('/usr/src/app/app/grafo/nmapIndividual_Scan.csv')
    service_ports = read_service_ports('/usr/src/app/app/grafo/anomaly/service-port.csv')
    graph_data = create_graph_data(nmap_df, service_ports)
    
    with open('/usr/src/app/app/grafo/anomaly/graph_data_anomaly.json', 'w') as outfile:
        json.dump(graph_data, outfile, indent=4)
