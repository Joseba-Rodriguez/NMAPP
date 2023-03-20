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
    <title>NMAPP</title>
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
                      <button
                        class="btn btn-danger text-uppercase"
                        type="submit"
                        name="eliminar"
                        data-bs-toggle="modal"
                        data-bs-target="#eliminarModal"
                      >
                        Eliminar
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </form>
          </div>
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
                  ¿Está seguro de que desea eliminar todos los datos de la base
                  de datos inspectIndividual?
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
          <?php
          # Query to group the data by IP
          $query = "SELECT ip, hostname, port, protocol, service, version FROM nmapNow";
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
          </tbody>
        </table>
          <?php
              }
          ?>
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
              </tr>
            </thead>
            <tbody>
              <?php
              # Update the current IP variable
              $currentIp = $row['ip'];
            }
          ?>
              <tr>
                <td><?php echo $row['hostname']; ?></td>
                <td><?php echo $row['port']; ?></td>
                <td><?php echo $row['protocol']; ?></td>
                <td><?php echo $row['service']; ?></td>
                <td><?php echo $row['version']; ?></td>
              </tr>
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
