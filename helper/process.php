<?php
require_once 'functions.php';

// Process login form 
if( isset( $_POST['form']) && $_POST['form'] == 'login' ) {
    // get all form field value
    $username = htmlspecialchars( $_POST['username'] );
    $password = htmlspecialchars( $_POST['password'] );
    $login_type = htmlspecialchars( $_POST['login_type'] );
    $hash_password = hash( 'sha512', $password );
    $status = '';

    // Hold all erros
    $errors = [];
    $errors['success'] = false;

    // Check username and password
    $check_user = "SELECT username,status FROM sms_registration WHERE username = '$username' AND password = '$hash_password' ";
    $user_query = mysqli_query( $mysqli, $check_user );
    $found_user = mysqli_num_rows( $user_query );
    if( $found_user ) {
        $result = mysqli_fetch_array( $user_query );
        $status = $result['status'];
    }
    

    if( isset( $username) && isset( $password ) ) {
        if( empty( $username ) && empty( $password ) ) {
            $errors[] = 'All fields is required';
        } else {
            // validate username
            if( empty( $username ) ) {
                $errors[] = 'Username is required';
            } elseif( empty( $password ) ) {
                $errors[] = 'Password is required';
            } elseif( $found_user == 0 ) {
                $errors[] = 'Username or password is incorrect';
            } elseif( $status == 1 ) {
                $errors[] = 'Your account is not active, Please contact administrative';
            } elseif( !in_array( $login_type, [1,2] ) ) {
                $errors[] = 'Invalid login type given';
            }
        }

        if( $errors['success'] ) {
            if( $found_user == 1 ) {
                echo "<div class='alert alert-success success'>Successfully Logged, Now you are redirecting....</div>";
                $_SESSION['username'] = $username;
                $_SESSION['login_type'] = $login_type; 
            } else {
                echo "<div class='alert alert-warning warning'>Opps! Something wen't wrong! Please contact administrator.</div>";
            }
        }

        // echo '<pre>';
        //      print_r(  $errors );
        // echo '</pre>';
        // Check if error found
        if( !empty( $errors ) ) {
            echo "<div class='alert alert-danger danger'>";
                foreach( $errors as $error ) {
                    $output[] = $error . '<br/>';
                }
            echo "</div>";
        } else {
            
        }
        echo json_encode($errors);
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
            $output[] = 'All fields is required';
        } else {
            // validate first name
            if( empty( $fname ) ) {
                $output['message'][] = 'First name is required.';
            } elseif( !preg_match('/^[a-zA-Z\d]+$/', $fname) ) {
                $output['message'][] = 'First name should contain only characters.';
            } elseif( strlen( $fname ) > 20 || strlen( $fname ) < 2 ) {
                $output['message'][] = 'First name length should be between 2-20 characters long.';
            }
            // validate last name
            if( empty( $lname ) ) {
                $output['message'][] = 'Last name is required';
            } elseif( !preg_match('/^[a-zA-Z\d]+$/', $lname) ) {
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