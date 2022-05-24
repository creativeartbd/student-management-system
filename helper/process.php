<?php
require_once 'functions.php';

// update group 
if( isset( $_POST['form'] ) && $_POST['form'] == 'update_group' ) {

    $group_members = isset( $_POST['group_members'] ) ? $_POST['group_members'] : '';
    $group_members_arr = [];
    $get_g_id = (int) $_POST['g_id'];
    $st_id = (int) $_SESSION['st_id'];

    $check_get_g_id = mysqli_query( $mysqli, "SELECT g_id FROM sms_group WHERE g_id = '$get_g_id' AND st_id = '$st_id' ");
    $found_get_g_id = mysqli_num_rows( $check_get_g_id );

    if (!empty( $group_members ) && is_array( $group_members ) ) {
        foreach( $group_members as $key => $value ) {
            $group_members_arr[] = filter_var( $value, FILTER_SANITIZE_STRING );
        }
    }

    $st_id = (int) $_SESSION['st_id'];
    $username = $_SESSION['username'];

    // Hold all errors
    $output['message'] = [];
    $output['success'] = false;
    $output['reload'] = true;

    if( isset( $group_members_arr ) ) {
        if( empty( $group_members_arr ) ) {
            $output['message'][] = 'Please add some group memebers.';
        }
        if( empty( $get_g_id ) ) {
            $output['message'][] = 'Group id is missing';
        } elseif( $found_get_g_id == 0 ) {
            $output['message'][] = 'Couldn\'t found the group id';
        }

        if( empty( $output['message'] ) ) {

            $new_group_members = json_encode( $group_members_arr );
            $update_group = mysqli_query( $mysqli, "UPDATE sms_group SET group_members = '$new_group_members' WHERE st_id = '$st_id' ");

            if( $update_group ) {

                // get the existing g_id of members
                foreach( $group_members_arr as $member ) {
                    $members_ex_gid = mysqli_query( $mysqli, "SELECT g_id FROM sms_registration WHERE st_id = '$member' ");
                    $members_ex_gid_s = [];
                    if( mysqli_num_rows( $members_ex_gid ) > 0 ) {
                        $members_ex_gid_result = mysqli_fetch_array( $members_ex_gid, MYSQLI_ASSOC );
                        $members_ex_gid_s = json_decode( $members_ex_gid_result['g_id'] );
                    }
                    
                    if( empty( $members_ex_gid_s ) ) {
                        
                            $members_ex_gid_s[] = $get_g_id;
                            $members_ex_gid_s = json_encode( $members_ex_gid_s );
                            $update_their_g_id = mysqli_query( $mysqli, "UPDATE sms_registration SET g_id = '$members_ex_gid_s' WHERE st_id = '$member' ");
                       
                    } else {
                        if( ! in_array( $get_g_id,  $members_ex_gid_s ) ) {
                            $members_ex_gid_s[] = $get_g_id;
                            $members_ex_gid_s = json_encode( $members_ex_gid_s );
                            $update_their_g_id = mysqli_query( $mysqli, "UPDATE sms_registration SET g_id = '$members_ex_gid_s' WHERE st_id = '$member' ");
                        } 
                    }
                    
                }

                $output['success'][] = true;
                $output['message'][] = 'Successfully updated the group.';
            } else {
                $g_id = 0;
                $output['success'][] = false;
                $output['message'][] = "Opps! Something wen't wrong! Please contact administrator.";
            }
        }

        echo json_encode($output);
    }
}

