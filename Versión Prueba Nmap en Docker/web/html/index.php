<!DOCTYPE html>
<html lang="en">
<head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <title>NMAP-ITP</title>

        <style>
                table, th, td{
                        border: 1px solid black;
                }
        </style>
</head>
<body>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
        <form action="conexion.php" method="POST">

<?php


$usuario="root";
$pass="root";
$host="db";
$db="nmap";
$port=5432;

$file = file('datos.xml');


$conexion = pg_connect("host=$host port=$port dbname=$db user=$usuario password=$pass");
if($conexion)
{
    echo "conectado a la base de datos";
}

$ip;
$mac;
$vendor;
$cve;
$cveArray = array();
$cveList;
$hostname;
$port;
$portArray = array();
$portList;
$time;


foreach ($file as $line){
    //ip
    if (strpos($line, 'addrtype="ipv4"') == TRUE) {
        preg_match('/addr=".* addrtype/',$line,$results);
        $ip = implode (" ", $results);
        $ip = ltrim($ip, 'addr="');
        $ip = rtrim($ip, '" addrtype');
        print "<br><strong><u>Device</u></strong></br>";
        print "IP Address: $ip<br>";
    }
    //vendor
    if (strpos($line, 'addrtype="mac"') == TRUE) {
        preg_match('/vendor=".*/',$line,$results);
        $vendor = implode (" ", $results);
        $vendor= ltrim($vendor, 'vendor="');
        $vendor = rtrim($vendor, '"');
        print "Vendor: $vendor<br>";
    }
    //mac
    if (strpos($line, 'addrtype="mac"') == TRUE) {
        preg_match('/addr=".* addrtype/',$line,$results);
        $mac = implode (" ", $results);
        $mac = ltrim($mac, 'addr="');
        $mac = rtrim($mac, '" addrtype');
        print "MAC Address: $mac<br>";
    }
    //hostname
    if (strpos($line, 'type="PTR"') == TRUE) {
        preg_match('/name=".*" type/',$line,$results);
        $hostname = implode (" ", $results);
        $hostname = ltrim($hostname, 'name="');
        $hostname = rtrim($hostname, ' type');
        $hostname = rtrim($hostname, '"');
        print "Hostname: $hostname<br>";
    }
    //puertos
    if (strpos($line, 'portid="') == TRUE) {
        preg_match('/portid=".*><state/',$line,$results);
        $port = implode(" ", $results);
        $port = ltrim($port, 'portid="');
        $port = rtrim($port, '"><state');
        print "Port: $port<br>";
        array_push($portArray, $port);
    }

    //cve
    if (strpos($line, 'key="id"') == TRUE) {
        preg_match('/key="id">.*</',$line,$results);
        $cve = implode(" ", $results);
        $cve = ltrim($cve, 'key="id">');
        $cve = rtrim($cve, '"</elem');
        print "CVE: $cve https://vulners.com/cve/$cve<br>";
        array_push($cveArray, $cve);
    }

    if (strpos($line, '/host>') == TRUE) {
        $time = time();
        $portList = implode(", ", $portArray);
        $cveList = implode(", ", $cveArray);
        $sql = "delete from nmapScan" ;
        pg_query($conexion, $sql);
        $sql = "insert into nmapScan(ip,hostname,ports,cve,time) values ('$ip','$hostname','$portList','$cveList','$time')";
        pg_query($conexion, $sql);

        
    
    $ip = " ";
    $hostname = " ";
    unset($portArray);
    $portArray = array();
    $portList = " ";
    unset($cveArray);
    $cveArray = array();
    $cveList = " ";
    $time = " ";
    }
}
?>
        </form>
</body>
</html>
