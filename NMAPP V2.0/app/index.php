<?php include "./resources/header.php" ?>
  <body>
    <div class="container-fluid">
      <div class="row">
        <div class="jwrapper">
          <header>
            <nav class="navbar navbar-expand-lg jnavbar">
              <a href="index.php" class="navbar-brand">
                <img
                  src="css/logo.png"
                  alt="NMAPP Logo"
                  class="brand-image img-circle elevation-2"
                  style="opacity: 0.7"
                />
                <span class="brand-text font-weight-light">NMAPP</span>
              </a>
              <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                  <li class="nav-item dropdown">
                    <a
                      class="nav-link dropdown-toggle"
                      href="#"
                      id="navbarDropdown"
                      role="button"
                      data-toggle="dropdown"
                      aria-haspopup="true"
                      aria-expanded="false"
                    >
                      Otros
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                      <a class="dropdown-item" href="changePassword.php"
                        >Cambia la contraseña</a
                      >
                      <a class="dropdown-item" href="register.php"
                        >Registra un nuevo usuario</a
                      >
                    </div>
                  </li>
                  <li class="nav-item">
                    <form>
                      <a class="nav-link" href="Discovery.php"
                        ><b>Descubrimientos</b></a
                      >
                    </form>
                  </li>
                  <li class="nav-item">
                    <form>
                      <a class="nav-link" href="index2.php"><b>nmapNow</b></a>
                    </form>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="logout.php"
                      ><?php echo $_SESSION['userID']; ?>, <b>Logout</b></a
                    >
                  </li>
                </ul>
              </div>
            </nav>
          </header>
        </div>
      </div>
      <div class="row">
        <div class="jwrapper">
          <h1>NMAPP Scheduled</h1>
          <p class="text-white mb-0 px-4 small">
            Último escaneo:
            <?php
                        include 'Connection.php';
                        $query = "SELECT * FROM stats ORDER BY idTime DESC LIMIT 1";
                        $result = pg_query($conexion, $query) or die('Query failed: ' . pg_last_error());
                        // Obtenemos los resultados
                        $row = pg_fetch_array($result, null, PGSQL_ASSOC);
                        // Mostramos el resultado
                        $summary = $row['summary'];
                        echo $summary;
                        ?>
          </p>
        </div>
      </div>
      <div class="row">
        <div class="col-4">
          <div class="chart-jwrapper">
          <?php
            include 'Connection.php';

            // Obtener los datos de los últimos 7 días
            $fecha_actual = date('Y-m-d');
            $fecha_anterior = date('Y-m-d', strtotime('-7 days', strtotime($fecha_actual)));
            $query = pg_query($conexion, "SELECT port, COUNT(DISTINCT ip) as dispositivos FROM nmapIndividual WHERE ts >= '$fecha_anterior' GROUP BY port");

            // Crear un array para almacenar los datos
            $data = array(
                array('Puerto', 'Dispositivos escaneados')
            );

            // Iterar sobre los resultados y agregarlos al array de datos
            while ($row = pg_fetch_assoc($query)) {
                $data[] = array($row['port'], intval($row['dispositivos']));
            }

            // Crear la gráfica de Google Chart
            ?>
            <style>
                #chart_div {
                    margin: 0 auto; /* Centrar el div horizontalmente */
                    width: 900px;
                    height: 500px;
                    background-color: transparent; /* Cambiar el fondo a transparente */
                }
            </style>
            <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
            <script type="text/javascript">
                google.charts.load('current', {'packages': ['corechart']});
                google.charts.setOnLoadCallback(drawChart);

                function drawChart() {
                    var data = google.visualization.arrayToDataTable(<?php echo json_encode($data); ?>);

                    var options = {
                        title: 'Top puertos escaneados',
                        legend: {textStyle: {color: '#FFF'}},
                        vAxis: {minValue: 0},
                        backgroundColor: 'transparent',
                        colors: ['#ff6969', '#a7f062', '#ed69ff'],
                        titleTextStyle: {color: '#FFF', fontSize: 18},
                        chartArea: {width: '100%', height: '80%'},
                        hAxis: {textStyle: {color: '#FFF'}},
                    };

                    var chart = new google.visualization.PieChart(document.getElementById('chart_div'));
                    chart.draw(data, options);
                }
            </script>
            <div id="chart_div"></div>
          </div>
        </div>
         <div class="col-4">
          <div class="chart-wrapper">
