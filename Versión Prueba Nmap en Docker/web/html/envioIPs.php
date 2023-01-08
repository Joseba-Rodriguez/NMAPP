<?php  
include "Connection.php";
if (isset($_POST["ip"])){
    $ip = $_POST["ip"];
    $query = "INSERT INTO inspect (ip) values ('".$ip."');";    
    $result = pg_query($conexion, $query);
echo'Enviando datos a la base de datos...';
$archivoIPs = "ips.txt";
$archivo = fopen($archivoIPs, "wb");
fwrite($archivo, $ip);
fclose($archivo);  
}
?>