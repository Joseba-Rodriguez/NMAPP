import os, stat

os.system("nmap -sV --script vulners --script-args mincvss=5.0 -sV -stats-every 2s -iL ./app/ips.txt -oX ./app/datos.xml")

os.system(" python3 ./app/Storer.py ./app/datos.xml ")
