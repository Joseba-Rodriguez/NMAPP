<?php
include "Connection.php";

// Check if "ip" or "ipIndividual" parameter has been sent
if (isset($_POST["ipIndividual"])) {
    $ip = $_POST["ipIndividual"];
    $archivoIPs = "./ipsReporte.txt";
    $query = "INSERT INTO inspectIndividual (ip) values ('".$ip."')";
    $result = pg_query($conexion, $query);

    // Empty the file and write the value of $ip
    file_put_contents($archivoIPs, $ip);

    // Redirect the user to the main page
    header('Location: index.php');
}
?>