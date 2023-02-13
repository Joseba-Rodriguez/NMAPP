<?php
session_start();

// Verificar si el usuario está en sesión
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

// Conexión a la base de datos
require("Connection.php");

// Comprobar si se ha enviado el formulario
    $userID = $_SESSION['userID'];
    $password = $_POST['password'];
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        // Actualizar la contraseña en la base de datos
        $query = "UPDATE users SET password = '$passwordHash' WHERE userID = '$userID'";
        $result = pg_query($conexion, $query);
            header("Location: index.php");
        

?>