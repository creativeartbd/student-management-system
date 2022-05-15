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
              </span> My Project Goal
            </h3>
          </div>
          <div class="row">
            <div class="col-md-12 col-sm-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                    <?php
                    $st_id = $_SESSION['st_id'];
                    $get_goal_query = mysqli_query( $mysqli, "SELECT sg.*, sga.goal_file FROM sms_goal AS sg LEFT JOIN sms_goal_answer AS sga ON sga.goal_id = sg.goal_id WHERE sg.goal_to = '$st_id' ");
                    if( mysqli_num_rows( $get_goal_query ) > 0 ) {
                        echo "<table class='table'>";
                            echo '<tr>';
                                echo '<th>S.l</th>';
                                echo '<th>Goal Title</th>';
                                echo '<th>Date</th>';
                                echo '<th>Action</th>';
                                echo '<th>Status</th>';
                            echo '</tr>';
                            $count = 1;
                        while ( $get_goal_result = mysqli_fetch_array( $get_goal_query, MYSQLI_ASSOC ) ) {
                            $goal_id = $get_goal_result['goal_id'];
                            $goal_title = $get_goal_result['goal_title'];
                            $goal_send = $get_goal_result['goal_send'];
                            $is_goal_approve = $get_goal_result['is_goal_approve'];
                            $goal_file = $get_goal_result['goal_file'];
                            if( !empty( $goal_file ) ) {
                              $is_answered = "<span class='btn btn-gradient-success btn-sm'>Answered</span>";
                            } else {
                              $is_answered = "<span class='btn btn-gradient-danger btn-sm'>Answer</span>";
                            }
                            if( 0 == $is_goal_approve ) {
                              $status = "<span class='btn btn-gradient-danger btn-sm'>No Approved</span>";
                            } else {
                              $status = "<span class='btn btn-gradient-success btn-sm'>Approved</span>";
                            }
                            echo '<tr>';
                                echo "<td>$count</td>";
                                echo "<td>$goal_title</td>";
                                echo "<td>$goal_send</td>";
                                echo "<td><a href='#' data-goal-id='$goal_id' data-bs-toggle='modal' data-bs-target='#exampleModal' class='goal_answer'>$is_answered</a></td>";
                                echo "<td>$status</td>";
                            echo '</tr>';
                            $count++;
                        }
                        echo "</table>";
                    } else {
                        echo "<div class='alert alert-warning'>Currenlty, You don't have any project goal.</div>";
                    }
                    ?>
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
                  <h5 class="modal-title" id="exampleModalLabel">Answer your project goal</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
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
                            <input type="hidden" name="goal_id" value="" class="set_goal_id">
                            <input type="hidden" name="form" value="goalreply">
                            <input type="submit" value="Submit Goal" class="btn btn-block btn-gradient-success btn-lg font-weight-medium auth-form-btn ajax-btn">
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                        </div>
                    </form>
                <?php } ?>
                </div>
              </div>
            </div>
          </div>
        <!-- content-wrapper ends -->
        <?php require_once 'partials/footer.php'; ?>