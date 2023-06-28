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
              <ul class="navbar-nav ml-auto"></ul>
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
                    <h1 class="tm-block-title mb-4">
                      Welcome to NMAPP, Login
                    </h1>
                  </div>
                </div>
                <div class="row mt-2">
                  <div class="col-12">
                    <form
                      action="loginSession.php"
                      method="post"
                      class="tm-login-form"
                    >
                      <div class="form-group">
                        <label for="username">Username</label>
                        <input
                          name="username"
                          type="text"
                          class="form-control validate"
                          id="username"
                          value=""
                          required
                        />
                      </div>
                      <div class="form-group mt-3">
                        <label for="password">Password</label>
                        <input
                          name="password"
                          type="password"
                          class="form-control validate"
                          id="password"
                          value=""
                          required
                        />
                      </div>
                      <div class="form-group mt-4">
                        <button
                          type="submit"
                          class="btn btn-primary btn-block text-uppercase"
                        >
                          Login
                        </button>
                      </div>
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
    <?php include "./resources/scripts.php" ?>
  </div>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>
</html>
