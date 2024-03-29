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
                <?php
                $username = htmlspecialchars( $_GET['username'] );
                $p_id = (int) htmlspecialchars($_GET['p_id'] );
                $get_st_id = (int) htmlspecialchars($_GET['st_id'] );

                $st_type = (int) $_SESSION['login_type'];
                // didn't answer the goal
                $get_all_goal = mysqli_query( $mysqli, "SELECT sga.goal_file, sga.st_id, sg.* FROM sms_goal AS sg LEFT JOIN sms_goal_answer AS sga ON sg.goal_id = sga.goal_id WHERE sg.goal_to = '$get_st_id' ");
                $found_row = mysqli_num_rows( $get_all_goal );
                $get_username = htmlspecialchars( $_GET['username'] );
                ?>
                <span class="page-title-icon bg-gradient-primary text-white me-2">
                  <i class="mdi mdi-home"></i>
                </span>Set the project goal for: <?php echo ucfirst( $get_username ); ?>
              </h3>
            </div>
            <div class="row">
              <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                  <div class="card-body">
                    
                    <h4 class="card-title">Set the project goal</h4>
                    <form action="" method="POST" id="form" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="">Write the project goal title</label>
                            <textarea  class="form-control form-control-lg" placeholder="Write your project goal" name="goal_title" id="" cols="30" rows="10"></textarea>
                        </div>
                        <div class="form-group"><div class="result"></div></div>
                        <div class="mt-3">
                            <input type="hidden" name="st_id" value=<?php echo $get_st_id; ?>>
                            <input type="hidden" name="form" value="setgoal">
                            <input type="submit" value="Add Project Goal" class="btn btn-block btn-gradient-primary btn-lg font-weight-medium auth-form-btn ajax-btn">
                        </div>
                    </form>
                  </div>
                </div>
              </div>
              <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                  <div class="card-body">
                    <h4 class="card-title">Al Goal </h4>
                    <?php if( 0 ==  $found_row ) : ?>
                      <div class="alert alert-warning">No data found.</div>
                    <?php endif; ?>
                    <table class="table">
                      <tr>
                        <th>Goal Title</th>
                        <th>File</th>
                        <th>Status</th>
                        <th>Action</th>
                      </tr>
                      <?php 
                      while( $goal_result = mysqli_fetch_array( $get_all_goal ) ) :
                        $goal_id = $goal_result['goal_id']; 
                        $goal_title = $goal_result['goal_title'];  
                        $is_goal_end = $goal_result['is_goal_end'];  
                        $is_goal_approve = $goal_result['is_goal_approve'];  
                        $goal_file = unserialize( $goal_result['goal_file'] );  
                        $st_id = (int) $goal_result['st_id'];  
                      ?>
                      <tr>
                        <td><?php echo $goal_result['goal_title']; ?></td>
                        <td>
                            <?php if( !empty( $goal_file ) ) : ?>
                                <a class="btn btn-gradient-info btn-sm" href="download.php?file=<?php echo urlencode( $goal_file ); ?>">Download <i class="mdi mdi-eye menu-icon"></i></a>
                            <?php else : ?>
                                <span class="btn btn-gradient-danger btn-sm">Wating for answer</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if( 1 == $is_goal_end && 1 == $is_goal_approve ) : ?>
                                <span class="btn btn-gradient-success btn-sm">Approved</span>
                            <?php else : ?>
                                <span class="btn btn-gradient-danger btn-sm">Not Approve</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if( 1 == $is_goal_end && 1 == $is_goal_approve ) : ?>
                                <span class="btn btn-gradient-danger btn-sm">N/A</span>
                            <?php else : ?>
                              <a data-goal-id="<?php echo $goal_id; ?>" data-st-id="<?php echo $st_id; ?>" href="#" class="btn btn-gradient-success btn-sm approve_goal" data-bs-toggle="modal" data-bs-target="#exampleModal">Make Approve</a>
                            <?php endif; ?>
                        </td>
                      </tr>
                      <?php endwhile; ?>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>
           <!-- Modal -->
           <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalLabel">Goal Approval</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  <div class="result"></div>
                  <h4 class="text text-danger">Are you sure to approve this goal?<h4>
                </div>
                <div class="modal-footer">
                  <form action="" id="form" method="POST">
                    <input type="hidden" name="form" value="approve_goal">
                    <input type="hidden" class="set_st_id" name="st_id">
                    <input type="hidden" class="set_goal_id" name="goal_id">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                    <input type="submit" name="submit" value="Yes, I am Sure!" class="btn btn-success ajax-btn">
                  </form>
                </div>
              </div>
            </div>
          </div>
          <!-- content-wrapper ends -->
          <?php require_once 'partials/footer.php'; ?>