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
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>NMAPP Now</title>
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css"
      rel="stylesheet"
      integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD"
      crossorigin="anonymous"
    />
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css"
    />
    <link
      rel="stylesheet"
      href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
    />
    <link href="css/styles.css" rel="stylesheet" />
  </head>
  <body>
    <div class="container-flex">
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
                      <a class="nav-link" href="index2.php"
                        ><b>nmapNow</b></a
                      >
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
        <h1>NMAPP Now</h1>
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
        <div class="jwrapper">
          <form action="envioIPs.php" method="post" name="formulario">
            <div class="row">
              <div class="col-md-10">
                <div class="form-group">
                  <input
                    class="form-control validate"
                    type="text"
                    name="ipNow"
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
      </div>
      <div class="row">
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
                  <input type="hidden" name="eliminarNow" value="true" />
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
                $query = "SELECT * FROM inspectNow ORDER BY idIpIndividual DESC LIMIT 1;";
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
            action="insert_now.php"
            method="post"
            class="justify-content-start text-center"
          >
              <button
                type="submit"
                class="btn btn-secondary"
                name="selection"
                value="now"
              >
                Ejecutar
              </button>
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
          $query = "SELECT ip, hostname, port, protocol, service, version, cve_str FROM nmapNow";
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
      <div class="row">
        <div class="jwrapper">
          <footer>
            <div class="container">
              <div class="row">
                <div class="col-lg-12">
                  <h6 class="text-center text-muted">
                    &copy; 2023 NMAPP. Todos los derechos reservados.
                  </h6>
                </div>
              </div>
            </div>
          </footer>
        </div>
      </div>
      <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN"
        crossorigin="anonymous"
      ></script>
      <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
      <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
      <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
      <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    </div>
  </body>
</html>
