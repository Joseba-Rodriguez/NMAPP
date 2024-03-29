<?php
session_start();

// Verify if the user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

// Connect to the database
require("Connection.php");

// Check if the form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userID = $_SESSION['userID'];
    $password = $_POST['password'];
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    // Update the password in the database
    $query = "UPDATE users SET password = '$passwordHash' WHERE userID = '$userID'";
    $result = pg_query($conexion, $query);
    header("Location: index.php");
}