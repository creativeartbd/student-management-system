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
              </span> Update Student Project
            </h3>
          </div>
          <div class="row">
            <div class="col-md-8 col-sm-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <?php
                        $username = htmlspecialchars( $_GET['username'] );
                        $st_id = (int) $_GET['st_id'];
                        $st_type = $_SESSION['login_type'];

                        $result = select('sms_projects', ['*'], "st_id='$st_id' ");
                        $project_title = $time_level = $project_description = $project_file = $uploaded_time = $is_approved = $edited_count = '';
                        $edited_count = 0;
                       
                        $project_title = $result['project_title'];
                        $project_description = $result['project_description'];
                        $project_file = $result['project_file'];
                        $project_file = unserialize( $project_file );
                        $uploaded_time = $result['uploaded_time'];
                        $edited_count = $result['edited_count'];
                        $is_approved = $result['is_approved'];
                        $res_supervisor = $result['supervisor'];
                        $time_left =  3 - $edited_count;

                        if( $time_left == 1 ) {
                          $time_level = ' time';
                        } else {
                          $time_level = ' times';
                        }

                        $disabled = '';
                        if( $res_supervisor == $_SESSION['st_id'] ) {
                          $disabled = 'readonly';
                        }
                        ?>
                        
                        <h4 class="card-title">Update your project.</h4>
                        <form class="pt-3" id="form" method="POST" action="" enctype="multipart/form-data">
                            <div class="form-group">
                                <input type="text" <?php echo $disabled; ?> value="<?php echo $project_title; ?>" class="form-control form-control-lg" placeholder="Project Title" name="ptitle">
                            </div>
                            <div class="form-group">
                                <textarea name="pdes" <?php echo $disabled; ?> id="" cols="30" rows="5" class="form-conrol form-control-lg textarea" placeholder="Project Description"><?php echo $project_description; ?></textarea>
                            </div>
                            <div class="form-group">
                                <label>Uploaded project file</label>
                                <?php
                                if( $res_supervisor == $_SESSION['st_id'] ) {
                                  echo '<p class="text text-danger">You don\'t have permission.</p>';
                                } else {
                                  echo '<p><a href="download.php?file=' . urlencode($project_file) . '">Download Your Project</a></p>';
                                }
                                
                                ?>
                            </div>
                            <div class="form-group">
                                <label>Upload/Change Project File</label>
                                <input  <?php echo $disabled; ?> type="file" class="form-control form-control-lg" name="pfile">
                            </div>
                            <div class="form-group group-member">
                              <label><b>Choose group member</b></label>
                                <?php 
                                $get_all_student = mysqli_query( $mysqli, "SELECT fname, lname, st_id FROM sms_registration WHERE st_type = 1 AND st_id != '$st_id' " );
                                
                                $get_group_members = mysqli_query( $mysqli, "SELECT * FROM sms_group WHERE st_id = '$st_id' ");
                                $found_group_members = mysqli_num_rows( $get_group_members );
                                
                                $group_members = [];
                                $g_id = 0;
                                if( $found_group_members > 0 ) {
                                  $result_group_members = mysqli_fetch_array( $get_group_members, MYSQLI_ASSOC );
                                  $group_members = $result_group_members['group_members'];
                                  if( !empty( $group_members ) ) {
                                    $group_members = unserialize( $group_members );
                                  } else {
                                    $group_members = [];
                                  }
                                  $g_id = (int) $result_group_members['g_id'];
                                }
                          
                                while( $get_all_student_result = mysqli_fetch_array( $get_all_student, MYSQLI_ASSOC ) ) {
                                 
                                  $fname = $get_all_student_result['fname'];
                                  $lname = $get_all_student_result['lname'];
                                  $student_id = $get_all_student_result['st_id'];
                                  $checked = '';
                                  if( in_array( $student_id, $group_members ) ) {
                                    $checked = 'checked';
                                  }

                                  echo '<div class="form-check form-check-success">';
                                    echo '<label class="form-check-label">';
                                      echo "<input type='checkbox' $checked class='form-check-input' name='group_members[]' value='$student_id'> ".ucfirst( $fname ) . ' ' . ucfirst( $lname );
                                    echo '<i class="input-helper"></i></label>';
                                  echo '</div>';
                                }
                                ?>
                            </div>
                            <div class="from-group">
                              <label><b>Choose Supervisor</b></label>
                              <?php
                              $teacher_ses_id = $_SESSION['st_id'];
                              $get_teacher = mysqli_query( $mysqli, "SELECT username, fname, lname, st_id FROM sms_registration WHERE st_type = 2 "); 
                              
                              while( $result_teacher = mysqli_fetch_array( $get_teacher, MYSQLI_ASSOC ) ) {
                                
                                $fname = $result_teacher['fname'];
                                $lname = $result_teacher['lname'];
                                $teacher_id = $result_teacher['st_id'];
                                
                                $checked = '';
                                if( $teacher_id == $res_supervisor ) {
                                  $checked = 'checked';
                                }

                                echo '<div class="form-check form-check-success">';
                                  echo '<label class="form-check-label">';
                                    echo "<input $disabled type='radio' $checked class='form-check-input' name='supervisor' value='$teacher_id'> ".ucfirst( $fname ) . ' ' . ucfirst( $lname );
                                  echo '<i class="input-helper"></i></label>';
                                echo '</div>';
                              }
                              ?>
                            </div>
                            <div class="form-group"><div class="result"></div></div>
                            <div class="mt-3">
                                <input type="hidden" name="g_id" value=<?php echo $g_id; ?>>
                                <input type="hidden" name="st_id" value=<?php echo $st_id; ?>>
                                <input type="hidden" name="login_type" value=<?php echo $st_type; ?>>
                                <input type="hidden" name="username" value=<?php echo $username; ?>>
                                <input type="hidden" name="supervisor" value=<?php echo $res_supervisor; ?>>
                                <input type="hidden" name="form" value="updateproject_by_teacher">
                                <input type="submit" value="Update Project" class="btn btn-block btn-gradient-primary btn-lg font-weight-medium auth-form-btn ajax-btn">
                            </div>
                        </form>
                    </div>
                </div>
              </div>
          </div>
        </div>
        <!-- content-wrapper ends -->
        <?php require_once 'partials/footer.php'; ?>