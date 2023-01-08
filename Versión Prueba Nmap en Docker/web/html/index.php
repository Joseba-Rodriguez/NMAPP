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
                <button class="navbar-toggler ml-auto mr-0" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
                    aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <i class="fas fa-bars tm-nav-icon"></i>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link d-block" href="login.html">
                                Admin, <b>Logout</b>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

        </nav>
        <div class="container">
            <div class="row">
                <div class="col">
                    <p class="text-white mt-5 mb-5">Welcome back, <b>Admin</b></p>
                </div>
            </div>
            <!-- row -->
            <div class="row tm-content-row">
                <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6 tm-block-col">
                    <div class="tm-bg-primary-dark tm-block">
                        <?php
                        include('Connection.php');
                   #All the data from the last execution    
                        $query = "SELECT * FROM nmapScan;";
                        
                        $result = pg_query($conexion, $query);
                        
                        $arr = pg_fetch_all($result);
                        echo'<table>
                                <tr>
                                 <th>ip</th>
                                 <th>hostname</th>
                                 <th>port</th>
                                 <th>protocol</th>
                                 <th>service</th>
                                 <th>version</th>
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
                                    </tr>';
                            }
                            echo'</table>';
                        
                        ?>
                    </div>
                </div>
                <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6 tm-block-col">
                    <div class="tm-bg-primary-dark tm-block">
                        <a href="Discovery.php"><button>Descubrimientos</button></a>
                        <h2>Introduce las ips o rangos de ips</h2>
                            
                        <form action="envioIPs.php" method="post" name="formulario">
                            <input type="text" name="ip" placeholder="p.e 192.168.0.1 o www.ehu.es">
                            <input type="submit" value="Enviar">
                        <?php   $query = "SELECT * FROM inspect;";
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
                                    echo'</table>';?>
                        </form><br>

                            
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
        $(function () {
            drawLineChart(); // Line Chart
            drawBarChart(); // Bar Chart
            drawPieChart(); // Pie Chart

            $(window).resize(function () {
                updateLineChart();
                updateBarChart();                
            });
        })
    </script>
</body>

</html>