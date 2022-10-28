#!/usr/bin/python3
import nmap

#Aqui le indicamos las ips(editar para hacer un rango)
ip=input("[+] IP Objetivo ==> ")
#Ejecutamos nmap de python y analizamos sus puertos
nm = nmap.PortScanner()
#indicamos en una variable cual va a ser el inicio de los puertos
puertos_abiertos="-p "
#con nm.scan(argumentos) podremos meter todos los argunmentos necesarios que queramos que ejecute nmap, así mismo con -T1 tarda mucho, utilizamos T4 y no T5 porque da errores
results = nm.scan(hosts=ip,arguments="-sT -n -Pn -T4")
#Inicializamos un contador
count=0
#printeamos los resultados de IP
print("\nHost : %s" % ip)
#Printeamos el estado de la ip con .state de nm[ip] para saber si esta abierto o cerrado
print("State : %s" % nm[ip].state())
#Por cada ip analiza y ordena todos los puertos que esten abiertos 
for proto in nm[ip].all_protocols():
	print("Protocol : %s" % proto)
	print()
	lport = nm[ip][proto].keys()
	sorted(lport)
	for port in lport:
		print ("port : %s\tstate : %s" % (port, nm[ip][proto][port]["state"]))
		if count==0:
			puertos_abiertos=puertos_abiertos+str(port)
			count=1
		else:
			puertos_abiertos=puertos_abiertos+","+str(port)

print("\nPuertos abiertos: "+ puertos_abiertos +" "+str(ip))

# A partir de aquí una vez tengas la lista de todos los puertos abiertos, ya podrás ejecutar las vulnerabilidadades con:
# nmap -sV -sC -p (pones todos los puertos de la ip)