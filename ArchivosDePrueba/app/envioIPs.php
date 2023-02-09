<?php  
//connection with the general ip scan and export to ips.txt file
include "Connection.php";
if (isset($_POST["ip"])){
    $ip = $_POST["ip"];
    $query = "INSERT INTO inspect (ip) values ('".$ip."');";    
    $result = pg_query($conexion, $query);
shell_exec('cat /dev/null > ips.txt');
$archivoIPs = "./ips.txt";
$archivo = fopen($archivoIPs, "w+");
fwrite($archivo, $ip );
fclose($archivo);
//we move to main location
header('Location: index.php');
}
?>
<?php  
//connection with the individual ip scan and export to ipsReporte.txt file
include "Connection.php";
if (isset($_POST["ipIndividual"])){
    $ip = $_POST["ipIndividual"];
    $query = "INSERT INTO inspectIndividual (ip) values ('".$ip."');";    
    $result = pg_query($conexion, $query);
shell_exec('cat /dev/null > ipsReporte.txt');
$archivoIPs = "./ipsReporte.txt";
$archivo = fopen($archivoIPs, "w+");
fwrite($archivo, $ip );
fclose($archivo);
//we move to main location
header('Location: index.php');
}
?>