// create group 
if( isset( $_POST['form'] ) && $_POST['form'] == 'create_group' ) {

    $group_members = isset( $_POST['group_members'] ) ? $_POST['group_members'] : '';
    $g_name = validate( $_POST['g_name'] );
    $group_members_arr = [];

    if (!empty( $group_members ) && is_array( $group_members ) ) {
        foreach( $group_members as $key => $value ) {
            $group_members_arr[] = filter_var( $value, FILTER_SANITIZE_STRING );
        }
    }

    $st_id = (int) $_SESSION['st_id'];
    $username = $_SESSION['username'];

    // Hold all errors
    $output['message'] = [];
    $output['success'] = false;
    $output['reload'] = true;

    if( isset( $group_members_arr ) && isset( $g_name ) ) {
    
        if( empty( $g_name ) ) {
            $output['message'][] = 'Group name is required';
        } elseif( !preg_match('/^[a-zA-Z0-9. \], \d]+$/', $g_name) ) {
            $output['message'][] = 'Group name should contain only alpha numeric characters.';
        } elseif( strlen( $g_name ) > 50 || strlen( $g_name ) < 2 ) {
            $output['message'][] = 'Group name length should be 2-50 characters long.';
        }

        if( empty( $group_members_arr ) ) {
            $output['message'][] = 'Please add some group memebers.';
        } 

        if( empty( $output['message'] ) ) {

            $group_members_json= json_encode( $group_members_arr );
            $insert_group = mysqli_query( $mysqli, "INSERT INTO sms_group ( g_name, group_members, st_id ) VALUES ( '$g_name', '$group_members_json', '$st_id' ) ");
            $last_g_id = mysqli_insert_id( $mysqli );
        
            if( $insert_group ) {
                // add group id to the student
                // $g_id = (array) mysqli_insert_id( $mysqli );
                // $g_id_json = json_encode( $g_id );
                // foreach( $group_members_arr as $member ) {
                //     $insert_g_id = mysqli_query( $mysqli, "UPDATE sms_registration SET g_id = '$g_id_json' WHERE st_id = '$member' ");
                // }

                // get the existing g_id of members
                foreach( $group_members_arr as $member ) {
                    $members_ex_gid = mysqli_query( $mysqli, "SELECT g_id FROM sms_registration WHERE st_id = '$member' ");
                    $members_ex_gid_s = [];
                    if( mysqli_num_rows( $members_ex_gid ) > 0 ) {
                        $members_ex_gid_result = mysqli_fetch_array( $members_ex_gid, MYSQLI_ASSOC );
                        $members_ex_gid_s = json_decode( $members_ex_gid_result['g_id'] );
                    }
                    
                    if( empty( $members_ex_gid_s ) ) {
                        $members_ex_gid_s[] = $last_g_id;
                        $members_ex_gid_s = json_encode( $members_ex_gid_s );
                        $update_their_g_id = mysqli_query( $mysqli, "UPDATE sms_registration SET g_id = '$members_ex_gid_s' WHERE st_id = '$member' ");
                    } else {
                        if( ! in_array( $last_g_id,  $members_ex_gid_s ) ) {
                            $members_ex_gid_s[] = $last_g_id;
                            $members_ex_gid_s = json_encode( $members_ex_gid_s );
                            $update_their_g_id = mysqli_query( $mysqli, "UPDATE sms_registration SET g_id = '$members_ex_gid_s' WHERE st_id = '$member' ");
                        } 
                    }
                    
                }


                $output['success'][] = true;
                $output['message'][] = 'Successfully created a new group.';
            } else {
                $g_id = 0;
                $output['success'][] = false;
                $output['message'][] = "Opps! Something wen't wrong! Please contact administrator.";
            }
        }

        echo json_encode($output);
    }
}

// Chat
if( isset( $_POST['form']) && $_POST['form'] == 'getchat' ) {
    
    // $st_id = (int) $_SESSION['st_id'];
    // $get_g_id = mysqli_query( $mysqli, "SELECT g_id FROM sms_registration WHERE st_id = '$st_id' ");
    // $result_g_id = mysqli_fetch_array( $get_g_id, MYSQLI_ASSOC );
    $g_id = isset( $_SESSION['ses_g_id'] ) ? (int) $_SESSION['ses_g_id'] : 0;
    if( $g_id ) {
        $get_msg =  mysqli_query( $mysqli, "SELECT sc.*, sr.username FROM sms_chat AS sc LEFT JOIN sms_registration AS sr ON sr.st_id = sc.st_id WHERE sc.g_id = '$g_id' ");
        while( $result_get_msg = mysqli_fetch_array( $get_msg, MYSQLI_ASSOC ) ) {
            $chat_text = $result_get_msg['chat_text'];
            $chat_time = $result_get_msg['chat_time'];
            $uname = $result_get_msg['username'];
            echo "<div class='row mb-2'><div class='col-9'><strong>$uname: </strong>$chat_text </div><div class='col-3 text-end text-small'>$chat_time</div></div>";
        }
    } 
}

