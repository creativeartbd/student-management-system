<?php require_once 'helper/functions.php' ?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?php echo PROJECT_TITLE; ?></title>
    <link rel="stylesheet" href="<?php echo ROOT; ?>assets/vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="<?php echo ROOT; ?>assets/vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="<?php echo ROOT; ?>assets/css/style.css">
    <link rel="shortcut icon" href="<?php echo ROOT; ?>assets/images/favicon.ico" />
  </head>
  <body>
    <div class="container-scroller">
      <div class="container-fluid page-body-wrapper full-page-wrapper">
        <div class="content-wrapper d-flex align-items-center auth">
          <div class="row flex-grow">
            <div class="col-lg-6 mx-auto">
              <div class="auth-form-light text-left p-5">
                <div class="brand-logo">
                  <h2>Login</h2>
                </div>
                <h6 class="font-weight-light">Enter your username and password to acccess your account!</h6>
                <form class="pt-3" id="form" method="POST" action="">
                  <div class="form-group">
                    <input type="text" class="form-control form-control-lg" id="exampleInputEmail1" placeholder="Username" name="username">
                  </div>
                  <div class="form-group">
                    <input type="password" class="form-control form-control-lg" id="exampleInputPassword1" placeholder="Password" name="password">
                  </div>
                  <div class="form-group">
                    <select name="login_type" class="form-control">
                        <option value="">-- Login As --</option>
                        <option value="1">Student</option>
                        <option value="2">Teacher</option>
                    </select>
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
        <!-- content-wrapper ends -->
      </div>
      <!-- page-body-wrapper ends -->
    </div>
    <!-- container-scroller -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="<?php echo ROOT; ?>assets/js/main.js"></script>
  </body>
</html>