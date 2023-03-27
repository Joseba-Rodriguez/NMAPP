<?php
if (isset($_POST['download_data'])) {
  $table = $_POST['download_data'];

  // Connect to database
  require "Connection.php";
  // Get data from table
  $result = pg_query($conexion, "SELECT * FROM $table");
  $data = pg_fetch_all($result);

  // Set header for downloading file
  header("Content-type: text/csv");
  header("Content-Disposition: attachment; filename=$table Scan.csv");
  header("Pragma: no-cache");
  header("Expires: 0");

  // Output data in CSV format
  $output = fopen("php://output", "w");
  fputcsv($output, array_keys($data[0]));
  foreach ($data as $row) {
    fputcsv($output, $row);
  }
  fclose($output);
}
?>