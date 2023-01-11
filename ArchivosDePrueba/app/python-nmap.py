import os
#Descargas de las bases de datos
os.system("cd /usr/share/nmap/scripts/ ; git clone https://github.com/vulnersCom/nmap-vulners.git ; git clone https://github.com/scipag/vulscan.git ; cd vulscan/utilities/updater/ ; chmod +x updateFiles.sh ; ./updateFiles")

os.system("cat ./app/web/html/ips2.txt >> ./app/ips.txt ")

os.system("nmap -sV --script nmap-vulners --script-args vulscandb=scipvuldb.csv -sV -stats-every 2s -iL ./app/ips.txt -oX ./app/web/datos.xml")

os.system(" python ./app/web/Storer.py ./app/web/datos.xml ")