<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>PostgreSQL</title>
	
	<style>
		table, th, td{
			border: 1px solid black;
		}
	</style>
</head>
<body>
	<form action="conexion.php" method="POST">

<?php 

$file = file('datos.xml');
$conexion = pg_connect("host=db port=5432 dbname=nmap user=root password=root");

$ip;
$mac;
$vendor;
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
    
    if (strpos($line, 'addrtype="mac"') == TRUE) {
        preg_match('/vendor=".*/',$line,$results);
        $vendor = implode (" ", $results);
        $vendor= ltrim($vendor, 'vendor="');
        $vendor = rtrim($vendor, '"');
        print "Vendor: $vendor<br>";
    }

    if (strpos($line, 'addrtype="mac"') == TRUE) {
        preg_match('/addr=".* addrtype/',$line,$results);
        $mac = implode (" ", $results);
        $mac = ltrim($mac, 'addr="');
        $mac = rtrim($mac, '" addrtype');
        print "MAC Address: $mac<br>";
    }

    if (strpos($line, 'type="PTR"') == TRUE) {
        preg_match('/name=".*" type/',$line,$results);
        $hostname = implode (" ", $results);
        $hostname = ltrim($hostname, 'name="');
        $hostname = rtrim($hostname, ' type');
        $hostname = rtrim($hostname, '"');
        print "Hostname: $hostname<br>";
    }
    
    if (strpos($line, 'portid="') == TRUE) {
        preg_match('/portid=".*><state/',$line,$results);
        $port = implode(" ", $results);
        $port = ltrim($port, 'portid="');
        $port = rtrim($port, '"><state');
        print "Port: $port<br>";
        array_push($portArray, $port);
    }
    
    if (strpos($line, '/host>') == TRUE) {
        $timestamp = time();
        $portList = implode(", ", $portArray);
        $sql = "insert into nmap(ip,mac,vendor,hostname,ports,timestamp) values ('$ip','$mac','$vendor','$hostname','$portList','$timestamp')";

    $ip = " ";
    $mac = " ";
    $vendor = " ";
    $hostname = " ";
    unset($portArray);
    $portArray = array();
    $portList = " ";
    }
}
?>
	</form>
</body>
</html>