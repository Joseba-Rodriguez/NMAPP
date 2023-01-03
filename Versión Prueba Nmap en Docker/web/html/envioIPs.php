<?php 
                                #insert the ips introducded in the web form
                                    $ip = isset($_POST['ips']);
                                    $ip = strip_tags($ip);
                                    $query = "INSERT INTO inspect (ip) values ('$ip');";    
                                    $result = pg_query($conexion, $query);
?>