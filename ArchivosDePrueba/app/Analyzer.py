import os, stat

#This is the main command of nmap
os.system("nmap -sV --script vulners --script-args mincvss=5.0 -sV -stats-every 2s -iL ./app/ipsReporte.txt -oX ./app/datos.xml")

#With the previous export, we call a new python to parse the data
os.system(" python3 ./app/storer.py 2 ")
