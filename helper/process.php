<?php
require_once 'functions.php';

// Process registration form 
if( isset( $_POST['form']) && $_POST['form'] = 'registration' ) {
    // get all form field value
    $fname = htmlspecialchars( $_POST['fname'] );
    $lname = htmlspecialchars( $_POST['lname'] );
    $username = htmlspecialchars( $_POST['username'] );
    $password = htmlspecialchars( $_POST['password'] );
    $email = htmlspecialchars( $_POST['email'] );
    
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

    // Hold all erros
    $errors = [];
    // check existence
    if( isset( $fname) && isset( $lname ) && isset( $username) && isset( $password ) && isset( $email ) ) {
        if( empty( $fname ) && empty( $lname ) && empty( $username ) && empty( $password ) && empty( $email ) ) {
            $errors[] = 'All fields is required';
        } else {
            // validate first name
            if( empty( $fname ) ) {
                $errors[] = 'First name is required';
            } elseif( !preg_match('/^[a-zA-Z\d]+$/', $fname) ) {
                $errors[] = 'First name should contain only characters.';
            } elseif( strlen( $fname ) > 20 || strlen( $fname ) < 2 ) {
                $errors[] = 'First name length should be between 2-20 characters long';
            }
            // validate last name
            if( empty( $lname ) ) {
                $errors[] = 'Last name is required';
            } elseif( !preg_match('/^[a-zA-Z\d]+$/', $lname) ) {
                $errors[] = 'Last name should contain only characters.';
            } elseif( strlen( $lname ) > 20 || strlen( $lname ) < 2 ) {
                $errors[] = 'Last name length should be between 2-20 characters long';
            }
            // validate username
            if( empty( $username ) ) {
                $errors[] = 'Last name is required';
            } elseif( !preg_match('/^[a-zA-Z\d]+$/', $username) ) {
                $errors[] = 'Username should contain only characters.';
            } elseif( strlen( $username ) > 20 || strlen( $username ) < 2 ) {
                $errors[] = 'Usernamelength should be between 2-20 characters long';
            } elseif( $found_username == 1 ) {
                $errors[] = 'Username is already exist, Please choose another';
            }
            // validate password
            if( empty( $password ) ) {
                $errors[] = 'Password is required';
            } elseif( strlen( $password ) < 6 ) {
                $errors[] = 'Password length should be at least 6 characters long';
            }
            // validate email
            if( empty( $email ) ) {
                $errors[] = 'Email address is required';
            } elseif( ! filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
                $errors[] = 'Email address is not correct';
            } elseif( $found_email == 1 ) {
                $errors[] = 'Email address is already exist, Please choose another';
            }
        }

        // Check if error found
        if( !empty( $errors ) ) {
            echo "<div class='alert alert-danger'>";
                foreach( $errors as $error ) {
                    echo $error . '.';
                    echo '<br/>';
                }
            echo "</div>";
        } else {
            echo "<div class='alert alert-success'>Successfully registered a new account.</div>";
        }
    }
}