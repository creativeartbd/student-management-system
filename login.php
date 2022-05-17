<?php require_once 'helper/functions.php'; ?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Student Project Management System</title>
    <!-- plugins:css -->
    <link rel="stylesheet" href="<?php echo ROOT; ?>assets/vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="<?php echo ROOT; ?>assets/vendors/css/vendor.bundle.base.css">
    <!-- endinject -->
    <!-- Layout styles -->
    <link rel="stylesheet" href="<?php echo ROOT; ?>assets/css/style.css">
    <!-- End layout styles -->
    <link rel="shortcut icon" href="<?php echo ROOT; ?>assets/images/favicon.ico" />
  </head>
  <body>
    <div class="container-scroller">
      <?php require_once 'partials/top-header.php'?>
      <div class="container-fluid page-body-wrapper">
      <?php require_once 'partials/sidebar.php'?>
        <div class="main-panel">
          <div class="content-wrapper">
            <div class="page-header">
              <h3 class="page-title">
                <span class="page-title-icon bg-gradient-primary text-white me-2">
                  <i class="mdi mdi-home"></i>
                </span> Login
              </h3>
            </div>
            <div class="row">
              <div class="col-md-6 col-sm-12 grid-margin stretch-card">
                <div class="card">
                  <div class="card-body">
                    <h4 class="card-title">Enter your username and password to acccess your account!</h4>
                    <form class="pt-3" id="form" method="POST" action="">
                      <div class="form-group">
                        <input type="text" class="form-control form-control-lg" id="exampleInputEmail1" placeholder="Username" name="username">
                      </div>
                      <div class="form-group">
                        <input type="password" class="form-control form-control-lg" id="exampleInputPassword1" placeholder="Password" name="password">
                      </div>
                      <div class="form-group">
                        <div class="form-check">
                          <label class="form-check-label">
                            <input type="radio" class="form-check-input" name="login_type" id="login_type" value="1"> Student <i class="input-helper"></i>
                          </label>
                        </div>
                        <div class="form-check">
                          <label class="form-check-label">
                            <input type="radio" class="form-check-input" name="login_type" id="login_type" value="2"> Teacher <i class="input-helper"></i>
                          </label>
                        </div>
                      </div>
                      <div class="form-group"><div class="result"></div></div>
                      <div class="mt-3">
                        <input type="submit" value="Sign In" class="btn btn-block btn-gradient-primary btn-lg font-weight-medium auth-form-btn">
                      </div>
                      <div class="form-group mt-3">
                      <input type="hidden" name="form" value="login">
                        <p><a href="#" class="auth-link text-black">Forgot password?</a></p>
                        <p>Don't have an account? <a href="registration.php" class="text-primary">Create a New Account</a></p>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- content-wrapper ends -->
          <?php require_once 'partials/footer.php'; ?>