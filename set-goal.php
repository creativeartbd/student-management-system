<?php require_once 'helper/functions.php'; 
check_user_login_status();
?>
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
                </span> Project Goal
              </h3>
            </div>
            <div class="row">
              <div class="col-md-6 grid-margin stretch-card">
                <div class="card">
                  <div class="card-body">
                    <?php
                    $username = htmlspecialchars( $_GET['username'] );
                    $st_id = (int) htmlspecialchars($_GET['st_id'] );
                    $st_type = (int) $_SESSION['login_type'];
                    $result = select('sms_goal', ['*'], "goal_to='$st_id'"); 
                    ?>
                    <h4 class="card-title">Set the project goal</h4>
                    <form action="" method="POST" id="form" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="">Write the project goal title</label>
                            <textarea  class="form-control form-control-lg" placeholder="Write your project goal" name="goal_title" id="" cols="30" rows="10"></textarea>
                        </div>
                        <div class="form-group"><div class="result"></div></div>
                        <div class="mt-3">
                            <input type="hidden" name="st_id" value=<?php echo $st_id; ?>>
                            <input type="hidden" name="form" value="setgoal">
                            <input type="submit" value="Add Project Goal" class="btn btn-block btn-gradient-primary btn-lg font-weight-medium auth-form-btn ajax-btn">
                        </div>
                    </form>
                  </div>
                </div>
              </div>
              <div class="col-md-6 grid-margin stretch-card">
                <div class="card">
                  <div class="card-body">
                    <h4 class="card-title">Al Goal </h4>
                    <table class="table table-bordered">
                      <tr class="table-success">
                        <th>Goal Title</th>
                      </tr>
                      <tr>
                        <td><?php echo $result['goal_title']; ?></td>
                      </tr>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <?php require_once 'partials/footer.php'; ?>