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
    
      // Insert the data into the users table
      $result = pg_query($conexion, "INSERT INTO users (userID, password) VALUES ('$username', '$password_hash')");
    
      // Check if the insert was successful
      if (!$result) {
        echo "An error occurred while registering the user.";
        exit;
      }
      header('Location: index.php');
    }
?>