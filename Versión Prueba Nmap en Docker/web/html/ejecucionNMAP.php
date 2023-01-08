<?php  
    $command = escapeshellcmd('python python-nmap.py');
    $output = shell_exec($command);
    echo $output;
?>