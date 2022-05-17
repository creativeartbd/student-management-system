<?php
require_once 'functions.php';

if( isset( $_POST['form']) && $_POST['form'] == 'getchat' ) {
    
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
}

// process approve goal 
if( isset( $_POST['form']) && $_POST['form'] == 'chat' ) {
    $chat_text = htmlspecialchars( trim( $_POST['chat'] ) );
    $st_id = (int) $_SESSION['st_id'];
    $username = $_SESSION['username'];

    $get_group_id = mysqli_query( $mysqli, "SELECT g_id FROM sms_registration WHERE  st_id = '$st_id' ");
    $found_groupu_id = mysqli_num_rows( $get_group_id );

    $group_id = '';
    if( $found_groupu_id > 0 ) {
        $result_group_id = mysqli_fetch_array( $get_group_id, MYSQLI_ASSOC );
        $group_id = $result_group_id['g_id'];
    }
   

    // Hold all errors
    $output['message'] = [];
    $output['success'] = false;

    if( isset( $chat_text ) ) {
        if( empty( $chat_text ) ) {
            $output['message'][] = 'Please enter your message.';
        } elseif( empty( $group_id ) ) {
            $output['message'][] = "Seems like you didn't added to any gourp.";
        }

        if( empty( $output['message'] ) ) {
            $output['success'][] = false;
            $insert = mysqli_query( $mysqli, "INSERT INTO sms_chat(chat_text, st_id, g_id ) value ( '$chat_text', '$st_id', '$group_id' ) ");
            if( $insert ) {
                $time = date("Y-m-d H:i:s", time());
                $output['success'][] = true;
                $output['message'][] = "<div class='row mb-2'><div class='col-9'><strong>$username: </strong>$chat_text </div><div class='col-3 text-end text-small'>$time</div></div>";
            } else {
                $output['success'][] = false;
                $output['message'][] = "Opps! Something wen't wrong! Please contact administrator.";
            }
        }
    
        echo json_encode($output);
    }
}

// process approve goal 
if( isset( $_POST['form']) && $_POST['form'] == 'approve_goal' ) {

    $st_id = (int) htmlspecialchars( $_POST['st_id'] );
    $goal_id = (int) htmlspecialchars( $_POST['goal_id'] );

    // Hold all errors
    $output['message'] = [];
    $output['success'] = false;
    $output['reload'] = true;

    if( isset( $st_id) && isset( $goal_id ) ) {
        if( empty( $st_id ) ) {
            $output['message'][] = 'Goal id is missing';
        }
        if( empty( $st_id ) ) {
            $output['message'][] = 'Student id is missing';
        }
    }

    if( empty( $output['message'] ) ) {
        $output['success'][] = false;
        $update_goal = mysqli_query( $mysqli, "UPDATE sms_goal SET is_goal_end = 1, is_goal_approve = 1 WHERE goal_id = '$goal_id' AND goal_to = '$st_id' ");
        if( $update_goal ) {
            $output['success'][] = true;
            $output['message'][] = "Successfully approved the goal.";
        } else {
            $output['success'][] = false;
            $output['message'][] = "Opps! Something wen't wrong! Please contact administrator.";
        }
    }

    echo json_encode($output);

}
// process goal reply 
if( isset( $_POST['form']) && $_POST['form'] == 'goalreply' ) {

    $goal_reply = htmlspecialchars(trim($_POST['goal_reply'] ) );
    $goal_id = (int) htmlspecialchars(trim($_POST['goal_id'] ) );
    $file_name = $file_tmp_name = $file_size = $file_type = $extension = '';
    if( isset( $_FILES['goal_pic']['name'] ) ) {
        $file_name = htmlspecialchars( $_FILES['goal_pic']['name'] );
        $file_tmp_name = htmlspecialchars( $_FILES['goal_pic']['tmp_name'] );
        $file_size = htmlspecialchars( $_FILES['goal_pic']['size'] );
        $file_type = htmlspecialchars( $_FILES['goal_pic']['type'] );

        $allowed_extension = [ 'pdf', 'doc', 'docx' ];
        $explode = explode( '.', $file_name );
        $extension = end( $explode );
    }
    $allowed_file_size = 5000000; // 5 MB file size allowed
    $new_file_name = time().'.'.$extension;
    $st_id = (int) $_SESSION['st_id'];

    // Hold all errors
    $output['message'] = [];
    $output['success'] = false;
    $output['reload'] = true;

    if( isset( $goal_reply ) && isset( $file_name ) ) {
        if( empty( $goal_reply ) && empty( $file_name ) ) {
            $output['message'][] = 'All fields are required.';
        } else {
            // validate project description
            if( empty( $goal_reply ) ) {
                $output['message'][] = 'Goal description is required';
            } elseif( !preg_match('/^[a-zA-Z0-9.!@#*()-_?<>}{[\], \d]+$/', $goal_reply) ) {
                $output['message'][] = 'Goal description should contain only alpha numeric characters.';
            } elseif( strlen( $goal_reply ) > 5000 || strlen( $goal_reply ) < 10 ) {
                $output['message'][] = 'Goal description length should be less than 5000 characters long.';
            }
            // Validate project file
            if( empty( $file_name ) ) {
                $output['message'][] = 'Please upload your project file.';
            } elseif ( ! in_array( $extension, $allowed_extension ) ) {
                $output['message'][] = 'Uploaded file type is not allowed. We are currently allowing ' . implode(', ', $allowed_extension )  .' filetype';
            } elseif( $file_size > $allowed_file_size ) {
                $output['message'][] = 'Your uploaded file size must be less than 5 MB';
            }

            if( empty( $goal_id ) ) {
                $output['message'][] = 'Missing gola id';
            }
            
        }

        if( empty( $output['message'] ) ) {
            $output['success'] = false;

            if( !empty( $file_name ) ) {
                if( move_uploaded_file($file_tmp_name, '../assets/images/projects/'.$new_file_name) ) {
                    if( insert( [ 
                        'goal_reply' => $goal_reply, 
                        'goal_file' => serialize( $new_file_name ), 
                        'st_id' => $st_id, 
                        'goal_id' => $goal_id
                    ], 'sms_goal_answer' ) ) {
                        $output['success'] = true;
                        $output['message'] = "Successfully submited your goal.";
                    } else {
                        $output['success'] = false;
                        $output['message'] = "Opps! Something wen't wrong! Please contact administrator.";
                    }
                } else {
                    $output['success'] = false;
                    $output['message'] = "Opps! Your project file is not uploading! Please contact administrator.";
                } 
            }
        }

        echo json_encode($output);
    }
}


