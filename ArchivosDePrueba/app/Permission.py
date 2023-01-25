import time
import os ,sys, stat

os.chmod("./ips.txt", stat.S_IRWXO) #establecer permisos para ips.txt
os.chmod("./datos.xml", stat.S_IRWXO) #establecer permisos para datos.txt
os.chmod("./Storer.py", stat.S_IRWXO) #establecer permisos para Storer.py
os.chmod("./programmer.py", stat.S_IRWXO) #establecer permisos para programmer.py
os.chmod("./DBVulners.py", stat.S_IRWXO) #establecer permisos para programmer.py
os.chmod("./Analyzer.py", stat.S_IRWXO) #establecer permisos para programmer.py
