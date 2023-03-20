<?php
shell_exec("nmap -Pn --open -sV --script vulners --script-args mincvss=5.0 --script-args vulners.cvesonly -sV -stats-every 2s -iL ./ipsNow.txt -oX ./datos.xml");
shell_exec("python3 storer.py 1");
header("Location: index2.php")
?>