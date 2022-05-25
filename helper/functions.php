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
    $string = strip_tags( $string );
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
    session_regenerate_id();
    if( ! isset( $_SESSION['username'] ) && ! isset( $_SESSION['login_type'] ) ) {
        header("location:login.php");
        exit();
    }
}

function student_notification( $student_id = null ) {
    if( ! $student_id ) return;
    global $mysqli;

    $get_goal = mysqli_query( $mysqli, "SELECT goal_title, goal_id, goal_send, is_answer FROM sms_goal WHERE goal_to = '$student_id' ");
    if( mysqli_num_rows( $get_goal) > 0 ) {
        echo "<table class='table table-bordered text-white'>";
        echo "<tr>";
            echo "<th>Title</th>";
            echo "<th>Send Date</th>";
            echo "<th>Status</th>";
        echo "</tr>";
        while ( $get_goal_result = mysqli_fetch_array( $get_goal ) ) {
            $goal_id = $get_goal_result['goal_id'];
            $goal_title = $get_goal_result['goal_title'];
            $goal_send = $get_goal_result['goal_send'];
            $is_answer = $get_goal_result['is_answer'];
            if( empty($is_answer) ) {
                $status = "<a class='btn btn-gradient-danger btn-sm' href='my-project-goal.php' style='z-index:1; position: relative;'>Reply</a>";
            } else {
                $status = "<span class='btn btn-gradient-success btn-sm' href=''>Answered</span>";
            }
            echo "<tr>";
                echo "<td>$goal_title</td>";
                echo "<td>$goal_send</td>";
                echo "<td>$status</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<div class='alert alert-secondary'>You don't have any notification yet.</div>";
    }
}

function student_project_progress() {
    global $mysqli;
    $ses_st_id = (int) $_SESSION['st_id'];
    $get_goal = mysqli_query( $mysqli, "SELECT goal_id FROM sms_goal WHERE goal_to = '$ses_st_id' ");
    $total_goal = mysqli_num_rows( $get_goal );
    if( $total_goal > 0 ) {
        $answer_goal = mysqli_query( $mysqli, "SELECT goal_id FROM sms_goal WHERE goal_to = '$ses_st_id' AND is_answer = 1 ");
        $answered_goal = mysqli_num_rows( $answer_goal );

        $progress = round( $answered_goal / $total_goal * 100 );
        ?>
        <div class="progress" style="height: 15px;">
            <div class="progress-bar progress-bar-striped progress-bar-animated bg-danger" role="progressbar" style="width: <?php echo $progress ?>%" aria-valuenow="<?php echo $progress ?>" aria-valuemin="0" aria-valuemax="100"><?php echo $progress ?>%</div>
        </div>
        <?php
    } else {
        echo "<div class='alert alert-warning'>You didn't submit any project yet.</div>";
    }
    
}

function teacher_notification( $teacher_id = null, $type = null ) {
    
    global $mysqli;
    if( empty( $teacher_id ) || empty( $type ) ) return;

    if( 'goal' == $type ) {
        $query = mysqli_query( $mysqli, "SELECT sg.goal_title, sg.goal_id, sg.goal_received, sr.name, sr.id FROM sms_goal AS sg LEFT JOIN sms_registration AS sr ON sg.goal_to = sr.st_id ORDER BY sg.goal_id DESC ");
    }  elseif( 'project' == $type ) {
        $query = mysqli_query( $mysqli, "SELECT sp.project_title, sp.p_id, sp.uploaded_time, sp.is_approved, sr.name, sr.id FROM sms_projects AS sp LEFT JOIN sms_registration AS sr ON sp.st_id = sr.st_id ORDER BY sp.p_id DESC ");
    }

    echo "<table class='table table-bordered text-white'>";

        if( 'goal' == $type ) {

            echo "<tr>";
                echo "<th>Title</th>";
                echo "<th>Student</th>";
                echo "<th>Submit Date</th>";
            echo "</tr>";

        } elseif ( 'project' ==  $type ) {

            echo "<tr>";
                echo "<th>Proposed Title</th>";
                echo "<th>Student</th>";
                echo "<th>Submit Date</th>";
                echo "<th>Status</th>";
            echo "</tr>";
        } 
        
        while( $get_result = mysqli_fetch_array( $query, MYSQLI_ASSOC ) ) {
            
            if( 'goal' == $type ) {

                $goal_title = $get_result['goal_title'];
                $goal_id = $get_result['goal_id'];
                $name = $get_result['name'];
                $goal_received = $get_result['goal_received'];
                $id = $get_result['id'];

                echo "<tr>";
                    echo "<td>$goal_title</td>";
                    echo "<td>$name ($id)</td>";
                    echo "<td>$goal_received</td>";
                echo "</tr>";

            } elseif( 'project' == $type ) {

                $project_title = $get_result['project_title'];
                $p_id = $get_result['p_id'];
                $uploaded_time = $get_result['uploaded_time'];
                $name = $get_result['name'];
                $id = $get_result['id'];
                $is_approved = $get_result['is_approved'];

                if( $is_approved ) {
                    $status = '<span class="btn btn-gradient-success btn-sm">Approved.</span>';
                } else {
                    $status = '<span class="btn btn-gradient-danger btn-sm">Waiting for approval.</span>';
                }

                echo "<tr>";
                    echo "<td>$project_title</td>";
                    echo "<td>$name ($id)</td>";
                    echo "<td>$uploaded_time</span></td>";
                    echo "<td><span>$status</span></td>";
                echo "</tr>";
            }
        }
    echo "</table>";
}