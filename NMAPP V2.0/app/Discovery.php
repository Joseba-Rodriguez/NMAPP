<?php include "./resources/header.php" ?>
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
                  <th>Timestamp</th>
                </tr>
                '; foreach($arr as $array) { echo'
                <tr>
                  <td>'. $array['ip'].'</td>
                  <td>'. $array['hostname'].'</td>
                  <td>'. $array['port'].'</td>
                  <td>'. $array['protocol'].'</td>
                  <td>'. $array['service'].'</td>
                  <td>'. $array['version'].'</td>
                  <td>'. $array['ts'].'</td>
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
                <th>Timestamp</th>
              </tr>
              '; foreach($arr as $array) { echo'
              <tr>
                <td>'. $array['ip'].'</td>
                <td>'. $array['hostname'].'</td>
                <td>'. $array['port'].'</td>
                <td>'. $array['protocol'].'</td>
                <td>'. $array['service'].'</td>
                <td>'. $array['version'].'</td>
                <td>'. $array['ts'].'</td>
              </tr>
              '; } echo'
            </table>
            '; ?>
          </div>
        </div>

          <?php
                                #Stay:
                                $query = "SELECT n.ip, n.hostname, n.port, n.protocol, n.service, n.version, l.ts
                                FROM nmapIndividual n
                                JOIN lastAnalyze l
                                ON n.ip = l.ip AND n.port = l.port";
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
                <th>Timestamp</th>
              </tr>
              '; foreach($arr as $array) { echo'
              <tr>
                <td>'. $array['ip'].'</td>
                <td>'. $array['hostname'].'</td>
                <td>'. $array['port'].'</td>
                <td>'. $array['protocol'].'</td>
                <td>'. $array['service'].'</td>
                <td>'. $array['version'].'</td>
                <td>'. $array['ts'].'</td>
              </tr>
              '; } echo'
            </table>
            '; ?>
          </div>
        </div>
      </div>
      <?php include "./resources/footer.php" ?>
      <?php include "./resources/scripts.php" ?>
    </div>
  </body>
</html>
