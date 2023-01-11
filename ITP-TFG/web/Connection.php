<?php

$usuario="root";
$pass="root";
$host="db";
$db="nmap";
$port=5432;

$conexion = pg_connect("host=$host port=$port dbname=$db user=$usuario password=$pass");
?>