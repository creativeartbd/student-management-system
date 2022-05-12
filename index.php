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
              </span> Dashboard
            </h3>
          </div>
          <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
              <p>Lorem ipsum dolor, sit amet consectetur adipisicing elit. Quos illum ipsum voluptatem, ex magni reprehenderit, quo reiciendis repudiandae enim illo deleniti accusantium veniam, magnam tempore dolores unde cum fugit ea?</p>
            </div>
          </div>
        </div>
        <!-- content-wrapper ends -->
        <?php require_once 'partials/footer.php'; ?>