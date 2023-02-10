<?php
    // Connect to the PostgreSQL database
    require 'Connection.php';
    // Connect to the PostgreSQL database
    
    // Check if the connection was successful
    if (!$conexion) {
      echo "An error occurred while connecting to the database.";
      exit;
    }
    
    // Check if the form was submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      // Retrieve the form data
      $username = $_POST['username'];
      $password = $_POST['password'];
      $confirm_password = $_POST['confirm_password'];
    
      // Check if the passwords match
      if ($password !== $confirm_password) {
        echo "The passwords do not match.";
        exit;
      }

      // Hash the password
      $password_hash = password_hash($password, PASSWORD_BCRYPT);

      $query = "SELECT * FROM users WHERE userID='$username'";
      $result = pg_query($conexion, $query);
      if (pg_num_rows($result) > 0) {
        // El usuario ya existe, mostrar un error
        echo "El usuario ya existe, por favor elige otro nombre de usuario.";
      } else {
        // El usuario no existe, registrar el usuario
        $result = pg_query($conexion, "INSERT INTO users (userID, password) VALUES ('$username', '$password_hash')");
        $result = pg_query($conexion, $query);

        if ($result) {
            // El usuario se registró correctamente
            header('Location: index.php');
        } else {
            // Ocurrió un error al registrar el usuario
            echo "Ocurrió un error al registrar el usuario.";
        }
       }
      // Check if the insert was successful
      pg_close($conexion);
    }
?>