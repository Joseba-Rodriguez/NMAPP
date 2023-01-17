import os
#Descargas de las bases de datos

os.system("nmap -sV --script nmap-vulners --script-args vulscandb=scipvuldb.csv -sV -stats-every 2s -iL ./app/ips.txt -oX ./app/datos.xml")

os.system(" python3 ./app/Storer.py ./app/datos.xml ")