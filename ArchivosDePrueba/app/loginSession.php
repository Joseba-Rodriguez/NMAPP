<?php 
session_start();
// Establecer una conexión a la base de datos PostgreSQL
require 'Connection.php';

// Verificar si la conexión a la base de datos fue exitosa
if (!$conexion) {
  echo "Ocurrió un error en la conexión con la base de datos.\n";
  exit;
}

// Comprobar si se envió el formulario de inicio de sesión
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  // Obtener los valores del formulario
  $username = $_POST['username'];
  $password = $_POST['password'];

  // Consultar la base de datos para verificar si el usuario existe
  $result = pg_query($conexion, "SELECT * FROM users WHERE userID='$username'");

  // Comprobar si el usuario existe en la base de datos
  if (pg_num_rows($result) == 0) {
    echo "<script>";
    echo "alert('El nombre de usuario o contraseña son incorrectos');";
    echo "window.location.href='login.php';";
    echo "</script>";
    exit;
  }

  // Obtener la información del usuario desde la base de datos
  $user = pg_fetch_assoc($result);

  // Comprobar si la contraseña ingresada coincide con la almacenada en la base de datos
  if (!($user && crypt($password, $user['password']) == $user['password'])) {
    echo "<script>";
    echo "alert('La contraseña no es correcta');";
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