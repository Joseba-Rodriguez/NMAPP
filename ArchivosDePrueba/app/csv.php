<?php
if (isset($_POST['download_data'])) {
    $table = $_POST['download_data'];

    // Connect to database
    require "Connection.php";
    
    // Escape the table name to prevent SQL injection
    $table = pg_escape_identifier($table);

    // Get data from table
    $result = pg_query($conexion, "SELECT * FROM $table");
    if (!$result) {
        // Query failed
        echo "Query failed: " . pg_last_error($conexion);
        exit;
    }
    $data = pg_fetch_all($result);
    if (!$data) {
        // No data found
        echo "No data found in table '$table'";
        exit;
    }

    // Set header for downloading file
    header("Content-type: text/csv");
    header("Content-Disposition: attachment; filename=${table}_Scan.csv");
    header("Pragma: no-cache");
    header("Expires: 0");

    // Output data in CSV format
    $output = fopen("php://output", "w");
    fputcsv($output, array_keys($data[0]));
    foreach ($data as $row) {
        fputcsv($output, $row);
    }
    fclose($output);
    exit;
}
?>