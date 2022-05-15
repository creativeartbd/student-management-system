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
              </span> Chat
            </h3>
          </div>
          <div class="row">
            <div class="col-md-8 grid-margin stretch-card">
				<div class="card">
					<div class="card-body">
						<h4 class="card-title">Chat with your group members</h4>
						<form class="pt-3" id="chat" method="POST" action="" enctype="multipart/form-data">
                            <div class="form-group">
                                <div class="all-message">
                                    <?php
                                    $st_id = (int) $_SESSION['st_id'];
                                    $get_g_id = mysqli_query( $mysqli, "SELECT g_id FROM sms_registration WHERE st_id = '$st_id' ");
                                    $result_g_id = mysqli_fetch_array( $get_g_id, MYSQLI_ASSOC );
                                    $g_id = (int) $result_g_id['g_id'];

                                    $get_msg =  mysqli_query( $mysqli, "SELECT sc.*, sr.username FROM sms_chat AS sc LEFT JOIN sms_registration AS sr ON sr.st_id = sc.st_id WHERE sc.g_id = '$g_id' ");
                                    while( $result_get_msg = mysqli_fetch_array( $get_msg, MYSQLI_ASSOC ) ) {
                                        $chat_text = $result_get_msg['chat_text'];
                                        $chat_time = $result_get_msg['chat_time'];
                                        $uname = $result_get_msg['username'];
                                        echo "<div class='row mb-2'><div class='col-9'><strong>$uname: </strong>$chat_text </div><div class='col-3 text-end text-small'>$chat_time</div></div>";
                                    }
                                    ?>
                                </div>
                            </div>
							<div class="form-group">
								<textarea name="chat" class="form-control form-control-lg" cols="10" rows="3" id="chat-text" placeholde="Please enter your message"></textarea>
							</div>
							<div class="form-group"><div class="result"></div></div>
							<div class="mt-3">
								<input type="hidden" name="form" value="chat">
								<input type="submit" value="Send" class="btn btn-block btn-gradient-primary btn-lg font-weight-medium auth-form-btn ajax-btn">
							</div>
						</form>
					</div>
				</div>
            </div>
            <div class="col-md-4 grid-margin stretch-card">
				<div class="card">
					<div class="card-body">
                        <h4 class="card-title">Your group members.</h4>
						<table class="table table-bordered">
                            <tr>
                                <th>S.l</th>
                                <th>Full Name</th>
                            </tr>
                            <?php 
                            $st_id = (int) $_SESSION['st_id'];
                            $get_members = mysqli_query( $mysqli, "SELECT sg.*, sr.fname, sr.lname FROM sms_group AS sg LEFT JOIN sms_registration AS sr ON sg.g_id = sr.g_id WHERE sr.st_id = '$st_id' ");
                            $found_members = mysqli_num_rows( $get_members );

                            if( $found_members > 0 ) {
                                $result_members = mysqli_fetch_array( $get_members, MYSQLI_ASSOC );
                                $group_members = unserialize( $result_members[ 'group_members' ] );
                              
                                $count = 1;
                                foreach( $group_members as $key => $member ) {
                                    $get_member_details = mysqli_query( $mysqli, "SELECT fname, lname FROM sms_registration WHERE st_id = '$member' ");
                                    $result_member_details = mysqli_fetch_array( $get_member_details, MYSQLI_ASSOC );
                                    $fname = $result_member_details['fname'];
                                    $lname = $result_member_details['lname'];
                                    echo "<tr>";
                                        echo "<td>$count</td>";
                                        echo "<td>$fname $lname</td>";
                                    echo "</tr>";
                                    $count++;
                                }   
                            }
                            ?>
                        </table>
					</div>
				</div>
            </div>
          </div>
        </div>
        <!-- content-wrapper ends -->
        <?php require_once 'partials/footer.php'; ?>