<?php require_once 'partials/header.php';?>

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
                </span> Registration
              </h3>
            </div>
            <div class="row">
              <div class="col-md-6 col-sm-12 grid-margin stretch-card">
                <div class="card">
                  <div class="card-body">
                    <h4 class="card-title">Enter fill up the form below to a reister a new account.</h4>
                    <form class="pt-3" id="form" method="POST" action="">
                      <div class="form-group">
                        <input type="text" class="form-control form-control-lg" placeholder="First Name" name="fname">
                      </div>
                      <div class="form-group">
                        <input type="text" class="form-control form-control-lg" placeholder="Last Name" name="lname">
                      </div>
                      <div class="form-group">
                        <input type="text" class="form-control form-control-lg" placeholder="Username" name="username">
                      </div>
                      <div class="form-group">
                        <input type="password" class="form-control form-control-lg" placeholder="Password" name="password">
                      </div>
                      <div class="form-group">
                        <input type="email" class="form-control form-control-lg" placeholder="Email Address" name="email">
                      </div>
                      <div class="form-group">
                        <select name="registration_type" class="form-control form-control-lg registration_type">
                            <option value="">--Select Registration Type--</option>
                            <option value="1">As Student</option>
                            <option value="2">As Teacher</option>
                        </select>
                      </div>
                      <div class="hide_me">
                        <div class="form-group">
                          <input type="text" class="form-control form-control-lg" placeholder="Your roll no" name="roll">
                        </div>
                        <div class="form-group">
                          <input type="text" class="form-control form-control-lg" placeholder="Your batch name" name="batch">
                        </div>
                        <div class="form-group">
                          <input type="text" class="form-control form-control-lg" placeholder="Your departnemtn" name="department">
                        </div>
                      </div>
                      <div class="form-group"><div class="result"></div></div>
                      <div class="mt-3">
                        <input type="hidden" name="form" value="registration">
                        <input type="submit" value="Registration" class="btn btn-block btn-gradient-primary btn-lg font-weight-medium auth-form-btn ajax-btn">
                      </div>
                      <div class="form-group mt-3">
                        <p>Already have an account? Please click <a href="login.php" class="text-primary">here</a> to login.</p>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- content-wrapper ends -->
          <?php require_once 'partials/footer.php'; ?>