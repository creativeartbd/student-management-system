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
                        </span> Submit Project
                    </h3>
                </div>
                <div class="row">
                    <div class="col-md-8 col-sm-12 grid-margin stretch-card">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Submit your project.</h4>
                                <!-- <p class="card-description">Please keep in mind that you can edit your uploaded project only 3 times max.</p> -->
                                <form class="pt-3" id="form" method="POST" action="" enctype="multipart/form-data">
                                    <div class="form-group">
                                        <input type="text" class="form-control form-control-lg" placeholder="Proposed Title of the Thesis/Project" name="ptitle">
                                    </div>
                                    <div class="form-group">
                                        <input type="number" class="form-control form-control-lg" placeholder="Group Leader's Contact Number" name="gnumber">
                                    </div>
                                    <div class="form-group">
                                        <input type="email" class="form-control form-control-lg" placeholder="Group Leader's E-mail Address" name="gemail">
                                    </div>
                                    <div class="form-group">
                                        <textarea name="pdes" id="" cols="30" rows="5" class="form-conrol form-control-lg textarea" placeholder="Proposal Details"></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label>Upload/Change Project File</label>
                                        <input type="file" class="form-control form-control-lg" name="pfile">
                                    </div>
                                    <div class="form-group group-member">
                                        <label><b>Choose a group</b></label>
                                        <?php 
                                        $st_id = (int) $_SESSION['st_id'];
                                        $get_groups = mysqli_query( $mysqli, "SELECT g_name, g_id FROM sms_group WHERE st_id != '$st_id' " );

                                        while( $group_result = mysqli_fetch_array( $get_groups, MYSQLI_ASSOC ) ) {
                                            $g_name = $group_result['g_name'];
                                            $g_id = $group_result['g_id'];

                                            echo '<div class="form-check form-check-success">';
                                                echo '<label class="form-check-label">';
                                                echo "<input type='radio' class='form-check-input' name='group_name' value='$g_id'> ".ucfirst( $g_name );
                                                echo '<i class="input-helper"></i></label>';
                                            echo '</div>';
                                        }
                                        ?>
                                    </div>
                                    <div class="form-group"><div class="result"></div></div>
                                    <div class="mt-3">
                                        <input type="hidden" name="form" value="submitproject">
                                        <input type="submit" value="Submit Project" class="btn btn-block btn-gradient-primary btn-lg font-weight-medium auth-form-btn ajax-btn">
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <!-- content-wrapper ends -->
        <?php require_once 'partials/footer.php'; ?>