// process approve goal 
if( isset( $_POST['form']) && $_POST['form'] == 'chat' ) {
    $chat_text = htmlspecialchars( trim( $_POST['chat'] ) );
    $st_id = (int) $_SESSION['st_id'];
    $username = $_SESSION['username'];

    // $get_group_id = mysqli_query( $mysqli, "SELECT g_id FROM sms_registration WHERE  st_id = '$st_id' ");
    // $found_groupu_id = mysqli_num_rows( $get_group_id );

    // $group_id = '';
    // if( $found_groupu_id > 0 ) {
    //     $result_group_id = mysqli_fetch_array( $get_group_id, MYSQLI_ASSOC );
    //     $group_id = $result_group_id['g_id'];
    // }
    $group_id = (int) $_POST['url_g_id'];
   

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
                        'goal_id' => $goal_id,
                    ], 'sms_goal_answer' ) ) {
                        $output['success'] = true;
                        $output['message'] = "Successfully submited your goal.";
                        $update_goal = mysqli_query( $mysqli, "UPDATE sms_goal SET is_answer = 1 WHERE goal_id = '$goal_id' " );
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

    $get_p_id = (int) $_POST['get_p_id'];
    $ses_st_id= (int) $_SESSION['st_id'];
    $ptitle = validate( $_POST['ptitle'] );
    $pdes = validate( $_POST['pdes'] );
    $g_id = (int) validate( $_POST['group_name'] );
    $supervisor = (int) validate( $_POST['supervisor'] );
    $gnumber = validate( $_POST['gnumber'] );
    $gemail = validate( $_POST['gemail'] );
    $group_members = isset( $_POST['group_members'] ) ? $_POST['group_members'] : '';

    $file_name = $file_tmp_name = $file_size = $file_type = $extension = '';
    if( isset( $_FILES['pfile']['name'] ) ) {
        $file_name = validate( $_FILES['pfile']['name'] );
        $file_tmp_name = validate( $_FILES['pfile']['tmp_name'] );
        $file_size = validate( $_FILES['pfile']['size'] );
        $file_type = validate( $_FILES['pfile']['type'] );

        $allowed_extension = [ 'pdf', 'doc', 'docx' ];
        $explode = explode( '.', $file_name );
        $extension = end( $explode );
    }

    $allowed_file_size = 5000000; // 5 MB file size allowed
    $new_file_name = time().'.'.$extension;

    // Hold all errors
    $output['message'] = [];
    $output['success'] = false;

    if( isset( $ptitle) && isset( $pdes ) && isset( $g_id ) && isset( $gnumber ) && isset( $gemail ) ) {
        if( empty( $ptitle ) && empty( $pdes ) && empty( $g_id ) && empty( $gnumber ) && empty( $gemail ) ) {
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

            // validate mobile
            if( empty( $gnumber ) ) {
                $output['message'][] = 'Mobile number is required.';
            } elseif( ! preg_match( "/^[0-9]{11}$/", $gnumber ) ) {
                $output['message'][] = 'Invalid mobile number given. Number should be start with 0 and 11 characters length';
            }
            // validate email
            if( empty( $gemail ) ) {
                $output['message'][] = 'Email address is required.';
            } elseif( ! filter_var( $gemail, FILTER_VALIDATE_EMAIL ) ) {
                $output['message'][] = 'Email address is not correct';
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
            if( empty( $g_id ) ) {
                $output['message'][] = 'Please choose your group name.';
            } elseif( !preg_match('/^[0-9\d]+$/', $g_id) ) {
                $output['message'][] = 'Invalid group name is given.';
            } 
        }

        if( empty( $output['message'] ) ) {

            $updating_data = [ 
                'project_title' => $ptitle, 
                'project_description' => $pdes, 
                'gnumber' => $gnumber, 
                'gemail' => $gemail,
                'g_id' => $g_id,
            ];

            // Supervisor can only edit group
            if( $supervisor === $ses_st_id ) {
                $updating_data = [ 
                    'g_id' => $g_id,
                ];
            }
 
            if( $supervisor !== $ses_st_id ) {
                if( ! empty( $file_name ) ) { 
                    if( move_uploaded_file($file_tmp_name, '../assets/images/projects/'.$new_file_name) ) {
                        $updating_data['project_file'] = serialize($new_file_name);
                    } else {
                        $output['success'][] = false;
                        $output['message'][] = "Opps! Your project file is not uploading! Please contact administrator.";
                    } 
                }
            }

            $update = update( 'sms_projects', $updating_data,  [ 
                'p_id' => $get_p_id, 
            ] );

            if( $update ) {
                $output['success'][] = true;
                $output['message'][] = "Successfully updated your project.";
            } else {
                $output['success'][] = false;
                $output['message'][] = "Opps! Something wen't wrong! Please contact administrator.";
            }
        }
    }
    echo json_encode($output);
}

// Process project update form 
if( isset( $_POST['form']) && $_POST['form'] == 'updateproject' ) {


    $username = validate( $_SESSION['username'] );
    $st_type = (int) $_SESSION['login_type'];
    $st_id = (int) $_SESSION['st_id'];
    
    $ptitle = validate( $_POST['ptitle'] );
    $pdes = validate( $_POST['pdes'] );
    $g_id = (int) validate( $_POST['group_name'] );
    $gnumber = validate( $_POST['gnumber'] );
    $gemail = validate( $_POST['gemail'] );
    $group_members = isset( $_POST['group_members'] ) ? $_POST['group_members'] : '';

    // $group_members_arr = [];
    // if (!empty( $group_members ) && is_array( $group_members ) ) {
    //     foreach( $group_members as $key => $value ) {
    //         $group_members_arr[] = filter_var( $value, FILTER_SANITIZE_STRING );
    //     }
    // }

    $file_name = $file_tmp_name = $file_size = $file_type = $extension = '';
    if( isset( $_FILES['pfile']['name'] ) ) {
        $file_name = validate( $_FILES['pfile']['name'] );
        $file_tmp_name = validate( $_FILES['pfile']['tmp_name'] );
        $file_size = validate( $_FILES['pfile']['size'] );
        $file_type = validate( $_FILES['pfile']['type'] );

        $allowed_extension = [ 'pdf', 'doc', 'docx' ];
        $explode = explode( '.', $file_name );
        $extension = end( $explode );
    }
    $allowed_file_size = 5000000; // 5 MB file size allowed
    $new_file_name = time().'.'.$extension;

    // Hold all errors
    $output['message'] = [];
    $output['success'] = false;

    // Check existing user
    $edited_count = "SELECT edited_count FROM sms_projects WHERE username = '$username' ";
    $edited_query = mysqli_query( $mysqli, $edited_count );
    $result = mysqli_fetch_array( $edited_query );
    $found_count = (int) $result['edited_count'];

    // // get existing members
    // $get_ex_members = mysqli_query( $mysqli, "SELECT group_members FROM sms_group WHERE st_id = '$st_id' ");
    // $ex_members = [];
    // if( mysqli_num_rows( $get_ex_members ) > 0 ) {
    //     $result_ex_members = mysqli_fetch_array( $get_ex_members, MYSQLI_ASSOC );
    //     $ex_members = unserialize( $result_ex_members[ 'group_members' ] );
    // }
    
    if( $found_count >= 300 ) {
        $output['message'][] = 'You have edited your project 3 times, No more edit is allowed.';
    } else {
        if( isset( $ptitle) && isset( $pdes ) && isset( $g_id ) && isset( $gnumber ) && isset( $gemail ) ) {
            if( empty( $ptitle ) && empty( $pdes ) && empty( $g_id ) && empty( $gnumber ) && empty( $gemail ) ) {
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

                // validate mobile
                if( empty( $gnumber ) ) {
                    $output['message'][] = 'Mobile number is required.';
                } elseif( ! preg_match( "/^[0-9]{11}$/", $gnumber ) ) {
                    $output['message'][] = 'Invalid mobile number given. Number should be start with 0 and 11 characters length';
                }
                // validate email
                if( empty( $gemail ) ) {
                    $output['message'][] = 'Email address is required.';
                } elseif( ! filter_var( $gemail, FILTER_VALIDATE_EMAIL ) ) {
                    $output['message'][] = 'Email address is not correct';
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
                if( empty( $g_id ) ) {
                    $output['message'][] = 'Please choose your group name.';
                } elseif( !preg_match('/^[0-9\d]+$/', $g_id) ) {
                    $output['message'][] = 'Invalid group name is given.';
                } 
            }
    
            if( empty( $output['message'] ) ) {
    
                $updating_data = [ 
                    'project_title' => $ptitle, 
                    'project_description' => $pdes, 
                    'username' => $username, 
                    'gnumber' => $gnumber, 
                    'gemail' => $gemail,
                    'g_id' => $g_id,
                ];
    
                if( ! empty( $file_name ) ) { 
                    if( move_uploaded_file($file_tmp_name, '../assets/images/projects/'.$new_file_name) ) {
                        $updating_data['project_file'] = serialize($new_file_name);
                    } else {
                        $output['success'][] = false;
                        $output['message'][] = "Opps! Your project file is not uploading! Please contact administrator.";
                    } 
                }
    
                $update = update( 'sms_projects', $updating_data,  [ 
                    'username' => $username, 
                ] );

                // $group_members_arr[] = $st_id;
                // $group_members = serialize( $group_members_arr );
    
                if( $update ) {
                    $output['success'][] = true;
                    $output['message'][] = "Successfully updated your project.";
                } else {
                    $output['success'][] = false;
                    $output['message'][] = "Opps! Something wen't wrong! Please contact administrator.";
                }

                // if( ! empty( $ex_members ) ) {
                //     $update_members = mysqli_query( $mysqli, "UPDATE sms_group SET group_members = '$group_members' WHERE st_id = '$st_id' ");
                // } else {
                //     $update_members = mysqli_query( $mysqli, "INSERT INTO sms_group( group_members, st_id ) VALUES ( '$group_members', '$st_id' ) ");
                // }

                // if( ! $update_members ) {
                //     $output['success'][] = false;
                //     $output['message'][] = "Opps! Something wen't wrong! Please contact administrator.";
                // }

                // $diffs = array_diff( $ex_members, $group_members_arr ) ;

                // foreach( $group_members_arr as $key  => $member ) {
                //     $update = mysqli_query( $mysqli, "UPDATE sms_registration SET g_id = '$g_id' WHERE st_id = '$member' ");
                // }

                // foreach( $diffs  as $key => $ex_member ) {
                //     $update1 = mysqli_query( $mysqli, "UPDATE sms_registration SET g_id = 0 WHERE st_id = '$ex_member' ");
                // }
            }
        }
    }
    echo json_encode($output);
}


// Proces submit project form 
if( isset( $_POST['form']) && $_POST['form'] == 'submitproject' ) {
    // get all form field value
    $ptitle = validate( $_POST['ptitle'] );
    $pdes = validate( $_POST['pdes'] );
    $gnumber = validate( $_POST['gnumber'] );
    $gemail = validate( $_POST['gemail'] );

    // $group_name = isset( $_POST['group_name'] ) ? $_POST['group_name'] : '';
    // $group_name_arr = [];
    // if (!empty( $group_name ) && is_array( $group_name ) ) {
    //     foreach( $group_name as $key => $value ) {
    //         $group_name_arr[] = filter_var( $value, FILTER_SANITIZE_STRING );
    //     }
    // }

    $g_id = isset( $_POST['group_name'] ) ? (int) validate( $_POST['group_name'] ) : 0;

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
    $output['redirect'] = 'my-project.php';
    
    // check existence

    if( $found_check > 0 ) {
        $output['message'][] = 'You have already submited your project. Please update your existing submitted project if it\'s required.';
    } else {
        if( isset( $ptitle) && isset( $pdes ) && isset( $file_name) && isset( $g_id ) && isset( $gnumber ) && isset( $gemail ) ) {
        
            if( empty( $ptitle ) && empty( $pdes ) && empty( $file_name ) && empty( $g_id ) && empty( $gnumber) && empty( $gemail ) ) {
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
                // validate mobile
                if( empty( $gnumber ) ) {
                    $output['message'][] = 'Mobile number is required.';
                } elseif( ! preg_match( "/^[0-9]{11}$/", $gnumber ) ) {
                    $output['message'][] = 'Invalid mobile number given. Number should be start with 0 and 11 characters length';
                }
                // validate email
                if( empty( $gemail ) ) {
                    $output['message'][] = 'Email address is required.';
                } elseif( ! filter_var( $gemail, FILTER_VALIDATE_EMAIL ) ) {
                    $output['message'][] = 'Email address is not correct';
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
                if( empty( $g_id ) ) {
                    $output['message'][] = 'Please choose a group.';
                } elseif( !preg_match('/^[0-9\d]+$/', $g_id ) ) {
                    $output['message'][] = 'Invalid group name is given.';
                } 
            }
    
            if( empty( $output['message'] ) ) {
                $output['success'] = false;
    
                if( !empty( $file_name ) ) {
                    if( move_uploaded_file($file_tmp_name, '../assets/images/projects/'.$new_file_name) ) {
                        if( insert( [ 
                            'project_title' => $ptitle, 
                            'gnumber' => $gnumber, 
                            'gemail' => $gemail, 
                            'project_description' => $pdes, 
                            'project_file' => serialize( $new_file_name ), 
                            'username' => $username, 
                            'st_id' => $st_id, 
                            'edited_count' => 0,
                            'g_id' => $g_id
                        ], 'sms_projects' ) ) {
                            $output['success'][] = true;
                            $output['message'][] = "Successfully submited your project.";

                            // $group_members_arr[] = $st_id;
                            // // addd the group member to group table
                            // $group_members = serialize( $group_members_arr );
                            // $insert_group = mysqli_query( $mysqli, "INSERT INTO sms_group( group_members, st_id ) VALUES ( '$group_members', '$st_id' ) ");
                            // $g_id = mysqli_insert_id( $mysqli );

                            // if( ! $insert_group ) {
                            //     $output['success'][] = false;
                            //     $output['message'][] = "Opps! Your project file is not uploading! Please contact administrator.";
                            // }
                                
                                // foreach( $group_members_arr as $key => $member ) {
                                //     $update_reg = mysqli_query( $mysqli, "UPDATE sms_registration SET g_id = '$g_id' WHERE st_id = '$member' " );  
                                // }

                            // if( ! $update_reg ) {
                            //     $output['success'][] = false;
                            //     $output['message'][] = "Opps! Your project file is not uploading! Please contact administrator.";
                            // } 
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
    
    // get all form field value
    $email = validate( $_POST['email'] );
    $mobile = validate( $_POST['mobile'] );
    $program = validate( $_POST['program'] );
    $session = validate( $_POST['session'] );
    $name = validate( $_POST['name'] );
    $id = validate( $_POST['id'] );
    $shift = validate( $_POST['shift'] );
    $password = validate( $_POST['password'] );
    $hash_password = hash( 'sha512', $password );
    $registration_type = isset( $_POST['registration_type'] ) ? validate( $_POST['registration_type'] ) : '';

    $username = $_SESSION['username'];
    $st_type = (int) $_SESSION['login_type'];
    $st_id = (int) $_SESSION['st_id'];

    $file_name = $file_tmp_name = $file_size = $file_type = $extension = '';
    if( isset( $_FILES['profile_pic']['name'] ) ) {
        $file_name = validate( $_FILES['profile_pic']['name'] );
        $file_tmp_name = validate( $_FILES['profile_pic']['tmp_name'] );
        $file_size = validate( $_FILES['profile_pic']['size'] );
        $file_type = validate( $_FILES['profile_pic']['type'] );

        $allowed_extension = [ 'jpg', 'jpeg', 'png', 'gif' ];
        $explode = explode( '.', $file_name );
        $extension = end( $explode );
    }
    $allowed_file_size = 5000000; // 5 MB file size allowed
    $new_file_name = time().'.'.$extension;

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

    if( isset( $email) && isset( $mobile ) && isset( $program) && isset( $session ) && isset( $name ) && isset( $id ) && isset( $shift ) && isset( $username) && isset( $password ) && isset( $registration_type ) ) {
        if( empty( $email) && empty( $mobile ) && empty( $program) && empty( $session ) && empty( $name ) && empty( $id ) && empty( $shift ) && empty( $username) && empty( $password ) && empty( $registration_type ) ) {
            $output['message'][] = 'All fields is required';
        } else {
            // validate email
            if( empty( $email ) ) {
                $output['message'][] = 'Email address is required.';
            } elseif( ! filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
                $output['message'][] = 'Email address is not correct.';
            } elseif( $found_email == 1 ) {
                $output['message'][] = 'Email address is already exist, Please choose another.';
            }
            // validate mobile
            if( empty( $mobile ) ) {
                $output['message'][] = 'Mobile number is required.';
            } elseif( ! preg_match( "/^[0-9]{11}$/", $mobile ) ) {
                $output['message'][] = 'Invalid mobile number given. Number should be start with 0 and 11 characters length';
            }
            // validate program
            if( empty( $program ) ) {
                $output['message'][] = 'Program is required.';
            } elseif( !preg_match('/^[a-zA-Z \d]+$/', $program) ) {
                $output['message'][] = 'Program should be contain only characters.';
            } 
            // validate session
            if( empty( $session ) ) {
                $output['message'][] = 'Session is required.';
            } elseif( !preg_match('/^[a-zA-Z \d]+$/', $session) ) {
                $output['message'][] = 'Session should be contain only characters.';
            } 
            // validate name
            if( empty( $name ) ) {
                $output['message'][] = 'Your name is required.';
            } elseif( !preg_match('/^[a-zA-Z \d]+$/', $name) ) {
                $output['message'][] = 'Your name should be contain only characters.';
            } 
            // validate ID
            if( empty( $id ) ) {
                $output['message'][] = 'Your ID is required.';
            } elseif( !preg_match('/^[a-zA-Z0-9 \d]+$/', $id) ) {
                $output['message'][] = 'Your ID should be contain only alpha numeric characters.';
            } 
            // validate ID
            if( empty( $shift ) ) {
                $output['message'][] = 'Your shift is required.';
            } elseif( !preg_match('/^[a-zA-Z \d]+$/', $shift) ) {
                $output['message'][] = 'Your shift name should be contain only characters.';
            }
            
            // validate password
            if( ! empty( $password ) ) {
                if( strlen( $password ) < 6 ) {
                    $output['message'][] = 'Password length should be at least 6 characters long.';
                }
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
                'email' => $email, 
                'mobile' => $mobile, 
                'program' => $program, 
                'session' => $session, 
                'name' => $name,
                'id' => $id,
                'shift' => $shift,   
            ];

            if( !empty( $file_name ) ) {
                if( move_uploaded_file($file_tmp_name, '../assets/images/profile/'.$new_file_name) ) {
                    $updating_data['profile_pic'] = $new_file_name;
                }
            }

            if( !empty( $password ) ) {
                $updating_data['password'] = $hash_password;
            }
            
            $update = update('sms_registration', $updating_data,  [ 
                'st_id' => $st_id, 
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
    $username = validate( $_POST['username'] );
    $password = validate( $_POST['password'] );
    $login_type = validate( $_POST['login_type'] );
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
    $email = validate( $_POST['email'] );
    $mobile = validate( $_POST['mobile'] );

    $program = validate( $_POST['program'] );
    $session = validate( $_POST['session'] );
    $shift = validate( $_POST['shift'] );

    $name = validate( $_POST['name'] );
    $id = validate( $_POST['id'] );
    $username = validate( $_POST['username'] );
    $password = validate( $_POST['password'] );
    $hash_password = hash( 'sha512', $password );
    $registration_type = isset( $_POST['registration_type'] ) ? validate( $_POST['registration_type'] ) : '';
    
    // Check existing data
    $found_id = '';
    if( ! empty( $id ) ) {
        $id_query = mysqli_query( $mysqli, "SELECT id FROM sms_registration WHERE id = '$id' ");
        $found_id = mysqli_num_rows( $id_query );
    }
    

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
    $output['redirect'] = 'login.php';
    
    // check existence
    if( isset( $email) && isset( $mobile ) && isset( $name ) && isset( $id ) && isset( $username) && isset( $password ) && isset( $registration_type ) ) {
        if( empty( $email) && empty( $mobile ) && empty( $name ) && empty( $id ) && empty( $username) && empty( $password ) && empty( $registration_type ) ) {
            $output['message'][] = 'All fields is required';
        } else {
            // validate email
            if( empty( $email ) ) {
                $output['message'][] = 'Email address is required.';
            } elseif( ! filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
                $output['message'][] = 'Email address is not correct.';
            } elseif( $found_email == 1 ) {
                $output['message'][] = 'Email address is already exist, Please choose another.';
            }
            // validate mobile
            if( empty( $mobile ) ) {
                $output['message'][] = 'Mobile number is required.';
            } elseif( ! preg_match( "/^[0-9]{11}$/", $mobile ) ) {
                $output['message'][] = 'Invalid mobile number given. Number should be start with 0 and 11 characters length';
            }
            if( 1 == $registration_type ) {
                // validate program
                if( empty( $program ) ) {
                    $output['message'][] = 'Program is required.';
                } elseif( !preg_match('/^[a-zA-Z \d]+$/', $program) ) {
                    $output['message'][] = 'Program should be contain only characters.';
                } 
                // validate session
                if( empty( $session ) ) {
                    $output['message'][] = 'Session is required.';
                } elseif( !preg_match('/^[a-zA-Z \d]+$/', $session) ) {
                    $output['message'][] = 'Session should be contain only characters.';
                } 
                // validate ID
                if( empty( $shift ) ) {
                    $output['message'][] = 'Your shift is required.';
                } elseif( !preg_match('/^[a-zA-Z \d]+$/', $shift) ) {
                    $output['message'][] = 'Your shift name should be contain only characters.';
                }
            }
            
            // validate name
            if( empty( $name ) ) {
                $output['message'][] = 'Your name is required.';
            } elseif( !preg_match('/^[a-zA-Z \d]+$/', $name) ) {
                $output['message'][] = 'Your name should be contain only characters.';
            } 
            
            // validate ID
            if( empty( $id ) ) {
                $output['message'][] = 'Your ID is required.';
            } elseif( !preg_match('/^[a-zA-Z0-9- \d]+$/', $id) ) {
                $output['message'][] = 'Your ID should be contain only alpha numeric characters.';
            } elseif( $found_id > 0 ) {
                $output['message'] = 'Your given ID is already exist';
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
            // validate registration type
            if( empty( $registration_type ) ) {
                $output['message'][] = 'Select registration type.';
            } elseif( !in_array( $registration_type, [1, 2]) ) {
                $output['message'][] = 'Invalid registration type given.';
            }
        }

        if( empty( $output['message'] ) ) {

            $inserting_data = [ 
                'email' => $email, 
                'mobile' => $mobile,
                'name' => $name,
                'id' => $id,
                'username' => $username,
                'password' => $hash_password,
                'st_type' => $registration_type,
            ];

            // If the tpe is student
            if( 1 == $registration_type ) {
                $inserting_data['session'] = $session; 
                $inserting_data['shift'] = $shift; 
                $inserting_data['program'] = $program; 
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