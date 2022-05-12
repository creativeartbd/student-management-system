<?php 
session_start();
ini_set('display_errors', 1);
// Report simple running errors
error_reporting(E_ERROR | E_WARNING | E_PARSE);
// Reporting E_NOTICE can be good too (to report uninitialized
// variables or catch variable name misspellings ...)
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
// Report all errors except E_NOTICE
error_reporting(E_ALL & ~E_NOTICE);
// Report all PHP errors (see changelog)
error_reporting(E_ALL);
// Report all PHP errors
error_reporting(-1);
// Same as error_reporting(E_ALL);
ini_set('error_reporting', E_ALL);

$host = 'localhost';
$user = 'root';
$password = 'root';
$db = 'student_mng_system';

$mysqli = new mysqli( $host, $user, $password, $db );
// Check connection
if ($mysqli -> connect_errno) {
    die(" Failed to connect to MySQL: " . $mysqli -> connect_error);
    exit();
}

// Set the project root folder location
$pathInPieces = explode('/', $_SERVER['DOCUMENT_ROOT']);
define( 'ROOT', $pathInPieces[0] );
define( 'PROJECT_TITLE', 'Student Management System' );

// Registration funciton 
function insert ( $data, $table_name ) {

    global $mysqli;
    if( empty( $data ) || empty( $table_name ) )  {
        return false;
    }

    $columns = implode( ', ', array_keys( $data ) );
    $columns_values = [];
    foreach( $data as $value ) {
        $columns_values[] = "'$value'";
    }
    $columns_values = implode( ', ', $columns_values );

    $sql = "INSERT INTO {$table_name} ( $columns ) VALUES ( $columns_values ) ";
    $query = mysqli_query( $mysqli, $sql );
    if( $query ) {
        return true;
    }
    echo mysqli_error( $mysqli );
    return false;
}

function check_user_login_status() {
    if( ! isset( $_SESSION['username'] ) && ! isset( $_SESSION['login_type'] ) ) {
        header("location:login.php");
        exit();
    }
}