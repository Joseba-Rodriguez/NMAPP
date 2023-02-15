<?php
session_start();
// Establish a connection to the PostgreSQL database
require 'Connection.php';

// Verify if the connection to the database was successful
if (!$conexion) {
  echo "Ocurrió un error en la conexión con la base de datos.\n";
  exit;
}

// Check if the login form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  // Obtiene los valores del formulario
  $username = $_POST['username'];
  $password = $_POST['password'];

 // Query the database to verify if the user exists
  $result = pg_query($conexion, "SELECT * FROM users WHERE userID='$username'");

  // Check if the user exists in the database
  if (pg_num_rows($result) == 0) {
    echo "<script>";
    echo "alert('El nombre de usuario no existe');";
    echo "window.location.href='login.php';";
    echo "</script>";
    exit;
  }

  // Get the user information from the database
  $user = pg_fetch_assoc($result);

  // Check if the entered password matches the one stored in the database
  if (!password_verify($password, $user['password'])) {
    echo "<script>";
    echo "alert('Contraseña incorrecta');";
    echo "window.location.href='login.php';";
    echo "</script>";
    exit;
    
  }
  // Successful login
  $_SESSION['logged_in'] = true;
  $_SESSION['userID'] = $username;
  header('Location: index.php');
  exit;
}

pg_close($conexion);

?>