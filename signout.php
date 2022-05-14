<?php 
require_once 'helper/functions.php';
if( isset( $_SESSION['username'] ) && isset( $_SESSION['login_type'] ) && isset( $_SESSION['st_id'] ) ) {
    unset( $_SESSION['username'] );
    unset( $_SESSION['login_type'] );
    unset( $_SESSION['st_id'] );
    header("location:index.php");
    exit();
} 