// Process set goal form 
if( isset( $_POST['form']) && $_POST['form'] == 'setgoal' ) {

    $goal_title = htmlspecialchars( $_POST['goal_title'] );
    $goal_to = (int) htmlspecialchars( $_POST['st_id'] );
    $username = $_SESSION['username'];
    $st_id = (int) $_SESSION['st_id'];

    // Hold all errors
    $output['message'] = [];
    $output['success'] = false;

    if( isset( $goal_title ) && isset( $goal_to ) ) {
        if( empty( $goal_title ) && empty( $goal_title ) ) {
            $output['message'][] = 'All fields are required.';
        } else {
            if( empty( $goal_title ) ) {
                $output['message'][] = 'Project goal title is required';
            } elseif( !preg_match('/^[a-zA-Z0-9.!@#*()-_,?{}[\]\\ \d]+$/', $goal_title) ) {
                $output['message'][] = 'Project goal should contain only alpha numeric characters.';
            } elseif( strlen( $goal_title ) < 10 ) {
                $output['message'][] = 'Project goal length should be more than 10 characters long.';
            }

            if( empty( $goal_to ) ) {
                $output['message'][] = 'Student id is missing';
            }
        }

        if( empty( $output['message'] ) ) {
            if( insert( [ 
                'goal_title' => $goal_title, 
                'goal_to' => $goal_to,
                'goal_by' => $username,
            ], 'sms_goal' ) ) {
                $output['success'] = true;
                $output['message'] = "Successfully added a new goal.";
            } else {
                $output['success'] = false;
                $output['message'] = "Opps! Something wen't wrong! Please contact administrator.";
            }
        }
        echo json_encode($output);
    }
}

// Process project approve project 
if( isset( $_POST['form']) && $_POST['form'] == 'approve_project' ) {
    
    $student_username = htmlspecialchars(trim($_POST['student_username']));
    $supervisor = htmlspecialchars(trim($_POST['supervisor']));
    $session_username = $_SESSION['username'];
    $check_username_sql = "SELECT username FROM sms_registration WHERE username = '$student_username' ";
    $check_username_query = mysqli_query( $mysqli, $check_username_sql );
    $found_username = mysqli_num_rows( $check_username_query );

    // Hold all errors
    $output['message'] = [];
    $output['success'] = false;
    $output['reload'] = true;

    if( isset( $student_username ) && isset( $supervisor ) ) {
        if( empty( $student_username ) ) {
            $output['message'][] = 'Student username is empty';
        } elseif( $found_username == 0 ) {
            $output['message'][] = 'Student username is not found';
        } elseif( empty( $supervisor ) ) {
            $output['message'][] = 'Please select an supervisor';
        }

        if( empty( $output['message'] ) ) {
            $approve_sql = "UPDATE sms_projects SET is_approved = 1, approved_by = '$session_username', supervisor = '$supervisor' WHERE username = '$student_username' ";
            $approve_query = mysqli_query( $mysqli, $approve_sql);

            if( $approve_query ) {
                $output['success'][] = true;
                $output['message'][] = "Successfully approved the student project.";
            } else {
                $output['success'][] = false;
                $output['message'][] = "Opps! Something wen't wrong! Please contact administrator.";
            }
        }
        echo json_encode($output);
    }
}

