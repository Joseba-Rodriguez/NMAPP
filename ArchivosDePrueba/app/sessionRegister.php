<?php
    require 'Connection.php';
    $user = $_POST['username'];
    $pass = $_POST['password'];

    $query = "INSERT INTO users (userID , password) VALUES ('".$user. "', '".$pass."')";

    $consulta = pg_query($conexion, $query);
    header('Location: index.php');
?>