<?php
session_start();

// Verifica si el usuario ya ha iniciado sesión
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
  header('Location: login.php');
  exit;
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Dashboard</title>
</head>
<body>
  <iframe src="http://localhost:80" style="width: 100%; height: 100vh; border: none;"></iframe>
</body>
</html>
