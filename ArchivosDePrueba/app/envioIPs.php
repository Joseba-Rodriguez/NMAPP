<?php  
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
header('Location: index.php');
}
?>
<?php  
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
header('Location: index.php');
}
?>