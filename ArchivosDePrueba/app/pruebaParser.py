#!/usr/bin/python

import sys
import xml.etree.ElementTree as ET

inputfile = './datos.xml'

#we input the file 
def parseForReport(inputfile):
	try:
		tree = ET.parse(inputfile)
		root = tree.getroot()
	except ET.ParseError as e:
		print("Parse error: {}".format(str(e)))
		sys.exit(2)
	except IOError as e:
		print ("IO error({0}): {1}".format(e.errno, e.strerror))
		sys.exit(2)
	except:
		print ("Unexpected error:", sys.exc_info()[0])
		sys.exit(2)

	#conn = psycopg2.connect(host="db",database="nmap", user="root", password="root")
	#cursor = conn.cursor()
		
	#cursor.execute("INSERT INTO lastAnalyze SELECT * FROM nmapIndividual")
	#conn.commit()
		#Delete the table nmapScan before de parse, just to have the table empty
	#cursor.execute("DELETE FROM nmapIndividual")
	#conn.commit()

	#Firstly we search all the host
	for host in root.findall('host'):
		ip = host.find('address').get('addr')
		hostname = ""
		if host.find('hostnames') is not None:
			if host.find('hostnames').find('hostname') is not None:
				#We get that hosts and we storage in hostname variable
				hostname = host.find('hostnames').find('hostname').get('name')
		#With all the host, we find all the ports inside thar host and we obtain; port, portid, services, vulnerabilities, products, version and extra info that is in the xml output
		for port in host.find('ports').findall('port'):
			protocol = port.get('protocol')
			if protocol is None:
				protocol = ""

			portnum = port.get('portid')
			if portnum is None:
				portnum = ""

			service = ""
			if port.find('service') is not None:
				if port.find('service').get('name') is not None:
					service = port.find('service').get('name')

			vuln = ""
			if port.find('script') is not None:
				if port.find('script').get('output') is not None:
					vuln2 = port.find('script').get('output')
					vuln = vuln2.replace("'", "")
			product = ""
			version = ""
			versioning = ""
			extrainfo = ""
			if port.find('service') is not None:
				if port.find('service').get('product') is not None:
					product = port.find('service').get('product')
					versioning = product.replace(",", "")
				if port.find('service').get('version') is not None:
					version = port.find('service').get('version')
					versioning = versioning + ' (' + version + ')'
				if port.find('service').get('extrainfo') is not None:
					extrainfo = port.find('service').get('extrainfo')
					versioning = versioning + ' (' + extrainfo + ')'
					versioning = product.replace("'", "")
				
				#The data will be inserted after parsing in nmapScan 
				#cursor.execute("INSERT INTO nmapIndividual(ip, hostname, port, protocol,service, version, vuln) VALUES ('"+ str(ip) + "' , '" + str(hostname) + "' , " + str(portnum) + " , '" + str(protocol) + "' , '"+ str(service) + "' , '" + str(versioning) + "', '" + str(vuln) + "' )")
				print("Dato insertado"+ ip + ',' + hostname + ',' + portnum + ',' + protocol + ',' + service + ',' + versioning + ',' + vuln + '\n')
				#conn.commit()
				
	#conn.close()

def parseForIndividual(inputfile):
	try:
		tree = ET.parse(inputfile)
		root = tree.getroot()
	except ET.ParseError as e:
		print("Parse error: {}".format(str(e)))
		sys.exit(2)
	except IOError as e:
		print ("IO error({0}): {1}".format(e.errno, e.strerror))
		sys.exit(2)
	except:
		print ("Unexpected error:", sys.exc_info()[0])
		sys.exit(2)

	#connection with the database
	#conn = psycopg2.connect(host="db",database="nmap", user="root", password="root")
	#cursor = conn.cursor()
	#Delete the table nmapScan before de parse
	#cursor.execute("INSERT INTO lastAnalyze SELECT * FROM nmapScan")
	#conn.commit()		
	#cursor.execute("DELETE FROM nmapScan")
	#conn.commit()

	for host in root.findall('host'):
		ip = host.find('address').get('addr')
		hostname = ""
		if host.find('hostnames') is not None:
			if host.find('hostnames').find('hostname') is not None:
				hostname = host.find('hostnames').find('hostname').get('name')

		for port in host.find('ports').findall('port'):
			protocol = port.get('protocol')
			if protocol is None:
				protocol = ""

			portnum = port.get('portid')
			if portnum is None:
				portnum = ""

			service = ""
			if port.find('service') is not None:
				if port.find('service').get('name') is not None:
					service = port.find('service').get('name')

			vuln = ""
			if port.find('script') is not None:
				if port.find('script').get('output') is not None:
					vuln2 = port.find('script').get('output')
					vuln = vuln2.replace("'", "")
			
			product = ""
			version = ""
			versioning = ""
			extrainfo = ""
			if port.find('service') is not None:
				if port.find('service').get('product') is not None:
					product = port.find('service').get('product')
					versioning = product.replace(",", "")
				if port.find('service').get('version') is not None:
					version = port.find('service').get('version')
					versioning = versioning + ' (' + version + ')'
				if port.find('service').get('extrainfo') is not None:
					extrainfo = port.find('service').get('extrainfo')
					versioning = versioning + ' (' + extrainfo + ')'
					versioning = product.replace("'", "")
				
				#The data will be inserted after parsing in nmapScan 
				#cursor.execute("INSERT INTO nmapScan(ip, hostname, port, protocol,service, version, vuln) VALUES ('"+ str(ip) + "' , '" + str(hostname) + "' , " + str(portnum) + " , '" + str(protocol) + "' , '"+ str(service) + "' , '" + str(versioning) + "', '" + str(vuln) + "' )")
				print("Dato insertado"+ ip + ',' + hostname + ',' + portnum + ',' + protocol + ',' + service + ',' + versioning + ',' + vuln + '\n')
				#conn.commit()
				
	#conn.close()

def pedirNumero(num):
    if num == 1:
        parseForReport(inputfile)
    else:
        parseForIndividual(inputfile)
 
num = int(sys.argv[1])
pedirNumero(num)
