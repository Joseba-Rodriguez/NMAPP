
import sys
import schedule
import time
import os ,sys, stat


os.chmod("./app/ips.txt", stat.S_IRWXO) #establecer permisos para ips.txt
os.chmod("./app/datos.xml", stat.S_IRWXO) #establecer permisos para datos.txt
os.chmod("./app/Storer.py", stat.S_IRWXO) #establecer permisos para Storer.py
os.chmod("./app/programmer.py", stat.S_IRWXO) #establecer permisos para programmer.py
os.chmod("./app/DBVulners.py", stat.S_IRWXO) #establecer permisos para programmer.py
os.chmod("./app/Analyzer.py", stat.S_IRWXO) #establecer permisos para programmer.py

def DBVulners():
     os.system("python3 ./app/DBVulners.py")

def Analyzer():
     os.system("python3 ./app/Analyzer.py")

def pdfReport():
     os.system("python3 ./app/pdfReport.py")

#programadores horarios     
schedule.every(10).minutes.do(DBVulners)
schedule.every().minute.do(Analyzer)
schedule.every(1).minutes.do(pdfReport)

while True:
    schedule.run_pending()
    time.sleep(1)



    
