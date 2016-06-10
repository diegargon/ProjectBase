<?php
if (!defined('IN_WEB')) { exit; }
/* switch to class, remove later
function db_connect() {
    global $dblink;
    
    $dblink = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB);
    
    if (!$dblink) {
        die ('Failed to connect to MySQL: ' . mysqli_connect_errno());
        exit();
    }   
    db_query("SET NAMES ". DB_CHARSET ."");
    
    return true;
}

function db_query($string) {
    global $dblink;
        
    $query = mysqli_query($dblink, $string) or db_die($query);

    return $query;
}

function db_fetch($query) {
	$row = mysqli_fetch_assoc($query);
	return $row;
}

function db_escape_string($var) {
	global $dblink;
        
	return mysqli_real_escape_string($dblink, $var);
}

function db_num_rows($query) {
	return mysqli_num_rows($query);
}

function db_close() {    
    global $dblink;
    
	if (!$dblink) {
	    die('Could not connect: ' . mysqli_error($dblink));
	}
	mysqli_close($dblink);
}

function db_die($query) {
   global $dblink;
   
   echo "\n<b>Error: Unable to retrieve information.</b>";
   echo "\n<br>$query";
   echo "\n<br>reported: ".mysqli_error($dblink);
   db_close();
   exit;
}

function db_insert_id() {
    global $dblink;
    
    if(!($id = mysqli_insert_id($dblink)) ) {
        die('Could not connect: ' . mysqli_error($dblink));
        db_close();
        exit;
    }

    return $id;
}

function db_free_result(& $query) {
    mysqli_free_result($query);
}

function db_get_next_num ($field, $table) {
    $q = "SELECT MAX( $field ) AS max FROM `$table`;";    
    $query = db_query($q);
    $row = db_fetch($query);
    
    return ++$row['max'];
}
 * 
 */