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
          <div class="container tm-mt-big tm-mb-big">
            <div class="row">
              <div class="col-12 mx-auto tm-login-col">
                <div class="tm-bg-primary-dark tm-block tm-block-h-auto">
                  <div class="row">
                    <div class="col-12 text-center">
                      <h1 class="tm-block-title mb-4">Change your password</h1>
                    </div>
                  </div>
                  <div class="row mt-2">
                    <div class="col-12">
                      <form action="changePasswordSession.php" method="post">
                        <input
                          class="form-control validate"
                          type="password"
                          name="password"
                          placeholder="Nueva contraseña"
                        />
                        <br />
                        <input
                          class="btn btn-primary text-uppercase"
                          type="submit"
                          name="reset-password"
                          pattern="(?=^.{8,12}$)(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?!.*\s).*$"
                          value="Cambiar contraseña"
                          required
                        />
                      </form>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <?php include "./resources/footer.php" ?>
    </div>
    <?php include "./resources/scripts.php" ?>
  </body>
</html>
