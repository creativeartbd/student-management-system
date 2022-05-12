<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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
function registration () {
    
}