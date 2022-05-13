<?php
require_once 'functions.php';

// Process registration form 
if( isset( $_POST['form']) && $_POST['form'] == 'submitproject' ) {
    // get all form field value
    $ptitle = htmlspecialchars( $_POST['ptitle'] );
    $pdes = htmlspecialchars( $_POST['pdes'] );

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

    // Check existence
    $check_existnece = "SELECT username, edited_count FROM projects WHERE username = '$username' ";
    $check_query = mysqli_query( $mysqli, $check_existnece );
    $found_check = mysqli_num_rows( $check_query );

    // Hold all errors
    $output['message'] = [];
    $output['success'] = false;
    
    // check existence

    if( $found_check > 0 ) {
        $output['message'][] = 'You have already submited your project. Please update your existing submitted project if it\'s required.';
    } else {
        if( isset( $ptitle) && isset( $pdes ) && isset( $file_name) ) {
        
            if( empty( $ptitle ) && empty( $pdes ) && empty( $file_name ) ) {
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
            }
    
            if( empty( $output['message'] ) ) {
                $output['success'] = false;
    
                if( !empty( $file_name ) ) {
                    if( move_uploaded_file($file_tmp_name, '../assets/images/projects/'.$new_file_name) ) {
                        if( insert( [ 
                            'project_title' => $ptitle, 
                            'project_description' => $pdes, 
                            'project_file' => serialize($new_file_name), 
                            'username' => $username, 
                            'edited_count' => 0,
                        ], 'projects' ) ) {
                            $output['success'] = true;
                            $output['message'] = "Successfully submited your project.";
                        } else {
                            $output['success'] = false;
                            $output['message'] = "Opps! Something wen't wrong! Please contact administrator.";
                        }
    
                        $sql_update = "UPDATE projects SET edited_count = edited_count + 1 WHERE username = '$username'";
                        $sql_query = mysqli_query($mysqli, $sql_update);
    
                    } else {
                        $output['success'] = false;
                        $output['message'] = "Opps! Your project file is not uploading! Please contact administrator.";
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
    $check_user = "SELECT username, status FROM sms_registration WHERE username = '$username' AND password = '$hash_password' AND st_type = '$login_type' ";
    $user_query = mysqli_query( $mysqli, $check_user );
    $found_user = mysqli_num_rows( $user_query );

    if( $found_user ) {
        $result = mysqli_fetch_array( $user_query );
        $status = $result['status'];
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
    $fname = htmlspecialchars( $_POST['fname'] );
    $lname = htmlspecialchars( $_POST['lname'] );
    $username = htmlspecialchars( $_POST['username'] );
    $password = htmlspecialchars( $_POST['password'] );
    $hash_password = hash( 'sha512', $password );
    $email = htmlspecialchars( $_POST['email'] );
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
    if( isset( $fname) && isset( $lname ) && isset( $username) && isset( $password ) && isset( $email ) && isset( $registration_type ) ) {
        if( empty( $fname ) && empty( $lname ) && empty( $username ) && empty( $password ) && empty( $email ) && empty( $registration_type ) ) {
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
        }

        if( empty( $output['message'] ) ) {
            $output['success'] = false;
            if( insert( [ 
                'fname' => $fname, 
                'lname' => $lname, 
                'username' => $username, 
                'password' => $hash_password, 
                'email' => $email,
                'st_type' => $registration_type,
            ], 'sms_registration' ) ) {
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