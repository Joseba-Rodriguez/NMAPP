<?php

//This is the main connection to the database executed in the container db
$configuracion = parse_ini_file('./postgresConfiguration.txt');
$usuario = $configuracion['DB_USER'];
$pass = $configuracion['DB_PASS'];
$host = $configuracion['DB_HOST'];
$db = $configuracion['DB_DB'];
$port=$configuracion['DB_PORT'];


$conexion = pg_connect("host=$host port=$port dbname=$db user=$usuario password=$pass");
?>