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
    <title>NMAPP</title>
    <link rel='icon' type='image/png' href="/css/logo.jpg">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:400,700">
    <!-- https://fonts.google.com/specimen/Roboto -->
    <link rel="stylesheet" href="css/fontawesome.min.css">
    <!-- https://fontawesome.com/ -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <!-- https://getbootstrap.com/ -->
    <link rel="stylesheet" href="css/templatemo-style.css">
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
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link d-block" href="logout.php">
                                <?php echo $_SESSION['userID']; ?>, <b>Logout</b>
                            </a>
                        </li>
                    </ul>
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <form>
                                <a class="nav-link d-block" href="changePassword.php">
                                    <b>Cambia la contraseña</b>
                                </a>
                            </form>
                        </li>
                        <form>
                            <a class="nav-link d-block" href="register.php">
                                <b>Registra un nuevo usuario</b>
                            </a>
                        </form>
                    </ul>
                </div>
            </div>

        </nav>
        <div class=" col-12 tm-block-col">
            <!-- row -->
            <?php
                        include('Connection.php');
                   #All the data from the last execution    
                        $query = "SELECT * FROM nmapIndividual;";
                        $result = pg_query($conexion, $query);
                        $arr = pg_fetch_all($result);
                        echo'
                        <div class="tm-bg-primary-dark tm-block tm-block-scroll">
                        <div class="col">
                        <h2 class="tm-block-title mb-4">Último reporte</h2>
                        <p class="text-white mt-5 mb-5">Aquí podrás ver el último reporte completo realizado</p>
                        <form action="csv.php" method="post">
                        <input type="hidden" name="download_data" value="nmapIndividual">
                        <input type="submit"  class=" btn-primary text-uppercase" value="Descargar datos">
                    </form>
                    </div>    
                            <table class="table">
                                <tr>
                                 <th>IP</th>
                                 <th>HOSTNAME</th>
                                 <th>PORT</th>
                                 <th>PROTOCOL</th>
                                 <th>SERVICE</th>
                                 <th>VERSION</th>
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
                                    </tr>'
                                    ;    
                            }
                            echo'</table>';
                        ?>
        </div>
        <p class="text-white mb-0 px-4 small">Tiempo restante hasta el siguiente escaneo: <span id="timer"></span>
            <?php
                        $query = "SELECT selection FROM buttons ORDER BY id DESC LIMIT 1";
                        $result = pg_query($conexion, $query) or die('Query failed: ' . pg_last_error());
                        // Obtenemos los resultados
                        $row = pg_fetch_array($result, null, PGSQL_ASSOC);
                        // Mostramos el resultado
                        $selected = $row['selection'];
                        echo $selected . " selected";
                        ?>
        </p>
        <div class="d-flex justify-content-center">
            <div class="media-body">
                <form action="insert_button.php" method="post">

                    <p class=" text-white mb-0 px-4 small">
                        <input type="submit" class="btn  text-uppercase" name="selection" value="2Weeks">
                        <input type="submit" class="btn  text-uppercase" name="selection" value="monthly">
                        <input type="submit" class="btn  text-uppercase" name="selection" value="now">
                </form>
                <div id="timer">
                    <?php
                        if ($selected == "2Weeks") {
                        $time = 24 * 60 * 60 * 7 * 2; // 24 horas en segundos
                        } else if ($selected == "monthly") {
                        $time = 30 * 24 * 60 * 60; // 30 días en segundos
                        }
                        ?>
                    </p>
                </div>
                <div class="media-body">
                    <a href="Discovery.php">
                        <p class="text-center text-white mb-0 px-4 tm-small">Pulsa para comparar los
                            escaneos</p>
                        <button class="btn btn-primary btn-block text-uppercase"></i>Descubrimientos</button>
                    </a>

                </div>
                <form action="envioIPs.php" method="post" name="formulario">
                    <input class="form-control validate" type="text" name="ipIndividual"
                        placeholder=" Introduce IP o rangos de IPs. p.e 192.168.0.1 o www.ehu.es">
                    <input class="btn btn-primary text-uppercase" type="submit" value="Enviar">
                    <input class="btn btn-primary text-uppercase" type="submit" value="Eliminar">
                    <?php  $query = "SELECT * FROM inspectIndividual ORDER BY idIpIndividual DESC LIMIT 1;";
                                $result = pg_query($conexion, $query);
                                $arr = pg_fetch_all($result);
                                echo'<h2 class="tm-block-title">Historial de ips</h2>
                                <table class="table tm-table-small tm-product-table">';
                                foreach($arr as $array){
                                    echo'<tr>
                                            <td  class="tm-product-name" >'. $array['ip'].'</td>
                                        </tr>';
                                  break;
                                    }
                                    echo'</table>';?>
                </form><br>

            </div>
        </div>
    </div>
    <!-- row -->
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
    <script>
    const timer = document.querySelector("#timer");
    let time = <?php echo $time; ?>;

    setInterval(() => {
        time--;
        const hours = Math.floor(time / 3600);
        const minutes = Math.floor((time % 3600) / 60);
        const seconds = time % 60;
        timer.innerHTML = `${hours}:${minutes}:${seconds}`;
        if (time === 0) {
            window.location.reload();
        }
    }, 1000);
    </script>

</body>

</html>