import os 
import subprocess

# This is the main command of nmap
os.system("nmap -Pn --open -sV --script vulners --script-args mincvss=5.0 --script-args vulners.cvesonly -sV -stats-every 2s -iL ./app/ipsReporte.txt -oX ./app/datos.xml")

# With the previous export, we call a new python to parse the data
os.system(" python3 ./app/storer.py 2")

os.system("python3 ./app/grafo/anomaly/etlPort.py")
subprocess.Popen(["python3", "./app/grafo/informe/informe.py"], stdout=subprocess.DEVNULL, stderr=subprocess.DEVNULL)