<<<<<<< HEAD
          <?php
              // Conectamos a la base de datos
              include 'Connection.php';

              // Realizamos una consulta para obtener el número total de entradas en la columna cve_str
              $query = "SELECT COUNT(*) AS total FROM nmapIndividual WHERE cve_str <> ''";
              $result = pg_query($conexion, $query);
              $row = pg_fetch_assoc($result);
              $totalVulnerabilidades = (int) $row['total']; // Convertimos el resultado a entero

              // Creamos un array con los datos para la gráfica
              $data = array(
                  array('Criticidad', 'Número de vulnerabilidades', array('role' => 'style')),
                  array('Total', $totalVulnerabilidades, '#fa4343')
              );

              // Convertimos el array a formato JSON
              $json_data = json_encode($data);

              // Cerramos la conexión a la base de datos
              pg_close($conexion);
              ?>
=======
            <?php
                // Conectamos a la base de datos
                include 'Connection.php';
>>>>>>> 5b39dbde8e057813fa31cd520abe380b95893f15

                // Realizamos una consulta para obtener todas las entradas de la columna               cve_str
                $query = "SELECT cve_str FROM nmapIndividual";
                $result = pg_query($conexion, $query);

                // Inicializamos los contadores
                $criticas = 0;
                $medias = 0;
                $bajas = 0;

                // Iteramos sobre cada entrada
                while ($row = pg_fetch_assoc($result)) {
                  // Extraemos la primera criticidad de la CVE utilizando expresiones re              gulares
                  preg_match('/\d+/', $row['cve_str'], $matches);
                  if (isset($matches[0])) {
                    $criticidad = intval($matches[0]);

                    // Clasificamos cada entrada según su criticidad
                    if ($criticidad >= 8) {
                      $criticas++;
                    } elseif ($criticidad >= 6) {
                      $medias++;
                    } else {
                      $bajas++;
                    }
                  }
                }

                // Creamos un array con los datos para la gráfica
                $data = array(
                  array('Criticidad', 'Número de vulnerabilidades'),
                  array('Críticas', $criticas),
                  array('Medias', $medias),
                  array('Bajas', $bajas)
                );

                // Convertimos el array a formato JSON
                $json_data = json_encode($data);

                // Creamos el gráfico de barras utilizando Google Charts
                ?>
                <div style="width: 80%; float: left">
                  <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
                  <script type="text/javascript">
                    google.charts.load('current', {'packages':['corechart']});
                    google.charts.setOnLoadCallback(drawChart);

                    function drawChart() {
                      var data = google.visualization.arrayToDataTable(<?php echo $json_data; ?>);
                    var options = {
                        title: 'Número de vulnerabilidades según su criticidad',
                        legend: {textStyle: {color: '#FFF'}},
                        vAxis: {minValue: 0},
                        backgroundColor: 'transparent',
                        colors: ['#fa4343', '#fa8955', '#55dffa'],
                        titleTextStyle: {color: '#FFF', fontSize: 18},
                        chartArea: {width: '100%', height: '80%'},
                        hAxis: {textStyle: {color: '#FFF'}},
                      };


                      var chart = new google.visualization.ColumnChart(document.getElementById('chart_div2'));

                      chart.draw(data, options);
                    }
                  </script>

                  <div id="chart_div2" style="width: 100%; height: 400px"></div>
                </div>

                <!-- Mostramos los contadores -->
                <div style="width: 50%; float: left">
                  <p>Vulnerabilidades críticas: <?php echo $criticas; ?></p>
                  <p>Vulnerabilidades medias: <?php echo $medias; ?></p>
                  <p>Vulnerabilidades bajas: <?php echo $bajas; ?></p>
                  </div>
            </div>
        </div>
        <div class="col-4">
          <div class="chat-wrappper">
                  <?php
            // Incluir el archivo de conexión
            include 'Connection.php';

            // Consulta para contar las filas en la tabla nmapIndividual que no existen en lastAnalyze
            $queryAppear = "SELECT COUNT(*) as count FROM nmapIndividual as n WHERE NOT EXISTS (SELECT * FROM lastAnalyze AS L WHERE n.ip = L.ip)";
            $resultAppear = pg_query($conexion, $queryAppear);
            $rowAppear = pg_fetch_assoc($resultAppear);
            $countAppear = intval($rowAppear['count']);

            // Consulta para contar las filas en la tabla lastAnalyze que no existen en nmapIndividual
            $queryLost = "SELECT COUNT(*) as count FROM lastAnalyze as n WHERE NOT EXISTS (SELECT * FROM nmapIndividual AS L WHERE n.ip = L.ip)";
            $resultLost = pg_query($conexion, $queryLost);
            $rowLost = pg_fetch_assoc($resultLost);
            $countLost = intval($rowLost['count']);

            // Consulta para obtener las filas en la tabla nmapIndividual que existen en lastAnalyze
            $queryStay = "SELECT COUNT(*) as count FROM nmapIndividual n JOIN lastAnalyze l ON n.ip = l.ip AND n.port = l.port";
            $resultStay = pg_query($conexion, $queryStay);
            $rowStay = pg_fetch_assoc($resultStay);
            $countStay = intval($rowStay['count']);
            ?>

            <script type="text/javascript">
              google.charts.load("current", {packages:["corechart"]});
              google.charts.setOnLoadCallback(drawChart);
              function drawChart() {
                var data = google.visualization.arrayToDataTable([
                  ["Element", "Count", { role: "style" } ],
                  ["Appear", <?php echo $countAppear; ?>, "#FFF"],
                  ["Lost", <?php echo $countLost; ?>, "silver"],
                  ["Stay", <?php echo $countStay; ?>, "gold"]
                ]);

                var view = new google.visualization.DataView(data);
                view.setColumns([0, 1,
                                { calc: "stringify",
                                  sourceColumn: 1,
                                  type: "string",
                                  role: "annotation" },
                                2]);

                var options = {
                  title: "IP Counts",
                  width: 600,
                  height: 400,
                  backgroundColor: 'transparent',
                  chartArea: {width: '100%', height: '80%'},
                  legend: {textStyle: {color: '#FFF'}},
                  titleTextStyle: {color: '#FFF', fontSize: 18},
                  hAxis: {textStyle: {color: '#FFF'}},
                  vAxis: {
                    textStyle: {color: '#FFF'},
                    minValue: 0,
                    title: 'Count'
                  },
                  annotations: {
                    textStyle: {color: '#FFF'},
                    highContrast: true,
                    stem: {
                      color: 'transparent'
                    }
                  }
                };

                var chart = new google.visualization.BarChart(document.getElementById("barchart_values"));
                chart.draw(view, options);
              }
            </script>

          <div id="barchart_values" style="width: 900px; height: 300px;"></div>
          <div style="display: flex; justify-content: center; margin-top: 100px;">
            <div style="margin-right: 20px;">
              <span style="color: #FFF;">Appear: <?php echo $countAppear; ?></span>
            </div>
            <div style="margin-right: 20px;">
              <span style="color: silver;">Lost: <?php echo $countLost; ?></span>
            </div>
            <div>
              <span style="color: gold;">Stay: <?php echo $countStay; ?></span>
            </div>
          </div>


          </div>
        </div>
      </div>
      <div class="row">
        <div class="jwrapper">
          <br />
          <form
            action="csv.php"
            method="post"
            class="justify-content-start text-center"
          >
            <input type="hidden" name="download_data" value="nmapIndividual" />
            <button type="submit" class="btn btn-primary text-uppercase">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                width="16"
                height="16"
                fill="currentColor"
                class="bi bi-box-arrow-down"
                viewBox="0 0 16 16"
              >
                <path
                  fill-rule="evenodd"
                  d="M3.5 10a.5.5 0 0 1-.5-.5v-8a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 .5.5v8a.5.5 0 0 1-.5.5h-2a.5.5 0 0 0 0 1h2A1.5 1.5 0 0 0 14 9.5v-8A1.5 1.5 0 0 0 12.5 0h-9A1.5 1.5 0 0 0 2 1.5v8A1.5 1.5 0 0 0 3.5 11h2a.5.5 0 0 0 0-1h-2z"
                />
                <path
                  fill-rule="evenodd"
                  d="M7.646 15.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 14.293V5.5a.5.5 0 0 0-1 0v8.793l-2.146-2.147a.5.5 0 0 0-.708.708l3 3z"
                />
              </svg>
              Descargar datos
            </button>
          </form>
        </div>
      </div>
      <div class="row">
        <div class="jwrapper">
          <form action="envioIPs.php" method="post" name="formulario">
            <div class="row">
              <div class="col-md-10">
                <div class="form-group">
                  <input
                    class="form-control validate"
                    type="text"
                    name="ipIndividual"
                    placeholder="Introduce IP o rangos de IPs. p.e 192.168.0.1 o ehu.es"
                  />
                </div>
              </div>
              <div class="col-md-10">
                <div class="form-group">
                  <div class="input-group-append">
                    <button
                      class="btn btn-primary text-uppercase me-2"
                      type="submit"
                    >
                      Enviar
                    </button>
                    <a
                      href="#eliminarModal"
                      class="btn btn-danger text-uppercase modal-trigger"
                      data-bs-toggle="modal"
                    >
                      Eliminar
                    </a>
                  </div>
                </div>
              </div>
            </div>
          </form>
        </div>  
        <div
          class="modal fade"
          id="eliminarModal"
          tabindex="-1"
          aria-labelledby="eliminarModalLabel"
          aria-hidden="true"
        >
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="eliminarModalLabel">
                  Eliminar datos
                </h5>
                <button
                  type="button"
                  class="btn-close"
                  data-bs-dismiss="modal"
                  aria-label="Close"
                ></button>
              </div>
              <div class="modal-body">
                <p>
                  ¿Está seguro de que desea eliminar todas las IPs introducidas
                  para escanear?
                </p>
              </div>
              <div class="modal-footer">
                <form action="envioIPs.php" method="post" name="eliminarForm">
                  <input type="hidden" name="eliminar" value="true" />
                  <button
                    type="button"
                    class="btn btn-secondary"
                    data-bs-dismiss="modal"
                  >
                    Cancelar
                  </button>
                  <button type="submit" class="btn btn-danger">Eliminar</button>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="jwrapper">
          <?php  
                $query = "SELECT * FROM inspectIndividual ORDER BY idIpIndividual DESC LIMIT 1;";
                $result = pg_query($conexion, $query);
                $arr = pg_fetch_all($result);?>
          <table class="table">
            <thead>
              <tr>
                <th>IPs introducidas</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($arr as $array): ?>
              <tr>
                <td><?php echo $array['ip']; ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
      <div class="row">
        <div class="jwrapper">
          <form
            action="insert_button.php"
            method="post"
            class="justify-content-start text-center"
          >
            <div class="btn-group">
              <button
                type="submit"
                class="btn btn-secondary"
                name="selection"
                value="2Weeks"
              >
                2 días
              </button>
              <button
                type="submit"
                class="btn btn-secondary"
                name="selection"
                value="monthly"
              >
                Semanal
              </button>
              <button
                type="submit"
                class="btn btn-secondary"
                name="selection"
                value="now"
              >
                Ahora
              </button>
            </div>
          </form>
        </div>
      </div>
      <div class="row">
        <div class="jwrapper">
          <p class="text-white mb-0 px-4 small">
            Botón seleccionado: <span id="timer"></span>
            <?php
                        $query = "SELECT selection FROM buttons ORDER BY id DESC LIMIT 1";
                        $result = pg_query($conexion, $query) or die('Query failed: ' . pg_last_error());
                        // Obtenemos los resultados
                        $row = pg_fetch_array($result, null, PGSQL_ASSOC);
                        // Mostramos el resultado
                        $selected = $row['selection'];
                        echo $selected;
                        ?>
          </p>
        </div>
      </div>
      <div class="row">
        <div class="jwrapper">
          <?php
          # Query to group the data by IP
          $query = "SELECT ip, hostname, port, protocol, service, version, cve_str FROM nmapIndividual";
          $result = pg_query($conexion, $query);
          
          # Initialize the current IP variable
          $currentIp = '';
          
          # Loop through the results and create a table for each IP
          while ($row = pg_fetch_assoc($result)) {
            # Check if the IP has changed
            if ($row['ip'] !== $currentIp) {
              # If it has, close the previous table (if it exists) and create a new one
              if (!empty($currentIp)) {
          ?>

          <?php
              }
          ?>
            </tbody>
          </table>
          <h2>
            IP:
            <?php echo $row['ip']; ?>
          </h2>
          <table class="table table-active jtable">
            <thead>
              <tr>
                <th>HOSTNAME</th>
                <th>PORT</th>
                <th>PROTOCOL</th>
                <th>SERVICE</th>
                <th>VERSION</th>
                <th>Vulnerabilities</th>
              </tr>
            </thead>
            <tbody>
              <?php
          # Update the current IP variable
          $currentIp = $row['ip'];
        }
        
        $cve_array = explode(';', $row['cve_str']);
        # Get the first vulnerability from the array
        $first_vulnerability = isset($cve_array[0]) ? $cve_array[0] : "";
        # Extract the criticity from the vulnerability string
        preg_match('/\d\.\d/', $first_vulnerability, $matches);
        $criticity = isset($matches[0]) ? $matches[0] : "";
        # Set the color of the button based on the criticity
        if ($criticity >
              8) { $button_color = 'btn-danger'; } elseif ($criticity >= 5) {
              $button_color = 'btn-warning'; } else { $button_color =
              'btn-primary'; } ?>
              <tr>
                <td><?php echo $row['hostname']; ?></td>
                <td><?php echo $row['port']; ?></td>
                <td><?php echo $row['protocol']; ?></td>
                <td><?php echo $row['service']; ?></td>
                <td><?php echo $row['version']; ?></td>
                <td>
                  <?php if (!empty($row['cve_str'])) { ?>
                  <button
                    type="button"
                    class="<?php echo $button_color; ?>"
                    onclick="showModal('<?php echo $row['cve_str']; ?>')"
                  >
                    Ver vulnerabilidades
                    <span class="vulnerability-level">
                      <?php
                      switch ($button_color) {
                        case 'btn-danger':
                          echo ' - Crítica';
                          break;
                        case 'btn-warning':
                          echo ' - Media';
                          break;
                        case 'btn-info':
                          echo ' - Baja';
                          break;
                        default:
                          echo '';
                          break;
                      }
                    ?>
                    </span>
                  </button>
                  <?php } ?>
                </td>
              </tr>

              <!-- Modal -->
              <div
                class="modal fade"
                id="vulnerabilityModal"
                tabindex="-1"
                aria-labelledby="vulnerabilityModalLabel"
                aria-hidden="true"
              >
                <div class="modal-dialog modal-lg">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title" id="vulnerabilityModalLabel">
                        Vulnerabilidades
                      </h5>
                      <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"
                      ></button>
                    </div>
                    <div class="modal-body">
                      <p id="vulnerabilityList"></p>
                    </div>
                  </div>
                </div>
              </div>
              <?php
              }
              # Close the final table
              if (!empty($currentIp)) {
              ?>
            </tbody>
          </table>
          <?php
          }
          ?>
        </div>
      </div>
      <?php include "./resources/footer.php" ?>
      <script>
        function showModal(cveStr) {
          // Split the CVE string by semicolon and create a list of vulnerabilities
          const vulnerabilities = cveStr
            .split(";")
            .map((str) => str.trim())
            .filter((str) => str !== "");

          // Update the modal content
          const vulnerabilityList =
            document.getElementById("vulnerabilityList");
          vulnerabilityList.innerHTML =
            vulnerabilities.length > 0
              ? vulnerabilities.join("<br>")
              : "No hay vulnerabilidades.";

          // Show the modal
          const modal = new bootstrap.Modal(
            document.getElementById("vulnerabilityModal")
          );
          modal.show();
        }
      </script>
       <?php include "./resources/scripts.php" ?>
    </div>
  </body>
</html>
