<?php 
require_once 'partials/header.php';
check_user_login_status();
?>
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
              </span> Your Project
            </h3>
          </div>
          <div class="row">
            <div class="col-md-6 col-sm-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                    <h4 class="card-title">Your project goal.</h4>
                        <?php
                        $st_id = $_SESSION['st_id'];
                        $get_goal_query = mysqli_query( $mysqli, "SELECT * FROM sms_goal WHERE goal_to = '$st_id' ");
                        if( mysqli_num_rows( $get_goal_query ) > 0 ) {
                          $get_goal_result = mysqli_fetch_array( $get_goal_query, MYSQLI_ASSOC );
                          $goal_id = $get_goal_result['goal_id'];
                          $goal_title = $get_goal_result['goal_title'];
                          $goal_send = $get_goal_result['goal_send'];
                          ?>
                          <form class="pt-3" id="form" method="POST" action="" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for=""><strong>Question: <?php echo $goal_title; ?></strong></label>
                            </div>
                            <div class="form-group">
                                <textarea name="goal_reply" id="" cols="30" rows="5" class="form-conrol form-control-lg textarea" placeholder="Project Description"></textarea>
                            </div>
                            <div class="form-group">
                              <label>Upload File</label>
                              <input type="file" class="form-control form-control-lg" name="goal_pic">
                            </div>
                            <div class="
                            <div class="form-group"><div class="result"></div></div>
                            <div class="mt-3">
                                <input type="hidden" name="form" value="goalreply">
                                <input type="submit" value="Submit Goal" class="btn btn-block btn-gradient-success btn-lg font-weight-medium auth-form-btn ajax-btn">
                            </div>
                          </form>
                          <?php
                        }
                        ?>
                    </div>
                </div>
            </div>
          </div>
        </div>
        <!-- content-wrapper ends -->
        <?php require_once 'partials/footer.php'; ?>