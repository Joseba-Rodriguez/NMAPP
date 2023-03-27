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
          <br>
            <?php
                        include('Connection.php');
                        #Appear: data that is in nmapScan but not in lastAnalyze 
                        $query = "SELECT * FROM nmapIndividual as n WHERE NOT EXISTS(SELECT * FROM lastAnalyze AS L WHERE n.ip = L.ip)";
                        $result = pg_query($conexion, $query);
                        $arr = pg_fetch_all($result);
                        echo'
                        <div class="col-12 tm-block-col">
            <div
              class="tm-bg-primary-dark tm-block tm-block-taller tm-block-scroll"
            >
              <h2 class="tm-block-title">Appear</h2>
              <table class="table">
                <tr>
                  <th>ip</th>
                  <th>hostname</th>
                  <th>port</th>
                  <th>protocol</th>
                  <th>service</th>
                  <th>version</th>
                </tr>
                '; foreach($arr as $array) { echo'
                <tr>
                  <td>'. $array['ip'].'</td>
                  <td>'. $array['hostname'].'</td>
                  <td>'. $array['port'].'</td>
                  <td>'. $array['protocol'].'</td>
                  <td>'. $array['service'].'</td>
                  <td>'. $array['version'].'</td>
                </tr>
                '; } echo'
              </table>
              '; ?>

          </div>
        </div>

          <?php
                            #Lost :data that is in LastAnalyze but not in nmapScan 
                            $query = "SELECT * FROM lastanalyze as n WHERE NOT EXISTS(SELECT * FROM nmapIndividual AS L WHERE n.ip = L.ip)";
                            $result = pg_query($conexion, $query);
                            $arr = pg_fetch_all($result);
                            echo'
                            <div class="col-12 tm-block-col">
          <div
            class="tm-bg-primary-dark tm-block tm-block-taller tm-block-scroll"
          >
            <h2 class="tm-block-title">Lost</h2>
            <table class="table">
              <tr>
                <th>ip</th>
                <th>hostname</th>
                <th>port</th>
                <th>protocol</th>
                <th>service</th>
                <th>version</th>
              </tr>
              '; foreach($arr as $array) { echo'
              <tr>
                <td>'. $array['ip'].'</td>
                <td>'. $array['hostname'].'</td>
                <td>'. $array['port'].'</td>
                <td>'. $array['protocol'].'</td>
                <td>'. $array['service'].'</td>
                <td>'. $array['version'].'</td>
              </tr>
              '; } echo'
            </table>
            '; ?>
          </div>
        </div>

          <?php
                                #Stay:
                                $query = "SELECT * FROM nmapIndividual INTERSECT  SELECT * from lastanalyze";
                                $result = pg_query($conexion, $query);
                                $arr = pg_fetch_all($result);
                                echo'<div class="col-12 tm-block-col">
          <div
            class="tm-bg-primary-dark tm-block tm-block-taller tm-block-scroll"
          >
            <h2 class="tm-block-title">Stay</h2>
            <table class="table">
              <tr>
                <th>ip</th>
                <th>hostname</th>
                <th>port</th>
                <th>protocol</th>
                <th>service</th>
                <th>version</th>
              </tr>
              '; foreach($arr as $array) { echo'
              <tr>
                <td>'. $array['ip'].'</td>
                <td>'. $array['hostname'].'</td>
                <td>'. $array['port'].'</td>
                <td>'. $array['protocol'].'</td>
                <td>'. $array['service'].'</td>
                <td>'. $array['version'].'</td>
              </tr>
              '; } echo'
            </table>
            '; ?>
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
