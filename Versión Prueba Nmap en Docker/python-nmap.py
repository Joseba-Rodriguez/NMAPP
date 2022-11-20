import nmap
#Aqui le indica las ips(editar para hacer un rango)
ip="169.254.169.254"
#Ejecuta nmap de python y analiza sus puertos
nm = nmap.PortScanner()
#indica en una variable cual va a ser el inicio de los puertos
puertos_abiertos="-p "
#con nm.scan(argumentos) se puede meter todos los argunmentos necesarios que queramos que ejecute nmap, así mismo con -T1 tarda mucho, utiliza T4 (mucho más rápida)y no T5 porque da errores
results = nm.scan(hosts=ip,arguments="-sT -n -Pn -T4")
#Inicializa un contador
count=0
#printea los resultados de IP
print("\nHost : %s" % ip)
#Printea el estado de la ip con .state de nm[ip] para saber si esta abierto o cerrado
print("State : %s" % nm[ip].state())
#Por cada ip analiza y ordena todos los puertos que esten abiertos 
for proto in nm[ip].all_protocols():
	print("Protocol : %s" % proto)
	print()
	lport = nm[ip][proto].keys()
	#ordena los puertos
	sorted(lport)
	for port in lport:
		print ("port : %s\tstate : %s" % (port, nm[ip][proto][port]["state"]))
		if count==0:
			puertos_abiertos=puertos_abiertos+str(port)
			count=1
		else:
			puertos_abiertos=puertos_abiertos+","+str(port)

print("\nPuertos abiertos: "+ puertos_abiertos +" "+str(ip))
