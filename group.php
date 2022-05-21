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
            </span>All of your group
        </h3>
    </div>
    <div class="row">
        <?php if( isset( $_SESSION['login_type'] ) && $_SESSION['login_type'] == 1 ) : ?>
        <div class="col-md-6 stretch-card grid-margin">
            <div class="card bg-gradient-default card-img-holder p-3">
                <div class="card-body">
                    <table class="table">
                        <tr>
                            <th>S.l</th>
                            <th>Group Name</th>
                            <th>Action</th>
                        </tr>
                        <?php 
                        $st_id = (int) $_SESSION['st_id'];
                        $get_all_groups = mysqli_query( $mysqli, "SELECT * FROM sms_group WHERE st_id = '$st_id' ");
                        $count = 1;
                        while ( $result_all_groups = mysqli_fetch_array( $get_all_groups ) ) {
                            $g_name = $result_all_groups['g_name'];
                            $g_id = $result_all_groups['g_id'];
                            $created = $result_all_groups['created'];
                            echo "<tr>";
                                echo "<td>$count</td>";
                                echo "<td>$g_name</td>";
                                echo "<td>
                                    <a class='btn btn-gradient-primary btn-sm' href='edit-group.php?g_id=$g_id'>Edit</a>
                                    <a class='btn btn-gradient-success btn-sm' href='index.php'>Create New</a>
                                    </td>";
                            echo "</tr>";
                            $count++;
                        }
                        ?>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
<!-- content-wrapper ends -->
<?php require_once 'partials/footer.php'; ?>