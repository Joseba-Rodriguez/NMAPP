<?php  
include "Connection.php";
if (isset($_POST["ip"])){
    $ip = $_POST["ip"];
    $query = "INSERT INTO inspect (ip) values ('".$ip."');";    
    $result = pg_query($conexion, $query);
} 
#All the data from the last execution    
$query = "SELECT * FROM inspect;";
$result = pg_query($conexion, $query);
$arr = pg_fetch_all($result);
echo'<table>
        <tr>
        <th>ip</th>
        </tr>';
foreach($arr as $array){
        echo'<tr>
            <td>'. $array['ip'].'</td>
            </tr>';
    }
    echo'</table>';

?>