// Process project update form 
if( isset( $_POST['form']) && $_POST['form'] == 'updateproject_by_teacher' ) {
    
    $ptitle = htmlspecialchars( $_POST['ptitle'] );
    $pdes = htmlspecialchars( $_POST['pdes'] );
    $g_id = (int) htmlspecialchars( $_POST['g_id'] );
    $username = htmlspecialchars( trim( $_POST['username'] ) );
    $supervisor = htmlspecialchars( trim( $_POST['supervisor'] ) );
    
    $st_type = htmlspecialchars( $_POST['login_type'] );
    $st_id = (int) $_POST['st_id'];

    $group_members = isset( $_POST['group_members'] ) ? $_POST['group_members'] : '';
    $group_members_arr = [];

    if (!empty( $group_members ) && is_array( $group_members ) ) {
        foreach( $group_members as $key => $value ) {
            $group_members_arr[] = filter_var( $value, FILTER_SANITIZE_STRING );
        }
    }

    $do_validation = true;
    if( $supervisor == $_SESSION['st_id'] ) {
        $do_validation = false;
    }

    $file_name = $file_tmp_name = $file_size = $file_type = $extension = '';

    if( isset( $_FILES['pfile']['name'] ) ) {
        $file_name = htmlspecialchars( $_FILES['pfile']['name'] );
        $file_tmp_name = htmlspecialchars( $_FILES['pfile']['tmp_name'] );
        $file_size = htmlspecialchars( $_FILES['pfile']['size'] );
        $file_type = htmlspecialchars( $_FILES['pfile']['type'] );

        $allowed_extension = [ 'pdf', 'doc', 'docx' ];
        $explode = explode( '.', $file_name );
        $extension = end( $explode );
    }

    $allowed_file_size = 5000000; // 5 MB file size allowed
    $new_file_name = time().'.'.$extension;

    // Hold all errors
    $output['message'] = [];
    $output['success'] = false;

    // get existing members
    $get_ex_members = mysqli_query( $mysqli, "SELECT group_members FROM sms_group WHERE st_id = '$st_id'");
    $result_ex_members = mysqli_fetch_array( $get_ex_members, MYSQLI_ASSOC );
    $ex_members = unserialize( $result_ex_members[ 'group_members' ] );

    if( isset( $ptitle) && isset( $pdes ) && isset( $group_members_arr ) && isset( $supervisor ) ) {
        if( empty( $ptitle ) && empty( $pdes ) && empty( $group_members_arr ) && empty( $supervisor ) ) {
            $output['message'][] = 'All fields is required';
        } else {

            // Only teacher can update not supervisor
            if( $do_validation ) {
                // validate project title
                if( empty( $ptitle ) ) {
                    $output['message'][] = 'Project title is required.';
                } elseif( !preg_match('/^[a-zA-Z0-9.!@#*()-_, \d]+$/', $ptitle) ) {
                    $output['message'][] = 'Project title should contain only alpha numeric characters.';
                } elseif( strlen( $ptitle ) > 255 || strlen( $ptitle ) < 10 ) {
                    $output['message'][] = 'Project title length should be between 10-255 characters long.';
                }
                // validate project description
                if( empty( $pdes ) ) {
                    $output['message'][] = 'Project description is required';
                } elseif( !preg_match('/^[a-zA-Z0-9.!@#*()-_, \d]+$/', $pdes) ) {
                    $output['message'][] = 'Project description should contain only alpha numeric characters.';
                } elseif( strlen( $pdes ) > 5000 ) {
                    $output['message'][] = 'Project description length should be less than 5000 characters long.';
                }
                // Validate project file
                if( ! empty( $file_name) ) {
                    if ( ! in_array( $extension, $allowed_extension ) ) {
                        $output['message'][] = 'Uploaded file type is not allowed. We are currently allowing ' . implode(', ', $allowed_extension )  .' filetype';
                    } elseif( $file_size > $allowed_file_size ) {
                        $output['message'][] = 'Your uploaded file size must be less than 5 MB';
                    }
                }
            }
            
            // validate group members
            if( empty( $group_members ) ) {
                $output['message'][] = 'Please choose your group members. You can choose upto 10 members.';
            } elseif( !preg_grep('/^[0-9\d]+$/', $group_members_arr) ) {
                $output['message'][] = 'Invalid group member is given.';
            } 

            if( $do_validation ) {
                // validate supervisor
                if( empty( $supervisor ) ) {
                    $output['message'][] = 'Supervisor is required.';
                }
            }
            
        }

        if( empty( $output['message'] ) ) {

            // On teacher can update

            $group_members_arr[] = $st_id;
            $group_members = serialize( $group_members_arr );

            if( $do_validation ) {
                
                $updating_data = [ 
                    'project_title' => $ptitle, 
                    'project_description' => $pdes, 
                    'username' => $username, 
                    'supervisor' => $supervisor
                ];
        
                if( ! empty( $file_name ) ) { 
                    if( move_uploaded_file($file_tmp_name, '../assets/images/projects/'.$new_file_name) ) {
                        $updating_data['project_file'] = serialize($new_file_name);
                    } else {
                        $output['success'] = false;
                        $output['message'] = "Opps! Your project file is not uploading! Please contact administrator.";
                    } 
                }

                $update = update( 'sms_projects', $updating_data,  [ 
                    'username' => $username, 
                ] );
                
                if( $update ) {
                    $output['success'] = true;
                    $output['message'] = "Successfully updated the project.";
                } else {
                    $output['success'] = false;
                    $output['message'] = "Opps! Something wen't wrong! Please contact administrator.";
                }

            }   

            $sql_update = "UPDATE sms_projects SET edited_count = edited_count + 1 WHERE username = '$username'";
            $sql_query = mysqli_query($mysqli, $sql_update);

            if( ! $sql_query ) {
                $output['success'] = false;
                $output['message'] = "Opps! Something wen't wrong! Please contact administrator.";
            } 

            $update_members = mysqli_query( $mysqli, "UPDATE sms_group SET group_members = '$group_members' WHERE st_id = '$st_id' ");

            if( ! $update_members ) {
                $output['success'] = false;
                $output['message'] = "Opps! Something wen't wrong! Please contact administrator.";
            }

            if( ! $do_validation ) {
                if( $update_members ) {
                    $output['success'] = true;
                    $output['message'] = "Successfully updated the project.";
                }
            }   

            $diffs = array_diff( $ex_members, $group_members_arr ) ;

            foreach( $group_members_arr as $key  => $member ) {
                $update = mysqli_query( $mysqli, "UPDATE sms_registration SET g_id = '$g_id' WHERE st_id = '$member' ");
            }

            foreach( $diffs  as $key => $ex_member ) {
                $update1 = mysqli_query( $mysqli, "UPDATE sms_registration SET g_id = 0 WHERE st_id = '$ex_member' ");
            }
        }
    }
    echo json_encode($output);
}

