<?php
session_start();
// Conexión a la base de datos PostgreSQL
require 'Connection.php';

// Verifica si la conexión a la base de datos fue exitosa
if (!$conexion) {
  echo "Ocurrió un error en la conexión con la base de datos.\n";
  exit;
}

// Verifica si el formulario de inicio de sesión fue enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  // Obtiene los valores del formulario
  $username = $_POST['username'];
  $password = $_POST['password'];

  // Consulta a la base de datos para verificar si el usuario existe
  $result = pg_query($conexion, "SELECT * FROM users WHERE userID='$username'");

  // Verifica si el usuario existe en la base de datos
  if (pg_num_rows($result) == 0) {
    echo "<script>";
    echo "alert('El nombre de usuario no existe');";
    echo "window.location.href='login.php';";
    echo "</script>";
    exit;
  }

  // Obtiene la información del usuario desde la base de datos
  $user = pg_fetch_assoc($result);

  // Verifica si la contraseña ingresada coincide con la almacenada en la base de datos
  if (!password_verify($password, $user['password'])) {
    echo "<script>";
    echo "alert('Contraseña incorrecta');";
    echo "window.location.href='login.php';";
    echo "</script>";
    exit;
    
  }
  // Inicio de sesión exitoso
  $_SESSION['logged_in'] = true;
  $_SESSION['userID'] = $username;
  header('Location: index.php');
  exit;
}

pg_close($conexion);

?>