<?php
// Get the value of the "selection" parameter from the POST request
$selection = $_POST["selection"];

// Connect to the database
require "Connection.php";

// Build the SQL query to insert the selection into the "buttons" table
$query = "INSERT INTO buttons (selection) VALUES ('$selection')";

// Execute the query
pg_query($conexion, $query);

// Close the database connection
pg_close($conexion);

// Redirect the user back to the index page
header("Location: index.php")
?>