// Process project update form 
if( isset( $_POST['form']) && $_POST['form'] == 'updateproject' ) {
    
    $ptitle = htmlspecialchars( $_POST['ptitle'] );
    $pdes = htmlspecialchars( $_POST['pdes'] );
    $g_id = (int) htmlspecialchars( $_POST['g_id'] );
    $group_members = isset( $_POST['group_members'] ) ? $_POST['group_members'] : '';
    $group_members_arr = [];
    if (!empty( $group_members ) && is_array( $group_members ) ) {
        foreach( $group_members as $key => $value ) {
            $group_members_arr[] = filter_var( $value, FILTER_SANITIZE_STRING );
        }
    }

    $file_name = $file_tmp_name = $file_size = $file_type = $extension = '';
    if( isset( $_FILES['pfile']['name'] ) ) {
        $file_name = htmlspecialchars( $_FILES['pfile']['name'] );
        $file_tmp_name = htmlspecialchars( $_FILES['pfile']['tmp_name'] );
        $file_size = htmlspecialchars( $_FILES['pfile']['size'] );
        $file_type = htmlspecialchars( $_FILES['pfile']['type'] );

        $allowed_extension = [ 'pdf', 'doc', 'docx' ];
        $explode = explode( '.', $file_name );
        $extension = end( $explode );
    }
    $allowed_file_size = 5000000; // 5 MB file size allowed
    $new_file_name = time().'.'.$extension;
    
    $username = $_SESSION['username'];
    $st_type = $_SESSION['login_type'];
    $st_id = $_SESSION['st_id'];

    // Hold all errors
    $output['message'] = [];
    $output['success'] = false;

    // Check existing user
    $edited_count = "SELECT edited_count FROM sms_projects WHERE username = '$username' ";
    $edited_query = mysqli_query( $mysqli, $edited_count );
    $result = mysqli_fetch_array( $edited_query );
    $found_count = (int) $result['edited_count'];

    // get existing members
    $get_ex_members = mysqli_query( $mysqli, "SELECT group_members FROM sms_group WHERE st_id = '$st_id' ");
    $result_ex_members = mysqli_fetch_array( $get_ex_members, MYSQLI_ASSOC );
    $ex_members = unserialize( $result_ex_members[ 'group_members' ] );

    if( $found_count >= 300 ) {
        $output['message'][] = 'You have edited your project 3 times, No more edit is allowed.';
    } else {
        if( isset( $ptitle) && isset( $pdes ) && isset( $group_members_arr ) ) {
            if( empty( $ptitle ) && empty( $pdes ) && empty( $group_members_arr ) ) {
                $output['message'][] = 'All fields is required';
            } else {
                // validate project title
                if( empty( $ptitle ) ) {
                    $output['message'][] = 'Project title is required.';
                } elseif( !preg_match('/^[a-zA-Z0-9.!@#*()-_, \d]+$/', $ptitle) ) {
                    $output['message'][] = 'Project title should contain only alpha numeric characters.';
                } elseif( strlen( $ptitle ) > 255 || strlen( $ptitle ) < 10 ) {
                    $output['message'][] = 'Project title length should be between 10-255 characters long.';
                }
                // validate project description
                if( empty( $pdes ) ) {
                    $output['message'][] = 'Project description is required';
                } elseif( !preg_match('/^[a-zA-Z0-9.!@#*()-_, \d]+$/', $pdes) ) {
                    $output['message'][] = 'Project description should contain only alpha numeric characters.';
                } elseif( strlen( $pdes ) > 5000 ) {
                    $output['message'][] = 'Project description length should be less than 5000 characters long.';
                }
                // Validate project file
                if( ! empty( $file_name) ) {
                    if ( ! in_array( $extension, $allowed_extension ) ) {
                        $output['message'][] = 'Uploaded file type is not allowed. We are currently allowing ' . implode(', ', $allowed_extension )  .' filetype';
                    } elseif( $file_size > $allowed_file_size ) {
                        $output['message'][] = 'Your uploaded file size must be less than 5 MB';
                    }
                }
                // validate group members
                if( empty( $group_members ) ) {
                    $output['message'][] = 'Please choose your group members. You can choose upto 10 members.';
                } elseif( !preg_grep('/^[0-9\d]+$/', $group_members_arr) ) {
                    $output['message'][] = 'Invalid group member is given.';
                } 
            }
    
            if( empty( $output['message'] ) ) {
    
                $updating_data = [ 
                    'project_title' => $ptitle, 
                    'project_description' => $pdes, 
                    'username' => $username, 
                ];
    
                if( ! empty( $file_name ) ) { 
                    if( move_uploaded_file($file_tmp_name, '../assets/images/projects/'.$new_file_name) ) {
                        $updating_data['project_file'] = serialize($new_file_name);
                    } else {
                        $output['success'] = false;
                        $output['message'] = "Opps! Your project file is not uploading! Please contact administrator.";
                    } 
                }
    
                $update = update( 'sms_projects', $updating_data,  [ 
                    'username' => $username, 
                ] );

                $group_members_arr[] = $st_id;
                $group_members = serialize( $group_members_arr );
    
                if( $update ) {
                    $output['success'] = true;
                    $output['message'] = "Successfully updated your project.";
                } else {
                    $output['success'] = false;
                    $output['message'] = "Opps! Something wen't wrong! Please contact administrator.";
                }
    
                $sql_update = "UPDATE sms_projects SET edited_count = edited_count + 1 WHERE username = '$username'";
                $sql_query = mysqli_query($mysqli, $sql_update);
    
                if( ! $sql_query ) {
                    $output['success'] = false;
                    $output['message'] = "Opps! Something wen't wrong! Please contact administrator.";
                } 

                $update_members = mysqli_query( $mysqli, "UPDATE sms_group SET group_members = '$group_members' WHERE st_id = '$st_id' ");

                if( ! $update_members ) {
                    $output['success'] = false;
                    $output['message'] = "Opps! Something wen't wrong! Please contact administrator.";
                }

                $diffs = array_diff( $ex_members, $group_members_arr ) ;

                foreach( $group_members_arr as $key  => $member ) {
                    $update = mysqli_query( $mysqli, "UPDATE sms_registration SET g_id = '$g_id' WHERE st_id = '$member' ");
                }

                foreach( $diffs  as $key => $ex_member ) {
                    $update1 = mysqli_query( $mysqli, "UPDATE sms_registration SET g_id = 0 WHERE st_id = '$ex_member' ");
                }
            }
        }
    }
    echo json_encode($output);
}


