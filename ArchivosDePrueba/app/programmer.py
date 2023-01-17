
import sys
import schedule
import time
import os



def DBVulners():
     os.system("cd /usr/share/nmap/scripts/ ; git clone https://github.com/vulnersCom/nmap-vulners.git ; git clone https://github.com/scipag/vulscan.git ; cd ./app/vulscan/utilities/updater/ ; chmod +x updateFiles.sh ; ./updateFiles.sh")

def Analyzer():
     os.system("python ./app/python-nmap.py")


schedule.every(5).minutes.do(DBVulners)
schedule.every(1).minutes.do(Analyzer)

while True:
    schedule.run_pending()
    time.sleep(1)



    
