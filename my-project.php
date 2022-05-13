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
            <div class="col-md-6 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <?php
                        $username = $_SESSION['username'];
                        $st_type = $_SESSION['login_type'];

                        $result = select('projects', ['*'], "username='$username' "); 
                        $project_title = $result['project_title'];
                        $project_description = $result['project_description'];
                        $project_file = $result['project_file'];
                        $project_file = unserialize( $project_file );
                        $uploaded_time = $result['uploaded_time'];
                        $edited_count = $result['edited_count'];

                        ?>
                        <h4 class="card-title">Update your project.</h4>
                        <p class="card-description">Please keep in mind that you can edit your uploaded project only 3 times max.</p>
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
                            <div class="form-group"><div class="result"></div></div>
                            <div class="mt-3">
                                <input type="hidden" name="form" value="updateproject">
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