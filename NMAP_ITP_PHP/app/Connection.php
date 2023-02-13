<?php

//This is the main connection to the database executed in the container db

$usuario="root";
$pass="root";
$host="db"; //We use de "db" host because is the name of the container that is launched
$db="nmap";
$port=5432;

$conexion = pg_connect("host=$host port=$port dbname=$db user=$usuario password=$pass");
?>
