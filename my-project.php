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
            <div class="col-md-8 col-sm-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <?php
                        $username = $_SESSION['username'];
                        $st_type = $_SESSION['login_type'];

                        $result = select('sms_projects', ['*'], "username='$username' ");
                        $project_title = $time_level = $project_description = $project_file = $uploaded_time = $is_approved = $edited_count = '';
                        $edited_count = 0;
                        
                        if( $result ) {
                          $project_title = $result['project_title'];
                          $project_description = $result['project_description'];
                          $project_file = $result['project_file'];
                          $project_file = unserialize( $project_file );
                          $uploaded_time = $result['uploaded_time'];
                          $edited_count = $result['edited_count'];
                          $is_approved = $result['is_approved'];
                          $group_members = unserialize($result['group_members']);
                          $time_left =  3 - $edited_count;
                          if( $time_left == 1 ) {
                            $time_level = ' time';
                          } else {
                            $time_level = ' times';
                          }
                        ?>
                        <?php 
                        
                        ?>
                        <h4 class="card-title">Update your project.</h4>
                        <form class="pt-3" id="form" method="POST" action="" enctype="multipart/form-data">
                            <div class="form-group">
                                <input type="text" value="<?php echo $project_title; ?>" class="form-control form-control-lg" placeholder="Project Title" name="ptitle">
                            </div>
                            <div class="form-group">
                                <textarea name="pdes" id="" cols="30" rows="5" class="form-conrol form-control-lg textarea" placeholder="Project Description"><?php echo $project_description; ?></textarea>
                            </div>
                            <div class="form-group">
                                <label>Your uploaded project file</label>
                                <?php
                                echo '<p><a href="download.php?file=' . urlencode($project_file) . '">Download Your Project</a></p>';
                                ?>
                                                        </div>
                            <div class="form-group">
                                <label>Upload/Change Project File</label>
                                <input type="file" class="form-control form-control-lg" name="pfile">
                            </div>
                            <div class="form-group group-member">
                              <label><b>Choose group member</b></label>
                                <?php 
                                $get_all_student = mysqli_query( $mysqli, "SELECT fname, lname, st_id FROM sms_registration" );
                                
                                while( $get_all_student_result = mysqli_fetch_array( $get_all_student, MYSQLI_ASSOC ) ) {
                                  $fname = $get_all_student_result['fname'];
                                  $lname = $get_all_student_result['lname'];
                                  $st_id = $get_all_student_result['st_id'];
                                  $checked = '';
                                  if( in_array( $st_id, $group_members ) ) {
                                    $checked = 'checked';
                                  }
                                  echo '<div class="form-check form-check-success">';
                                    echo '<label class="form-check-label">';
                                      echo "<input type='checkbox' $checked class='form-check-input' name='group_members[]' value='$st_id'> ".ucfirst( $fname ) . ' ' . ucfirst( $lname );
                                    echo '<i class="input-helper"></i></label>';
                                  echo '</div>';
                                }
                                ?>
                            </div>
                            <div class="form-group"><div class="result"></div></div>
                            <div class="mt-3">
                                <input type="hidden" name="form" value="updateproject">
                                <input type="submit" value="Update Project" class="btn btn-block btn-gradient-primary btn-lg font-weight-medium auth-form-btn ajax-btn">
                            </div>
                        </form>
                        
                        <?php
                        } else {
                          echo "<div class='alert alert-warning'>Seems like you didn't upload your project. Please go to Submit Project page and then submit.</div>";
                        }
                        ?>
                    </div>
                </div>
              </div>
          </div>
        </div>
        <!-- content-wrapper ends -->
        <?php require_once 'partials/footer.php'; ?>