<!DOCTYPE html>
<html lang="en">
<head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <title>NMAP-ITP</title>

        <style>
                table, th, td{
                        border: 1px solid black;
                }
        </style>
</head>
<body>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
        <form action="conexion.php" method="POST">

<?php


$usuario="root";
$pass="root";
$host="db";
$db="nmap";
$port=5432;

$file = file('datos.xml');


$conexion = pg_connect("host=$host port=$port dbname=$db user=$usuario password=$pass");
if($conexion)
{
    echo "conectado a la base de datos";
}

$query = "SELECT * FROM nmapScan;";

$result = pg_query($conexion, $query);

$arr = pg_fetch_all($result);

print_r($arr);
?>
        </form>
</body>
</html>
