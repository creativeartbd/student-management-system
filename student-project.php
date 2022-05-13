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
                    <table class="table table-striped">
                      <thead>
                        <tr>
                          <th> User </th>
                          <th> Full Name</th>
                          <th> Email Address</th>
                          <th> Project </th>
                          <th> Edited </th>
                          <th> Action </th>
                        </tr>
                      </thead>
                      <tbody>
                          <?php
                          $get_all_projects = "SELECT r.fname, r.lname, r.email, r.profile_pic, p.project_file, p.edited_count, p.username FROM sms_projects AS p LEFT JOIN sms_registration AS r ON r.username = p.username ";
                          $get_all_projects_query = mysqli_query( $mysqli, $get_all_projects ); 

                          while( $get_all_projects_results = mysqli_fetch_array( $get_all_projects_query ) ) {

                            $fname = $get_all_projects_results['fname'];
                            $lname = $get_all_projects_results['lname'];
                            $email = $get_all_projects_results['email'];
                            $project_file = $get_all_projects_results['project_file'];
                            $project_file = unserialize( $project_file );
                            $profile_pic = $get_all_projects_results['profile_pic'];
                            $edited_count = $get_all_projects_results['edited_count'];
                            $class_name = '';
                            if( $edited_count >= 3 ) {
                              $class_name = 'btn btn-gradient-danger btn-sm';
                            }
                            ?>
                        <tr>
                          <td class="py-1">
                            <img src="assets/images/profile/<?php echo $profile_pic; ?>" alt="image" />
                          </td>
                          <td><?php echo $fname . ' ' . $lname; ?></td>
                          <td><?php echo $email; ?></td>
                          <td><a class="btn btn-gradient-info btn-sm" href="download.php?file=<?php echo urlencode( $project_file ); ?>">Download <i class="mdi mdi-eye menu-icon"></i></a></td>
                          <td><span class="<?php echo $class_name; ?>"><?php echo $edited_count . ' Time/s'; ?><span></td>
                          <td><a href="#" class="btn btn-gradient-success btn-sm" data-bs-toggle="modal" data-bs-target="#exampleModal">Approve Project</a></td>
                        </tr>
                        <?php } ?>
                      </tbody>
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
                  <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  ...
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                  <button type="button" class="btn btn-primary">Save changes</button>
                </div>
              </div>
            </div>
          </div>
          <!-- content-wrapper ends -->
          <?php require_once 'partials/footer.php'; ?>