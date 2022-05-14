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
              </span> Profile
            </h3>
          </div>
          <div class="row">
            <div class="col-md-6 grid-margin stretch-card">
				<div class="card">
					<div class="card-body">
						<?php
						$username = $_SESSION['username'];
						$st_type = $_SESSION['login_type'];
						$result = select('sms_registration', ['*'], "username='$username' AND st_type = '$st_type'"); 
						$fname = $result['fname'];
						$lname = $result['lname'];
						$email = $result['email'];
						$username = $result['username'];
						
						// echo '<pre>';
						// 	 print_r( $result );
						// echo '</pre>';
						
						?>
						<h4 class="card-title">Update your profile.</h4>
						<form class="pt-3" id="form" method="POST" action="" enctype="multipart/form-data">
							<div class="form-group">
								<input type="text" value="<?php echo $fname; ?>" class="form-control form-control-lg" placeholder="First Name" name="fname">
							</div>
							<div class="form-group">
								<input type="text" value="<?php echo $lname; ?>" class="form-control form-control-lg" placeholder="Last Name" name="lname">
							</div>
							<div class="form-group">
								<input type="text" value="<?php echo $username; ?>" class="form-control form-control-lg" placeholder="Username" readonly>
							</div>
							<div class="form-group">
								<input type="password" class="form-control form-control-lg" placeholder="Password" name="password">
							</div>
							<div class="form-group">
								<input type="email" value="<?php echo $email; ?>" class="form-control form-control-lg" placeholder="Email Address" name="email">
							</div>
							<div class="form-group">
								<label>Upload/Change Profile Picture</label>
								<input type="file" class="form-control form-control-lg" name="profile_pic">
							</div>
							<div class="form-group"><div class="result"></div></div>
							<div class="mt-3">
								<input type="hidden" name="form" value="profile">
								<input type="submit" value="Update Profile" class="btn btn-block btn-gradient-primary btn-lg font-weight-medium auth-form-btn ajax-btn">
							</div>
						</form>
					</div>
				</div>
            </div>
          </div>
        </div>
        <!-- content-wrapper ends -->
        <?php require_once 'partials/footer.php'; ?>