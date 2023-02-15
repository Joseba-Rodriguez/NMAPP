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
    <title>NMAPP - Discovery</title>
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
                    <h1 class="tm-site-title mb-0"> NMAPP </h1>
                </a>
                <button class="navbar-toggler ml-auto mr-0" type="button" data-toggle="collapse"
                    data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                    aria-label="Toggle navigation">
                    <i class="fas fa-bars tm-nav-icon"></i>
                </button>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link d-block" href="logout.php">
                            <?php echo $_SESSION['userID']; ?>, <b>Logout</b>
                        </a>
                    </li>
                </ul>
            </div>
    </div>

    </nav>
    <div class="container">
        <!-- row -->
        <div class="row tm-content-row">

            <?php
                        include('Connection.php');
                        #Appear: data that is in nmapScan but not in lastAnalyze 
                        $query = "SELECT * FROM nmapIndividual as n WHERE NOT EXISTS(SELECT * FROM lastAnalyze AS L WHERE n.ip = L.ip)";
                        $result = pg_query($conexion, $query);
                        $arr = pg_fetch_all($result);
                        echo'
                        <div class="col-12 tm-block-col">
                        <div class="tm-bg-primary-dark tm-block tm-block-taller tm-block-scroll">
                        <h2 class="tm-block-title">Appear</h2>
                            <table class="table">
                                <tr>
                                 <th>ip</th>
                                 <th>hostname</th>
                                 <th>port</th>
                                 <th>protocol</th>
                                 <th>service</th>
                                 <th>version</th>
                                 <th>vulnerabilities</th>
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
                                    <td>'. $array['vuln'].'</td>
                                    </tr>';
                            }
                            echo'</table>';
                            ?>
        </div>
    </div>
    </div>
    <div class="row tm-content-row">
        <?php
                            #Lost :data that is in LastAnalyze but not in nmapScan 
                            $query = "SELECT * FROM lastanalyze as n WHERE NOT EXISTS(SELECT * FROM nmapIndividual AS L WHERE n.ip = L.ip)";
                            $result = pg_query($conexion, $query);
                            $arr = pg_fetch_all($result);
                            echo'
                            <div class="col-12 tm-block-col">
                        <div class="tm-bg-primary-dark tm-block tm-block-taller tm-block-scroll">
                        <h2 class="tm-block-title">Lost</h2>
                                <table class="table">
                                    <tr>
                                    <th>ip</th>
                                    <th>hostname</th>
                                    <th>port</th>
                                    <th>protocol</th>
                                    <th>service</th>
                                    <th>version</th>
                                    <th>vulnerabilities</th>
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
                                        <td>'. $array['vuln'].'</td>
                                        </tr>';
                                }
                                echo'</table>';
                                ?>
    </div>
    </div>
    </div>
    <div class="row tm-content-row">
        <?php
                                #Stay:
                                $query = "SELECT * FROM nmapIndividual INTERSECT  SELECT * from lastanalyze";
                                $result = pg_query($conexion, $query);
                                $arr = pg_fetch_all($result);
                                echo'<div class="col-12 tm-block-col">
                                <div class="tm-bg-primary-dark tm-block tm-block-taller tm-block-scroll">
                                <h2 class="tm-block-title">Stay</h2>
                                    <table class="table">
                                        <tr>
                                        <th>ip</th>
                                        <th>hostname</th>
                                        <th>port</th>
                                        <th>protocol</th>
                                        <th>service</th>
                                        <th>version</th>
                                        <th>vulnerabilities</th>
                                        </tr>';
                                foreach($arr as $array)
                                    {
                                        echo'<tr>
                                            <td>'. $array['ip'].'</td>
                                            <td>'. $array['hostname'].'</td>
                                            <td>'. $array['port'].'</td>
                                            <td>'. $array['protocol'].'</td>
                                            <td>'. $array['service'].'</td>
                                            <td>'. $array['vuln'].'</td>
                                            </tr>';
                                    }
                                    echo'</table>';
                            ?>
    </div>
    </div>
    </div>
    </div>
    </div>
    <footer class="tm-footer row tm-mt-small">
        <div class="col-12 font-weight-light">
            <p class="text-center text-white mb-0 px-4 small">
                Copyright &copy; <b>2023</b> All rights reserved by Joseba Rodríguez.

                <!-- row  Design: <a rel="nofollow noopener" href="https://templatemo.com" class="tm-footer-link">Template Mo</a>-->
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
</body>

</html>