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
						$st_id = (int) $_SESSION['st_id'];
						$st_type = $_SESSION['login_type'];
						$result = select('sms_registration', ['*'], "st_id = '$st_id' AND st_type = '$st_type'"); 

						$email = $result['email'];
						$mobile = $result['mobile'];
						$program = $result['program'];
						$session = $result['session'];
						$name = $result['name'];
						$id = $result['id'];
						$shift = $result['shift'];
						$username = $result['username'];
						$username = $result['username'];
						?>

						<h4 class="card-title">Update your profile.</h4><form class="pt-3" id="form" method="POST" action="">
						<form class="pt-3" id="form" method="POST" action="" enctype="multipart/form-data">
							<div class="form-group">
								<input type="email" value="<?php echo $email; ?>"" class="form-control form-control-lg" placeholder="Email Address" name="email">
							</div>
							<div class="form-group">
								<input type="number" value="<?php echo $mobile; ?>" class="form-control form-control-lg" placeholder="Mobile Number" name="mobile">
							</div>
							<div class="form-group">
								<input type="text" value="<?php echo $program; ?>" class="form-control form-control-lg" placeholder="Program" name="program">
							</div>
							<div class="form-group">
								<input type="text" value="<?php echo $session; ?>" class="form-control form-control-lg" placeholder="Session" name="session">
							</div>
							<div class="form-group">
								<input type="text" value="<?php echo $name; ?>" class="form-control form-control-lg" placeholder="Your name" name="name">
							</div>
							<div class="form-group">
								<input type="text" value="<?php echo $id; ?>" class="form-control form-control-lg" placeholder="Your ID" name="id">
							</div>
							<div class="form-group">
								<input type="text" value="<?php echo $shift; ?>" class="form-control form-control-lg" placeholder="Your shift" name="shift">
							</div>
							<div class="form-group">
								<input type="text" value="<?php echo $username; ?>" class="form-control form-control-lg" placeholder="Username" disabled>
							</div>
							<div class="form-group">
								<input type="password" class="form-control form-control-lg" placeholder="Update Password" name="password">
							</div>
							<div class="form-group">
								<div class="result"></div>
								<div class="form-group">
									<input type="hidden" name="form" value="profile">
									<input type="submit" value="Update Profile" class="btn btn-block btn-gradient-primary btn-lg font-weight-medium auth-form-btn ajax-btn">
								</div>
							</div>
						</form>					
					</div>
				</div>
            </div>
        </div>
    </div>
<!-- content-wrapper ends -->
<?php require_once 'partials/footer.php'; ?>