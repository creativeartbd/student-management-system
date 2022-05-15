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

function validate( $string ) {
    global $mysqli;
    $string = htmlspecialchars( $string );
    $string = trim( $string );
    $string = mysqli_real_escape_string( $mysqli, $string );
    return $string;
}

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

function select( $table_name, array $columns, $where = null, $limit = 1 ) {
    
    global $mysqli;
    $columns = implode(', ', $columns );
    $sql = "SELECT $columns FROM $table_name";

    if( $where ) {
        $sql .= " WHERE $where ";
    }
    $sql .= " LIMIT $limit";
    $query = mysqli_query( $mysqli, $sql );

    if( mysqli_num_rows( $query ) > 0 ) {
        $result = mysqli_fetch_array($query, MYSQLI_ASSOC);
        return $result;
    }
    return;
    
} 

function update ( $table_name, array $columns_values, $where_cols ) {
    global $mysqli;

    $cols_values = [];
    foreach( $columns_values as $key => $col_value ) {
        $cols_values[] = " $key = '$col_value' ";
    }
    $cols_values = implode(', ', $cols_values );

    $where_col_array = [];
    foreach( $where_cols as $key => $where_col ) {
        $where_col_array[] = " $key = '$where_col' ";
    }
    $where_col_array = implode( ' AND ', $where_col_array );

    $sql = "UPDATE $table_name SET $cols_values WHERE $where_col_array ";
    $query = mysqli_query( $mysqli, $sql );
    return $query;
}

function check_user_login_status() {
    if( ! isset( $_SESSION['username'] ) && ! isset( $_SESSION['login_type'] ) ) {
        header("location:login.php");
        exit();
    }
}