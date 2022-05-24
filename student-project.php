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
            </span> All student project
        </h3>
    </div>
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Check all the students submitted projects.</h4>
                    <div class="row">
                        <?php 
                        $get_all_projects = "SELECT r.st_id, r.id, r.program, r.session, r.name, r.email, r.profile_pic, p.project_title, p.project_description, p.* FROM sms_projects AS p LEFT JOIN sms_registration AS r ON r.username = p.username ";
                    
                        $get_all_projects_query = mysqli_query( $mysqli, $get_all_projects ); 
                        $username = $_SESSION['username'];
                        $st_ses_id = (int) $_SESSION['st_id'];
                        
                        if( 0 == mysqli_num_rows( $get_all_projects_query ) ) {
                            echo "<div class='alert alert-warning'>No data found.</div>";
                        }
                        $count = 0;
                        while( $get_all_projects_results = mysqli_fetch_array( $get_all_projects_query ) ) {
                            $count++;
                            $fname = $get_all_projects_results['name'];
                            $gnumber = $get_all_projects_results['gnumber'];
                            $gemail = $get_all_projects_results['gemail'];
                            $st_id = $get_all_projects_results['st_id'];
                            $email = $get_all_projects_results['email'];
                            $is_approved = $get_all_projects_results['is_approved'];
                            $project_file = $get_all_projects_results['project_file'];
                            $project_file = unserialize( $project_file );
                            $profile_pic = $get_all_projects_results['profile_pic'];
                            $p_username = $get_all_projects_results['username'];
                            $roll = $get_all_projects_results['id'];
                            $department = $get_all_projects_results['program'];
                            $batch = $get_all_projects_results['session'];
                            $approved_by = $get_all_projects_results['approved_by'];
                            $supervisor = $get_all_projects_results['supervisor'];
                            $project_title = $get_all_projects_results['project_title'];
                            $project_description = $get_all_projects_results['project_description'];
                            $g_id = $get_all_projects_results['g_id'];
                            $p_id = $get_all_projects_results['p_id'];
                            $class_name = '';
                            
                            if( $is_approved == 0 ) {
                                $status = "<span class='btn btn-gradient-danger btn-sm'>Not Approve</span>";
                            } elseif( $is_approved == 1 ) {
                                $status = "<span class='btn btn-gradient-success btn-sm'>Approve</span>";
                            }
                            ?>
                        <div class="col-md-12">
                            <div class="card border">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <?php echo $fname; ?>
                                    </h5>
                                    <h6 class="card-subtitle mb-2 text-muted"><?php echo $department; ?>, <?php echo $batch; ?>.</h6>
                                    <hr>
                                    <p><strong>Group Members: </strong></p>
                                    <?php 
                                    $get_members = mysqli_query( $mysqli, "SELECT group_members FROM sms_group WHERE g_id = '$g_id' ");
                                    if( mysqli_num_rows( $get_members) > 0 ) {
                                        $get_reesult = mysqli_fetch_array( $get_members );
                                        $all_members = json_decode( $get_reesult['group_members'] );
                                        echo "<ul class='list-inline'>";
                                        foreach ( $all_members as $member ) {
                                            $get_members_info = mysqli_query( $mysqli, "SELECT name, id FROM sms_registration WHERE st_id = '$member' ");
                                            if( mysqli_num_rows( $get_members_info ) ) {
                                                while ( $get_info_result = mysqli_fetch_array( $get_members_info ) ) {
                                                    $member_name = $get_info_result['name'];
                                                    $member_id = $get_info_result['id'];
                                                    echo "<li class='list-inline-item'>" . $member_name . ' ( ' . $member_id . ' ),' . "</li>";
                                                }
                                            }
                                        }
                                        echo "</ul>";
                                    }
                                    ?>
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <p><b>Proposed Title: <br/> </b><?php echo $project_title; ?></p>
                                        </div>
                                        <div class="col-sm-2">
                                            <p><b>Group leader number: <br/> </b><?php echo $gnumber; ?></p>
                                        </div>
                                        <div class="col-sm-3">
                                            <p><b>Group leader email: <br/> </b><?php echo $gemail; ?></p>
                                        </div>
                                        <div class="col-sm-2">
                                            <p><b>Download: </b><br/> <?php echo '<p><a href="download.php?file=' . urlencode($project_file) . '">Download File</a></p>'; ?></p>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            <p><b>Proposed Details: </b> <br/> <?php echo $project_description; ?></p>
                                        </div>
                                    </div>

                                    <?php if( 0 == $is_approved ) : ?>
                                        <a data-username="<?php echo $p_username;  ?>" href="#" class="btn btn-gradient-danger btn-sm approve_project" data-bs-toggle="modal" data-bs-target="#exampleModal">Approve Project</a>
                                    <?php else : ?>
                                        <span class="btn btn-gradient-success btn-sm">Project Approved</span>
                                    <?php endif; ?>

                                    <?php if( $is_approved == 1 ) : ?>
                                            <a class="btn btn-gradient-primary btn-sm" href="set-goal.php?p_id=<?php echo $p_id; ?>&username=<?php echo $p_username; ?>&st_id=<?php echo $st_id; ?>">Set Goal</a>
                                        <?php if( $approved_by == $username || $supervisor == $st_ses_id ) : ?>
                                            <a class="btn btn-gradient-info btn-sm" href="edit-student-project.php?p_id=<?php echo $p_id; ?>&username=<?php echo $p_username; ?>">Edit</a>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Project Approval</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="result"></div>
                <h4 class="text text-danger">
                Are you sure to approve this project?
                <h4>
                <?php
                    $get_teacer = mysqli_query( $mysqli, "SELECT name, id, st_id FROM sms_registration WHERE st_type = 2 AND st_id != '$st_ses_id' ");
                    
                    ?>
                <form class="pt-3" id="form" method="POST" action="">
                    <div class="form-group">
                        <select name="supervisor" class="form-control form-control-lg">
                            <option value="">--Select Superviosr</option>
                            <?php
                                while( $result_teacher = mysqli_fetch_array( $get_teacer, MYSQLI_ASSOC ) ) {
                                    $name = $result_teacher['name'];
                                    $id = $result_teacher['id'];
                                    $teacher_id = $result_teacher['st_id'];
                                    echo "<option value='$teacher_id'>$name ($id)</option>";
                                }
                                ?>
                        </select>
                    </div>
                    <input type="hidden" name="form" value="approve_project">
                    <input type="hidden" class="set_username" name="student_username">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                    <input type="submit" name="submit" value="Yes, I am Sure!" class="btn btn-success ajax-btn">
                </form>
            </div>
        </div>
    </div>
</div>
<!-- content-wrapper ends -->
<?php require_once 'partials/footer.php'; ?>