// Proces submit project form 
if( isset( $_POST['form']) && $_POST['form'] == 'submitproject' ) {
    // get all form field value
    $ptitle = htmlspecialchars( $_POST['ptitle'] );
    $pdes = htmlspecialchars( $_POST['pdes'] );

    $group_members = isset( $_POST['group_members'] ) ? $_POST['group_members'] : '';
    $group_members_arr = [];
    if (!empty( $group_members ) && is_array( $group_members ) ) {
        foreach( $group_members as $key => $value ) {
            $group_members_arr[] = filter_var( $value, FILTER_SANITIZE_STRING );
        }
    }

    $file_name = $file_tmp_name = $file_size = $file_type = $extension = '';
    if( isset( $_FILES['pfile']['name'] ) ) {
        $file_name = htmlspecialchars( $_FILES['pfile']['name'] );
        $file_tmp_name = htmlspecialchars( $_FILES['pfile']['tmp_name'] );
        $file_size = htmlspecialchars( $_FILES['pfile']['size'] );
        $file_type = htmlspecialchars( $_FILES['pfile']['type'] );

        $allowed_extension = [ 'pdf', 'doc', 'docx' ];
        $explode = explode( '.', $file_name );
        $extension = end( $explode );
    }
    $allowed_file_size = 5000000; // 5 MB file size allowed
    $new_file_name = time().'.'.$extension;
    $username = $_SESSION['username'];
    $st_id = (int) $_SESSION['st_id'];

    // Check existence
    $check_existnece = "SELECT username, edited_count FROM sms_projects WHERE username = '$username' ";
    $check_query = mysqli_query( $mysqli, $check_existnece );
    $found_check = mysqli_num_rows( $check_query );

    // Hold all errors
    $output['message'] = [];
    $output['success'] = false;
    
    // check existence

    if( $found_check > 0 ) {
        $output['message'][] = 'You have already submited your project. Please update your existing submitted project if it\'s required.';
    } else {
        if( isset( $ptitle) && isset( $pdes ) && isset( $file_name) && isset( $group_members_arr ) ) {
        
            if( empty( $ptitle ) && empty( $pdes ) && empty( $file_name ) && empty( $group_members_arr ) ) {
                $output['message'][] = 'All fields is required';
            } else {
                // validate project title
                if( empty( $ptitle ) ) {
                    $output['message'][] = 'Project title is required.';
                } elseif( !preg_match('/^[a-zA-Z0-9.!@#*()-_, \d]+$/', $ptitle) ) {
                    $output['message'][] = 'Project title should contain only alpha numeric characters.';
                } elseif( strlen( $ptitle ) > 255 || strlen( $ptitle ) < 10 ) {
                    $output['message'][] = 'Project title length should be between 10-255 characters long.';
                }
                // validate project description
                if( empty( $pdes ) ) {
                    $output['message'][] = 'Project description is required';
                } elseif( !preg_match('/^[a-zA-Z0-9.!@#*()-_, \d]+$/', $pdes) ) {
                    $output['message'][] = 'Project description should contain only alpha numeric characters.';
                } elseif( strlen( $pdes ) > 5000 ) {
                    $output['message'][] = 'Project description length should be less than 5000 characters long.';
                }
                // Validate project file
                if( empty( $file_name ) ) {
                    $output['message'][] = 'Please upload your project file.';
                } elseif ( ! in_array( $extension, $allowed_extension ) ) {
                    $output['message'][] = 'Uploaded file type is not allowed. We are currently allowing ' . implode(', ', $allowed_extension )  .' filetype';
                } elseif( $file_size > $allowed_file_size ) {
                    $output['message'][] = 'Your uploaded file size must be less than 5 MB';
                }
                // validate group members
                if( empty( $group_members ) ) {
                    $output['message'][] = 'Please choose your group members. You can choose upto 10 members.';
                } elseif( !preg_grep('/^[0-9\d]+$/', $group_members) ) {
                    $output['message'][] = 'Invalid group member is given.';
                } 
            }
    
            if( empty( $output['message'] ) ) {
                $output['success'] = false;
    
                if( !empty( $file_name ) ) {
                    if( move_uploaded_file($file_tmp_name, '../assets/images/projects/'.$new_file_name) ) {
                        if( insert( [ 
                            'project_title' => $ptitle, 
                            'project_description' => $pdes, 
                            'project_file' => serialize( $new_file_name ), 
                            'username' => $username, 
                            'st_id' => $st_id, 
                            'edited_count' => 0,
                        ], 'sms_projects' ) ) {
                            $output['success'][] = true;
                            $output['message'][] = "Successfully submited your project.";

                            $group_members_arr[] = $st_id;
                            // addd the group member to group table
                            $group_members = serialize( $group_members_arr );
                            $insert_group = mysqli_query( $mysqli, "INSERT INTO sms_group( group_members, st_id ) VALUES ( '$group_members', '$st_id' ) ");
                            $g_id = mysqli_insert_id( $mysqli );

                            if( ! $insert_group ) {
                                $output['success'][] = false;
                                $output['message'][] = "Opps! Your project file is not uploading! Please contact administrator.";
                            }
                            
                            foreach( $group_members_arr as $key => $member ) {
                                $update_reg = mysqli_query( $mysqli, "UPDATE sms_registration SET g_id = '$g_id' WHERE st_id = '$member' " );  
                            }

                            if( ! $update_reg ) {
                                $output['success'][] = false;
                                $output['message'][] = "Opps! Your project file is not uploading! Please contact administrator.";
                            } 
                        } else {
                            $output['success'][] = false;
                            $output['message'][] = "Opps! Something wen't wrong! Please contact administrator.";
                        }
                    } else {
                        $output['success'][] = false;
                        $output['message'][] = "Opps! Your project file is not uploading! Please contact administrator.";
                    } 
                }
            }
        }
    }

    echo json_encode($output);
}

