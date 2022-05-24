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
        <?php if( isset( $_SESSION['login_type'] ) && $_SESSION['login_type'] == 1 ) : ?>
        <div class="col-md-6 stretch-card grid-margin">
            <div class="card bg-gradient-default card-img-holder p-3">
                <div class="card-body">
                    <?php 
                    $st_id = (int) $_SESSION['st_id'];
                    $get_members = mysqli_query( $mysqli, "SELECT name, st_id, id FROM sms_registration WHERE st_id != '$st_id' and st_type = 1 ");
                    $found_members = mysqli_num_rows( $get_members );
                    ?>

                    <h4 class="mb-3">Create a new group.</h4>
                    <form class="" id="form" method="POST" action="" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="">Write your group name</label>
                            <input type="text" name="g_name" class="form-control" placeholder="Enter your group name">
                        </div>
                        <div class="form-group">
                        <label for="">Choose Group Member</label>
                            <?php
                            if( $found_members > 0 ) : ?>
                                <?php while( $result = mysqli_fetch_array( $get_members, MYSQLI_ASSOC ) ) : 
                                    $name = $result['name'];
                                    $result_st_id = $result['st_id'];
                                    $result_id = $result['id'];
                                    ?>
                                    <div class="form-check">
                                        <label class="form-check-label">
                                        <input type="checkbox" class="form-check-input" name="group_members[]" value="<?php echo $result_st_id; ?>"> <?php echo $name . ' ('. $result_id . ')'; ?> <i class="input-helper"></i></label>
                                    </div>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </div>
                        <div class="form-group">
                            <div class="result"></div>
                            <div class="form-group">
                                <input type="hidden" name="form" value="create_group">
                                <input type="submit" value="Create Group" class="btn btn-block btn-gradient-danger btn-lg font-weight-medium auth-form-btn ajax-btn">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-6 stretch-card grid-margin">
            <div class="card bg-gradient-default card-img-holder p-3">
                <div class="card-body">
                    <h4 class="mb-3">All Groups.</h4>
                    <table class="table">
                        <tr>
                            <th>S.l</th>
                            <th>Group Name</th>
                            <th>Action</th>
                        </tr>
                        <?php 
                        $get_all_groups = mysqli_query( $mysqli, "SELECT * FROM sms_group WHERE st_id = '$st_id' ");
                        $count = 1;
                        while ( $result_all_groups = mysqli_fetch_array( $get_all_groups ) ) {
                            $g_name = $result_all_groups['g_name'];
                            $g_id = $result_all_groups['g_id'];
                            $created = $result_all_groups['created'];
                            echo "<tr>";
                                echo "<td>$count</td>";
                                echo "<td>$g_name</td>";
                                echo "<td><a class='btn btn-gradient-primary btn-sm' href='edit-group.php?g_id=$g_id'>Update Group</a></td>";
                            echo "</tr>";
                            $count++;
                        }
                        ?>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>
        <div class="col-md-12 stretch-card grid-margin">
            <div class="card bg-gradient-info card-img-holder text-white">
                <div class="card-body">
                    <img src="assets/images/dashboard/circle.svg" class="card-img-absolute" alt="circle-image">
                    <h4 class="font-weight-normal mb-3">Notification<i class="mdi mdi-bookmark-outline mdi-24px float-right"></i></h4>
                    <h3 class="mb-5">Goal Notification</h3>
                    <?php student_notification( $st_id ); ?>
                </div>
            </div>
        </div>
        <div class="col-md-12 stretch-card grid-margin">
            <div class="card bg-gradient-primary card-img-holder text-white">
                <div class="card-body">
                    <img src="assets/images/dashboard/circle.svg" class="card-img-absolute" alt="circle-image">
                    <h4 class="font-weight-normal mb-3">Progress<i class="mdi mdi-bookmark-outline mdi-24px float-right"></i></h4>
                    <h3 class="mb-5">Project Progress</h3>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- content-wrapper ends -->
<?php require_once 'partials/footer.php'; ?>