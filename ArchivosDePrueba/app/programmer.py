
import sys
import schedule
import time
import os


os.system("cd /usr/share/nmap/scripts/ ; git clone https://github.com/vulnersCom/nmap-vulners.git ; git clone https://github.com/scipag/vulscan.git ; cd vulscan/utilities/updater/ ; chmod +x updateFiles.sh ; ./updateFiles")
os.system("chmod 777 ./app/ips.txt ; chmod 777 ./app/datos.xml ")

def DBVulners():
     os.system("cd /usr/share/nmap/scripts/vulscan/utilities/updater/ ; chmod +x updateFiles.sh ; ./updateFiles.sh")

def Analyzer():
    os.system("nmap -sV --script nmap-vulners --script-args vulscandb=scipvuldb.csv -sV -stats-every 2s -iL ./ips.txt -oX ./web/datos.xml")

    os.system(" python ./app/Storer.py ./app/datos.xml ")


schedule.every(5).minutes.do(DBVulners)
schedule.every(1).minutes.do(Analyzer)

while True:
    schedule.run_pending()
    time.sleep(1)



    