// Process profile update form 
if( isset( $_POST['form']) && $_POST['form'] == 'profile' ) {
    
    $fname = htmlspecialchars( $_POST['fname'] );
    $lname = htmlspecialchars( $_POST['lname'] );
    $password = htmlspecialchars( $_POST['password'] );
    $hash_password = hash( 'sha512', $password );
    $email = htmlspecialchars( $_POST['email'] );
    $username = $_SESSION['username'];

    $file_name = $file_tmp_name = $file_size = $file_type = $extension = '';
    if( isset( $_FILES['profile_pic']['name'] ) ) {
        $file_name = htmlspecialchars( $_FILES['profile_pic']['name'] );
        $file_tmp_name = htmlspecialchars( $_FILES['profile_pic']['tmp_name'] );
        $file_size = htmlspecialchars( $_FILES['profile_pic']['size'] );
        $file_type = htmlspecialchars( $_FILES['profile_pic']['type'] );

        $allowed_extension = [ 'jpg', 'jpeg', 'png', 'gif' ];
        $explode = explode( '.', $file_name );
        $extension = end( $explode );
    }
    $allowed_file_size = 5000000; // 5 MB file size allowed
    $new_file_name = time().'.'.$extension;
    
    $username = $_SESSION['username'];
    $st_type = $_SESSION['login_type'];

    // Hold all errors
    $output['message'] = [];
    $output['success'] = false;

    // Check existing user
    $found_email = '';
    if( !empty( $email ) ) {
        $email_sql = "SELECT email FROM sms_registration WHERE email = '$email' AND username != '$username' AND st_type != '$st_type'  ";
        $email_query = mysqli_query( $mysqli, $email_sql );
        $found_email = mysqli_num_rows( $email_query );
    }

    if( isset( $fname) && isset( $lname ) && isset( $password ) && isset( $email ) ) {
        if( empty( $fname ) && empty( $lname ) && empty( $username ) && empty( $email ) ) {
            $output['message'][] = 'All fields is required';
        } else {
            // validate first name
            if( empty( $fname ) ) {
                $output['message'][] = 'First name is required.';
            } elseif( !preg_match('/^[a-zA-Z \d]+$/', $fname) ) {
                $output['message'][] = 'First name should contain only characters.';
            } elseif( strlen( $fname ) > 20 || strlen( $fname ) < 2 ) {
                $output['message'][] = 'First name length should be between 2-20 characters long.';
            }
            // validate last name
            if( empty( $lname ) ) {
                $output['message'][] = 'Last name is required';
            } elseif( !preg_match('/^[a-zA-Z \d]+$/', $lname) ) {
                $output['message'][] = 'Last name should contain only characters.';
            } elseif( strlen( $lname ) > 20 || strlen( $lname ) < 2 ) {
                $output['message'][] = 'Last name length should be between 2-20 characters long.';
            }
            // validate password
            if( !empty( $password ) ) {
                if( empty( $password ) ) {
                    $output['message'][] = 'Password is required.';
                } elseif( strlen( $password ) < 6 ) {
                    $output['message'][] = 'Password length should be at least 6 characters long.';
                }
            }
            // validate email
            if( empty( $email ) ) {
                $output['message'][] = 'Email address is required.';
            } elseif( ! filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
                $output['message'][] = 'Email address is not correct.';
            } elseif( $found_email == 1 ) {
                $output['message'][] = 'Email address is already exist, Please choose another.';
            }

            // Validate file upload
            if( ! empty( $file_name ) ) {
                if( ! in_array( $extension, $allowed_extension ) ) {
                    $output['message'][] = 'Uploaded file type is not allowed. We are currently allowing ' . implode(', ', $allowed_extension )  .' filetype';
                } elseif( $file_size > $allowed_file_size ) {
                    $output['message'][] = 'Your uploaded file size must be less than 5 MB';
                }
            }
        }

        if( empty( $output['message'] ) ) {
            // Upload the file to the directory
            $updating_data = [
                'fname' =>  $fname, 
                'lname' => $lname, 
                'email' => $email,
            ];

            if( !empty( $file_name ) ) {
                if( move_uploaded_file($file_tmp_name, '../assets/images/profile/'.$new_file_name) ) {
                    $updating_data['profile_pic'] = $new_file_name;
                }
            }
            
            $update = update('sms_registration', $updating_data,  [ 
                'username' => $username, 
                'st_type' => $st_type 
            ] );

            if( $update ) {
                $output['success'] = true;
                $output['message'][] = "Successfully updated.";
            } else {
                $output['success'] = false;
                $output['message'][] = "Opps, Somethng wen't wrong, Please contact administrative.";
            }
        }

        echo json_encode($output);
    }
}

