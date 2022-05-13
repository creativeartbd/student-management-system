<!-- partial:partials/_sidebar.html -->
<nav class="sidebar sidebar-offcanvas mt-2" id="sidebar">
  <ul class="nav">
    <?php if( isset( $_SESSION['username'] ) && isset( $_SESSION['login_type']) ) : ?>
    <li class="nav-item nav-profile">
      <a href="#" class="nav-link">
        <div class="nav-profile-image">
          <?php 
          $username = $_SESSION['username'];
          $st_type = $_SESSION['login_type'];
          $result = select('sms_registration', ['profile_pic'], "username='$username' AND st_type = '$st_type'");
          $profile_pic = $result['profile_pic']; 
          ?>
          <img src="assets/images/profile/<?php echo $profile_pic; ?>" alt="profile">
          <span class="login-status online"></span>
          <!--change to offline or busy as needed-->
        </div>
        <div class="nav-profile-text d-flex flex-column">
          <span class="font-weight-bold mb-2"><?php echo $_SESSION['username']; ?></span>
          <span class="text-secondary text-small">Logged as 
            <?php 
            if( isset( $_SESSION['login_type'] ) ) { 
              $user = $_SESSION['login_type']; 
              if( 2 == $user ) { 
                echo '<strong>Teacher</strong>'; 
              } elseif ( 1 == $user ) { 
                echo '<strong>Student</strong>'; 
              } 
            } 
            ?></span>
        </div>
        <i class="mdi mdi-bookmark-check text-success nav-profile-badge"></i>
      </a>
    </li>
    <?php endif; ?>
    <li class="nav-item">
      <a class="nav-link" href="index.php">
        <span class="menu-title">Home</span>
        <i class="mdi mdi-home menu-icon"></i>
      </a>
    </li>
    <?php if( ! isset( $_SESSION['username'] ) && ! isset( $_SESSION['login_type'] ) ) : ?>
    <li class="nav-item">
      <a class="nav-link" href="registration.php">
        <span class="menu-title">Registration</span>
        <i class="mdi mdi-login-variant menu-icon"></i>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="login.php">
        <span class="menu-title">Login</span>
        <i class="mdi mdi-login menu-icon"></i>
      </a>
    </li>
    <?php endif; ?>
    <li class="nav-item">
      <a class="nav-link" href="profile.php">
        <span class="menu-title">Profile</span>
        <i class="mdi mdi-face-profile menu-icon"></i>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="submit-project.php">
        <span class="menu-title">Submit Project</span>
        <i class="mdi mdi-face-profile menu-icon"></i>
      </a>
    </li>
    <?php if( isset( $_SESSION['username'] ) && isset( $_SESSION['login_type'] ) ) : ?>
    <li class="nav-item">
      <a class="nav-link" href="signout.php">
        <span class="menu-title">Signout</span>
        <i class="mdi mdi-chart-bar menu-icon"></i>
      </a>
    </li>
    <?php endif; ?>
  </ul>
</nav>
<!-- partial -->