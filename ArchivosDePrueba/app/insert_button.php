<?php
$selection = $_POST["selection"];

require "Connection.php";

$query = "INSERT INTO buttons (selection) VALUES ('$selection')";
pg_query($conexion, $query);

pg_close($conexion);
header("Location: index.php")
?>