// Process login form 
if( isset( $_POST['form']) && $_POST['form'] == 'login' ) {
    // get all form field value
    $username = htmlspecialchars( $_POST['username'] );
    $password = htmlspecialchars( $_POST['password'] );
    $login_type = htmlspecialchars( $_POST['login_type'] );
    $hash_password = hash( 'sha512', $password );
    $status = '';

    // Hold all errors
    $output['message'] = [];
    $output['success'] = false;
    $output['redirect'] = 'index.php';

    // Check username and password
    $check_user = "SELECT username, st_id, status FROM sms_registration WHERE username = '$username' AND password = '$hash_password' AND st_type = '$login_type' ";
    $user_query = mysqli_query( $mysqli, $check_user );
    $found_user = mysqli_num_rows( $user_query );
    $st_id = '';

    if( $found_user ) {
        $result = mysqli_fetch_array( $user_query, MYSQLI_ASSOC );
        $status = $result['status'];
        $st_id = $result['st_id'];
    }
    
    if( isset( $username) && isset( $password ) ) {
        if( empty( $username ) && empty( $password ) ) {
            $output['message'][] = 'All fields is required';
        } else {
            // validate username
            if( empty( $username ) ) {
                $output['message'][] = 'Username is required';
            } elseif( empty( $password ) ) {
                $output['message'][] = 'Password is required';
            } elseif( empty( $login_type ) )  {
                $output['message'][] = 'Please choose a login typye';
            } elseif( $found_user == 0 ) {
                $output['message'][] = 'Username or password is incorrect';
            } elseif( $status == 1 ) {
                $output['message'][] = 'Your account is not active, Please contact administrative';
            } elseif( !in_array( $login_type, [1,2] ) ) {
                $output['message'][] = 'Invalid login type given';
            }
        }

        if( empty( $output['message'] ) ) {
            if( $found_user == 1 ) {
                $output['success'] = true;
                $output['message'][] = "Successfully Logged, Now you are redirecting....";
                $_SESSION['username'] = $username;
                $_SESSION['login_type'] = $login_type; 
                $_SESSION['st_id'] = $st_id; 
            } else {
                $output['success'] = false;
                $output['message'][] = "Opps! Something wen't wrong! Please contact administrator";
            }
        }
        echo json_encode($output);
    }
}

