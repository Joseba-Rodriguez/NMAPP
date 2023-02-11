<?php
include "Connection.php";

// Chequea si se ha enviado el parámetro "ip" o "ipIndividual"
if (isset($_POST["ip"]) || isset($_POST["ipIndividual"])) {
    $ip = isset($_POST["ip"]) ? $_POST["ip"] : $_POST["ipIndividual"];
    $archivoIPs = isset($_POST["ip"]) ? "./ips.txt" : "./ipsReporte.txt";
    $tabla = isset($_POST["ip"]) ? "inspect" : "inspectIndividual";
    $query = "INSERT INTO $tabla (ip) values ('".$ip."');";    
    $result = pg_query($conexion, $query);
    
    // Vacía el archivo y escribe el valor de $ip
    shell_exec("cat /dev/null > $archivoIPs");
    $archivo = fopen($archivoIPs, "w+");
    fwrite($archivo, $ip );
    fclose($archivo);
    
    // Redirige al usuario a la página principal
    header('Location: index.php');
}
?>