<?php require_once 'partials/header.php';?>
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
                			</span> Registration
              			</h3>
            		</div>
					<div class="row">
						<div class="col-md-8 col-sm-12 grid-margin stretch-card">
							<div class="card">
								<div class="card-body">
									<h4 class="card-title">Enter fill up the form below to a reister a new account.</h4>
									<form class="pt-3" id="form" method="POST" action="">
										<div class="form-group">
											<label for="">Please choose registration type.</label>
											<div class="form-check form-check-inline">
												<label class="form-check-label">
													<input type="radio" class="form-check-input registration_type" name="registration_type" id="registration_type" value="1"> Student <i class="input-helper"></i>
												</label>
											</div>
											<div class="form-check form-check-inline">
												<label class="form-check-label">
													<input type="radio" class="form-check-input registration_type" name="registration_type" id="registration_type" value="2"> Teacher <i class="input-helper"></i>
												</label>
											</div>
										</div>
										<div class="form-group">
											<input type="email" class="form-control form-control-lg" placeholder="Email Address" name="email">
										</div>
										<div class="form-group">
											<input type="number" class="form-control form-control-lg" placeholder="Mobile Number" name="mobile">
										</div>
										<div class="hide-me">
											<div class="form-group">
												<input type="text" class="form-control form-control-lg" placeholder="Program" name="program">
											</div>
											<div class="form-group">
												<input type="text" class="form-control form-control-lg" placeholder="Session" name="session">
											</div>
											<div class="form-group">
												<input type="text" class="form-control form-control-lg" placeholder="Your shift" name="shift">
											</div>
										</div>
										<div class="form-group">
											<input type="text" class="form-control form-control-lg" placeholder="Your ID" name="id">
										</div>
										<div class="form-group">
											<input type="text" class="form-control form-control-lg" placeholder="Your name" name="name">
										</div>
										<div class="form-group">
											<input type="text" class="form-control form-control-lg" placeholder="Username" name="username">
										</div>
										<div class="form-group">
											<input type="password" class="form-control form-control-lg" placeholder="Password" name="password">
										</div>
										<div class="form-group">
											<div class="result"></div>
												<div class="form-group">
											<input type="hidden" name="form" value="registration">
											<input type="submit" value="Registration" class="btn btn-block btn-gradient-primary btn-lg font-weight-medium auth-form-btn ajax-btn">
										</div>
										<div class="form-group mt-3">
											<p>Already have an account? Please click <a href="login.php" class="text-primary">here</a> to login.</p>
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