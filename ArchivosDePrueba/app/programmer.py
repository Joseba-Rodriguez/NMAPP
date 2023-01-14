import sys
import schedule
import time 
import os

def DBVulners():
     os.system("cd /usr/share/nmap/scripts/ ; git clone https://github.com/vulnersCom/nmap-vulners.git ; git clone https://github.com/scipag/vulscan.git ; cd ./app/vulscan/utilities/updater/ ; chmod +x updateFiles.sh ; ./updateFiles.sh")

def Analyzer():
    os.system("cd /usr/share/nmap/scripts/ ; git clone https://github.com/vulnersCom/nmap-vulners.git ; git clone https://github.com/scipag/vulscan.git ")

    os.system("chmod 777 ./app/ips.txt ; chmod 777 ./app/datos.xml ")

    os.system("nmap -sV --script nmap-vulners --script-args vulscandb=scipvuldb.csv -sV -stats-every 2s -iL ./app/ips.txt -oX ./app/datos.xml")

    os.system(" python ./app/Storer.py ./app/datos.xml ")


schedule.every(10).minutes.do(DBVulners)
schedule.every(1).minutes.do(Analyzer)

while True:
    schedule.run_pending()
    time.sleep(1)

    