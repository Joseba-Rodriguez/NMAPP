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
    <link href="css/styles.css" rel="stylesheet" />
  </head>

  <body>
    <div class="container-fluid">
      <div class="row">
        <header>
          <nav class="navbar navbar-expand-lg bg-body-tertiary">
            <a class="navbar-brand" href="index.php">
              <h1 class="tm-site-title mb-0">NMAPP</h1>
            </a>
            <div class="collapse navbar-collapse" id="navbarNav">
              <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                  <a class="nav-link" href="logout.php"
                    ><?php echo $_SESSION['userID']; ?>, <b>Logout</b></a
                  >
                </li>
                <li class="nav-item">
                  <form>
                    <a class="nav-link" href="changePassword.php"
                      ><b>Cambia la contraseña</b></a
                    >
                  </form>
                </li>
                <li class="nav-item">
                  <form>
                    <a class="nav-link" href="register.php"
                      ><b>Registra un nuevo usuario</b></a
                    >
                  </form>
                </li>
              </ul>
            </div>
          </nav>
        </header>
      </div>
      <div class="row">
        <div class="jwrapper">
          <h1 class="offcanvas-title">Último reporte</h1>
        </div>
      </div>
      <div class="row">
        <div class="jwrapper">
          <form
            action="csv.php"
            method="post"
            class="justify-content-start text-center"
          >
            <input type="hidden" name="download_data" value="nmapIndividual" />
            <input
              type="submit"
              class="btn btn-primary text-uppercase"
              value="Descargar datos"
            />
          </form>
        </div>
      </div>
      <div class="row">
        <div class="jwrapper"></div>
        <?php
            include('Connection.php');
            #All the data from the last execution
            $query = "SELECT * FROM nmapIndividual;";
            $result = pg_query($conexion, $query);
            $arr = pg_fetch_all($result);
            ?>
        <table class="table table-active">
          <thead>
            <tr>
              <th>IP</th>
              <th>HOSTNAME</th>
              <th>PORT</th>
              <th>PROTOCOL</th>
              <th>SERVICE</th>
              <th>VERSION</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($arr as $array): ?>
            <tr>
              <td><?php echo $array['ip']; ?></td>
              <td><?php echo $array['hostname']; ?></td>
              <td><?php echo $array['port']; ?></td>
              <td><?php echo $array['protocol']; ?></td>
              <td><?php echo $array['service']; ?></td>
              <td><?php echo $array['version']; ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
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
        <form action="insert_button.php" method="post">
          <div class="btn-group">
            <button
              type="submit"
              class="btn btn-secondary"
              name="selection"
              value="2Weeks"
            >
              2 semanas
            </button>
            <button
              type="submit"
              class="btn btn-secondary"
              name="selection"
              value="monthly"
            >
              Mensual
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
        <div class="media-body">
          <a href="Discovery.php">
            <button class="btn btn-primary btn-block text-uppercase">
              Descubrimientos
            </button>
          </a>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="jwrapper">
        <form action="envioIPs.php" method="post" name="formulario">
          <div class="input-group">
            <input
              class="form-control validate"
              type="text"
              name="ipIndividual"
              placeholder=" Introduce IP o rangos de IPs. p.e 192.168.0.1 o ehu.es"
            />
            <div class="input-group-append">
              <input
                class="btn btn-primary text-uppercase"
                type="submit"
                value="Enviar"
              />
              <input
                class="btn btn-primary text-uppercase"
                type="submit"
                value="Eliminar"
              />
            </div>
          </div>
        </form>
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
      <footer class="fixed-bottom text-center">
        <p>&copy; 2023 My Website</p>
      </footer>
    </div>
    <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"
      integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN"
      crossorigin="anonymous"
    ></script>
  </body>
</html>
