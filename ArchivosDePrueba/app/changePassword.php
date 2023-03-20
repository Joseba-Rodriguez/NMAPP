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
                </ul>
              </div>
            </nav>
          </header>
        </div>
      </div>
        <div class="row">
        <div class="jwrapper">
        <div class="container tm-mt-big tm-mb-big">
        <div class="row">
            <div class="col-12 mx-auto tm-login-col">
                <div class="tm-bg-primary-dark tm-block tm-block-h-auto">
                    <div class="row">
                        <div class="col-12 text-center">
                            <h2 class="tm-block-title mb-4">Change your password</h2>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-12">
                            <form action="changePasswordSession.php" method="post">
                                <input class="form-control validate" type="password" name="password"
                                    placeholder="Nueva contraseña">
                                <input class="btn btn-primary text-uppercase" type="submit" name="reset-password"
                                    pattern="(?=^.{8,12}$)(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?!.*\s).*$"
                                    value="Cambiar contraseña" required>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
