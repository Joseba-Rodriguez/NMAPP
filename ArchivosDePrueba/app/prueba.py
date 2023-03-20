#!/usr/bin/python

import sys
import xml.etree.ElementTree as ET
import psycopg2
import os

# Constants

NUM = int(sys.argv[1])
INPUT_FILE = './datos.xml'


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
        summary = ""   

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
                versioning = version.replace("'", "")
                for cve in port.find('script').findall("table"):
                    cpe = cve.get('key')
            print(
                f"Dato insertado {ip}, {hostname}, {portnum}, {protocol}, {service}, {product}, {cpe}\n")

raw_parser(NUM)
