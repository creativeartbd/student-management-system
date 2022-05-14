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
                </span> All student project
              </h3>
            </div>
            <div class="row">
              <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                  <div class="card-body">
                    <h4 class="card-title">Check all the students submitted projects.</h4>
                    <div class="table-responsive">
                      <table class="table table-striped">
                        <thead>
                          <tr>
                            <th> User </th>
                            <th> Full Name</th>
                            <th> Roll/Batch/Department</th>
                            <th> Project </th>
                            <th> Approve </th>
                            <th> Status </th>
                            <th> Action </th>
                          </tr>
                        </thead>
                        <tbody>
                            <?php
                            $get_all_projects = "SELECT r.st_id, r.roll, r.batch, r.department, r.fname, r.lname, r.email, r.profile_pic, p.project_file, p.edited_count, p.username, p.is_approved FROM sms_projects AS p LEFT JOIN sms_registration AS r ON r.username = p.username ";
                            $get_all_projects_query = mysqli_query( $mysqli, $get_all_projects ); 

                            while( $get_all_projects_results = mysqli_fetch_array( $get_all_projects_query ) ) {

                              $fname = $get_all_projects_results['fname'];
                              $st_id = $get_all_projects_results['st_id'];
                              $lname = $get_all_projects_results['lname'];
                              $email = $get_all_projects_results['email'];
                              $is_approved = $get_all_projects_results['is_approved'];
                              $project_file = $get_all_projects_results['project_file'];
                              $project_file = unserialize( $project_file );
                              $profile_pic = $get_all_projects_results['profile_pic'];
                              $edited_count = $get_all_projects_results['edited_count'];
                              $p_username = $get_all_projects_results['username'];
                              $roll = $get_all_projects_results['roll'];
                              $batch = $get_all_projects_results['batch'];
                              $department = $get_all_projects_results['department'];
                              $class_name = '';
                              
                              if( $edited_count >= 3 ) {
                                $class_name = 'btn btn-gradient-danger btn-sm';
                              }

                              if( $is_approved == 0 ) {
                                $status = "<span class='btn btn-gradient-danger btn-sm'>Not Approve</span>";
                              } elseif( $is_approved == 1 ) {
                                $status = "<span class='btn btn-gradient-success btn-sm'>Approve</span>";
                              }
                              ?>
                          <tr>
                            <td class="py-1">
                              <img src="assets/images/profile/<?php echo $profile_pic; ?>" alt="image" />
                            </td>
                            <td><?php echo $fname . ' ' . $lname; ?></td>
                            <td><?php if( !empty( $roll ) && !empty( $batch) && !empty( $department ) ) { echo $roll . ' <br/> '.$batch.' <br/> '.$department; } ?></td>
                            <td><a class="btn btn-gradient-info btn-sm" href="download.php?file=<?php echo urlencode( $project_file ); ?>">Download <i class="mdi mdi-eye menu-icon"></i></a></td>
                            <td><a data-username="<?php echo $p_username;  ?>" href="#" class="btn btn-gradient-success btn-sm approve_project" data-bs-toggle="modal" data-bs-target="#exampleModal">Approve Project</a></td>
                            <td><?php echo $status; ?></td>
                            <td>
                              <?php if( $is_approved == 1 ) : ?>
                                <a class="btn btn-gradient-danger btn-sm" href="set-goal.php?st_id=<?php echo $st_id; ?>&username=<?php echo $p_username; ?>">Set Goal</a>
                              <?php else : ?>
                                <a class="btn btn-gradient-danger btn-sm" href="#">...</a>
                              <?php endif; ?>
                            </td>
                          </tr>
                          <?php } ?>
                        </tbody>
                      </table>
                    </div>
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
                  <h5 class="modal-title" id="exampleModalLabel">Project Approval</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  <div class="result"></div>
                  <h4 class="text text-danger">Are you sure to approve this project?<h4>
                </div>
                <div class="modal-footer">
                  <form action="" id="form" method="POST">
                    <input type="hidden" name="form" value="approve_project">
                    <input type="hidden" class="set_username" name="student_username">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                    <input type="submit" name="submit" value="Yes, I am Sure!" class="btn btn-success ajax-btn">
                  </form>
                </div>
              </div>
            </div>
          </div>
          <!-- content-wrapper ends -->
          <?php require_once 'partials/footer.php'; ?>