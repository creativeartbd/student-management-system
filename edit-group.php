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
            </span>Update group members.
        </h3>
    </div>
    <div class="row">
        <?php if( isset( $_SESSION['login_type'] ) && $_SESSION['login_type'] == 1 ) : ?>
        <div class="col-md-6 stretch-card grid-margin">
            <div class="card bg-gradient-default card-img-holder p-3">
                <div class="card-body">
                    <?php 
                    // $query = mysqli_query( $mysqli, "SELECT * FROM sms_group WHERE JSON_SEARCH( group_members, 'one', '33') IS NOT NULL;" );
                    // echo $num = mysqli_num_rows($query);

                    // $ds = mysqli_query("UPDATE sms_grouup SET group_members = CONCAT_WS(' ', group_members, 44) WHERE st_id = 27 ");
                    // echo $num2 = mysqli_num_rows( $query2 );

                    
                    $st_id = (int) $_SESSION['st_id'];
                    $get_members = mysqli_query( $mysqli, "SELECT name, st_id, id FROM sms_registration WHERE st_id != '$st_id' and st_type = 1 ");
                    $found_members = mysqli_num_rows( $get_members );
                    ?>

                    <h4 class="mb-3"><?php if( $found_members > 0 ) echo 'Update Group'; else { echo 'Create a new group'; } ?></h4>
                    <form class="" id="form" method="POST" action="" enctype="multipart/form-data">
                        <div class="form-group">
                        <label for="">Choose Group Member</label>
                            <?php
                            $g_id = (int) $_GET['g_id'];
                            $ex_members = mysqli_query( $mysqli, "SELECT sg.group_members, sr.name, sr.st_id FROM sms_group AS sg LEFT JOIN sms_registration AS sr ON sg.st_id = sr.st_id WHERE sg.st_id = '$st_id' AND sg.g_id = '$g_id' ");
                            $found_ex_members = mysqli_num_rows( $ex_members );

                            $all_ex_members = [];
                            if( $found_ex_members > 0 ) {
                                $result_ex_members = mysqli_fetch_array( $ex_members, MYSQLI_ASSOC );
                                $all_ex_members = json_decode( $result_ex_members['group_members'] );
                            }

                            
                            if( $found_members > 0 && $found_ex_members > 0 ) : ?>
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
                                        <input type="checkbox" <?php echo $checked; ?> class="form-check-input" name="group_members[]" value="<?php echo $result_st_id; ?>"> <?php echo $name . ' ('. $result_id . ')' . $result_st_id; ?> <i class="input-helper"></i></label>
                                    </div>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </div>
                        <div class="form-group">
                            <div class="result"></div>
                            <div class="form-group">
                                <input type="hidden" name="form" value="update_group">
                                <input type="hidden" name="g_id" value="<?php echo $g_id; ?>">
                                <input type="submit" value="Update Group" class="btn btn-block btn-gradient-success btn-lg font-weight-medium auth-form-btn ajax-btn">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
<!-- content-wrapper ends -->
<?php require_once 'partials/footer.php'; ?>