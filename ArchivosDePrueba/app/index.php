<?php 
  session_start();

  // Verifica si el usuario ya ha iniciado sesión
  if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit;
  }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Product Admin - Dashboard HTML Template</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:400,700">
    <!-- https://fonts.google.com/specimen/Roboto -->
    <link rel="stylesheet" href="css/fontawesome.min.css">
    <!-- https://fontawesome.com/ -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <!-- https://getbootstrap.com/ -->
    <link rel="stylesheet" href="css/templatemo-style.css">
    <!--
	Product Admin CSS Template
	https://templatemo.com/tm-524-product-admin
	-->
</head>

<body id="reportsPage">
    <div class="" id="home">
        <nav class="navbar navbar-expand-xl">
            <div class="container h-100">
                <a class="navbar-brand" href="index.php">
                    <h1 class="tm-site-title mb-0"> ITP Aero - TFG </h1>
                </a>
                <button class="navbar-toggler ml-auto mr-0" type="button" data-toggle="collapse"
                    data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                    aria-label="Toggle navigation">
                    <i class="fas fa-bars tm-nav-icon"></i>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link d-block" href="logout.php">
                                Admin, <b>Logout</b>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

        </nav>
        <div class="col-12 tm-block-col">
            <div class="col">
            </div>
            <div class="container">
                <div class="row">
                </div>
                <!-- row -->
                <?php
                        include('Connection.php');
                   #All the data from the last execution    
                        $query = "SELECT * FROM nmapIndividual;";
                        $result = pg_query($conexion, $query);
                        $arr = pg_fetch_all($result);
                        echo'
                        <div class="col-12 tm-block-col">
                        <div class="tm-bg-primary-dark tm-block tm-block-taller tm-block-scroll">
                        <div class="col">
                        <h2 class="tm-block-title mb-4">Último reporte</h2>
                        <p class="text-white mt-5 mb-5">Aquí podrás ver el último reporte completo realizado</p>
                    </div>    
                            <table class="table">
                                <tr>
                                 <th>IP</th>
                                 <th>HOSTNAME</th>
                                 <th>PORT</th>
                                 <th>PROTOCOL</th>
                                 <th>SERVICE</th>
                                 <th>VERSION</th>
                                 <th>VULNERABILITIES</th>
                                </tr>';
                        foreach($arr as $array)
                            {
                                echo'<tr>
                                    <td>'. $array['ip'].'</td>
                                    <td>'. $array['hostname'].'</td>
                                    <td>'. $array['port'].'</td>
                                    <td>'. $array['protocol'].'</td>
                                    <td>'. $array['service'].'</td>
                                    <td>'. $array['version'].'</td>
                                    <td><div class="media tm-notification-item"><span>'. $array['vuln'] .'</span></div></td>
                                    </tr>'
                                    ;    
                            }
                            echo'</table>';
                        ?>
            </div>
            <div class="tm-bg-primary-dark tm-block tm-block-taller tm-block-scroll">
                <div class="media-body">
                    <a href="Discovery.php">
                        <p class="text-center text-white mb-0 px-4 tm-small">Pulsa para comparar los escaneos</p>
                        <button class="btn btn-primary btn-block text-uppercase"></i>Descubrimientos</button>
                    </a>

                </div>
                <form action="envioIPs.php" method="post" name="formulario">
                    <input class="form-control validate" type="text" name="ipIndividual"
                        placeholder=" Introduce IP o rangos de IPs. p.e 192.168.0.1 o www.ehu.es">
                    <input class="btn btn-primary text-uppercase" type="submit" value="Enviar">
                    <input class="btn btn-primary text-uppercase" type="submit" value="Eliminar">
                    <?php   $query = "SELECT * FROM inspectIndividual;";
                                $result = pg_query($conexion, $query);
                                $arr = pg_fetch_all($result);
                                echo'<h2 class="tm-block-title">Historial de ips</h2>
                                <table class="table tm-table-small tm-product-table">';
                                foreach($arr as $array){
                                        echo'<tr>
                                            <td  class="tm-product-name" >'. $array['ip'].'</td>
                                            </tr>';
                                    }
                                    echo'</table>';?>
                </form><br>
            </div>
        </div>
    </div>

    <div class="col-12 tm-block-col">
        <div class="container">
            <div class="row">
            </div>
            <!-- row -->
            <?php
                        include('Connection.php');
                   #All the data from the last execution    
                        $query = "SELECT * FROM nmapScan;";
                        $result = pg_query($conexion, $query);
                        $arr = pg_fetch_all($result);
                        echo'
                        <div class="col-12 tm-block-col">
                        <div class="tm-bg-primary-dark tm-block tm-block-taller tm-block-scroll">
                        <div class="col">
                        <h2 class="tm-block-title mb-4">Escaneos individuales</h2>
                        <p class="text-white mt-5 mb-5">Aquí podrás realizar escaneos individuales<b> PRUÉBALO!</b></p>
                    </div>
                            <table class="table">
                                <tr>
                                 <th>IP</th>
                                 <th>HOSTNAME</th>
                                 <th>PORT</th>
                                 <th>PROTOCOL</th>
                                 <th>SERVICE</th>
                                 <th>VERSION</th>
                                 <th>VULNERABILITIES</th>
                                </tr>';
                        foreach($arr as $array)
                            {
                                echo'<tr>
                                    <td>'. $array['ip'].'</td>
                                    <td>'. $array['hostname'].'</td>
                                    <td>'. $array['port'].'</td>
                                    <td>'. $array['protocol'].'</td>
                                    <td>'. $array['service'].'</td>
                                    <td>'. $array['version'].'</td>
                                    <td><div class="media tm-notification-item"><span>'. $array['vuln'] .'</span></div></td>
                                    </tr>';
                            }
                            echo'</table>';
                        ?>
        </div>
        <div class="tm-bg-primary-dark tm-block tm-block-taller tm-block-scroll">
            <form action="envioIPs.php" method="post" name="formulario">
                <input class="form-control validate" type="text" name="ip"
                    placeholder=" Introduce IP o rangos de IPs. p.e 192.168.0.1 o www.ehu.es">
                <input class="btn btn-primary text-uppercase" onclick="refreshPage()" type="submit" value="Enviar">
                <input class="btn btn-primary text-uppercase" type="submit" value="Eliminar">
                <?php           $query = "SELECT * FROM inspect;";
                                $result = pg_query($conexion, $query);
                                $arr = pg_fetch_all($result);
                                echo'
                                
                                <h2 class="tm-block-title">Historial de ips</h2>
                                
                                <table class="table tm-table-small tm-product-table">';
                                foreach($arr as $array){
                                        echo'<tr>
                                            <td  class="tm-product-name" >'. $array['ip'].'</td>
                                            </tr>';
                                    }
                                    echo'</table>';?>
            </form><br>
            <!-- In this form we have 3 types of execution, in which we can choose diffetent types of executions-->
            <!-- The executions are executed inside the php directly-->
            <form method="post" name="formulario2">
                <input class="btn btn-primary btn-block text-uppercase" type="submit" value="Ejecución rápida"
                    name="nmapExecute">
            </form><br>
            <?php 	if(isset($_POST['nmapExecute']))
                                {   
				    //To execute "Ejecución rápida" with any script and most common ports
                                    shell_exec("nmap -sV -stats-every 2s -iL ./ips.txt -oX ./datos.xml");
                                    shell_exec("  python3 ./storer.py 1 ");
                                }?>
            <form method="post" name="formulario2">
                <input class="btn btn-primary btn-block text-uppercase" type="submit"
                    value="Ejecución todos los puertos" name="nmapExecute">
            </form><br>
            <?php 	if(isset($_POST['nmapExecute']))
                                {   
				    //To execute "Ejecución todos los puertos" with no script and most common ports
                                    shell_exec("nmap -p- -sV -stats-every 2s -iL ./ips.txt -oX ./datos.xml");
                                    shell_exec("  python3 ./storer.py 1 ");
                                }?>
            <form method="post" name="formulario2">
                <input class="btn btn-primary btn-block text-uppercase" type="submit" value="Ejecución total"
                    name="nmapExecute">
            </form><br>
            <?php 	if(isset($_POST['nmapExecute']))
                                {   
				    //To execute "Ejecución total" with the vulners script and most common ports
                                    shell_exec("nmap -p- -sV --script vulners --script-args mincvss=5.0 -sV -stats-every 2s -iL ./ips.txt -oX ./datos.xml");
                                    shell_exec(" python3 ./storer.py 1 ");
                                }?>
        </div>
    </div>
    </div>
    </div>
    <footer class="tm-footer row tm-mt-small">
        <div class="col-12 font-weight-light">
            <p class="text-center text-white mb-0 px-4 small">
                Copyright &copy; <b>2023</b> All rights reserved.

                Design: <a rel="nofollow noopener" href="https://templatemo.com" class="tm-footer-link">Template Mo</a>
            </p>
        </div>
    </footer>
    </div>

    <script src="js/jquery-3.3.1.min.js"></script>
    <!-- https://jquery.com/download/ -->
    <script src="js/moment.min.js"></script>
    <!-- https://momentjs.com/ -->
    <script src="js/Chart.min.js"></script>
    <!-- http://www.chartjs.org/docs/latest/ -->
    <script src="js/bootstrap.min.js"></script>
    <!-- https://getbootstrap.com/ -->
    <script src="js/tooplate-scripts.js"></script>
    <script>
    Chart.defaults.global.defaultFontColor = 'white';
    let ctxLine,
        ctxBar,
        ctxPie,
        optionsLine,
        optionsBar,
        optionsPie,
        configLine,
        configBar,
        configPie,
        lineChart;
    barChart, pieChart;
    // DOM is ready
    $(function() {
        drawLineChart(); // Line Chart
        drawBarChart(); // Bar Chart
        drawPieChart(); // Pie Chart

        $(window).resize(function() {
            updateLineChart();
            updateBarChart();
        });
    })
    </script>
    <script>
    function refreshPage() {
        window.location.reload();
    }
    </script>
</body>

</html>