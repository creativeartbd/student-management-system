<?php 
require_once 'helper/functions.php';
if( isset( $_SESSION['username'] ) && isset( $_SESSION['login_type'] ) ) {
    unset( $_SESSION['username'] );
    unset( $_SESSION['login_type'] );
    header("location:index.php");
    exit();
} 