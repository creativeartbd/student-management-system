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
            </span>Welcome to your dashboard <?php echo ucfirst( $_SESSION['username'] ); ?>
        </h3>
    </div>
    <div class="row">
        <div class="col-md-6 stretch-card grid-margin">
            <div class="card bg-gradient-default card-img-holder p-3">
                <div class="card-body">
                    <?php 
                    $st_id = (int) $_SESSION['st_id'];
                    $get_members = mysqli_query( $mysqli, "SELECT name, st_id, id FROM sms_registration WHERE st_id != '$st_id' ");
                    $found_members = mysqli_num_rows( $get_members );
                    ?>

                    <h4 class="mb-3"><?php if( $found_members > 0 ) echo 'Update Group'; else { echo 'Create a new group'; } ?></h4>
                    <form class="" id="form" method="POST" action="" enctype="multipart/form-data">
                        <div class="form-group">
                        <label for="">Choose Group Member</label>
                            <?php 
                            $ex_members = mysqli_query( $mysqli, "SELECT sg.group_members, sr.name, sr.st_id FROM sms_group AS sg LEFT JOIN sms_registration AS sr ON sg.st_id = sr.st_id WHERE sg.st_id = '$st_id' ");
                            $found_ex_members = mysqli_num_rows( $ex_members );

                            $all_ex_members = [];
                            if( $found_ex_members > 0 ) {
                                $result_ex_members = mysqli_fetch_array( $ex_members, MYSQLI_ASSOC );
                                $all_ex_members = unserialize( $result_ex_members['group_members'] );
                            }

                            
                            if( $found_members > 0 ) : ?>
                                <?php while( $result = mysqli_fetch_array( $get_members, MYSQLI_ASSOC ) ) : 
                                    $name = $result['name'];
                                    $result_st_id = $result['st_id'];
                                    $result_id = $result['id'];
                                    $checked = '';
                                    if( in_array( $result_st_id, $all_ex_members ) ) {
                                        $checked = 'checked';
                                    }
                                    ?>
                                    <div class="form-check">
                                        <label class="form-check-label">
                                        <input type="checkbox" <?php echo $checked; ?> class="form-check-input" name="group_members[]" value="<?php echo $result_st_id; ?>"> <?php echo $name . ' ('. $result_id . ')'; ?> <i class="input-helper"></i></label>
                                    </div>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </div>
                        <div class="form-group">
                            <div class="result"></div>
                            <div class="form-group">
                                <?php if( $found_ex_members > 0 ) : ?>
                                    <input type="hidden" name="form" value="update_group">
                                    <input type="submit" value="Update Group" class="btn btn-block btn-gradient-success btn-lg font-weight-medium auth-form-btn ajax-btn">
                                <?php else : ?>
                                    <input type="hidden" name="form" value="create_group">
                                    <input type="submit" value="Create Group" class="btn btn-block btn-gradient-danger btn-lg font-weight-medium auth-form-btn ajax-btn">
                                <?php endif; ?>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-6 stretch-card grid-margin">
            <div class="card bg-gradient-success card-img-holder text-white">
                <div class="card-body">
                    <img src="assets/images/dashboard/circle.svg" class="card-img-absolute" alt="circle-image">
                    <h4 class="font-weight-normal mb-3">Notification<i class="mdi mdi-bookmark-outline mdi-24px float-right"></i></h4>
                    <h3 class="mb-5">Some notification will goes to here.</h3>
                </div>
            </div>
        </div>
        <div class="col-md-6 stretch-card grid-margin">
            <div class="card bg-gradient-info card-img-holder text-white">
                <div class="card-body">
                    <img src="assets/images/dashboard/circle.svg" class="card-img-absolute" alt="circle-image">
                    <h4 class="font-weight-normal mb-3">Project Progress<i class="mdi mdi-bookmark-outline mdi-24px float-right"></i></h4>
                    <h3 class="mb-5">Some notification will goes to here.</h3>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- content-wrapper ends -->
<?php require_once 'partials/footer.php'; ?>