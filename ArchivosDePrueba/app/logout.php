<?php
  // Start session and destroy it to log out the user
  session_start();
  session_destroy();
  
  // Redirect to the login page after logging out
  header('Location: login.php');
  exit;
?>