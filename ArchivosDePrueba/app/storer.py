#!/usr/bin/python

import sys
import xml.etree.ElementTree as ET
import psycopg2
import os

# Constants

NUM = int(sys.argv[1])


if NUM == 1:
    INPUT_FILE = './datos.xml'
    POSTGRES_CONFIG_FILE = './postgresConfiguration.txt'
# Read postgresConfiguration.txt and set env variables
    with open(POSTGRES_CONFIG_FILE, encoding='utf-8') as f:
        for line in f:
            name, value = line.strip().split('=')
            os.environ[name] = value
        # Get env variables
    user = os.environ['DB_USER']
    password = os.environ['DB_PASS']
    hostdb = os.environ['DB_HOST']
    database = os.environ['DB_DB']
    
    conn = psycopg2.connect(host=hostdb, database=database,
                            user=user, password=password)
    cursor = conn.cursor()
    # Insert data into lastAnalyze table

    cursor.execute("DELETE FROM nmapNow")
    conn.commit()
    cursor.execute("DELETE FROM stats")
    conn.commit()

if NUM != 1:
    INPUT_FILE = './app/datos.xml'
    POSTGRES_CONFIG_FILE = './app/postgresConfiguration.txt'
# Read postgresConfiguration.txt and set env variables
    with open(POSTGRES_CONFIG_FILE, encoding='utf-8') as f:
        for line in f:
            name, value = line.strip().split('=')
            os.environ[name] = value
        # Get env variables
    user = os.environ['DB_USER']
    password = os.environ['DB_PASS']
    hostdb = os.environ['DB_HOST']
    database = os.environ['DB_DB']
    
    conn = psycopg2.connect(host=hostdb, database=database,
                            user=user, password=password)
    cursor = conn.cursor()
    # Insert data into lastAnalyze table
    cursor.execute("INSERT INTO lastAnalyze SELECT * FROM nmapIndividual")
    conn.commit()
    cursor.execute("DELETE FROM nmapIndividual")
    conn.commit()
    cursor.execute("DELETE FROM stats")
    conn.commit()
    

# we input the file


def parse_for_individual(ip, hostname, portnum, protocol, service, versioning, cve_str):
    """
    Inserts individual nmap scan results into a PostgreSQL database.

    Args:
        ip (str): The IP address of the scanned host.
        hostname (str): The hostname of the scanned host.
        portnum (int): The port number of the scanned service.
        protocol (str): The protocol of the scanned service (e.g. tcp or udp).
        service (str): The name of the scanned service.
        versioning (str): The version information of the scanned service.
        vuln (str): The vulnerability information of the scanned service.

    Returns:
        None
    """
    conn = psycopg2.connect(host=hostdb, database=database,
                            user=user, password=password)
    cursor = conn.cursor()
    # Insert data into nmapIndividual table
    cursor.execute("INSERT INTO nmapIndividual(ip, hostname, port, protocol,service, version, cve_str) VALUES (%s, %s, %s, %s, %s, %s, %s)",
                   (ip, hostname, portnum, protocol, service, versioning, cve_str))
    conn.commit()

def parse_for_now(ip, hostname, portnum, protocol, service, versioning, cve_str):
    """
    Inserts now nmap scan results into a PostgreSQL database.

    Args:
        ip (str): The IP address of the scanned host.
        hostname (str): The hostname of the scanned host.
        portnum (int): The port number of the scanned service.
        protocol (str): The protocol of the scanned service (e.g. tcp or udp).
        service (str): The name of the scanned service.
        versioning (str): The version information of the scanned service.
        vuln (str): The vulnerability information of the scanned service.

    Returns:
        None
    """
    conn = psycopg2.connect(host=hostdb, database=database,
                            user=user, password=password)
    cursor = conn.cursor()
    # Insert data into nmapIndividual table
    cursor.execute("INSERT INTO nmapNow(ip, hostname, port, protocol,service, version, cve_str) VALUES (%s, %s, %s, %s, %s, %s, %s)",
                   (ip, hostname, portnum, protocol, service, versioning, cve_str))
    conn.commit()

def raw_parser(NUM):
    """
    This function parses an NMAP XML file and inserts the data into a PostgreSQL database.
    :param NUM: an integer representing a parameter for the function. 
    If it is 1 the code executes parse_for_individual. 
    This is like this because in a future you can implement another function
    with this parser.
    """

    try:
        tree = ET.parse(INPUT_FILE)
        root = tree.getroot()
    except ET.ParseError as parse_error:
        print("Parse error: {}".format(str(parse_error)))
        sys.exit(2)
    except IOError as io_error:
        print("IO error({0}): {1}".format(io_error.errno, io_error.strerror))
        sys.exit(2)
    except Exception as unexpected_error:
        print("Unexpected error:", str(unexpected_error))
        sys.exit(2)

    #Get the summary of the xml
    summary = root.find('runstats').find('finished').get('summary')
    if summary is None:
        summary = ""   # Asignar una cadena vacía si el valor de summary es nulo
    cursor = conn.cursor()
    # Insert data into stats table
    cursor.execute("INSERT INTO stats (summary) VALUES (%s)", (summary,))
    conn.commit()
    print(summary)

    for host in root.findall('host'):
        ip = host.find('address').get('addr')
        hostname = ""
        if host.find('hostnames') is not None:
            if host.find('hostnames').find('hostname') is not None:
                hostname = host.find('hostnames').find('hostname').get('name')

        for port in host.find('ports').findall('port'):
            protocol = port.get('protocol') or ""
            portnum = port.get('portid') or ""
            state = port.find('state').get('state') or ""
            if state != "filtered":
                service = port.find('service').get(
                    'name') if port.find('service') is not None else ""
                vuln = port.find('script').get('output').replace("'", "") if port.find(
                    'script') is not None and port.find('script').get('output') is not None else ""
                product = port.find('service').get('product') if port.find(
                    'service') is not None and port.find('service').get('product') is not None else ""
                version = port.find('service').get('version') if port.find(
                    'service') is not None and port.find('service').get('version') is not None else ""
                extrainfo = port.find('service').get('extrainfo') if port.find(
                    'service') is not None and port.find('service').get('extrainfo') is not None else ""
                versioning = product.replace(",", "")
                versioning += ' (' + version + ')' if version else ''
                versioning += ' (' + extrainfo + ')' if extrainfo else ''
                versioning = product.replace("'", "")
                cve_list = []
                for script in port.findall('script'):
                    if 'output' in script.attrib:
                        output = script.attrib['output']
                        for line in output.split('\n'):
                            if 'CVE-' in line:
                                cve_list.append(line.strip())
                cve_str = ";".join(cve_list)
            print(
                f"Dato insertado {ip}, {hostname}, {portnum}, {protocol}, {service}, {versioning}, {cve_str}\n")
            if NUM != 1:
                parse_for_individual(ip, hostname, portnum,
                                     protocol, service, versioning,cve_str)
            else: parse_for_now(ip, hostname, portnum,
                                     protocol, service, versioning,cve_str)

raw_parser(NUM)
