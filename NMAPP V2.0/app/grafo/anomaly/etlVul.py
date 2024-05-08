import csv
import json

# Leer nmapIndividual_Scan.csv
def read_nmap_file(filename):
    data = []
    with open(filename, 'r') as file:
        reader = csv.DictReader(file)
        for row in reader:
            data.append(row)
    return data

# Leer service-ports.csv
def read_service_ports(filename):
    service_ports = set()
    with open(filename, 'r') as file:
        reader = csv.DictReader(file)
        for row in reader:
            service_ports.add((row['Service Name'], row['Port Number']))
    return service_ports

# Comprobar si un puerto y servicio es común o anómalo
def check_service(service_ports, service_name, port_number):
    return (service_name, port_number) in service_ports

# Crear el grafo en formato JSON
def create_graph_data(nmap_data, service_ports):
    nodes = []
    links = []
    
    for entry in nmap_data:
        ip = entry['ip']
        port = entry['port']
        service = entry['service']
        vulnerabilities = entry.get('cve_str', None)
        
        # Nodo IP
        if {'id': ip, 'group': 1} not in nodes:
            nodes.append({'id': ip, 'group': 1})
        
        # Nodo puerto y servicio
        if check_service(service_ports, service, port):
            node_id = f"{service}:{port}"
            if {'id': node_id, 'group': 2} not in nodes:
                node_data = {'id': node_id, 'group': 2}
                if vulnerabilities:
                    node_data['vulnerabilities'] = vulnerabilities.split(';')
                nodes.append(node_data)
        else:
            node_id = f"{service}:{port}"
            if {'id': node_id, 'group': 3} not in nodes:
                node_data = {'id': node_id, 'group': 3}
                if vulnerabilities:
                    node_data['vulnerabilities'] = vulnerabilities.split(';')
                nodes.append(node_data)
        
        # Enlace entre IP y puerto-servicio
        links.append({'source': ip, 'target': f"{service}:{port}", 'value': 1})
    
    return {'nodes': nodes, 'links': links}

if __name__ == "__main__":
    nmap_data = read_nmap_file('/usr/src/app/app/grafo/nmapIndividual_Scan.csv')
    service_ports = read_service_ports('/usr/src/app/app/grafo/anomaly/service-port.csv')
    graph_data = create_graph_data(nmap_data, service_ports)
    
    with open('/usr/src/app/app/grafo/anomaly/graph_data_anomaly.json', 'w') as outfile:
        json.dump(graph_data, outfile, indent=4)
