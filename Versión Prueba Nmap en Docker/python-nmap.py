import os
#Aqui le indica las ips(editar para hacer un rango)
#Descargas de las bases de datos
os.system("cd /usr/share/nmap/scripts/ ; git clone https://github.com/vulnersCom/nmap-vulners.git ; git clone https://github.com/scipag/vulscan.git ; cd vulscan/utilities/updater/ ; chmod +x updateFiles.sh ; ./updateFiles")

os.system("nmap -sV --script nmap-vulners --script-args vulscandb=scipvuldb.csv -sV -stats-every 2s -iL ./ips.txt -oX ./web/html/datos.xml")

