import os
#Descargas de las bases de datos
os.system("cd /usr/share/nmap/scripts/ ; git clone https://github.com/vulnersCom/nmap-vulners.git ; git clone https://github.com/scipag/vulscan.git ; cd vulscan/utilities/updater/ ; chmod +x updateFiles.sh ; ./updateFiles")

os.system("chmod 777 ./app/ips.txt ; chmod 777 ./app/datos.xml ")

#os.system("cat ./app/ips2.txt >> ./app/ips.txt ")

os.system("nmap -sV --script nmap-vulners --script-args vulscandb=scipvuldb.csv -sV -stats-every 2s -iL ./app/ips.txt -oX ./app/datos.xml")

os.system(" python ./app/Storer.py ./app/datos.xml ")