<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NMAP-ITP Aero</title>
</head>
<body>
<?php 

$file = file('datos.xml');

$servername = "db";
$username = "root";
$password = "root" ;
$db = "nmap";

$conn = pg_connect("host=db dbname=nmap user=root password=root");

$ip;
$hostname;
$port;
$portArray = array();
$portList;
$timestamp;


foreach ($file as $line){
    if (strpos($line, 'addrtype="ipv4"') == TRUE) {
        preg_match('/addr=".* addrtype/',$line,$results);
        $ip = implode (" ", $results);
        $ip = ltrim($ip, 'addr="');
        $ip = rtrim($ip, '" addrtype');
        print "<br><strong><u>Device</u></strong></br>";
        print "IP Address: $ip<br>";
    }
    
    if (strpos($line, 'type="PTR"') == TRUE) {
        preg_match('/name=".* type/',$line,$results);
        $hostname = implode (" ", $results);
        $hostname = ltrim($hostname, 'name="');
        $hostname = rtrim($hostname, ' type');
        $hostname = rtrim($rtrim, '"');
        print "Hostname: $hostname<br>";
        }
    
    if (strpos($line, 'portid="') == TRUE) {
        preg_match('/portid=".*><state/',$line,$results);
        $port = implode (" ", $results);
        $port = ltrim($port, 'portid="');
        $port = rtrim($port, '"><state');
        print "Port: $hostname<br>";
        array_push($portArray, $port);
        }
    
    if (strpos($line, '/host>') == TRUE) {
        $timestamp = time ();
        $portList = implode(", ", $portArray);
        $sql = "insert into nmap (ip, hostname, ports, timestamp) values ('$ip','$hostname','$portList','$timestamp')";

    $ip = " ";
    $hostname = " ";
    unset($portArray);
    $portArray = array();
    $portList = " ";
    }
}
$pg_close();
?>
</body>
</html>