// Process registration form 
if( isset( $_POST['form']) && $_POST['form'] == 'registration' ) {
    // get all form field value
    $fname = validate( $_POST['fname'] );
    $lname = htmlspecialchars( $_POST['lname'] );
    $username = htmlspecialchars( $_POST['username'] );
    $password = htmlspecialchars( $_POST['password'] );
    $hash_password = hash( 'sha512', $password );
    $email = htmlspecialchars( $_POST['email'] );
    $roll = htmlspecialchars( $_POST['roll'] );
    $batch = htmlspecialchars( $_POST['batch'] );
    $department = htmlspecialchars( $_POST['department'] );
    $registration_type = htmlspecialchars( $_POST['registration_type'] );
    
    // Check existing user
    $found_username = '';
    if( !empty( $username_query ) ) {
        $username_sql = "SELECT username FROM sms_registration WHERE username = '$username' ";
        $username_query = mysqli_query( $mysqli, $username_sql );
        $found_username = mysqli_num_rows( $username_query );
    }

    // Check existing user
    $found_email = '';
    if( !empty( $email ) ) {
        $email_sql = "SELECT email FROM sms_registration WHERE email = '$email' ";
        $email_query = mysqli_query( $mysqli, $email_sql );
        $found_email = mysqli_num_rows( $email_query );
    }

    // Hold all errors
    $output['message'] = [];
    $output['success'] = false;
    
    // check existence
    if( isset( $fname) && isset( $lname ) && isset( $username) && isset( $password ) && isset( $email ) && isset( $registration_type ) && isset( $roll ) && isset( $batch) && isset( $department ) ) {
        if( empty( $fname ) && empty( $lname ) && empty( $username ) && empty( $password ) && empty( $email ) && empty( $registration_type ) && empty( $roll ) && empty( $batch ) && empty( $department ) ) {
            $output['message'][] = 'All fields is required';
        } else {
            // validate first name
            if( empty( $fname ) ) {
                $output['message'][] = 'First name is required.';
            } elseif( !preg_match('/^[a-zA-Z \d]+$/', $fname) ) {
                $output['message'][] = 'First name should contain only characters.';
            } elseif( strlen( $fname ) > 20 || strlen( $fname ) < 2 ) {
                $output['message'][] = 'First name length should be between 2-20 characters long.';
            }
            // validate last name
            if( empty( $lname ) ) {
                $output['message'][] = 'Last name is required';
            } elseif( !preg_match('/^[a-zA-Z \d]+$/', $lname) ) {
                $output['message'][] = 'Last name should contain only characters.';
            } elseif( strlen( $lname ) > 20 || strlen( $lname ) < 2 ) {
                $output['message'][] = 'Last name length should be between 2-20 characters long.';
            }
            // validate username
            if( empty( $username ) ) {
                $output['message'][] = 'Username is required.';
            } elseif( !preg_match('/^[a-zA-Z\d]+$/', $username) ) {
                $output['message'][] = 'Username should contain only characters.';
            } elseif( strlen( $username ) > 20 || strlen( $username ) < 2 ) {
                $output['message'][] = 'Usernamelength should be between 2-20 characters long.';
            } elseif( $found_username == 1 ) {
                $output['message'][] = 'Username is already exist, Please choose another.';
            }
            // validate password
            if( empty( $password ) ) {
                $output['message'][] = 'Password is required.';
            } elseif( strlen( $password ) < 6 ) {
                $output['message'][] = 'Password length should be at least 6 characters long.';
            }
            // validate email
            if( empty( $email ) ) {
                $output['message'][] = 'Email address is required.';
            } elseif( ! filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
                $output['message'][] = 'Email address is not correct.';
            } elseif( $found_email == 1 ) {
                $output['message'][] = 'Email address is already exist, Please choose another.';
            }
            // validate registration type
            if( empty( $registration_type ) ) {
                $output['message'][] = 'Select registration type.';
            } elseif( !in_array( $registration_type, [1, 2]) ) {
                $output['message'][] = 'Invalid registration type given.';
            }

            // if registration type for student then validate this
            if( 1 == $registration_type ) {
                // Validate roll name
                if( empty( $roll ) ) {
                    $output['message'][] = 'Your roll number is required';
                } elseif( !preg_match('/^[a-zA-Z0-9 \d]+$/', $roll) ) {
                    $output['message'][] = 'Your roll number should contain only alpha numeric characters.';
                } elseif( strlen( $roll ) > 15 || strlen( $roll ) < 2 ) {
                    $output['message'][] = 'Invalid roll number given';
                }
                // Validate batch
                if( empty( $batch ) ) {
                    $output['message'][] = 'Your batch name is required';
                } elseif( !preg_match('/^[a-zA-Z0-9 \d]+$/', $batch) ) {
                    $output['message'][] = 'Your batch name should contain only alpha numeric characters.';
                } elseif( strlen( $batch ) > 15 || strlen( $batch ) < 2 ) {
                    $output['message'][] = 'Invalid batch name given';
                }
                // Validate department
                if( empty( $department ) ) {
                    $output['message'][] = 'Your department name is required';
                } elseif( !preg_match('/^[a-zA-Z0-9 \d]+$/', $department) ) {
                    $output['message'][] = 'Your department name should contain only alpha numeric characters.';
                } elseif( strlen( $batch ) > 30 || strlen( $department ) < 2 ) {
                    $output['message'][] = 'Invalid department name given';
                }
            }
        }

        if( empty( $output['message'] ) ) {
            $output['success'] = false;

            $inserting_data = [ 
                'fname' => $fname, 
                'lname' => $lname, 
                'username' => $username, 
                'password' => $hash_password, 
                'email' => $email,
                'st_type' => $registration_type,
            ];

            // add follow column if the registration is for student
            if( 1 == $registration_type ) {
                $inserting_data['roll'] = $roll;
                $inserting_data['batch'] = $batch;
                $inserting_data['department'] = $department;
            }

            if( insert( $inserting_data, 'sms_registration' ) ) {
                $output['success'] = true;
                $output['message'] = "Successfully registered a new account.";
            } else {
                $output['success'] = false;
                $output['message'] = "Opps! Something wen't wrong! Please contact administrator.";
            }
        }

        echo json_encode($output);
    }
}